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
use App\Models\GamblingDepositLog;
use App\Models\Nns;
use App\Models\Provider;
use App\Models\Website;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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

        // filter tambahan
        $memberFilter = $request->get('member'); // "member", "non-member", atau null
        $dateStart = $request->get('date_start'); // format YYYY-MM-DD
        $dateEnd = $request->get('date_end');     // format YYYY-MM-DD

        $query = GamblingDeposit::with(['channel.customer', 'creator', 'gamblingDepositAccounts', 'website']);

        // search
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

        // filter member / non-member
        if ($memberFilter) {
            if ($memberFilter === 'member') {
                $query->where('is_non_member', false);
            } elseif ($memberFilter === 'non-member') {
                $query->where('is_non_member', true);
            }
        }

        // filter date range
        if ($dateStart && $dateEnd) {
            $query->whereBetween('created_at', [
                $dateStart . ' 00:00:00',
                $dateEnd . ' 23:59:59'
            ]);
        }

        $paginated = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $data = $paginated->getCollection()->transform(function ($item) {
            $accountChannel = $item->gamblingDepositAccounts->first();
            $channelName = $accountChannel ? $accountChannel->channel_name : null;

            return [
                'id' => $item->id,
                'website_name' => $item->website->website_name,
                'website_url' => $item->website->website_url,
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
                'website_name'   => 'required|string|max:255',
                'website_url'    => 'required|url',
                'website_proofs' => 'required|file|mimes:jpeg,jpg,png,pdf',

                'accounts'                   => 'required|array|min:1',
                'accounts.*.channel_id'      => 'nullable',
                'accounts.*.channel_type'    => 'required|in:transfer,ewallet,qris,virtual_account,pulsa',
                'accounts.*.account_name'    => 'nullable|string|max:255',
                'accounts.*.account_number'  => 'nullable|string|max:50',
                'accounts.*.account_proofs'  => 'nullable|file|mimes:jpeg,jpg,png,pdf',
                'accounts.*.qris_proofs'     => 'nullable|file|mimes:jpeg,jpg,png,pdf',
            ], [
                'website_name.required' => 'Nama website harus diisi.',
                'website_url.required'  => 'URL website harus diisi.',
                'website_url.url'       => 'Format URL website tidak valid.',
                'website_proofs.required' => 'Bukti website harus diunggah.',
                'website_proofs.mimes'  => 'Format bukti website harus berupa jpeg, jpg, png, atau pdf.',

                'accounts.required'     => 'Minimal 1 rekening harus diisi.',
                'accounts.*.channel_type.required' => 'Tipe channel harus dipilih.',
            ]);

            DB::beginTransaction();

            // 1. Simpan Website
            $website = Website::create([
                'website_name'   => $validated['website_name'],
                'website_url'    => $validated['website_url'],
                'is_confirmed_gambling' => false,
                'is_accessible'  => false,
                'created_by'     => Auth::id(),
            ]);

            // Simpan bukti website
            if ($request->hasFile('website_proofs')) {
                $path = $request->file('website_proofs')->store('attachments/website_proof');
                $website->update(['website_proofs' => $path]);
            }

            foreach ($validated['accounts'] as $index => $acc) {
                $deposit = new GamblingDeposit();
                $deposit->website_id = $website->id;
                $deposit->created_by = Auth::id();

                $gamblingDepositAccount = new GamblingDepositAccount();

                switch ($acc['channel_type']) {
                    case 'qris':
                        $accountNumber = '';
                        $accountName = '';
                        $gatewayDomain = null;

                        if ($request->hasFile("accounts.$index.qris_proofs")) {
                            $proofFile = $request->file("accounts.$index.qris_proofs");
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
                        $accountNumber = $acc['account_number'] ?? '';
                        $prefix4 = substr($accountNumber, 0, 4);
                        $prefix5 = substr($accountNumber, 0, 5);

                        $foundChannel = Channel::where('channel_type', 'virtual_account')
                            ->where(function ($q) use ($prefix4, $prefix5) {
                                $q->where('channel_code', $prefix4)
                                    ->orWhere('channel_code', $prefix5);
                            })->first();

                        $bank = Bank::where('id', $acc['channel_id'])->first();

                        if ($foundChannel) {
                            $deposit->customer_id = $foundChannel->customer_id;
                            $deposit->channel_id = $foundChannel->id;
                            $deposit->is_non_member = 0;
                        } else {
                            $deposit->channel_id = null;
                            $deposit->is_non_member = 1;

                            $gamblingDepositAccount->account_name = $acc['account_name'] ?? '';
                            $gamblingDepositAccount->account_number = $accountNumber;
                            $gamblingDepositAccount->channel_name = $bank->name;
                            $gamblingDepositAccount->channel_code = $prefix4 ?: $prefix5;
                            $gamblingDepositAccount->channel_type = 'virtual_account';
                            $gamblingDepositAccount->is_non_member = 1;
                        }

                        $deposit->account_name = $acc['account_name'] ?? '';
                        $deposit->account_number = $accountNumber;
                        break;

                    case 'pulsa':
                        $accountNumber = $acc['account_number'] ?? '';
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

                                $gamblingDepositAccount->account_name = $acc['account_name'] ?? '';
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

                        $deposit->account_name = $acc['account_name'] ?? '';
                        $deposit->account_number = $accountNumber;
                        break;

                    case 'transfer':
                        $accountNumber = $acc['account_number'] ?? '';
                        $bank = Bank::where('id', $acc['channel_id'])->first();

                        $foundChannel = Channel::where('channel_name', $bank->name)->first();

                        if ($foundChannel) {
                            $deposit->customer_id = $foundChannel->customer_id;
                            $deposit->channel_id = $foundChannel->id;
                            $deposit->is_non_member = 0;
                        } else {
                            $deposit->channel_id = null;
                            $deposit->is_non_member = 1;

                            $gamblingDepositAccount->account_name = $acc['account_name'] ?? '';
                            $gamblingDepositAccount->account_number = $accountNumber;
                            $gamblingDepositAccount->channel_name = $bank->name;
                            $gamblingDepositAccount->channel_code = $bank->code;
                            $gamblingDepositAccount->channel_type = 'transfer';
                            $gamblingDepositAccount->is_non_member = 1;
                        }

                        $deposit->account_name = $acc['account_name'] ?? '';
                        $deposit->account_number = $accountNumber;
                        break;

                    default:
                        $deposit->channel_id = $acc['channel_id'] ?? null;
                        $deposit->account_name = $acc['account_name'] ?? '';
                        $deposit->account_number = $acc['account_number'] ?? '';
                        $deposit->is_non_member = 1;
                        break;
                }


                $deposit->save();
                $gamblingDepositAccount->gambling_deposit_id = $deposit->id;
                $gamblingDepositAccount->save();
                if ($request->hasFile("accounts.$index.account_proofs")) {
                    $path = $request->file("accounts.$index.account_proofs")->store('attachments/account_proof');
                    $deposit->account_proof = $path;
                }

                if ($request->hasFile("accounts.$index.account_proofs")) {
                    $path = $request->file("accounts.$index.account_proofs")->store('attachments/account_proof');
                    GamblingDepositAttachment::create([
                        'gambling_deposit_id' => $deposit->id,
                        'attachment_type' => 'account_proof',
                        'file_path' => $path,
                    ]);
                }
            }

            DB::commit();

            Log::info('Website & gambling deposits created', [
                'website_id' => $website->id,
                'user_id'    => Auth::id()
            ]);

            return response()->json([
                'success' => 1,
                'message' => 'Data berhasil disimpan.',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => 0,
                'message' => 'Validasi gagal, periksa input Anda.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error creating website/gambling deposit', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => 0,
                'message' => 'Terjadi kesalahan pada server. Silakan coba lagi nanti.',
                'error' => $e->getMessage(),
                'request' => $request
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

    public function destroy($id)
    {
        $deposit = GamblingDeposit::find($id);

        if (!$deposit) {
            return response()->json([
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        if ($deposit->report_status !== 'pending') {
            return response()->json([
                'message' => 'Hanya data dengan status pending yang bisa dihapus.'
            ], 403);
        }

        GamblingDepositAttachment::where('gambling_deposit_id', $deposit->id)->delete();
        GamblingDepositLog::where('gambling_deposit_id', $deposit->id)->delete();
        GamblingDepositAccount::where('gambling_deposit_id', $deposit->id)->delete();

        $deposit->delete();

        return response()->json([
            'message' => 'Data berhasil dihapus.'
        ], 200);
    }
}
