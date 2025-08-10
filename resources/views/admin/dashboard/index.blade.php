@extends('template.app')
@section('title', 'Dashboard')
@section('content')
    <div class="row g-6">
        <!-- Gambling Deposit yang Sudah Diinput -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ti tabler-database icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ $myDepositsCount }}</h4>
                    </div>
                    <p class="mb-1">Gambling Deposit yang Diinput</p>
                    <p class="mb-0">
                        <span
                            class="text-heading fw-medium me-2 {{ $myDepositsGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format(abs($myDepositsGrowth), 1) }}%
                        </span>
                        <small class="text-body-secondary">dibanding 7 hari lalu</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Pending Gambling Deposit -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-warning h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-warning">
                                <i class="icon-base ti tabler-hourglass icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ $pendingCount }}</h4>
                    </div>
                    <p class="mb-1">Gambling Deposit Pending</p>
                    <p class="mb-0">
                        <span
                            class="text-heading fw-medium me-2 {{ $pendingGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format(abs($pendingGrowth), 1) }}%
                        </span>
                        <small class="text-body-secondary">dibanding 7 hari lalu</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Approved Gambling Deposit -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-success h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="icon-base ti tabler-check icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ $approvedCount }}</h4>
                    </div>
                    <p class="mb-1">Gambling Deposit Disetujui</p>
                    <p class="mb-0">
                        <span
                            class="text-heading fw-medium me-2 {{ $approvedGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format(abs($approvedGrowth), 1) }}%
                        </span>
                        <small class="text-body-secondary">dibanding 7 hari lalu</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Rejected Gambling Deposit -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-danger h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="icon-base ti tabler-x icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ $rejectedCount }}</h4>
                    </div>
                    <p class="mb-1">Gambling Deposit Ditolak</p>
                    <p class="mb-0">
                        <span
                            class="text-heading fw-medium me-2 {{ $rejectedGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format(abs($rejectedGrowth), 1) }}%
                        </span>
                        <small class="text-body-secondary">dibanding 7 hari lalu</small>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection
