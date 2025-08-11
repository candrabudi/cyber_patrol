@extends('template.app')

@section('title', 'Dashboard Customer')

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

            <!-- Total Laporan Deposit -->
            <div class="col-lg-4 col-sm-6">
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
                        <p class="mb-1">Total Laporan Deposit</p>
                        <p class="mb-0">
                            <span class="text-heading fw-medium me-2 {{ $totalDepositsGrowth >= 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format(abs($totalDepositsGrowth), 1) }}%
                            </span>
                            <small class="text-body-secondary">dibanding 7 hari lalu</small>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Laporan Selesai -->
            <div class="col-lg-4 col-sm-6 mt-4">
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
                            <small class="text-body-secondary">Dari total laporan Anda</small>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total Lampiran -->
            <div class="col-lg-4 col-sm-6 mt-4">
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
                            <small class="text-body-secondary">Bukti laporan yang Anda unggah</small>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-xxl-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="card-title m-0 me-2">
                            <h5 class="mb-1">Status Validasi Akun</h5>
                            <p class="card-subtitle">Ringkasan status akun Anda</p>
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
                                    <div class="user-progress d-flex align-items-center gap-1">
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
                                    <div class="user-progress d-flex align-items-center gap-1">
                                        <h6 class="mb-0">{{ $validationStatusCounts['blocked'] ?? 0 }}</h6>
                                    </div>
                                </div>
                            </li>

                            <li class="d-flex mb-3 pb-1 align-items-center">
                                <div class="badge bg-label-info me-4 rounded p-1_5">
                                    <i class="icon-base ti tabler-freeze-row icon-md"></i>
                                </div>
                                <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                    <div class="me-2">
                                        <h6 class="mb-0">Dibekukan</h6>
                                    </div>
                                    <div class="user-progress d-flex align-items-center gap-1">
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
                                    <div class="user-progress d-flex align-items-center gap-1">
                                        <h6 class="mb-0">{{ $validationStatusCounts['not_gambling_account'] ?? 0 }}</h6>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
