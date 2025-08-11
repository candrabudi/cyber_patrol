<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\ErrorLog;
use App\Models\GamblingDeposit;
use App\Models\GamblingDepositAttachment;
use App\Models\Provider;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Zxing\QrReader;

class AGamblingDepositController extends Controller
{
    public function index()
    {
        return view('admin.gambling_deposits.index');
    }

    public function data(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = GamblingDeposit::with(['channel.customer', 'creator'])
            ->where('created_by', Auth::id());

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

        $data = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($data);
    }

    public function detail($id)
    {
        $gamblingDeposit = GamblingDeposit::with([
            'channel',
            'attachments',
            'logs.changer'
        ])
            ->where('created_by', Auth::id())
            ->findOrFail($id);

        return view('admin.gambling_deposits.detail', compact('gamblingDeposit'));
    }

    public function create()
    {
        $channels = Channel::all();
        return view('admin.gambling_deposits.create', compact('channels'));
    }

    public function store(Request $request)
    {
        // return $request;
        try {
            $validated = $request->validate([
                'website_name' => 'required|string|max:255',
                'website_url' => 'required|url',
                'channel_type' => 'required|in:transfer,ewallet,qris,virtual_account,pulsa',
                'channel_id' => 'nullable|exists:channels,id',
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
                'account_proofs.required' => 'Bukti akun harus diunggah.',
                'account_proofs.mimes' => 'Format bukti akun harus berupa jpeg, jpg, png, atau pdf.',
                'qris_proofs.mimes' => 'Format bukti QRIS harus berupa jpeg, jpg, png, atau pdf.',
            ]);

            $deposit = new GamblingDeposit();
            $deposit->website_name = $validated['website_name'];
            $deposit->website_url = $validated['website_url'];
            $deposit->is_confirmed_gambling = false;
            $deposit->is_accessible = false;

            if ($validated['channel_type'] === 'qris') {
                if ($request->hasFile('qris_proofs')) {
                    $proofFile = $request->file('qris_proofs');
                    $qrReader = new QrReader($proofFile->getRealPath());
                    $qrText = $qrReader->text();

                    if ($qrText && str_starts_with($qrText, '000201')) {
                        $parsedQR = $this->parseEMV($qrText);

                        $accountName = $parsedQR['59'] ?? '';
                        $merchantData = $parsedQR['26'] ?? [];
                        $accountNumber = '';
                        if (is_array($merchantData)) {
                            $accountNumber = $merchantData['01'] ?? '';
                            $nssn = $accountNumber ? substr($accountNumber, 0, 8) : null;
                            $providerCode = $merchantData['00'] ?? null;
                            if ($providerCode && $nssn) {
                                $foundChannel = Channel::where('channel_type', 'qris')
                                    ->where('channel_code', $nssn)
                                    ->first();
                                $deposit->channel_id = $foundChannel->id ?? null;
                            } else {
                                $deposit->channel_id = null;
                            }
                        } else {
                            $deposit->channel_id = null;
                        }

                        $deposit->account_name = $accountName;
                        $deposit->account_number = $accountNumber;
                    } else {
                        $deposit->account_name = '';
                        $deposit->account_number = '';
                        $deposit->channel_id = null;
                    }
                } else {
                    $deposit->account_name = '';
                    $deposit->account_number = '';
                    $deposit->channel_id = null;
                }
            } else {
                $deposit->account_name = $validated['account_name'] ?? '';
                $deposit->account_number = $validated['account_number'] ?? '';

                if ($validated['channel_type'] === 'virtual_account' && !empty($deposit->account_number)) {
                    $prefix4 = substr($deposit->account_number, 0, 4);
                    $foundChannel = Channel::where('channel_type', 'virtual_account')
                        ->where('channel_code', $prefix4)
                        ->first();

                    if ($foundChannel) {
                        $deposit->channel_id = $foundChannel->id;
                    } else {
                        $prefix5 = substr($deposit->account_number, 0, 5);
                        $foundChannel = Channel::where('channel_type', 'virtual_account')
                            ->where('channel_code', $prefix5)
                            ->first();

                        if ($foundChannel) {
                            $deposit->channel_id = $foundChannel->id;
                        } else {
                            $provider = Provider::where('id', $request->channel_id)->first();
                            $newChannel = Channel::create([
                                'provider_id'   => $provider ? $provider->id : null,
                                'channel_type'  => 'virtual_account',
                                'channel_code'  => $prefix4,
                            ]);
                            $deposit->channel_id = $newChannel->id;
                        }
                    }
                } else {
                    $deposit->channel_id = $validated['channel_id'];
                }
            }

            $deposit->created_by = Auth::id();
            $deposit->save();

            if ($request->hasFile('website_proofs')) {
                $path = $request->file('website_proofs')->store('attachments/website_proof', 'public');
                GamblingDepositAttachment::create([
                    'gambling_deposit_id' => $deposit->id,
                    'attachment_type' => 'website_proof',
                    'file_path' => $path,
                ]);
            }

            if ($request->hasFile('account_proofs')) {
                $path = $request->file('account_proofs')->store('attachments/account_proof', 'public');
                GamblingDepositAttachment::create([
                    'gambling_deposit_id' => $deposit->id,
                    'attachment_type' => 'account_proof',
                    'file_path' => $path,
                ]);
            }

            if ($request->hasFile('qris_proofs') && $validated['channel_type'] === 'qris') {
                $path = $request->file('qris_proofs')->store('attachments/qris_proof', 'public');
                GamblingDepositAttachment::create([
                    'gambling_deposit_id' => $deposit->id,
                    'attachment_type' => 'qris_proof',
                    'file_path' => $path,
                ]);
            }

            Log::info('Gambling deposit created', ['id' => $deposit->id, 'user_id' => Auth::id()]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan.',
                'data' => $deposit->load('attachments', 'channel', 'creator')
            ], 201);
        } catch (ValidationException $e) {
            $errors = [];
            foreach ($e->errors() as $field => $messages) {
                $errors[$field] = $messages;
            }

            return response()->json([
                'success' => false,
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
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.' . $e->getMessage(),
                'error_detail' => $e->getMessage()
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
