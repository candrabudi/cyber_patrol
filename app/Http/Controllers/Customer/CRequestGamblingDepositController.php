<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RequestGamblingDeposit;
use App\Models\Website;
use Illuminate\Support\Facades\Auth;

class CRequestGamblingDepositController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'website_id' => 'required|integer',
            'channel_id' => 'required|integer',
            'reason'     => 'required|string|max:500',
        ]);

        try {
            $exists = RequestGamblingDeposit::where('website_id', $request->website_id)
                ->where('channel_id', $request->channel_id)
                ->where('requested_by', Auth::id())
                ->whereIn('status', ['pending', 'process'])
                ->exists();

            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pengajuan serupa sudah ada dan masih diproses. Tidak boleh duplikat.'
                ], 422);
            }

            $website = Website::findOrFail($request->website_id);

            $data = RequestGamblingDeposit::create([
                'website_id'   => $request->website_id,
                'channel_id'   => $request->channel_id,
                'reason'       => $request->reason,
                'requested_by' => Auth::id(),
                'to_user'      => $website->created_by,
                'status'       => 'pending',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil dikirim!',
                'data'    => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}
