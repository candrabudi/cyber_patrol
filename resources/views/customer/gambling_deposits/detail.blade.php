@extends('template.app')
@section('title', 'Detail Rekening Penampung')
@section('content')
    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0">Detail Rekening Penampung</h4>
            <a href="{{ route('customer.gambling_deposits.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="row g-4">
                    <!-- Kolom Kiri -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded h-100">
                            <h5 class="text-primary fw-semibold mb-3">Informasi Website</h5>
                            <dl class="row mb-0">
                                <dt class="col-5">Nama Website</dt>
                                <dd class="col-7">{{ $gamblingDeposit->website_name }}</dd>

                                <dt class="col-5">URL Website</dt>
                                <dd class="col-7">
                                    <a href="{{ $gamblingDeposit->website_url }}" target="_blank">
                                        {{ $gamblingDeposit->website_url }}
                                    </a>
                                </dd>

                                <dt class="col-5">Teridentifikasi Judi?</dt>
                                <dd class="col-7">
                                    {!! $gamblingDeposit->is_confirmed_gambling
                                        ? '<span class="badge bg-success">Ya</span>'
                                        : '<span class="badge bg-secondary">Tidak</span>' !!}
                                </dd>

                                <dt class="col-5">Dapat Diakses?</dt>
                                <dd class="col-7">
                                    {!! $gamblingDeposit->is_accessible
                                        ? '<span class="badge bg-success">Ya</span>'
                                        : '<span class="badge bg-secondary">Tidak</span>' !!}
                                </dd>
                            </dl>

                            <hr>

                            <h5 class="text-primary fw-semibold mb-3">Koordinasi dengan Kominfo</h5>
                            <dl class="row mb-0">
                                <dt class="col-5">Tanggal Laporan</dt>
                                <dd class="col-7">{{ $gamblingDeposit->report_date?->format('d M Y') ?? '-' }}</dd>

                                <dt class="col-5">Bukti Laporan</dt>
                                <dd class="col-7">
                                    @if ($gamblingDeposit->report_evidence)
                                        <a href="{{ $gamblingDeposit->report_evidence }}" target="_blank">Lihat Bukti</a>
                                    @else
                                        -
                                    @endif
                                </dd>

                                <dt class="col-5">Tanggal Penutupan Link</dt>
                                <dd class="col-7">{{ $gamblingDeposit->link_closure_date?->format('d M Y') ?? '-' }}</dd>

                                <dt class="col-5">Status Penutupan Link</dt>
                                <dd class="col-7">
                                    {{ $gamblingDeposit->link_closure_status ? ucfirst(str_replace('_', ' ', $gamblingDeposit->link_closure_status)) : '-' }}
                                </dd>
                            </dl>
                        </div>
                    </div>

                    <!-- Kolom Kanan -->
                    <div class="col-md-6">
                        <div class="p-3 border rounded h-100">
                            <h5 class="text-primary fw-semibold mb-3">Informasi Rekening Pembayaran</h5>
                            <dl class="row mb-0">
                                <dt class="col-5">Tipe Channel</dt>
                                <dd class="col-7">{{ ucfirst($gamblingDeposit->channel->channel_type) }}</dd>

                                <dt class="col-5">Nama Rekening</dt>
                                <dd class="col-7">{{ $gamblingDeposit->account_name }}</dd>

                                <dt class="col-5">Nomor Rekening</dt>
                                <dd class="col-7">{{ $gamblingDeposit->account_number }}</dd>
                            </dl>

                            <hr>

                            <h5 class="text-primary fw-semibold mb-3">Hasil Validasi Akun</h5>
                            <dl class="row mb-0">
                                <dt class="col-5">Tanggal Validasi Akun</dt>
                                <dd class="col-7">{{ $gamblingDeposit->account_validation_date?->format('d M Y') ?? '-' }}
                                </dd>

                                <dt class="col-5">Status Validasi Akun</dt>
                                <dd class="col-7">
                                    {{ $gamblingDeposit->account_validation_status ? ucfirst(str_replace('_', ' ', $gamblingDeposit->account_validation_status)) : '-' }}
                                </dd>
                            </dl>

                            <hr>

                            <h5 class="text-primary fw-semibold mb-3">Status & Catatan</h5>
                            <dl class="row mb-0">
                                <dt class="col-5">Status Laporan</dt>
                                <dd class="col-7">
                                    @php
                                        $statusColors = [
                                            'pending' => 'warning',
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                        ];
                                    @endphp
                                    <span
                                        class="badge bg-{{ $statusColors[$gamblingDeposit->report_status] ?? 'secondary' }}">
                                        {{ ucfirst($gamblingDeposit->report_status) }}
                                    </span>
                                </dd>

                                <dt class="col-5">Sudah Selesai?</dt>
                                <dd class="col-7">
                                    {!! $gamblingDeposit->is_solved
                                        ? '<span class="badge bg-success">Ya</span>'
                                        : '<span class="badge bg-secondary">Tidak</span>' !!}
                                </dd>

                                <dt class="col-5">Catatan</dt>
                                <dd class="col-7">{{ $gamblingDeposit->remarks ?: '-' }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <h5 class="text-primary fw-semibold mb-3">Lampiran Bukti</h5>
                @php
                    $attachmentLabels = [
                        'website_proof' => 'Bukti Website',
                        'account_proof' => 'Bukti Bahwa di pakai website',
                        'qris_proof' => 'Bukti QRIS',
                    ];
                @endphp

                <div class="row g-4">
                    @forelse ($gamblingDeposit->attachments as $attachment)
                        <div class="col-md-6 col-xxl-4">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-body d-flex flex-column">
                                    <!-- Gambar dalam background label -->
                                    <div class="bg-label-primary rounded text-center mb-4 pt-4">
                                        <img class="img-fluid" src="{{ asset('storage/' . $attachment->file_path) }}"
                                            alt="{{ $attachmentLabels[$attachment->attachment_type] ?? 'Lampiran' }}"
                                            style="max-height:160px; object-fit:contain; cursor:pointer;"
                                            onclick="window.open('{{ asset('storage/' . $attachment->file_path) }}', '_blank')">
                                    </div>

                                    <!-- Judul -->
                                    <h6 class="mb-2 fw-semibold text-center">
                                        {{ $attachmentLabels[$attachment->attachment_type] ?? ucwords(str_replace('_', ' ', $attachment->attachment_type)) }}
                                    </h6>

                                    <!-- Deskripsi singkat -->
                                    <p class="small text-muted text-center mb-4">
                                        Klik tombol di bawah untuk melihat berkas asli.
                                    </p>

                                    <!-- Tombol aksi -->
                                    <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank"
                                        class="btn btn-primary w-100 mt-auto">
                                        <i class="bi bi-eye me-1"></i> Lihat Berkas
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Tidak ada lampiran.</p>
                    @endforelse
                </div>

            </div>
        </div>
    </div>
@endsection
