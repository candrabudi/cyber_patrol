<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Channel;
use App\Models\Customer;
use App\Models\ErrorLog;
use App\Models\GamblingDeposit;
use App\Models\GamblingDepositAccount;
use App\Models\GamblingDepositAttachment;
use App\Models\Nns;
use App\Models\Provider;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Zxing\QrReader;

class SGamblingDepositController extends Controller
{
    public function index()
    {
        return view('superadmin.gambling_deposits.index');
    }
    public function data(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = GamblingDeposit::with(['channel.customer', 'creator', 'gamblingDepositAccounts']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('website_name', 'like', "%{$search}%")
                    ->orWhere('website_url', 'like', "%{$search}%")
                    ->orWhere('account_name', 'like', "%{$search}%")
                    ->orWhere('account_number', 'like', "%{$search}%")
                    ->orWhereHas('channel.customer', function ($q2) use ($search) {
                        $q2->where('full_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('creator', function ($q3) use ($search) {
                        $q3->where('username', 'like', "%{$search}%");
                    });
            });
        }

        $paginated = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $data = $paginated->getCollection()->transform(function ($item) {
            $accountChannel = $item->gamblingDepositAccounts->first();
            $channelName = $accountChannel ? $accountChannel->channel_name : null;

            return [
                'id' => $item->id,
                'website_name' => $item->website_name,
                'website_url' => $item->website_url,
                'channel' => [
                    'channel_type' => $item->channel ? $item->channel->channel_type : $accountChannel->channel_type,
                    'customer' => [
                        'full_name' => $item->channel?->customer?->full_name,
                    ],
                ],
                'account_name' => $item->account_name,
                'account_number' => $item->account_number,
                'creator' => [
                    'username' => $item->creator?->username,
                ],
                'created_at' => $item->created_at,
                'updated_at' => $item->updated_at,
                'report_status' => $item->report_status,
                'is_non_member' => $item->is_non_member,
                'channel_name' => $channelName,
            ];
        });

        return response()->json([
            'data' => $data,
            'current_page' => $paginated->currentPage(),
            'last_page' => $paginated->lastPage(),
            'total' => $paginated->total(),
        ]);
    }

    public function detail($id)
    {
        $gamblingDeposit = GamblingDeposit::with([
            'channel',
            'attachments',
            'logs.changer',
            'gamblingDepositAccount'
        ])->findOrFail($id);

        return view('superadmin.gambling_deposits.detail', compact('gamblingDeposit'));
    }

    public function create()
    {
        $banks = Bank::all();
        $providers = Provider::all();
        return view('superadmin.gambling_deposits.create', compact('banks', 'providers'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'website_name' => 'required|string|max:255',
                'website_url' => 'required|url',
                'channel_type' => 'required|in:transfer,ewallet,qris,virtual_account,pulsa',
                'account_name' => 'nullable|string|max:255',
                'account_number' => 'nullable|string|max:50',
                'website_proofs' => 'required|file|mimes:jpeg,jpg,png,pdf',
                'account_proofs' => 'nullable|file|mimes:jpeg,jpg,png,pdf',
                'qris_proofs' => 'nullable|file|mimes:jpeg,jpg,png,pdf',
            ], [
                'website_name.required' => 'Nama website harus diisi.',
                'website_url.required' => 'URL website harus diisi.',
                'website_url.url' => 'Format URL website tidak valid.',
                'channel_type.required' => 'Tipe channel harus dipilih.',
                'channel_type.in' => 'Tipe channel tidak valid.',
                'channel_id.exists' => 'Channel tidak ditemukan.',
                'website_proofs.required' => 'Bukti website harus diunggah.',
                'website_proofs.mimes' => 'Format bukti website harus berupa jpeg, jpg, png, atau pdf.',
                // 'account_proofs.required' => 'Bukti akun harus diunggah.',
                'account_proofs.mimes' => 'Format bukti akun harus berupa jpeg, jpg, png, atau pdf.',
                'qris_proofs.mimes' => 'Format bukti QRIS harus berupa jpeg, jpg, png, atau pdf.',
            ]);

            $deposit = new GamblingDeposit();
            $deposit->website_name = $validated['website_name'];
            $deposit->website_url = $validated['website_url'];
            $deposit->is_confirmed_gambling = 0;
            $deposit->is_accessible = 0;

            $gamblingDepositAccount = new GamblingDepositAccount();

            switch ($validated['channel_type']) {
                case 'qris':
                    $accountNumber = '';
                    $accountName = '';
                    $gatewayDomain = null;

                    if ($request->hasFile('qris_proofs')) {
                        $proofFile = $request->file('qris_proofs');
                        $qrReader = new QrReader($proofFile->getRealPath());
                        $qrText = $qrReader->text();

                        if ($qrText && str_starts_with($qrText, '000201')) {
                            $parsedQR = $this->parseEMV($qrText);

                            $accountName = $parsedQR['59'] ?? '';

                            $merchantData = $parsedQR['26'] ?? [];

                            if (is_array($merchantData)) {
                                $accountNumber = $merchantData['01'] ?? '';
                            } else {
                                $accountNumber = $merchantData;
                            }

                            $gatewayDomain = null;

                            $patterns = [
                                '/ID\.[A-Z]{2}\.([A-Z0-9\-]+)\.WWW/i',
                                '/ID\.[A-Z]{2}\.([A-Z0-9\-]+)\.(com|id|co\.id|net|org|biz)/i',
                                '/www\.([a-z0-9\-]+)\.(com|id|co\.id|net|org|biz)/i',
                                '/www\.([a-z0-9\-]+)/i',
                            ];

                            foreach ($patterns as $pattern) {
                                if (preg_match($pattern, strtolower($qrText), $matches)) {
                                    $gatewayDomain = strtoupper($matches[1]);
                                    break;
                                }
                            }
                        }
                    }

                    $prefix8 = substr($accountNumber, 0, 8);
                    // return $prefix8;
                    $foundChannel = Channel::where('channel_type', 'qris')
                        ->where('channel_code', $prefix8)
                        ->first();

                    if ($foundChannel) {
                        $deposit->channel_id = $foundChannel->id;
                        $deposit->is_non_member = 0;
                    } else {
                        $foundNns = Nns::where('code', $accountNumber)->first();

                        if ($foundNns) {
                            $deposit->channel_id = null;
                            $deposit->is_non_member = 1;

                            $gamblingDepositAccount->account_name = $accountName;
                            $gamblingDepositAccount->account_number = $accountNumber;
                            $gamblingDepositAccount->channel_name = $foundNns->name;
                            $gamblingDepositAccount->channel_code = $foundNns->code;
                            $gamblingDepositAccount->channel_type = 'qris';
                            $gamblingDepositAccount->is_non_member = 1;
                        } else {
                            if (!$gatewayDomain) {
                                return response()->json([
                                    'success' => 0,
                                    'message' => 'Maaf, Qris gateway tidak di temukan.'
                                ], 500);
                            }
                            $deposit->channel_id = null;
                            $deposit->is_non_member = 1;

                            $gamblingDepositAccount->account_name = $accountName;
                            $gamblingDepositAccount->account_number = $accountNumber;
                            $gamblingDepositAccount->channel_name = $gatewayDomain;
                            $gamblingDepositAccount->channel_code = $prefix8;
                            $gamblingDepositAccount->channel_type = 'qris';
                            $gamblingDepositAccount->is_non_member = 1;
                        }
                    }

                    $deposit->account_name = $accountName;
                    $deposit->account_number = $accountNumber;
                    break;

                case 'virtual_account':
                    $accountNumber = $validated['account_number'] ?? '';
                    $prefix4 = substr($accountNumber, 0, 4);
                    $prefix5 = substr($accountNumber, 0, 5);

                    $foundChannel = Channel::where('channel_type', 'virtual_account')
                        ->where(function ($q) use ($prefix4, $prefix5) {
                            $q->where('channel_code', $prefix4)
                                ->orWhere('channel_code', $prefix5);
                        })->first();

                    $bank = Bank::where('id', $request->channel_id)
                        ->first();

                    if ($foundChannel) {
                        $deposit->customer_id = $foundChannel->customer_id;
                        $deposit->channel_id = $foundChannel->id;
                        $deposit->is_non_member = 0;
                    } else {
                        $deposit->channel_id = null;
                        $deposit->is_non_member = 1;

                        $gamblingDepositAccount->account_name = $validated['account_name'] ?? '';
                        $gamblingDepositAccount->account_number = $accountNumber;
                        $gamblingDepositAccount->channel_name = $bank->name;
                        $gamblingDepositAccount->channel_code = $prefix4 ?: $prefix5;
                        $gamblingDepositAccount->channel_type = 'virtual_account';
                        $gamblingDepositAccount->is_non_member = 1;
                    }

                    $deposit->account_name = $validated['account_name'] ?? '';
                    $deposit->account_number = $accountNumber;
                    break;

                case 'pulsa':
                    $accountNumber = $validated['account_number'] ?? '';

                    $prefix4 = substr($accountNumber, 0, 4);

                    $provider = Provider::whereJsonContains('prefixes', $prefix4)->first();

                    if ($provider) {
                        $foundChannel = Channel::where('channel_name', $provider->name)
                            ->whereJsonContains('channel_code', $prefix4)
                            ->first();

                        if ($foundChannel) {
                            $deposit->channel_id = $foundChannel->id;
                            $deposit->is_non_member = 0;
                        } else {
                            $deposit->channel_id = null;
                            $deposit->is_non_member = 1;

                            $gamblingDepositAccount->account_name = $validated['account_name'] ?? '';
                            $gamblingDepositAccount->account_number = $accountNumber;
                            $gamblingDepositAccount->channel_name = $provider->name;
                            $gamblingDepositAccount->channel_code = $prefix4;
                            $gamblingDepositAccount->channel_type = 'pulsa';
                            $gamblingDepositAccount->is_non_member = 1;
                        }
                    } else {
                        return response()->json([
                            'success' => 0,
                            'message' => 'Maaf, kode nomor handphone tidak terdaftar.'
                        ], 500);
                    }

                    $deposit->account_name = $validated['account_name'] ?? '';
                    $deposit->account_number = $accountNumber;

                    break;

                case 'transfer':
                    $accountNumber = $validated['account_number'] ?? '';
                    $bank = Bank::where('id', $request->channel_id)
                        ->first();

                    $foundChannel = Channel::where('channel_name', $bank->name)
                        ->first();

                    if ($foundChannel) {
                        $deposit->customer_id = $foundChannel->customer_id;
                        $deposit->channel_id = $foundChannel->id;
                        $deposit->channel_id = $foundChannel->id;
                        $deposit->is_non_member = 0;
                    } else {
                        $deposit->channel_id = null;
                        $deposit->is_non_member = 1;

                        $gamblingDepositAccount->account_name = $validated['account_name'] ?? '';
                        $gamblingDepositAccount->account_number = $accountNumber;
                        $gamblingDepositAccount->channel_name = $bank->name;
                        $gamblingDepositAccount->channel_code = $bank->code;
                        $gamblingDepositAccount->channel_type = 'transfer';
                        $gamblingDepositAccount->is_non_member = 1;
                    }

                    $deposit->account_name = $validated['account_name'] ?? '';
                    $deposit->account_number = $accountNumber;
                    break;
                default:
                    $deposit->channel_id = $validated['channel_id'] ?? null;
                    $deposit->account_name = $validated['account_name'] ?? '';
                    $deposit->account_number = $validated['account_number'] ?? '';
                    $deposit->is_non_member = 1;
                    break;
            }

            $deposit->created_by = Auth::id();
            $deposit->save();

            if ($deposit->is_non_member) {
                $gamblingDepositAccount->gambling_deposit_id = $deposit->id;
                $gamblingDepositAccount->save();
            }

            if ($request->hasFile('website_proofs')) {
                $path = $request->file('website_proofs')->store('attachments/website_proof');
                GamblingDepositAttachment::create([
                    'gambling_deposit_id' => $deposit->id,
                    'attachment_type' => 'website_proof',
                    'file_path' => $path,
                ]);
            }

            if ($request->hasFile('account_proofs')) {
                $path = $request->file('account_proofs')->store('attachments/account_proof');
                GamblingDepositAttachment::create([
                    'gambling_deposit_id' => $deposit->id,
                    'attachment_type' => 'account_proof',
                    'file_path' => $path,
                ]);
            }

            if ($request->hasFile('qris_proofs') && $validated['channel_type'] === 'qris') {
                $path = $request->file('qris_proofs')->store('attachments/qris_proof');
                GamblingDepositAttachment::create([
                    'gambling_deposit_id' => $deposit->id,
                    'attachment_type' => 'qris_proof',
                    'file_path' => $path,
                ]);
            }

            Log::info('Gambling deposit created', ['id' => $deposit->id, 'user_id' => Auth::id()]);

            return response()->json([
                'success' => 1,
                'message' => 'Data berhasil disimpan.',
                'data' => $deposit->load('attachments', 'channel', 'creator')
            ], 201);
        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $field => $messages) {
                $errors[$field] = $messages;
            }

            return response()->json([
                'success' => 0,
                'message' => 'Validasi gagal, periksa input Anda.',
                'errors' => $errors
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error creating gambling deposit', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'stack_trace' => $e->getTraceAsString(),
                'user_id' => Auth::id(),
                'ip' => $request->ip(),
            ]);

            ErrorLog::create([
                'error_message' => $e->getMessage(),
                'stack_trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'user_id' => Auth::id(),
                'ip_address' => $request->ip(),
                'occurred_at' => now(),
            ]);

            return response()->json([
                'success' => 0,
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi nanti.'
            ], 500);
        }
    }

    private function parseEMV($data)
    {
        $result = [];
        $i = 0;

        while ($i < strlen($data)) {
            $tag = substr($data, $i, 2);
            $i += 2;
            $length = intval(substr($data, $i, 2));
            $i += 2;
            $value = substr($data, $i, $length);
            $i += $length;

            if (in_array($tag, ['26', '62'])) {
                $value = $this->parseEMV($value);
            }

            $result[$tag] = $value;
        }

        return $result;
    }
}
