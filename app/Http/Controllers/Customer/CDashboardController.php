<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CDashboardController extends Controller
{
    public function index()
    {
        $customerId = Auth::user()->customer->id;

        $totalDepositsCount = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->join('providers', 'channels.provider_id', '=', 'providers.id')
            ->where('report_status', 'approved')
            ->join('customer_providers', function ($join) use ($customerId) {
                $join->on('providers.id', '=', 'customer_providers.provider_id')
                    ->where('customer_providers.customer_id', '=', $customerId);
            })
            ->count();

        $countLast7Days = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->join('providers', 'channels.provider_id', '=', 'providers.id')
            ->where('report_status', 'approved')
            ->join('customer_providers', function ($join) use ($customerId) {
                $join->on('providers.id', '=', 'customer_providers.provider_id')
                    ->where('customer_providers.customer_id', '=', $customerId);
            })
            ->where('gambling_deposits.created_at', '>=', now()->subDays(7))
            ->count();

        $countPrev7Days = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->join('providers', 'channels.provider_id', '=', 'providers.id')
            ->join('customer_providers', function ($join) use ($customerId) {
                $join->on('providers.id', '=', 'customer_providers.provider_id')
                    ->where('customer_providers.customer_id', '=', $customerId);
            })
            ->where('report_status', 'approved')
            ->whereBetween('gambling_deposits.created_at', [now()->subDays(14), now()->subDays(7)])
            ->count();

        $totalDepositsGrowth = $this->calculateGrowth($countLast7Days, $countPrev7Days);

        $solvedCount = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->join('providers', 'channels.provider_id', '=', 'providers.id')
            ->join('customer_providers', function ($join) use ($customerId) {
                $join->on('providers.id', '=', 'customer_providers.provider_id')
                    ->where('customer_providers.customer_id', '=', $customerId);
            })
            ->where('report_status', 'approved')
            ->where('is_solved', true)
            ->count();

        $attachmentsCount = DB::table('gambling_deposit_attachments')
            ->join('gambling_deposits', 'gambling_deposit_attachments.gambling_deposit_id', '=', 'gambling_deposits.id')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->join('providers', 'channels.provider_id', '=', 'providers.id')
            ->join('customer_providers', function ($join) use ($customerId) {
                $join->on('providers.id', '=', 'customer_providers.provider_id')
                    ->where('customer_providers.customer_id', '=', $customerId);
            })
            ->count();

        $validationStatusCounts = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->join('providers', 'channels.provider_id', '=', 'providers.id')
            ->join('customer_providers', function ($join) use ($customerId) {
                $join->on('providers.id', '=', 'customer_providers.provider_id')
                    ->where('customer_providers.customer_id', '=', $customerId);
            })
            ->select('account_validation_status', DB::raw('count(*) as total'))
            ->groupBy('account_validation_status')
            ->pluck('total', 'account_validation_status')
            ->toArray();

        return view('customer.dashboard.index', compact(
            'totalDepositsCount',
            'totalDepositsGrowth',
            'solvedCount',
            'attachmentsCount',
            'validationStatusCounts'
        ));
    }

    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 2);
    }
}
