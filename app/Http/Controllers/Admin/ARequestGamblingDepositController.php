<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GamblingDeposit;
use App\Models\RequestGamblingDeposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Channel;
use App\Models\Bank;
use App\Models\Provider;
use App\Models\GamblingDepositAccount;
use App\Models\GamblingDepositAttachment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ARequestGamblingDepositController extends Controller
{
    public function index()
    {
        return view('admin.request_gambling_deposits.index');
    }

    public function data(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search  = $request->get('search', '');

        $query = RequestGamblingDeposit::with([
            'website',
            'channel.customer',
            'creator'
        ])
            ->where('to_user', Auth::id());

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('website', function ($q1) use ($search) {
                    $q1->where('website_name', 'like', "%{$search}%")
                        ->orWhere('website_url', 'like', "%{$search}%");
                })
                    ->orWhereHas('channel', function ($q2) use ($search) {
                        $q2->where('channel_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('channel.customer', function ($q3) use ($search) {
                        $q3->where('full_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('creator', function ($q4) use ($search) {
                        $q4->where('username', 'like', "%{$search}%");
                    });
            });
        }

        $data = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($data);
    }

    public function detail($id)
    {
        $requestDeposit = RequestGamblingDeposit::with([
            'website',
            'channel',
            'channel.customer',
            'creator',
        ])->findOrFail($id);

        if ($requestDeposit->to_user !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke data ini.');
        }

        $gamblingAccounts = GamblingDeposit::where('source_type', "request")
            ->where('website_id', $requestDeposit->website_id)
            ->get();

        return view('admin.request_gambling_deposits.detail', compact('requestDeposit', 'gamblingAccounts'));
    }

    public function store(Request $request)
    {
        $requestGamblingDeposit = RequestGamblingDeposit::find($request->input('request_id'));
        try {
            $validated = $request->validate([
                'request_id'     => 'required|exists:request_gambling_deposits,id',

                'account_name'   => 'nullable|string|max:255',
                'account_number' => 'nullable|string|max:50',

                'proof'          => 'required|file|mimes:jpeg,jpg,png,pdf|max:2048',
            ], [
                'proof.required' => 'Bukti transaksi wajib diunggah.',
                'proof.mimes'    => 'Format bukti hanya jpeg, jpg, png, atau pdf.',
            ]);

            switch ($requestGamblingDeposit->channel->channel_type) {
                case 'transfer':
                    if (empty($validated['account_name'])) {
                        throw ValidationException::withMessages([
                            'account_name' => ['Nama rekening wajib diisi untuk transfer.'],
                        ]);
                    }
                    if (empty($validated['account_number'])) {
                        throw ValidationException::withMessages([
                            'account_number' => ['Nomor rekening wajib diisi untuk transfer.'],
                        ]);
                    }
                    break;

                case 'virtual_account':
                    if (empty($validated['account_number'])) {
                        throw ValidationException::withMessages([
                            'account_number' => ['Nomor virtual account wajib diisi.'],
                        ]);
                    }
                    if (!preg_match('/^[0-9]{8,20}$/', $validated['account_number'])) {
                        throw ValidationException::withMessages([
                            'account_number' => ['Nomor virtual account tidak valid.'],
                        ]);
                    }
                    break;

                case 'pulsa':
                    if (empty($validated['account_number'])) {
                        throw ValidationException::withMessages([
                            'account_number' => ['Nomor handphone wajib diisi.'],
                        ]);
                    }
                    if (!preg_match('/^[0-9]{10,15}$/', $validated['account_number'])) {
                        throw ValidationException::withMessages([
                            'account_number' => ['Nomor handphone harus 10–15 digit angka.'],
                        ]);
                    }
                    break;

                case 'qris':
                    if (!$request->hasFile('proof')) {
                        throw ValidationException::withMessages([
                            'proof' => ['QRIS wajib diunggah.'],
                        ]);
                    }
                    break;
            }

            DB::beginTransaction();

            $deposit = new GamblingDeposit();
            $deposit->source_type = "request";
            $deposit->customer_id = $requestGamblingDeposit->channel->customer_id;
            $deposit->website_id = $requestGamblingDeposit->website_id;
            $deposit->request_id   = $validated['request_id'];
            $deposit->channel_id   = $requestGamblingDeposit->channel->id;
            $deposit->created_by   = Auth::id();
            $deposit->account_name = $validated['account_name'] ?? '';
            $deposit->account_number = $validated['account_number'] ?? '';
            $deposit->save();

            $account = new GamblingDepositAccount();
            $account->gambling_deposit_id = $deposit->id;
            $account->account_name   = $deposit->account_name;
            $account->account_number = $deposit->account_number;
            $account->channel_type   = $deposit->channel->channel_type;
            $account->is_non_member  = 0;
            $account->save();

            if ($request->hasFile('proof')) {
                $path = $request->file('proof')->store('attachments/account_proof');
                GamblingDepositAttachment::create([
                    'gambling_deposit_id' => $deposit->id,
                    'attachment_type'     => 'account_proof',
                    'file_path'           => $path,
                ]);
            }

            $requestGamblingDeposit->status = 'process';
            $requestGamblingDeposit->save();

            DB::commit();

            Log::info('Deposit created', [
                'deposit_id' => $deposit->id,
                'user_id'    => Auth::id(),
            ]);

            return response()->json([
                'success' => 1,
                'message' => 'Data berhasil disimpan.',
            ], 201);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => 0,
                'message' => 'Validasi gagal.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Error create deposit', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'user_id' => Auth::id(),
            ]);

            return response()->json([
                'success' => 0,
                'message' => 'Server error, coba lagi.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}
