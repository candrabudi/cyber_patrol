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

        // Total gambling deposits milik customer
        $totalDepositsCount = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->where('channels.customer_id', $customerId)
            ->count();

        // Growth 7 hari terakhir vs 7 hari sebelumnya
        $countLast7Days = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->where('channels.customer_id', $customerId)
            ->where('gambling_deposits.created_at', '>=', now()->subDays(7))
            ->count();

        $countPrev7Days = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->where('channels.customer_id', $customerId)
            ->whereBetween('gambling_deposits.created_at', [now()->subDays(14), now()->subDays(7)])
            ->count();

        $totalDepositsGrowth = $this->calculateGrowth($countLast7Days, $countPrev7Days);

        // Count by report_status (milik customer)
        $pendingCount = $this->countByStatus($customerId, 'pending');
        $approvedCount = $this->countByStatus($customerId, 'approved');
        $rejectedCount = $this->countByStatus($customerId, 'rejected');

        // Growth untuk tiap status
        $pendingGrowth = $this->calculateStatusGrowth($customerId, 'pending');
        $approvedGrowth = $this->calculateStatusGrowth($customerId, 'approved');
        $rejectedGrowth = $this->calculateStatusGrowth($customerId, 'rejected');

        // Jumlah yang sudah solved
        $solvedCount = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->where('channels.customer_id', $customerId)
            ->where('is_solved', true)
            ->count();

        // Jumlah lampiran milik customer
        $attachmentsCount = DB::table('gambling_deposit_attachments')
            ->join('gambling_deposits', 'gambling_deposit_attachments.gambling_deposit_id', '=', 'gambling_deposits.id')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->where('channels.customer_id', $customerId)
            ->count();

        // Jumlah per status validasi akun
        $validationStatusCounts = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->where('channels.customer_id', $customerId)
            ->select('account_validation_status', DB::raw('count(*) as total'))
            ->groupBy('account_validation_status')
            ->pluck('total', 'account_validation_status')
            ->toArray();

        return view('customer.dashboard.index', compact(
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

    private function countByStatus($customerId, $status)
    {
        return DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->where('channels.customer_id', $customerId)
            ->where('report_status', $status)
            ->count();
    }

    private function calculateStatusGrowth($customerId, $status)
    {
        $countLast7Days = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->where('channels.customer_id', $customerId)
            ->where('report_status', $status)
            ->where('gambling_deposits.created_at', '>=', now()->subDays(7))
            ->count();

        $countPrev7Days = DB::table('gambling_deposits')
            ->join('channels', 'gambling_deposits.channel_id', '=', 'channels.id')
            ->where('channels.customer_id', $customerId)
            ->where('report_status', $status)
            ->whereBetween('gambling_deposits.created_at', [now()->subDays(14), now()->subDays(7)])
            ->count();

        return $this->calculateGrowth($countLast7Days, $countPrev7Days);
    }

    private function calculateGrowth($current, $previous)
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 2);
    }
}
