<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Channel;
use App\Models\ErrorLog;
use App\Models\GamblingDeposit;
use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;

class SDashboardController extends Controller
{
    public function index()
    {
        // Total Users
        $totalUsers = User::count();
        // Misal growth dari bulan lalu
        $totalUsersLastMonth = User::where('created_at', '<', now()->subMonth())->count();
        $totalUsersGrowth = $totalUsersLastMonth > 0
            ? (($totalUsers - $totalUsersLastMonth) / $totalUsersLastMonth) * 100
            : 100; // jika sebelumnya 0, langsung 100%

        // Total Channels
        $totalChannels = Channel::count();
        $totalChannelsLastMonth = Channel::where('created_at', '<', now()->subMonth())->count();
        $totalChannelsGrowth = $totalChannelsLastMonth > 0
            ? (($totalChannels - $totalChannelsLastMonth) / $totalChannelsLastMonth) * 100
            : 100;

        // Confirmed Gambling Sites
        $confirmedGamblingSites = GamblingDeposit::where('is_confirmed_gambling', true)->count();
        $confirmedGamblingSitesLastMonth = GamblingDeposit::where('is_confirmed_gambling', true)
            ->where('updated_at', '<', now()->subMonth())
            ->count();
        $confirmedGamblingSitesGrowth = $confirmedGamblingSitesLastMonth > 0
            ? (($confirmedGamblingSites - $confirmedGamblingSitesLastMonth) / $confirmedGamblingSitesLastMonth) * 100
            : 100;

        // Pending Reports
        $pendingReports = GamblingDeposit::where('report_status', 'pending')->count();
        $pendingReportsLastWeek = GamblingDeposit::where('report_status', 'pending')
            ->where('updated_at', '<', now()->subWeek())
            ->count();
        $pendingReportsGrowth = $pendingReportsLastWeek > 0
            ? (($pendingReports - $pendingReportsLastWeek) / $pendingReportsLastWeek) * 100
            : 100;

        // Blocked Accounts
        $blockedAccounts = GamblingDeposit::where('account_validation_status', 'blocked')->count();
        $blockedAccountsLastMonth = GamblingDeposit::where('account_validation_status', 'blocked')
            ->where('updated_at', '<', now()->subMonth())
            ->count();
        $blockedAccountsGrowth = $blockedAccountsLastMonth > 0
            ? (($blockedAccounts - $blockedAccountsLastMonth) / $blockedAccountsLastMonth) * 100
            : 100;

        // Login Attempts Today
        $loginAttemptsToday = LoginLog::whereDate('logged_in_at', now()->toDateString())->count();
        $loginAttemptsYesterday = LoginLog::whereDate('logged_in_at', now()->subDay()->toDateString())->count();
        $loginAttemptsGrowth = $loginAttemptsYesterday > 0
            ? (($loginAttemptsToday - $loginAttemptsYesterday) / $loginAttemptsYesterday) * 100
            : 100;

        // Failed Logins
        $failedLogins = LoginLog::where('login_status', 'failed')->count();
        $failedLoginsLastWeek = LoginLog::where('login_status', 'failed')
            ->where('created_at', '>=', now()->subWeek())
            ->where('created_at', '<', now()->subDays(1))
            ->count();
        $failedLoginsGrowth = $failedLoginsLastWeek > 0
            ? (($failedLogins - $failedLoginsLastWeek) / $failedLoginsLastWeek) * 100
            : 100;

        // Error Logs Last 7 Days
        $errorLogsLast7Days = ErrorLog::where('occurred_at', '>=', now()->subDays(7))->count();
        $errorLogsPrevious7Days = ErrorLog::whereBetween('occurred_at', [now()->subDays(14), now()->subDays(7)])->count();
        $errorLogsGrowth = $errorLogsPrevious7Days > 0
            ? (($errorLogsLast7Days - $errorLogsPrevious7Days) / $errorLogsPrevious7Days) * 100
            : 100;

        return view('superadmin.dashboard.index', compact(
            'totalUsers',
            'totalUsersGrowth',
            'totalChannels',
            'totalChannelsGrowth',
            'confirmedGamblingSites',
            'confirmedGamblingSitesGrowth',
            'pendingReports',
            'pendingReportsGrowth',
            'blockedAccounts',
            'blockedAccountsGrowth',
            'loginAttemptsToday',
            'loginAttemptsGrowth',
            'failedLogins',
            'failedLoginsGrowth',
            'errorLogsLast7Days',
            'errorLogsGrowth'
        ));
    }
}
