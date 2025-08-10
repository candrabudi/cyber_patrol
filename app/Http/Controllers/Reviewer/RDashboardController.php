<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RDashboardController extends Controller
{
    public function index()
    {
        // Total gambling deposits
        $totalDepositsCount = DB::table('gambling_deposits')->count();

        // Growth contoh (bandingkan 7 hari terakhir dengan 7 hari sebelumnya)
        $countLast7Days = DB::table('gambling_deposits')
            ->where('created_at', '>=', now()->subDays(7))
            ->count();
        $countPrev7Days = DB::table('gambling_deposits')
            ->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])
            ->count();
        $totalDepositsGrowth = $this->calculateGrowth($countLast7Days, $countPrev7Days);

        // Count by report_status
        $pendingCount = DB::table('gambling_deposits')->where('report_status', 'pending')->count();
        $approvedCount = DB::table('gambling_deposits')->where('report_status', 'approved')->count();
        $rejectedCount = DB::table('gambling_deposits')->where('report_status', 'rejected')->count();

        // Growth for each status (7 days comparison)
        $pendingGrowth = $this->calculateStatusGrowth('pending');
        $approvedGrowth = $this->calculateStatusGrowth('approved');
        $rejectedGrowth = $this->calculateStatusGrowth('rejected');

        // Solved reports count
        $solvedCount = DB::table('gambling_deposits')->where('is_solved', true)->count();

        // Attachments count
        $attachmentsCount = DB::table('gambling_deposit_attachments')->count();

        // Validation status counts
        $validationStatusCounts = DB::table('gambling_deposits')
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

    private function calculateGrowth($recentCount, $previousCount)
    {
        if ($previousCount == 0) {
            return $recentCount > 0 ? 100 : 0;
        }
        return (($recentCount - $previousCount) / $previousCount) * 100;
    }

    private function calculateStatusGrowth($status)
    {
        $countLast7Days = DB::table('gambling_deposits')
            ->where('report_status', $status)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        $countPrev7Days = DB::table('gambling_deposits')
            ->where('report_status', $status)
            ->whereBetween('created_at', [now()->subDays(14), now()->subDays(7)])
            ->count();

        return $this->calculateGrowth($countLast7Days, $countPrev7Days);
    }
}
