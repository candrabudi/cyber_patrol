<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RDashboardController extends Controller
{
    public function index()
    {
        $totalDepositsCount = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->join('providers', 'channels.provider_id', '=', 'providers.id')
            ->count();

        $countLast7Days = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->join('providers', 'channels.provider_id', '=', 'providers.id')
            ->where('gambling_deposits.created_at', '>=', now()->subDays(7))
            ->count();

        $countPrev7Days = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->join('providers', 'channels.provider_id', '=', 'providers.id')
            ->whereBetween('gambling_deposits.created_at', [now()->subDays(14), now()->subDays(7)])
            ->count();

        $totalDepositsGrowth = $this->calculateGrowth($countLast7Days, $countPrev7Days);

        $pendingCount = $this->countByStatus('pending');
        $approvedCount = $this->countByStatus('approved');
        $rejectedCount = $this->countByStatus('rejected');

        $pendingGrowth = $this->calculateStatusGrowth('pending');
        $approvedGrowth = $this->calculateStatusGrowth('approved');
        $rejectedGrowth = $this->calculateStatusGrowth('rejected');

        $solvedCount = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->join('providers', 'channels.provider_id', '=', 'providers.id')
            ->where('is_solved', true)
            ->count();

        $attachmentsCount = DB::table('gambling_deposit_attachments')
            ->join('gambling_deposits', 'gambling_deposit_attachments.gambling_deposit_id', '=', 'gambling_deposits.id')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->join('providers', 'channels.provider_id', '=', 'providers.id')
            ->count();

        $validationStatusCounts = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->join('providers', 'channels.provider_id', '=', 'providers.id')
            ->select('account_validation_status', DB::raw('count(*) as total'))
            ->groupBy('account_validation_status')
            ->pluck('total', 'account_validation_status')
            ->toArray();

        return view('reviewer.dashboard.index', compact(
            'totalDepositsCount',
            'totalDepositsGrowth',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'pendingGrowth',
            'approvedGrowth',
            'rejectedGrowth',
            'solvedCount',
            'attachmentsCount',
            'validationStatusCounts'
        ));
    }

    private function countByStatus($status)
    {
        return DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->join('providers', 'channels.provider_id', '=', 'providers.id')
            ->where('report_status', $status)
            ->count();
    }

    private function calculateGrowth($recentCount, $previousCount)
    {
        if ($previousCount == 0) {
            return $recentCount > 0 ? 100 : 0;
        }
        return round((($recentCount - $previousCount) / $previousCount) * 100, 2);
    }

    private function calculateStatusGrowth($status)
    {
        $countLast7Days = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->join('providers', 'channels.provider_id', '=', 'providers.id')
            ->where('report_status', $status)
            ->where('gambling_deposits.created_at', '>=', now()->subDays(7))
            ->count();

        $countPrev7Days = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->join('providers', 'channels.provider_id', '=', 'providers.id')
            ->where('report_status', $status)
            ->whereBetween('gambling_deposits.created_at', [now()->subDays(14), now()->subDays(7)])
            ->count();

        return $this->calculateGrowth($countLast7Days, $countPrev7Days);
    }
}
