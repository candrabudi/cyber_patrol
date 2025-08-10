@extends('template.app')
@section('title', 'Dashboard')
@section('content')
    <div class="row g-6">
        <!-- Total Laporan Deposit Judi -->
        <div class="col-lg-3 col-sm-6">
            <div class="card card-border-shadow-primary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="icon-base ti tabler-database icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ $totalDepositsCount }}</h4>
                    </div>
                    <p class="mb-1">Total Laporan Deposit Judi</p>
                    <p class="mb-0">
                        <span
                            class="text-heading fw-medium me-2 {{ $totalDepositsGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ number_format(abs($totalDepositsGrowth), 1) }}%
                        </span>
                        <small class="text-body-secondary">dibanding 7 hari lalu</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Laporan Menunggu Review -->
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
                    <p class="mb-1">Laporan Menunggu Review</p>
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

        <!-- Laporan Disetujui -->
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
                    <p class="mb-1">Laporan Disetujui</p>
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

        <!-- Laporan Ditolak -->
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
                    <p class="mb-1">Laporan Ditolak</p>
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

        <!-- Laporan Selesai -->
        <div class="col-lg-3 col-sm-6 mt-4">
            <div class="card card-border-shadow-info h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="icon-base ti tabler-checkup-list icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ $solvedCount }}</h4>
                    </div>
                    <p class="mb-1">Laporan Selesai</p>
                    <p class="mb-0">
                        <small class="text-body-secondary">Dari total laporan</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Total Lampiran -->
        <div class="col-lg-3 col-sm-6 mt-4">
            <div class="card card-border-shadow-secondary h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        <div class="avatar me-4">
                            <span class="avatar-initial rounded bg-label-secondary">
                                <i class="icon-base ti tabler-paperclip icon-28px"></i>
                            </span>
                        </div>
                        <h4 class="mb-0">{{ $attachmentsCount }}</h4>
                    </div>
                    <p class="mb-1">Total Lampiran</p>
                    <p class="mb-0">
                        <small class="text-body-secondary">Bukti laporan yang diunggah</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- Ringkasan Validasi Akun -->
        <div class="col-md-6 col-xxl-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <div class="card-title m-0 me-2">
                        <h5 class="mb-1">Status Validasi Akun</h5>
                        <p class="card-subtitle">Ringkasan status akun Anda</p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-text-secondary btn-icon rounded-pill text-body-secondary border-0 me-n1"
                            type="button" id="validationStatusMenu" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <i class="icon-base ti tabler-dots-vertical icon-22px text-body-secondary"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" aria-labelledby="validationStatusMenu">
                            <a class="dropdown-item" href="#">Lihat Detail</a>
                            <a class="dropdown-item" href="#">Filter</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="p-0 m-0">
                        <li class="d-flex mb-3 pb-1 align-items-center">
                            <div class="badge bg-label-danger me-4 rounded p-1_5">
                                <i class="icon-base ti tabler-ban icon-md"></i>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Ditutup</h6>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">{{ $validationStatusCounts['closed'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex mb-3 pb-1 align-items-center">
                            <div class="badge bg-label-warning me-4 rounded p-1_5">
                                <i class="icon-base ti tabler-lock icon-md"></i>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Diblokir</h6>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">{{ $validationStatusCounts['blocked'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex mb-3 pb-1 align-items-center">
                            <div class="badge bg-label-info me-4 rounded p-1_5">
                                <i class="icon-base ti tabler-snowflake icon-md"></i>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Dibekukan</h6>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">{{ $validationStatusCounts['frozen'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </li>
                        <li class="d-flex align-items-center">
                            <div class="badge bg-label-success me-4 rounded p-1_5">
                                <i class="icon-base ti tabler-check icon-md"></i>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0">Bukan Akun Judi</h6>
                                </div>
                                <div class="user-progress">
                                    <h6 class="mb-0">{{ $validationStatusCounts['not_gambling_account'] ?? 0 }}</h6>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

    </div>
@endsection
