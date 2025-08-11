@extends('template.app')
@section('title', 'Tambah Rekening Penampung')
@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
@endpush
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

                <div id="alertContainer"></div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="website_name" class="form-label">Nama Website</label>
                            <input type="text" class="form-control" id="website_name" name="website_name" required>
                        </div>

                        <div class="mb-3">
                            <label for="channel_type" class="form-label">Pilih Tipe Channel</label>
                            <select id="channel_type" name="channel_type" class="form-select" required>
                                <option value="">Pilih Tipe Channel</option>
                                <option value="transfer">Transfer</option>
                                <option value="qris">QRIS</option>
                                <option value="virtual_account">Virtual Account</option>
                                <option value="pulsa">Pulsa</option>
                            </select>
                        </div>

                        <div class="mb-3" id="account_name_div" style="display:none;">
                            <label for="account_name" class="form-label">Nama Rekening</label>
                            <input type="text" class="form-control" id="account_name" name="account_name">
                        </div>

                        <div class="mb-3" id="account_number_div" style="display:none;">
                            <label for="account_number" class="form-label">Nomor Rekening / Nomor Handphone</label>
                            <input type="text" class="form-control" id="account_number" name="account_number">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Bukti Website</label>
                            <input type="file" class="form-control" name="website_proofs"
                                accept="image/*,application/pdf" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="website_url" class="form-label">URL Website</label>
                            <input type="url" class="form-control" id="website_url" name="website_url" required>
                        </div>

                        <div class="mb-3" id="channel_select_div" style="display:none;">
                            <label for="channel_id" class="form-label" id="channel_label">Pilih Channel</label>
                            <select name="channel_id" id="channel_id" class="select2 form-select">
                                <option value="">Pilih Channel</option>
                                @foreach ($channels as $channel)
                                    <option value="{{ $channel->id }}" data-type="{{ $channel->channel_type }}"
                                        data-is-bank="{{ $channel->is_bank ?? false }}">
                                        {{ $channel->provider->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3" id="account_proofs_div">
                            <label class="form-label">Bukti Rekening</label>
                            <input type="file" class="form-control" name="account_proofs"
                                accept="image/*,application/pdf" required>
                        </div>

                        <div class="mb-3" id="qris_proof_div" style="display:none;">
                            <label class="form-label">Bukti QRIS</label>
                            <input type="file" class="form-control" name="qris_proofs" accept="image/*,application/pdf">
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary" id="submitBtn">Simpan</button>
                </div>
            </form>

        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('template/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/tagify/tagify.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/bootstrap-select/bootstrap-select.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/typeahead-js/typeahead.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/bloodhound/bloodhound.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>
        'use strict';

        $(function() {
            const select2 = $('.select2');

            if (select2.length) {
                select2.each(function() {
                    var $this = $(this);
                    $this.wrap('<div class="position-relative"></div>').select2({
                        placeholder: 'Select value',
                        dropdownParent: $this.parent()
                    });
                });
            }
        });
    </script>

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
                if (!option.value) continue;

                if (type === 'virtual_account') {
                    option.style.display = (option.getAttribute('data-type') === 'transfer') ? 'block' : 'none';
                } else if (type === 'pulsa') {
                    option.style.display = 'none';
                } else {
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

            const accountNumberLabel = accountNumberDiv.querySelector('label.form-label');

            if (type === 'transfer') {
                accountNumberLabel.textContent = 'Nomor Rekening';
            } else if (type === 'pulsa') {
                accountNumberLabel.textContent = 'Nomor Handphone';
            } else if (type === 'virtual_account') {
                accountNumberLabel.textContent = 'Nomor BIN (dikasi notes 4-5 digit depan)';
            } else {
                accountNumberLabel.textContent = 'Nomor Rekening / Nomor Handphone';
            }

            if (type === 'qris') {
                channelSelectDiv.style.display = 'none';
                channelSelect.required = false;

                accountProofsInput.required = false;
                accountProofsDiv.style.display = 'none';

                qrisProofDiv.style.display = 'block';
                qrisProofDiv.querySelector('input').required = true;

                accountNameDiv.style.display = 'none';
                accountNumberDiv.style.display = 'none';

            } else if (type === 'virtual_account' || type === 'transfer') {
                channelSelectDiv.style.display = 'block';
                channelSelect.required = true;

                channelLabel.textContent = 'Pilih Bank';
                channelSelect.options[0].text = 'Pilih Bank';

                filterChannelsByType(type);

                qrisProofDiv.style.display = 'none';
                qrisProofDiv.querySelector('input').required = false;
                qrisProofDiv.querySelector('input').value = '';

                accountProofsInput.required = true;
                accountProofsDiv.style.display = 'block';

                if (type === 'transfer') {
                    accountNameDiv.style.display = 'block';
                    accountNumberDiv.style.display = 'block';
                    accountNameInput.required = true;
                    accountNumberInput.required = true;
                } else {
                    accountNameDiv.style.display = 'none';
                    accountNumberDiv.style.display = 'block';
                    accountNameInput.required = false;
                    accountNumberInput.required = true;
                }

            } else if (type === 'pulsa') {
                channelSelectDiv.style.display = 'block';
                channelLabel.textContent = 'Pilih Provider';
                channelSelect.required = true;

                channelSelect.options[0].text = 'Pilih Provider';

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
                channelSelectDiv.style.display = 'block';
                channelLabel.textContent = 'Pilih Channel';
                channelSelect.required = true;

                channelSelect.options[0].text = 'Pilih Channel';

                filterChannelsByType(type);

                qrisProofDiv.style.display = 'none';
                qrisProofDiv.querySelector('input').required = false;
                qrisProofDiv.querySelector('input').value = '';

                accountProofsInput.required = true;
                accountProofsDiv.style.display = 'block';

                accountNameDiv.style.display = 'none';
                accountNumberDiv.style.display = 'none';
                accountNameInput.required = false;
                accountNumberInput.required = false;
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

        // Axios form submission handling (tidak berubah)
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

                alertContainer.innerHTML = '';

                submitBtn.disabled = true;
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Menyimpan...';

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
