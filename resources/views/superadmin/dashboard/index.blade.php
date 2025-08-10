@extends('template.app')
@section('title', 'Dashboard')
@section('content')
    <div class="row g-4">
        <div class="col-lg-4 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="bg-label-danger rounded text-center mb-4 pt-4">
                        <img class="img-fluid w-100 rounded" src="{{ asset('template/img/banner-01.jpg') }}"
                            alt="Anti Judi Online" style="object-fit: cover; height: 350px;">
                    </div>
                    <h5 class="mb-2 text-wrap" style="word-break: break-word; color: #d9534f;">
                        Stop Judi Online Sekarang!
                    </h5>
                    <p class="small">
                        Lindungi diri dan keluarga dari bahaya judi online. Berantas kecanduan sejak dini.
                    </p>
                    <a href="javascript:void(0);" class="btn btn-danger w-100 waves-effect waves-light">
                        Berantas Sekarang
                    </a>
                </div>
            </div>
        </div>



        <!-- Right: Stats Cards -->
        <div class="col-lg-8 col-12">
            <div class="row g-4">
                <div class="col-md-6 col-sm-6">
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

                <div class="col-md-6 col-sm-6">
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

                <div class="col-md-6 col-sm-6">
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

                <div class="col-md-6 col-sm-6">
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

                <div class="col-md-6 col-sm-6">
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

                <div class="col-md-6 col-sm-6">
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

                <div class="col-md-6 col-sm-6">
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

                <div class="col-md-6 col-sm-6">
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
        </div>
    </div>
@endsection
