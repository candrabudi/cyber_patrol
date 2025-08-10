@extends('template.app')
@section('title', 'Dashboard')
@section('content')
    <div class="row g-6">
        <!-- Total Users -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ti tabler-users icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ $totalUsers }}</h4>
                    </div>
                    <p class="mb-1">Total Users</p>
                    <p class="mb-0">
                        <span
                            class="text-heading fw-medium me-2 {{ $totalUsersGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format(abs($totalUsersGrowth), 1) }}%
                        </span>
                        <small class="text-body-secondary">than last month</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Channels -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="icon-base ti tabler-network icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ $totalChannels }}</h4>
                    </div>
                    <p class="mb-1">Total Channels</p>
                    <p class="mb-0">
                        <span
                            class="text-heading fw-medium me-2 {{ $totalChannelsGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format(abs($totalChannelsGrowth), 1) }}%
                        </span>
                        <small class="text-body-secondary">than last month</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Confirmed Gambling Sites -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-globe icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ $confirmedGamblingSites }}</h4>
                    </div>
                    <p class="mb-1">Confirmed Gambling Sites</p>
                    <p class="mb-0">
                        <span
                            class="text-heading fw-medium me-2 {{ $confirmedGamblingSitesGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format(abs($confirmedGamblingSitesGrowth), 1) }}%
                        </span>
                        <small class="text-body-secondary">than last month</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Pending Gambling Reports -->
        <div class="col-lg-3 col-sm-6 mt-4">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="icon-base ti tabler-hourglass icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ $pendingReports }}</h4>
                    </div>
                    <p class="mb-1">Pending Gambling Reports</p>
                    <p class="mb-0">
                        <span
                            class="text-heading fw-medium me-2 {{ $pendingReportsGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format(abs($pendingReportsGrowth), 1) }}%
                        </span>
                        <small class="text-body-secondary">than last week</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Blocked Accounts -->
        <div class="col-lg-3 col-sm-6 mt-4">
            <div class="card card-border-shadow-secondary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-secondary">
                                <i class="icon-base ti tabler-lock icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ $blockedAccounts }}</h4>
                    </div>
                    <p class="mb-1">Blocked Accounts</p>
                    <p class="mb-0">
                        <span
                            class="text-heading fw-medium me-2 {{ $blockedAccountsGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format(abs($blockedAccountsGrowth), 1) }}%
                        </span>
                        <small class="text-body-secondary">than last month</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Login Attempts Today -->
        <div class="col-lg-3 col-sm-6 mt-4">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="icon-base ti tabler-login icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ $loginAttemptsToday }}</h4>
                    </div>
                    <p class="mb-1">Login Attempts Today</p>
                    <p class="mb-0">
                        <span
                            class="text-heading fw-medium me-2 {{ $loginAttemptsGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format(abs($loginAttemptsGrowth), 1) }}%
                        </span>
                        <small class="text-body-secondary">than yesterday</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Failed Logins -->
        <div class="col-lg-3 col-sm-6 mt-4">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="icon-base ti tabler-lock-open icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ $failedLogins }}</h4>
                    </div>
                    <p class="mb-1">Failed Logins</p>
                    <p class="mb-0">
                        <span
                            class="text-heading fw-medium me-2 {{ $failedLoginsGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format(abs($failedLoginsGrowth), 1) }}%
                        </span>
                        <small class="text-body-secondary">than last week</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Error Logs Last 7 Days -->
        <div class="col-lg-3 col-sm-6 mt-4">
            <div class="card card-border-shadow-secondary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-secondary">
                                <i class="icon-base ti tabler-bug icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ $errorLogsLast7Days }}</h4>
                    </div>
                    <p class="mb-1">Error Logs Last 7 Days</p>
                    <p class="mb-0">
                        <span
                            class="text-heading fw-medium me-2 {{ $errorLogsGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format(abs($errorLogsGrowth), 1) }}%
                        </span>
                        <small class="text-body-secondary">than previous 7 days</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
