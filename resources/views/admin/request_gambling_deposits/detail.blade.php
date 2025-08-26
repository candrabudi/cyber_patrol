@extends('template.app')
@section('title', 'Detail Request Gambling Deposit')
@section('content')
    <div class="row gap-y-64">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>Detail Request Gambling Deposit</h4>
            <a href="{{ route('admin.request_gambling_deposits.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
        <div class="card shadow-sm">
            <div class="card-body px-4 py-3">

                <div class="row g-4">
                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Informasi Request</h5>
                        <dl class="row">
                            <dt class="col-5">Website</dt>
                            <dd class="col-7">{{ $requestDeposit->website->website_name }}</dd>

                            <dt class="col-5">Channel</dt>
                            <dd class="col-7">{{ $requestDeposit->channel->channel_type ?? '-' }}</dd>

                            <dt class="col-5">Alasan</dt>
                            <dd class="col-7">{{ $requestDeposit->reason }}</dd>

                            <dt class="col-5">Diajukan Oleh</dt>
                            <dd class="col-7">{{ $requestDeposit->requestedBy->name ?? '-' }}</dd>

                            <dt class="col-5">Status</dt>
                            <dd class="col-7">
                                @php
                                    $statusColors = [
                                        'pending' => 'warning',
                                        'process' => 'info',
                                        'completed' => 'success',
                                        'rejected' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$requestDeposit->status] ?? 'secondary' }}">
                                    {{ ucfirst($requestDeposit->status) }}
                                </span>
                            </dd>
                        </dl>
                    </div>

                    <div class="col-md-6">
                        <h5 class="text-primary mb-3">Tambah Rekening/Channel</h5>
                        <form id="formTambahRekening" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="request_id" value="{{ $requestDeposit->id }}">

                            @if ($requestDeposit->channel->channel_type == "transfer")
                                <div class="mb-3">
                                    <label class="form-label">Nama Rekening</label>
                                    <input type="text" name="account_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nomor Rekening</label>
                                    <input type="text" name="account_number" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Upload Bukti Transfer</label>
                                    <input type="file" name="proof" class="form-control" accept="image/*" required>
                                </div>
                            @elseif($requestDeposit->channel->channel_type == "virtual_account")
                                <div class="mb-3">
                                    <label class="form-label">Nomor Virtual Account</label>
                                    <input type="text" name="va_number" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Upload Bukti Transaksi</label>
                                    <input type="file" name="proof" class="form-control" accept="image/*" required>
                                </div>
                            @elseif($requestDeposit->channel->channel_type == "pulsa")
                                <div class="mb-3">
                                    <label class="form-label">Nomor Handphone</label>
                                    <input type="text" name="phone_number" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Upload Bukti Transfer Pulsa</label>
                                    <input type="file" name="proof" class="form-control" accept="image/*" required>
                                </div>
                            @elseif($requestDeposit->channel->channel_type == "qris")
                                <div class="mb-3">
                                    <label class="form-label">Upload QRIS</label>
                                    <input type="file" name="qris_image" class="form-control" accept="image/*" required>
                                </div>
                            @endif

                            <button type="submit" id="btnSimpanRekening" class="btn btn-primary w-100">
                                <span class="spinner-border spinner-border-sm d-none" id="loadingRekening"></span>
                                Simpan
                            </button>
                        </form>
                    </div>
                </div>

                <hr>
                <h5 class="text-primary mb-3">Daftar Rekening Terkait</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Nama Rekening</th>
                                <th>No Rekening</th>
                                <th>Channel</th>
                                <th>Tanggal Dibuat</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($gamblingAccounts as $acc)
                                <tr>
                                    <td>{{ $acc->account_name }}</td>
                                    <td>{{ $acc->account_number }}</td>
                                    <td>{{ ucfirst($acc->channel->channel_name) }}</td>
                                    <td>{{ $acc->created_at->format('d M Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada rekening ditambahkan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const form = document.getElementById("formTambahRekening");
            const btnSubmit = document.getElementById("btnSimpanRekening");
            const loading = document.getElementById("loadingRekening");

            form.addEventListener("submit", async function(e) {
                e.preventDefault();

                let formData = new FormData(form);
                let channelType = "{{ $requestDeposit->channel->channel_type }}";
                let errors = [];

                if (channelType === "transfer") {
                    if (!formData.get("account_name")) errors.push("Nama rekening wajib diisi.");
                    if (!formData.get("account_number")) errors.push("Nomor rekening wajib diisi.");
                    if (!formData.get("proof")?.name) errors.push("Bukti transfer wajib diupload.");
                } else if (channelType === "virtual_account") {
                    if (!formData.get("va_number")) errors.push("Nomor VA wajib diisi.");
                    if (!formData.get("proof")?.name) errors.push("Bukti transaksi wajib diupload.");
                } else if (channelType === "pulsa") {
                    if (!formData.get("phone_number")) errors.push("Nomor handphone wajib diisi.");
                    if (!formData.get("proof")?.name) errors.push("Bukti transfer pulsa wajib diupload.");
                } else if (channelType === "qris") {
                    if (!formData.get("qris_image")?.name) errors.push("QRIS wajib diupload.");
                }

                if (errors.length > 0) {
                    Swal.fire({
                        icon: "error",
                        title: "Validasi Gagal",
                        html: errors.map(e => `<li>${e}</li>`).join(""),
                    });
                    return;
                }

                btnSubmit.setAttribute("disabled", true);
                loading.classList.remove("d-none");

                try {
                    let response = await axios.post(
                        "{{ route('admin.request_gambling_deposits.store') }}",
                        formData,
                        {
                            headers: {
                                "Content-Type": "multipart/form-data",
                                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                            },
                        }
                    );

                    if (response.data.success) {
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil",
                            text: response.data.message,
                        }).then(() => window.location.reload());
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal",
                            text: response.data.message || "Terjadi kesalahan.",
                        });
                    }
                } catch (err) {
                    Swal.fire({
                        icon: "error",
                        title: "Error Server",
                        text: err.response?.data?.message || "Terjadi kesalahan pada server.",
                    });
                } finally {
                    btnSubmit.removeAttribute("disabled");
                    loading.classList.add("d-none");
                }
            });
        });
    </script>
@endpush
