<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\GamblingDeposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CGamblingDepositController extends Controller
{
    public function index()
    {
        return view('customer.gambling_deposits.index');
    }

    public function data(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $userId = Auth::id();

        $query = GamblingDeposit::with(['channel.customer', 'creator'])
            ->whereHas('channel.customer', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });

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
            'channel.customer',
            'attachments',
            'logs.changer'
        ])
            ->whereHas('channel.customer', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->findOrFail($id);

        $validationStatusCounts = GamblingDeposit::whereHas('channel.customer', function ($q) {
            $q->where('user_id', Auth::id());
        })
            ->selectRaw("report_status, COUNT(*) as count")
            ->groupBy('report_status')
            ->pluck('count', 'report_status')
            ->toArray();

        return view('customer.gambling_deposits.detail', compact(
            'gamblingDeposit',
            'validationStatusCounts'
        ));
    }
}
