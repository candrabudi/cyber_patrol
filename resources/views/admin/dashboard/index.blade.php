@extends('template.app')
@section('title', 'Dashboard')
@section('content')
    <div class="row g-6">
        <!-- Anti Judi Online Card -->
        <div class="col-lg-4 col-12">
            <div class="card h-100">
                <div class="card-body">
                    <div class="bg-label-danger rounded text-center mb-4 pt-4">
                        <img
                            class="img-fluid w-100 rounded"
                            src="{{ asset('template/img/banner-01.jpg') }}"
                            alt="Anti Judi Online"
                            style="object-fit: cover; height: 350px;"
                        >
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

        <!-- Stats Cards -->
        <div class="col-lg-8 col-12">
            <div class="row g-3">
                <!-- Gambling Deposit yang Sudah Diinput -->
                <div class="col-lg-6 col-sm-6">
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
                <div class="col-lg-6 col-sm-6">
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
                <div class="col-lg-6 col-sm-6">
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
                <div class="col-lg-6 col-sm-6">
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
            </div> <!-- /.row g-3 -->
        </div> <!-- /.col-lg-8 -->
    </div> <!-- /.row g-6 -->
@endsection
