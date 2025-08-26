@extends('template.app')
@section('title', 'Tambah Data Rekening Penampung')
@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
@endpush

@section('content')
    <div class="card mb-6">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Tambah Data Rekening Penampung</h5>
        </div>
        <div class="card-body mt-6">
            <form action="{{ route('admin.gambling_deposits.store') }}" method="POST" enctype="multipart/form-data"
                id="gamblingDepositForm" novalidate>
                @csrf

                <div id="alertContainer"></div>

                <h6>Data Website</h6>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="website_name" class="form-label">Nama Website</label>
                            <input type="text" class="form-control" id="website_name" name="website_name" required>
                            <div class="invalid-feedback">Nama website wajib diisi.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bukti Website</label>
                            <input type="file" class="form-control" name="website_proofs"
                                accept="image/*,application/pdf" required>
                            <div class="invalid-feedback">Bukti website wajib diupload (jpg/png/pdf, max 2MB).</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="website_url" class="form-label">URL Website</label>
                            <input type="url" class="form-control" id="website_url" name="website_url"
                                placeholder="https://contoh.com" required>
                            <div class="invalid-feedback">URL tidak valid, gunakan format https://...</div>
                        </div>
                    </div>
                </div>

                <hr>
                <h6>Data Rekening</h6>
                <div id="rekeningWrapper">
                    <div class="rekening-item border rounded p-3 mb-3">
                        <button type="button" class="btn-close float-end removeRekeningBtn" aria-label="Close"></button>

                        <div class="mb-3">
                            <label class="form-label">Pilih Tipe Channel</label>
                            <select class="form-select channel_type" required>
                                <option value="">-- Pilih Tipe Channel --</option>
                                <option value="transfer">Transfer</option>
                                <option value="qris">QRIS</option>
                                <option value="virtual_account">Virtual Account</option>
                                <option value="pulsa">Pulsa</option>
                            </select>
                            <div class="invalid-feedback">Tipe channel wajib dipilih.</div>
                        </div>

                        <div class="mb-3 account_name_div" style="display:none;">
                            <label class="form-label">Nama Rekening</label>
                            <input type="text" class="form-control account_name">
                            <div class="invalid-feedback">Nama rekening wajib diisi.</div>
                        </div>

                        <div class="mb-3 account_number_div" style="display:none;">
                            <label class="form-label account_number_label">Nomor Rekening / Nomor Handphone</label>
                            <input type="text" class="form-control account_number">
                            <div class="invalid-feedback">Nomor rekening / HP tidak valid.</div>
                        </div>

                        <div class="mb-3 channel_select_div" style="display:none;">
                            <label class="form-label channel_label">Pilih Channel</label>
                            <select class="form-select select2 channel_id">
                                <option value="">-- Pilih Channel --</option>
                            </select>
                            <div class="invalid-feedback">Channel wajib dipilih.</div>
                        </div>

                        <div class="mb-3 account_proofs_div">
                            <label class="form-label">Bukti Rekening</label>
                            <input type="file" class="form-control account_proofs" accept="image/*,application/pdf"
                                required>
                            <div class="invalid-feedback">Bukti rekening wajib diupload (jpg/png/pdf, max 2MB).</div>
                        </div>

                        <div class="mb-3 qris_proof_div" style="display:none;">
                            <label class="form-label">Bukti QRIS</label>
                            <input type="file" class="form-control qris_proofs" accept="image/*,application/pdf">
                            <div class="invalid-feedback">Bukti QRIS wajib diupload (jpg/png/pdf, max 2MB).</div>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-secondary mt-2" id="addRekeningBtn">+ Tambah Rekening</button>

                <div class="mt-4">
                    <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="{{ asset('template/assets/vendor/libs/select2/select2.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const websiteInput = document.getElementById('website_url');
            const submitBtn = document.getElementById('submitBtn');
            const rekeningWrapper = document.getElementById('rekeningWrapper');
            const addRekeningBtn = document.getElementById('addRekeningBtn');
            const form = document.getElementById('gamblingDepositForm');
            const alertContainer = document.getElementById('alertContainer');

            const banks = @json($banks);
            const providers = @json($providers);

            const urlRegex = /^(https?:\/\/)?([\w-]+\.)+[\w-]{2,}(\/.*)?$/i;
            const hpRegex = /^[0-9]{10,15}$/;
            const rekeningRegex = /^[0-9]{6,20}$/;

            websiteInput.addEventListener('blur', () => {
                const inputUrl = websiteInput.value.trim();
                if (!inputUrl) return;
                axios.get('/websites/check-url', {
                        params: {
                            url: inputUrl
                        }
                    })
                    .then(res => {
                        const {
                            exists,
                            message
                        } = res.data;
                        if (exists) {
                            websiteInput.classList.add('is-invalid');
                            websiteInput.nextElementSibling.textContent = message;
                            submitBtn.disabled = true;
                        } else {
                            websiteInput.classList.remove('is-invalid');
                            submitBtn.disabled = false;
                        }
                    })
                    .catch(err => console.error(err));
            });

            function updateFormFields(item, type) {
                const accountNameDiv = item.querySelector('.account_name_div');
                const accountNumberDiv = item.querySelector('.account_number_div');
                const accountNumberLabel = item.querySelector('.account_number_label');
                const channelSelectDiv = item.querySelector('.channel_select_div');
                const channelSelect = item.querySelector('.channel_id');
                const channelLabel = item.querySelector('.channel_label');
                const qrisProofDiv = item.querySelector('.qris_proof_div');
                const accountProofsDiv = item.querySelector('.account_proofs_div');

                accountNameDiv.style.display = 'none';
                accountNumberDiv.style.display = 'none';
                channelSelectDiv.style.display = 'none';
                qrisProofDiv.style.display = 'none';
                accountProofsDiv.style.display = 'block';

                if (type === 'qris') {
                    qrisProofDiv.style.display = 'block';
                    accountProofsDiv.style.display = 'none';
                    return;
                }

                channelSelectDiv.style.display = 'block';
                channelSelect.innerHTML = '';

                let placeholder = '-- Pilih Channel --';
                let items = [];
                if (['bank', 'transfer', 'virtual_account'].includes(type)) {
                    placeholder = '-- Pilih Bank --';
                    items = banks;
                } else if (type === 'pulsa') {
                    placeholder = '-- Pilih Provider --';
                    items = providers;
                }

                channelLabel.textContent = placeholder.replace('-- ', '').replace(' --', '');
                const opt = document.createElement('option');
                opt.value = '';
                opt.textContent = placeholder;
                channelSelect.appendChild(opt);

                items.forEach(i => {
                    const o = document.createElement('option');
                    o.value = i.id;
                    o.textContent = `${i.name} (${i.code})`;
                    channelSelect.appendChild(o);
                });

                if (['bank', 'transfer'].includes(type)) {
                    accountNameDiv.style.display = 'block';
                    accountNumberDiv.style.display = 'block';
                    accountNumberLabel.textContent = 'Nomor Rekening';
                } else if (type === 'virtual_account') {
                    accountNumberDiv.style.display = 'block';
                    accountNumberLabel.textContent = 'Nomor BIN (4-5 digit depan)';
                } else if (type === 'pulsa') {
                    accountNumberDiv.style.display = 'block';
                    accountNumberLabel.textContent = 'Nomor Handphone';
                }
            }

            function validateInput(input) {
                let valid = true;
                let message = "";
                const parent = input.closest('.rekening-item');
                const type = parent?.querySelector('.channel_type')?.value;

                // default required check (kecuali file)
                if (input.hasAttribute('required') && !input.value.trim() && input.type !== 'file') {
                    valid = false;
                    message = input.closest('.mb-3').querySelector('label')?.textContent + " wajib diisi.";
                }

                // validasi khusus website url
                if (input.id === 'website_url' && input.value && !urlRegex.test(input.value)) {
                    valid = false;
                    message = "URL tidak valid, gunakan format https://...";
                }

                // ===== TRANSFER =====
                if (type === 'transfer') {
                    if (input.classList.contains('account_name') && !input.value.trim()) {
                        valid = false;
                        message = "Nama Rekening wajib diisi untuk Transfer.";
                    }
                    if (input.classList.contains('account_number')) {
                        if (!input.value.trim()) {
                            valid = false;
                            message = "Nomor Rekening wajib diisi untuk Transfer.";
                        } else if (!rekeningRegex.test(input.value)) {
                            valid = false;
                            message = "Nomor Rekening harus 6–20 digit angka.";
                        }
                    }
                    if (input.classList.contains('channel_id')) {
                        if (!input.value.trim()) {
                            valid = false;
                            message = "Bank wajib dipilih untuk Transfer.";
                        }
                    }
                }

                // ===== QRIS =====
                if (type === 'qris') {
                    if (input.classList.contains('qris_proofs')) {
                        if (input.files.length === 0) {
                            valid = false;
                            message = "Bukti QRIS wajib diupload.";
                        } else {
                            const file = input.files[0];
                            const allowed = ['image/jpeg', 'image/png', 'application/pdf'];
                            if (!allowed.includes(file.type)) {
                                valid = false;
                                message = "Format Bukti QRIS harus jpg/png/pdf.";
                            } else if (file.size > 2 * 1024 * 1024) {
                                valid = false;
                                message = "Ukuran Bukti QRIS maksimal 2MB.";
                            }
                        }
                    }
                }

                // ===== PULSA =====
                if (type === 'pulsa' && input.classList.contains('account_number') && input.value) {
                    if (!hpRegex.test(input.value)) {
                        valid = false;
                        message = "Nomor HP harus 10–15 digit angka.";
                    }
                }

                // ===== VIRTUAL ACCOUNT =====
                if (type === 'virtual_account' && input.classList.contains('account_number') && input.value) {
                    if (!/^[0-9]{4,5}$/.test(input.value)) {
                        valid = false;
                        message = "Nomor BIN VA harus 4–5 digit angka.";
                    }
                }

                // ===== FILE umum (kecuali qris_proofs karena udah dicek di atas) =====
                if (input.type === 'file' && !input.classList.contains('qris_proofs')) {
                    if (input.required && input.files.length === 0) {
                        valid = false;
                        message = "File wajib diupload.";
                    } else if (input.files.length > 0) {
                        const file = input.files[0];
                        const allowed = ['image/jpeg', 'image/png', 'application/pdf'];
                        if (!allowed.includes(file.type)) {
                            valid = false;
                            message = "Format file harus jpg/png/pdf.";
                        } else if (file.size > 2 * 1024 * 1024) {
                            valid = false;
                            message = "Ukuran file maksimal 2MB.";
                        }
                    }
                }

                // apply error class & message
                if (!valid) {
                    input.classList.add('is-invalid');
                    input.nextElementSibling.textContent = message;
                } else {
                    input.classList.remove('is-invalid');
                }

                return valid;
            }


            function initRekeningEvents(item) {
                const typeSelect = item.querySelector('.channel_type');
                typeSelect.addEventListener('change', () => updateFormFields(item, typeSelect.value));

                const removeBtn = item.querySelector('.removeRekeningBtn');
                removeBtn.addEventListener('click', () => {
                    if (document.querySelectorAll('.rekening-item').length > 1) {
                        item.remove();
                    } else {
                        showAlert('warning', 'Minimal 1 rekening harus ada.');
                    }
                });

                item.querySelectorAll('input, select').forEach(el => {
                    el.addEventListener('blur', () => validateInput(el));
                    el.addEventListener('change', () => validateInput(el));
                });
            }

            rekeningWrapper.querySelectorAll('.rekening-item').forEach(initRekeningEvents);

            addRekeningBtn.addEventListener('click', () => {
                const firstItem = rekeningWrapper.querySelector('.rekening-item');
                const clone = firstItem.cloneNode(true);

                clone.querySelectorAll('input').forEach(i => {
                    i.value = '';
                    i.classList.remove('is-invalid');
                });
                clone.querySelectorAll('select').forEach(s => {
                    s.value = '';
                    s.classList.remove('is-invalid');
                });

                rekeningWrapper.appendChild(clone);
                initRekeningEvents(clone);
            });

            function showAlert(type, message) {
                alertContainer.innerHTML = `
                    <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>`;
                alertContainer.scrollIntoView({
                    behavior: 'smooth'
                });
            }

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                let valid = true;
                let errorList = [];

                form.querySelectorAll('input, select').forEach(el => {
                    if (!validateInput(el)) {
                        valid = false;
                        const label = el.closest('.mb-3')?.querySelector('label')?.textContent || el
                            .name;
                        errorList.push(label);
                    }
                });

                if (!valid) {
                    const uniqueErrors = [...new Set(errorList)];
                    showAlert('danger', "Harap periksa kembali field berikut:<br> - " + uniqueErrors.join(
                        "<br> - "));
                    return;
                }

                submitBtn.disabled = true;
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Menyimpan...';

                const formData = new FormData();
                formData.append('website_name', document.getElementById('website_name').value);
                formData.append('website_url', document.getElementById('website_url').value);
                formData.append('website_proofs', document.querySelector('[name="website_proofs"]').files[
                    0]);

                const rekeningItems = document.querySelectorAll('.rekening-item');
                rekeningItems.forEach((item, index) => {
                    const account = {
                        channel_type: item.querySelector('.channel_type').value,
                        account_name: item.querySelector('.account_name')?.value || null,
                        account_number: item.querySelector('.account_number')?.value || null,
                        channel_id: item.querySelector('.channel_id')?.value || null
                    };
                    formData.append(`accounts[${index}][channel_type]`, account.channel_type);
                    if (account.account_name) formData.append(`accounts[${index}][account_name]`,
                        account.account_name);
                    if (account.account_number) formData.append(
                        `accounts[${index}][account_number]`, account.account_number);
                    if (account.channel_id) formData.append(`accounts[${index}][channel_id]`,
                        account.channel_id);

                    const accProof = item.querySelector('.account_proofs');
                    if (accProof && accProof.files.length > 0) {
                        formData.append(`accounts[${index}][account_proofs]`, accProof.files[0]);
                    }
                    const qrisProof = item.querySelector('.qris_proofs');
                    if (qrisProof && qrisProof.files.length > 0) {
                        formData.append(`accounts[${index}][qris_proofs]`, qrisProof.files[0]);
                    }
                });

                axios.post(form.action, formData)
                    .then(res => {
                        if (res.data && res.data.success) {
                            showAlert('success', res.data.message || 'Data berhasil disimpan');
                            form.reset();
                            console.log('Saved Data:', res.data.data);
                        } else {
                            showAlert('warning', res.data.message || 'Terjadi masalah');
                        }
                    })
                    .catch(err => {
                        let msg = 'Gagal menyimpan data.';
                        if (err.response && err.response.data && err.response.data.message) {
                            msg = err.response.data.message;
                        }
                        showAlert('danger', msg);
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.textContent = originalText;
                    });

            });
        });
    </script>
@endpush
