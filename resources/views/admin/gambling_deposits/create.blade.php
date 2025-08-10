@extends('template.app')

@section('content')
    <div class="card mb-6">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Tambah Data Rekening Penampung</h5>
            <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
                <div class="col-md-4 user_role"></div>
                <div class="col-md-4 user_plan"></div>
                <div class="col-md-4 user_status"></div>
            </div>
        </div>
        <div class="card-body mt-6">
            <form action="{{ route('admin.gambling_deposits.store') }}" method="POST" enctype="multipart/form-data"
                id="gamblingDepositForm">
                @csrf

                <!-- Alert container -->
                <div id="alertContainer"></div>

                <div class="row">
                    <!-- Kiri -->
                    <div class="col-md-6">
                        <!-- Nama Website -->
                        <div class="mb-3">
                            <label for="website_name" class="form-label">Nama Website</label>
                            <input type="text" class="form-control" id="website_name" name="website_name" required>
                        </div>

                        <!-- Tipe Channel -->
                        <div class="mb-3">
                            <label for="channel_type" class="form-label">Pilih Tipe Channel</label>
                            <select id="channel_type" name="channel_type" class="form-select" required>
                                <option value="">-- Pilih Tipe Channel --</option>
                                <option value="transfer">Transfer</option>
                                <option value="qris">QRIS</option>
                                <option value="virtual_account">Virtual Account</option>
                                <option value="pulsa">Pulsa</option>
                            </select>
                        </div>

                        <!-- Nama Rekening -->
                        <div class="mb-3" id="account_name_div" style="display:none;">
                            <label for="account_name" class="form-label">Nama Rekening</label>
                            <input type="text" class="form-control" id="account_name" name="account_name">
                        </div>

                        <!-- Nomor Rekening / Nomor Handphone -->
                        <div class="mb-3" id="account_number_div" style="display:none;">
                            <label for="account_number" class="form-label">Nomor Rekening / Nomor Handphone</label>
                            <input type="text" class="form-control" id="account_number" name="account_number">
                        </div>

                        <!-- Bukti Website -->
                        <div class="mb-3">
                            <label class="form-label">Bukti Website</label>
                            <input type="file" class="form-control" name="website_proofs"
                                accept="image/*,application/pdf" required>
                        </div>
                    </div>

                    <!-- Kanan -->
                    <div class="col-md-6">
                        <!-- URL Website -->
                        <div class="mb-3">
                            <label for="website_url" class="form-label">URL Website</label>
                            <input type="url" class="form-control" id="website_url" name="website_url" required>
                        </div>

                        <!-- Pilih Channel / Provider -->
                        <div class="mb-3" id="channel_select_div" style="display:none;">
                            <label for="channel_id" class="form-label" id="channel_label">Pilih Channel</label>
                            <select name="channel_id" id="channel_id" class="form-select">
                                <option value="">-- Pilih Channel --</option>
                                @foreach ($channels as $channel)
                                    <option value="{{ $channel->id }}" data-type="{{ $channel->channel_type }}"
                                        data-is-bank="{{ $channel->is_bank ?? false }}">
                                        {{ $channel->customer->full_name }} ({{ $channel->channel_type }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Bukti Rekening -->
                        <div class="mb-3" id="account_proofs_div">
                            <label class="form-label">Bukti Rekening</label>
                            <input type="file" class="form-control" name="account_proofs"
                                accept="image/*,application/pdf" required>
                        </div>

                        <!-- Bukti QRIS -->
                        <div class="mb-3" id="qris_proof_div" style="display:none;">
                            <label class="form-label">Bukti QRIS</label>
                            <input type="file" class="form-control" name="qris_proofs" accept="image/*,application/pdf">
                        </div>
                    </div>
                </div>

                <!-- Tombol Submit Full Width -->
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        const channelTypeSelect = document.getElementById('channel_type');
        const channelSelectDiv = document.getElementById('channel_select_div');
        const channelSelect = document.getElementById('channel_id');
        const channelLabel = document.getElementById('channel_label');
        const accountNameDiv = document.getElementById('account_name_div');
        const accountNameInput = document.getElementById('account_name');
        const accountNumberDiv = document.getElementById('account_number_div');
        const accountNumberInput = document.getElementById('account_number');
        const qrisProofDiv = document.getElementById('qris_proof_div');
        const accountProofsDiv = document.getElementById('account_proofs_div');
        const accountProofsInput = accountProofsDiv.querySelector('input[name="account_proofs"]');

        function filterChannelsByType(type) {
            for (let option of channelSelect.options) {
                if (!option.value) continue; // skip placeholder

                // Untuk Virtual Account, hanya tampilkan channel yang tipe 'transfer' dan is_bank=true
                if (type === 'virtual_account') {
                    const isBank = option.getAttribute('data-is-bank') === '1' || option.getAttribute('data-is-bank') ===
                        'true';
                    option.style.display = (option.getAttribute('data-type') === 'transfer' && isBank) ? 'block' : 'none';
                } else if (type === 'pulsa') {
                    // Pulsa tidak pakai channel, nanti diganti jadi provider — sembunyikan semua channel
                    option.style.display = 'none';
                } else {
                    // Tipe lainnya tampilkan channel sesuai type
                    option.style.display = (option.getAttribute('data-type') === type) ? 'block' : 'none';
                }
            }
            channelSelect.value = '';
        }

        function resetAccountInputs() {
            accountNameInput.value = '';
            accountNumberInput.value = '';
            accountNameInput.required = false;
            accountNumberInput.required = false;
        }

        function updateFormFields(type) {
            resetAccountInputs();

            if (type === 'qris') {
                // QRIS tidak perlu bukti rekening
                channelSelectDiv.style.display = 'none';
                channelSelect.required = false;

                accountProofsInput.required = false;
                accountProofsDiv.style.display = 'none';

                qrisProofDiv.style.display = 'block';
                qrisProofDiv.querySelector('input').required = true;

                accountNameDiv.style.display = 'none';
                accountNumberDiv.style.display = 'none';

            } else if (type === 'virtual_account' || type === 'transfer') {
                // Virtual Account pilih channel bank (transfer channel + is_bank)
                channelSelectDiv.style.display = 'block';
                channelLabel.textContent = 'Pilih Bank';
                channelSelect.required = true;
                filterChannelsByType(type);

                qrisProofDiv.style.display = 'none';
                qrisProofDiv.querySelector('input').required = false;
                qrisProofDiv.querySelector('input').value = '';

                accountProofsInput.required = true;
                accountProofsDiv.style.display = 'block';

                accountNameDiv.style.display = 'none';
                accountNumberDiv.style.display = 'block';
                accountNumberInput.required = true;

            } else if (type === 'pulsa') {
                // Pulsa pilih provider, bukan channel
                channelSelectDiv.style.display = 'block';
                channelLabel.textContent = 'Pilih Provider';
                channelSelect.required = true;

                // Filter channel select supaya kosong / atau kamu bisa render provider list dari backend yg beda
                // Untuk contoh ini, kita kosongkan opsi selain placeholder
                for (let option of channelSelect.options) {
                    if (!option.value) continue;
                    option.style.display = 'none';
                }
                channelSelect.value = '';

                qrisProofDiv.style.display = 'none';
                qrisProofDiv.querySelector('input').required = false;
                qrisProofDiv.querySelector('input').value = '';

                accountProofsInput.required = true;
                accountProofsDiv.style.display = 'block';

                accountNameDiv.style.display = 'none';
                accountNumberDiv.style.display = 'block';
                accountNumberInput.required = true;

            } else {
                // Tipe transfer dan lainnya
                channelSelectDiv.style.display = 'block';
                channelLabel.textContent = 'Pilih Channel';
                channelSelect.required = true;
                filterChannelsByType(type);

                qrisProofDiv.style.display = 'none';
                qrisProofDiv.querySelector('input').required = false;
                qrisProofDiv.querySelector('input').value = '';

                accountProofsInput.required = true;
                accountProofsDiv.style.display = 'block';

                if (type === 'transfer' || type === 'ewallet') {
                    accountNameDiv.style.display = 'block';
                    accountNumberDiv.style.display = 'block';
                    accountNameInput.required = true;
                    accountNumberInput.required = true;
                } else {
                    accountNameDiv.style.display = 'none';
                    accountNumberDiv.style.display = 'none';
                    accountNameInput.required = false;
                    accountNumberInput.required = false;
                }
            }
        }

        channelTypeSelect.addEventListener('change', function() {
            updateFormFields(this.value);
        });

        window.addEventListener('DOMContentLoaded', () => {
            if (channelTypeSelect.value) {
                updateFormFields(channelTypeSelect.value);
            }
        });

        // ==== Axios submit form ====
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('gamblingDepositForm');
            const submitBtn = document.getElementById('submitBtn');
            const alertContainer = document.getElementById('alertContainer');

            function showAlert(type, message) {
                alertContainer.innerHTML = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
                alertContainer.scrollIntoView({
                    behavior: 'smooth'
                });
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Clear alert
                alertContainer.innerHTML = '';

                // Disable button + show loading
                submitBtn.disabled = true;
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Menyimpan...';

                // FormData karena ada file upload
                const formData = new FormData(form);

                axios.post(form.action, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    })
                    .then(response => {
                        if (response.data.success) {
                            showAlert('success', response.data.message || 'Data berhasil disimpan.');
                            form.reset();
                            // Reset form fields yang dinamis
                            channelTypeSelect.dispatchEvent(new Event('change'));
                        } else {
                            showAlert('warning', response.data.message || 'Terjadi masalah.');
                        }
                    })
                    .catch(error => {
                        if (error.response) {
                            if (error.response.status === 422) {
                                let errors = error.response.data.errors;
                                let messages = '<ul class="mb-0">';
                                for (const key in errors) {
                                    if (errors.hasOwnProperty(key)) {
                                        errors[key].forEach(msg => {
                                            messages += `<li>${msg}</li>`;
                                        });
                                    }
                                }
                                messages += '</ul>';
                                showAlert('danger', `<strong>Validasi gagal:</strong> ${messages}`);
                            } else if (error.response.data.message) {
                                showAlert('danger', error.response.data.message);
                            } else {
                                showAlert('danger', 'Terjadi kesalahan server.');
                            }
                        } else {
                            showAlert('danger', 'Gagal menghubungi server.');
                        }
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    });
            });
        });
    </script>
@endpush
