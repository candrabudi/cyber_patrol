@extends('template.app')
@section('title', 'Edit Rekening Penampung')
@section('content')
    @php
        $isFinal = in_array($gamblingDeposit->report_status, ['approved', 'rejected']);
        $attachmentLabels = [
            'website_proof' => 'Bukti Website',
            'account_proof' => 'Bukti Rekening',
            'qris_proof' => 'Bukti QRIS',
        ];
    @endphp

    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2 text-primary"></i>Edit Rekening Penampung</h4>
            <a href="{{ route('admin.gambling_deposits.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <form action="{{ route('reviewer.gambling_deposits.update', $gamblingDeposit->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <div class="row g-4">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="p-3 border rounded h-100">
                                <h5 class="text-primary fw-semibold mb-3"><i class="bi bi-globe me-1"></i> Informasi Website
                                </h5>
                                <dl class="row mb-0">
                                    <dt class="col-5">Nama Website</dt>
                                    <dd class="col-7">{{ $gamblingDeposit->website_name }}</dd>
                                    <dt class="col-5">URL Website</dt>
                                    <dd class="col-7"><a href="{{ $gamblingDeposit->website_url }}"
                                            target="_blank">{{ $gamblingDeposit->website_url }}</a></dd>
                                    <dt class="col-5">Teridentifikasi Judi?</dt>
                                    <dd class="col-7">{!! $gamblingDeposit->is_confirmed_gambling
                                        ? '<span class="badge bg-success">Ya</span>'
                                        : '<span class="badge bg-secondary">Tidak</span>' !!}</dd>
                                    <dt class="col-5">Dapat Diakses?</dt>
                                    <dd class="col-7">{!! $gamblingDeposit->is_accessible
                                        ? '<span class="badge bg-success">Ya</span>'
                                        : '<span class="badge bg-secondary">Tidak</span>' !!}</dd>
                                </dl>

                                <hr>

                                <h5 class="text-primary fw-semibold mb-3"><i class="bi bi-shield-check me-1"></i> Koordinasi
                                    dengan Kominfo</h5>
                                <dl class="row mb-0">
                                    <dt class="col-5">Tanggal Laporan</dt>
                                    <dd class="col-7">{{ $gamblingDeposit->report_date?->format('d M Y') ?? '-' }}</dd>
                                    <dt class="col-5">Bukti Laporan</dt>
                                    <dd class="col-7">
                                        @if ($gamblingDeposit->report_evidence)
                                            <a href="{{ $gamblingDeposit->report_evidence }}" target="_blank">Lihat
                                                Bukti</a>
                                        @else
                                            -
                                        @endif
                                    </dd>
                                    <dt class="col-5">Tanggal Penutupan Link</dt>
                                    <dd class="col-7">{{ $gamblingDeposit->link_closure_date?->format('d M Y') ?? '-' }}
                                    </dd>
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
                                <h5 class="text-primary fw-semibold mb-3"><i class="bi bi-credit-card me-1"></i> Informasi
                                    Rekening Pembayaran</h5>
                                <dl class="row mb-0">
                                    <dt class="col-5">Tipe Channel</dt>
                                    <dd class="col-7">{{ ucfirst($gamblingDeposit->channel->channel_type) }}</dd>
                                    <dt class="col-5">Nama Rekening</dt>
                                    <dd class="col-7">{{ $gamblingDeposit->account_name }}</dd>
                                    <dt class="col-5">Nomor Rekening</dt>
                                    <dd class="col-7">{{ $gamblingDeposit->account_number }}</dd>
                                </dl>

                                <hr>

                                <h5 class="text-primary fw-semibold mb-3"><i class="bi bi-check-circle me-1"></i> Hasil
                                    Validasi Akun</h5>
                                <dl class="row mb-0">
                                    <dt class="col-5">Tanggal Validasi Akun</dt>
                                    <dd class="col-7">
                                        {{ $gamblingDeposit->account_validation_date?->format('d M Y') ?? '-' }}</dd>
                                    <dt class="col-5">Status Validasi Akun</dt>
                                    <dd class="col-7">
                                        {{ $gamblingDeposit->account_validation_status ? ucfirst(str_replace('_', ' ', $gamblingDeposit->account_validation_status)) : '-' }}
                                    </dd>
                                </dl>

                                <hr>

                                <h5 class="text-primary fw-semibold mb-3"><i class="bi bi-list-check me-1"></i> Status &
                                    Catatan</h5>
                                <div class="mb-3 row">
                                    <label class="col-sm-5 col-form-label">Status Laporan *</label>
                                    <div class="col-sm-7">
                                        <select name="report_status" class="form-select" {{ $isFinal ? 'disabled' : '' }}
                                            required>
                                            <option value="pending" @selected(old('report_status', $gamblingDeposit->report_status) == 'pending')>Pending</option>
                                            <option value="approved" @selected(old('report_status', $gamblingDeposit->report_status) == 'approved')>Disetujui</option>
                                            <option value="rejected" @selected(old('report_status', $gamblingDeposit->report_status) == 'rejected')>Ditolak</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-sm-5 col-form-label">Teridentifikasi Judi? *</label>
                                    <div class="col-sm-7">
                                        <select name="is_confirmed_gambling" class="form-select"
                                            {{ $isFinal ? 'disabled' : '' }} required>
                                            <option value="1" @selected(old('is_confirmed_gambling', $gamblingDeposit->is_confirmed_gambling))>Ya</option>
                                            <option value="0" @selected(!old('is_confirmed_gambling', $gamblingDeposit->is_confirmed_gambling))>Tidak</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-sm-5 col-form-label">Dapat Diakses? *</label>
                                    <div class="col-sm-7">
                                        <select name="is_accessible" class="form-select" {{ $isFinal ? 'disabled' : '' }}
                                            required>
                                            <option value="1" @selected(old('is_accessible', $gamblingDeposit->is_accessible))>Ya</option>
                                            <option value="0" @selected(!old('is_accessible', $gamblingDeposit->is_accessible))>Tidak</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-sm-5 col-form-label">Catatan</label>
                                    <div class="col-sm-7">
                                        <textarea name="remarks" rows="3" class="form-control" {{ $isFinal ? 'disabled' : '' }}>{{ old('remarks', $gamblingDeposit->remarks) }}</textarea>
                                    </div>
                                </div>

                                <div class="mb-3 row">
                                    <label class="col-sm-5 col-form-label">Sudah Selesai?</label>
                                    <div class="col-sm-7">
                                        {!! $gamblingDeposit->is_solved
                                            ? '<span class="badge bg-success">Ya</span>'
                                            : '<span class="badge bg-secondary">Tidak</span>' !!}
                                    </div>
                                </div>
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

                    <hr class="my-4">

                    <!-- Riwayat Perubahan -->
                    <h5 class="text-primary fw-semibold mb-3"><i class="bi bi-clock-history me-1"></i> Riwayat Perubahan
                    </h5>
                    <div class="table-responsive shadow-sm rounded">
                        <table class="table table-striped table-bordered align-middle">
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
                                        <td>{{ $log->changer->username ?? 'System' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Tidak ada riwayat perubahan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Tombol Submit -->
                    @if (!$isFinal)
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i> Simpan Perubahan
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </form>
    </div>
@endsection
