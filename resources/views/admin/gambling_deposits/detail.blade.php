@extends('template.app')
@section('title', 'Detail Rekening Penampung')
@section('content')
    <div class="row gap-y-64">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Detail Rekening Penampung</h4>
            <a href="{{ route('admin.gambling_deposits.index') }}" class="btn btn-secondary">Kembali</a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body px-4 py-3">

                <div class="row g-4">

                    <!-- Kiri -->
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Informasi Website</h5>
                        <dl class="row">
                            <dt class="col-5">Nama Website</dt>
                            <dd class="col-7">{{ $gamblingDeposit->website_name }}</dd>

                            <dt class="col-5">URL Website</dt>
                            <dd class="col-7">
                                <a href="{{ $gamblingDeposit->website_url }}"
                                    target="_blank">{{ $gamblingDeposit->website_url }}</a>
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

                        <h5 class="text-primary mb-3">Koordinasi dengan Kominfo</h5>
                        <dl class="row">
                            <dt class="col-5">Tanggal Laporan</dt>
                            <dd class="col-7">
                                {{ $gamblingDeposit->report_date ? $gamblingDeposit->report_date->format('d M Y') : '-' }}
                            </dd>

                            <dt class="col-5">Bukti Laporan</dt>
                            <dd class="col-7">
                                @if ($gamblingDeposit->report_evidence)
                                    <a href="{{ $gamblingDeposit->report_evidence }}" target="_blank">Lihat Bukti</a>
                                @else
                                    -
                                @endif
                            </dd>

                            <dt class="col-5">Tanggal Penutupan Link</dt>
                            <dd class="col-7">
                                {{ $gamblingDeposit->link_closure_date ? $gamblingDeposit->link_closure_date->format('d M Y') : '-' }}
                            </dd>

                            <dt class="col-5">Status Penutupan Link</dt>
                            <dd class="col-7">
                                {{ $gamblingDeposit->link_closure_status ? ucfirst(str_replace('_', ' ', $gamblingDeposit->link_closure_status)) : '-' }}
                            </dd>
                        </dl>
                    </div>

                    <!-- Kanan -->
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Informasi Rekening Pembayaran</h5>
                        <dl class="row">
                            <dt class="col-5">Tipe Channel</dt>
                            <dd class="col-7">{{ ucfirst($gamblingDeposit->channel->channel_type) }}</dd>

                            <dt class="col-5">Nama Rekening</dt>
                            <dd class="col-7">{{ $gamblingDeposit->account_name }}</dd>

                            <dt class="col-5">Nomor Rekening</dt>
                            <dd class="col-7">{{ $gamblingDeposit->account_number }}</dd>
                        </dl>

                        <hr>

                        <h5 class="text-primary mb-3">Hasil Validasi Akun</h5>
                        <dl class="row">
                            <dt class="col-5">Tanggal Validasi Akun</dt>
                            <dd class="col-7">
                                {{ $gamblingDeposit->account_validation_date ? $gamblingDeposit->account_validation_date->format('d M Y') : '-' }}
                            </dd>

                            <dt class="col-5">Status Validasi Akun</dt>
                            <dd class="col-7">
                                {{ $gamblingDeposit->account_validation_status ? ucfirst(str_replace('_', ' ', $gamblingDeposit->account_validation_status)) : '-' }}
                            </dd>
                        </dl>

                        <hr>

                        <h5 class="text-primary mb-3">Status & Catatan</h5>
                        <dl class="row">
                            <dt class="col-5">Status Laporan</dt>
                            <dd class="col-7">
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'approved' => 'success',
                                        'rejected' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$gamblingDeposit->report_status] ?? 'secondary' }}">
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

                <hr>

                <h5 class="text-primary mb-3">Lampiran Bukti</h5>
                @php
                    $attachmentLabels = [
                        'website_proof' => 'Bukti Website',
                        'account_proof' => 'Bukti Selain Qris',
                        'qris_proof' => 'Bukti QRIS',
                    ];
                @endphp

                <div class="row g-3 mb-4">
                    @forelse ($gamblingDeposit->attachments as $attachment)
                        <div class="col-md-4">
                            <div class="card p-2 h-100">
                                <div class="mb-2 text-center">
                                    <img src="{{ asset('storage/' . $attachment->file_path) }}"
                                        alt="{{ $attachmentLabels[$attachment->attachment_type] ?? 'Lampiran' }}"
                                        class="img-fluid rounded"
                                        style="max-height: 150px; object-fit: contain; cursor: pointer;"
                                        onclick="window.open('{{ asset('storage/' . $attachment->file_path) }}', '_blank')">
                                </div>
                                <div class="text-center fw-semibold mb-2 text-truncate">
                                    {{ $attachmentLabels[$attachment->attachment_type] ?? ucwords(str_replace('_', ' ', $attachment->attachment_type)) }}
                                </div>
                                <a href="{{ asset('storage/' . $attachment->file_path) }}" target="_blank"
                                    class="btn btn-outline-primary w-100">
                                    Lihat Berkas
                                </a>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-muted">Tidak ada lampiran.</p>
                    @endforelse
                </div>



                <hr>

                <h5 class="text-primary mb-3">Riwayat Perubahan</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Tanggal</th>
                                <th>Field yang Diubah</th>
                                <th>Nilai Lama</th>
                                <th>Nilai Baru</th>
                                <th>Diubah Oleh</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($gamblingDeposit->logs as $log)
                                <tr>
                                    <td>{{ $log->created_at->format('d M Y H:i:s') }}</td>
                                    <td>{{ $log->field_changed }}</td>
                                    <td>{{ $log->old_value ?? '-' }}</td>
                                    <td>{{ $log->new_value ?? '-' }}</td>
                                    <td>{{ $log->changedBy->name ?? 'System' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Tidak ada riwayat perubahan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection
