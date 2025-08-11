<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\ErrorLog;
use App\Models\GamblingDeposit;
use App\Models\GamblingDepositAttachment;
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
        $search  = $request->get('search', '');

        $query = GamblingDeposit::with([
            'channel.provider',
            'creator'
        ]);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('website_name', 'like', "%{$search}%")
                  ->orWhere('website_url', 'like', "%{$search}%")
                  ->orWhere('account_name', 'like', "%{$search}%")
                  ->orWhere('account_number', 'like', "%{$search}%")
                  ->orWhereHas('channel.provider.customerProviders.customer', function ($q2) use ($search) {
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
            'channel.provider',
            'attachments',
            'logs.changer'
        ])->findOrFail($id);

        return view('superadmin.gambling_deposits.detail', compact('gamblingDeposit'));
    }

    public function create()
    {
        $channels = Channel::all();
        return view('superadmin.gambling_deposits.create', compact('channels'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'website_name'    => 'required|string|max:255',
                'website_url'     => 'required|url',
                // sesuaikan channel_type dengan migration: transfer, qris, virtual_account, pulsa
                'channel_type'    => 'required|in:transfer,qris,virtual_account,pulsa',
                'channel_id'      => 'nullable|exists:channels,id',
                'account_name'    => 'nullable|string|max:255',
                'account_number'  => 'nullable|string|max:50',
                'website_proofs'  => 'required|file|mimes:jpeg,jpg,png,pdf',
                'account_proofs'  => 'required|file|mimes:jpeg,jpg,png,pdf',
                'qris_proofs'     => 'nullable|file|mimes:jpeg,jpg,png,pdf',
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

            // Jika bukan QRIS, wajib ada channel_id (karena gambling_deposits.channel_id NOT NULL)
            if ($validated['channel_type'] !== 'qris' && empty($validated['channel_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Channel harus dipilih untuk tipe channel selain QRIS.'
                ], 422);
            }

            $deposit = new GamblingDeposit();
            $deposit->website_name = $validated['website_name'];
            $deposit->website_url  = $validated['website_url'];
            $deposit->is_confirmed_gambling = false;
            $deposit->is_accessible = false;

            if ($validated['channel_type'] === 'qris') {
                $qrisResult = $this->handleQrisUpload($request, $validated['channel_id'] ?? null);

                if (isset($qrisResult['error'])) {
                    // Jika parsing QRIS gagal dan tidak ada fallback channel_id, kembalikan error
                    return response()->json([
                        'success' => false,
                        'message' => $qrisResult['error'],
                    ], 422);
                }

                $deposit->channel_id   = $qrisResult['channel_id'];
                $deposit->account_name = $qrisResult['account_name'] ?? ($validated['account_name'] ?? '');
                $deposit->account_number = $qrisResult['account_number'] ?? ($validated['account_number'] ?? '');
            } else {
                $deposit->channel_id    = $validated['channel_id'];
                $deposit->account_name  = $validated['account_name'] ?? '';
                $deposit->account_number= $validated['account_number'] ?? '';
            }

            $deposit->created_by = Auth::id();
            $deposit->save();

            $this->saveAttachments($request, $deposit);

            Log::info('Gambling deposit created', [
                'id' => $deposit->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil disimpan.',
                'data' => $deposit->load('attachments', 'channel.provider.customerProviders.customer', 'creator')
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal, periksa input Anda.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            $this->logError($e, $request);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Parse QRIS file, cari channel berdasarkan channel_code (NSSN),
     * kembalikan array ['channel_id', 'account_name', 'account_number'] atau ['error' => msg]
     */
    private function handleQrisUpload(Request $request, $fallbackChannelId = null)
    {
        // Jika ada file qris_proofs -> coba parsing
        if ($request->hasFile('qris_proofs')) {
            $proofFile = $request->file('qris_proofs');
            $qrReader  = new QrReader($proofFile->getRealPath());
            $qrText    = $qrReader->text();

            if ($qrText && str_starts_with($qrText, '000201')) {
                $parsedQR      = $this->parseEMV($qrText);
                $accountName   = $parsedQR['59'] ?? '';
                $merchantData  = $parsedQR['26'] ?? [];
                $accountNumber = '';

                if (is_array($merchantData)) {
                    $accountNumber = $merchantData['01'] ?? '';
                    $nssn = $accountNumber ? substr($accountNumber, 0, 8) : null;

                    // Cari channel berdasarkan NSSN pada channel.channel_code
                    if ($nssn) {
                        $foundChannel = Channel::where('channel_type', 'qris')
                            ->where('channel_code', $nssn)
                            ->first();

                        $channelId = $foundChannel->id ?? null;
                    } else {
                        $channelId = null;
                    }
                } else {
                    $channelId = null;
                }

                // Jika parsing tidak menemukan channel, gunakan fallback jika ada
                if (empty($channelId) && !empty($fallbackChannelId)) {
                    $channelId = $fallbackChannelId;
                }

                if (empty($channelId)) {
                    return ['error' => 'Gagal mengenali QRIS atau channel QRIS tidak ditemukan. Mohon pilih channel secara manual.'];
                }

                return [
                    'channel_id' => $channelId,
                    'account_name' => $accountName,
                    'account_number' => $accountNumber,
                ];
            }

            // QR tidak valid
            if (!empty($fallbackChannelId)) {
                return [
                    'channel_id' => $fallbackChannelId,
                    'account_name' => '',
                    'account_number' => '',
                ];
            }

            return ['error' => 'File QRIS tidak valid atau tidak terbaca.'];
        }

        // Tidak ada file QRIS: kalau ada fallback, gunakan; kalau tidak, error
        if (!empty($fallbackChannelId)) {
            return [
                'channel_id' => $fallbackChannelId,
                'account_name' => '',
                'account_number' => '',
            ];
        }

        return ['error' => 'Tidak ada file QRIS yang diunggah dan tidak ada channel fallback.'];
    }

    private function saveAttachments(Request $request, GamblingDeposit $deposit)
    {
        $attachments = [
            'website_proofs' => 'website_proof',
            'account_proofs' => 'account_proof',
            'qris_proofs'    => 'qris_proof',
        ];

        foreach ($attachments as $field => $type) {
            if ($request->hasFile($field)) {
                $path = $request->file($field)->store("attachments/{$type}");
                GamblingDepositAttachment::create([
                    'gambling_deposit_id' => $deposit->id,
                    'attachment_type'     => $type,
                    'file_path'           => $path,
                ]);
            }
        }
    }

    private function logError(\Exception $e, Request $request)
    {
        Log::error('Error creating gambling deposit', [
            'message'     => $e->getMessage(),
            'file'        => $e->getFile(),
            'line'        => $e->getLine(),
            'stack_trace' => $e->getTraceAsString(),
            'user_id'     => Auth::id(),
            'ip'          => $request->ip(),
        ]);

        ErrorLog::create([
            'error_message' => $e->getMessage(),
            'stack_trace'   => $e->getTraceAsString(),
            'file'          => $e->getFile(),
            'line'          => $e->getLine(),
            'user_id'       => Auth::id(),
            'ip_address'    => $request->ip(),
            'occurred_at'   => now(),
        ]);
    }

    private function parseEMV($data)
    {
        $result = [];
        $i = 0;

        while ($i < strlen($data)) {
            $tag    = substr($data, $i, 2);
            $i     += 2;
            $length = intval(substr($data, $i, 2));
            $i     += 2;
            $value  = substr($data, $i, $length);
            $i     += $length;

            if (in_array($tag, ['26', '62'])) {
                $value = $this->parseEMV($value);
            }

            $result[$tag] = $value;
        }

        return $result;
    }
}
