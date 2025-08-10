<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\GamblingDeposit;
use Carbon\Carbon;

class ADashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $now = Carbon::now();
        $sevenDaysAgo = $now->copy()->subDays(7);

        // Fungsi helper untuk hitung growth %
        $calculateGrowth = function ($current, $previous) {
            if ($previous == 0) {
                return $current > 0 ? 100 : 0;
            }
            return (($current - $previous) / $previous) * 100;
        };

        // 1. Gambling deposit user input
        $myDepositsCount = GamblingDeposit::where('created_by', $userId)->count();
        $myDepositsLastWeek = GamblingDeposit::where('created_by', $userId)
            ->where('created_at', '<', $sevenDaysAgo)
            ->count();
        $myDepositsGrowth = $calculateGrowth($myDepositsCount, $myDepositsLastWeek);

        // 2. Pending gambling deposits
        $pendingCount = GamblingDeposit::where('report_status', 'pending')
            ->where('created_by', $userId)
            ->count();
        $pendingLastWeek = GamblingDeposit::where('report_status', 'pending')
            ->where('created_by', $userId)
            ->where('updated_at', '<', $sevenDaysAgo)
            ->count();
        $pendingGrowth = $calculateGrowth($pendingCount, $pendingLastWeek);

        // 3. Approved gambling deposits
        $approvedCount = GamblingDeposit::where('report_status', 'approved')
            ->where('created_by', $userId)
            ->count();
        $approvedLastWeek = GamblingDeposit::where('report_status', 'approved')
            ->where('created_by', $userId)
            ->where('updated_at', '<', $sevenDaysAgo)
            ->count();
        $approvedGrowth = $calculateGrowth($approvedCount, $approvedLastWeek);

        // 4. Rejected gambling deposits
        $rejectedCount = GamblingDeposit::where('report_status', 'rejected')
            ->where('created_by', $userId)
            ->count();
        $rejectedLastWeek = GamblingDeposit::where('report_status', 'rejected')
            ->where('created_by', $userId)
            ->where('updated_at', '<', $sevenDaysAgo)
            ->count();
        $rejectedGrowth = $calculateGrowth($rejectedCount, $rejectedLastWeek);

        return view('admin.dashboard.index', compact(
            'myDepositsCount',
            'myDepositsGrowth',
            'pendingCount',
            'pendingGrowth',
            'approvedCount',
            'approvedGrowth',
            'rejectedCount',
            'rejectedGrowth'
        ));
    }
}
