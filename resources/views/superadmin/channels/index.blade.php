@extends('template.app')
@section('title', 'Data Channel')
@push('styles')
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/select2/select2.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/tagify/tagify.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/bootstrap-select/bootstrap-select.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/typeahead-js/typeahead.css') }}" />
@endpush
@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Data Channel</h5>
            <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
                <div class="col-md-4 user_role"></div>
                <div class="col-md-4 user_plan"></div>
                <div class="col-md-4 user_status"></div>
            </div>
        </div>
        <div class="card-datatable">
            <div id="DataTables_Table_0_wrapper" class="dt-container dt-bootstrap5 dt-empty-footer">

                <div class="row m-3 my-0 justify-content-between">
                    <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                        <div class="dt-length mb-md-6 mb-0">
                            <select name="DataTables_Table_0_length" aria-controls="DataTables_Table_0"
                                class="form-select ms-0" id="dt-length-0">
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                            <label for="dt-length-0"></label>
                        </div>
                    </div>
                    <div
                        class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
                        <div class="dt-search"><input type="search" class="form-control" id="dt-search-0"
                                placeholder="Cari channel" aria-controls="DataTables_Table_0"><label
                                for="dt-search-0"></label>
                        </div>
                        <div class="dt-buttons btn-group flex-wrap d-flex gap-4 mb-md-0 mb-4">
                            <button class="btn add-new btn-primary" tabindex="0" aria-controls="DataTables_Table_0"
                                type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddChannel">
                                <span>
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="icon-base ti tabler-plus icon-xs"></i>
                                        <span class="d-none d-sm-inline-block">Tambah Channel</span>
                                    </span>
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="justify-content-between dt-layout-table">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-full table-responsive">
                    <table class="datatables-users table dataTable dtr-column" id="DataTables_Table_0"
                        aria-describedby="DataTables_Table_0_info">
                        <thead class="border-top">
                            <tr>
                                <th style="width: 50px">#</th>
                                <th>Master Data</th>
                                <th>Channels</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="dt-empty text-center">Loading...</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center m-3">
                <div id="DataTables_Table_0_info" class="dataTables_info" role="status" aria-live="polite"></div>
                <ul id="pagination" class="pagination pagination-primary pagination-sm"></ul>
            </div>

        </div>
    </div>


    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddChannel" aria-labelledby="offcanvasAddChannelLabel">
        <div class="offcanvas-header border-bottom">
            <h5 id="offcanvasAddChannelLabel" class="offcanvas-title">Tambah Channel</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
            <form id="addNewChannel" novalidate>
                <div class="mb-6 form-control-validation fv-plugins-icon-container">
                    <label class="form-label" for="add-channel-customer">Master Data</label>
                    <select name="provider_id" id="add-channel-customer" class="select2 form-select" required>
                        <option value="">Pilih Master Data</option>
                        @foreach ($providers as $provider)
                            <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-6 form-control-validation fv-plugins-icon-container">
                    <label class="form-label" for="add-channel-type">Tipe Channel</label>
                    <select id="add-channel-type" name="channel_type" class="form-select" required>
                        <option value="">Pilih Tipe</option>
                        <option value="transfer">Transfer</option>
                        <option value="qris">QRIS</option>
                        <option value="virtual_account">Virtual Account</option>
                        <option value="pulsa">Pulsa</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-6 form-control-validation fv-plugins-icon-container">
                    <label class="form-label" for="add-channel-code">Kode Channel</label>
                    <input type="text" id="add-channel-code" class="form-control" name="channel_code"
                        placeholder="Kode Channel" required>
                    <div class="invalid-feedback"></div>
                </div>
                <button type="submit" class="btn btn-primary me-3 data-submit waves-effect waves-light">
                    <span class="btn-text">Submit</span>
                    <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                </button>
                <button type="reset" class="btn btn-label-danger waves-effect"
                    data-bs-dismiss="offcanvas">Cancel</button>
            </form>
        </div>
    </div>


    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditChannel"
        aria-labelledby="offcanvasEditChannelLabel">
        <div class="offcanvas-header border-bottom">
            <h5 id="offcanvasEditChannelLabel" class="offcanvas-title">Edit Channel</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
            <form id="editChannelForm" novalidate>
                <input type="hidden" id="edit-channel-id" name="id" />
                <div class="mb-6 form-control-validation fv-plugins-icon-container">
                    <label class="form-label" for="edit-channel-customer">Master Data</label>
                    <select name="provider_id" id="edit-channel-customer" class="form-select" required>
                        <option value="">Pilih Master Data</option>
                        @foreach ($providers as $provider)
                            <option value="{{ $provider->id }}">{{ $provider->name }}</option>
                        @endforeach
                    </select>
                    <div class="invalid-feedback"></div>
                </div>

                <div class="mb-6 form-control-validation fv-plugins-icon-container">
                    <label class="form-label" for="edit-channel-type">Tipe Channel</label>
                    <select id="edit-channel-type" name="channel_type" class="form-select" required>
                        <option value="">Pilih Tipe</option>
                        <option value="transfer">Transfer</option>
                        <option value="qris">QRIS</option>
                        <option value="virtual_account">Virtual Account</option>
                        <option value="pulsa">Pulsa</option>
                    </select>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-6 form-control-validation fv-plugins-icon-container">
                    <label class="form-label" for="edit-channel-code">Kode Channel</label>
                    <input type="text" id="edit-channel-code" class="form-control" name="channel_code"
                        placeholder="Kode Channel" required>
                    <div class="invalid-feedback"></div>
                </div>
                <button type="submit" class="btn btn-primary me-3 data-submit waves-effect waves-light">
                    <span class="btn-text">Update</span>
                    <span class="spinner-border spinner-border-sm ms-2 d-none" role="status" aria-hidden="true"></span>
                </button>
                <button type="reset" class="btn btn-label-danger waves-effect"
                    data-bs-dismiss="offcanvas">Cancel</button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute(
            'content');

        const tableBody = document.querySelector('#DataTables_Table_0 tbody');
        const pagination = document.querySelector('#pagination');
        const infoText = document.getElementById('DataTables_Table_0_info');
        const perPageSelect = document.getElementById('dt-length-0');
        const searchInput = document.getElementById('dt-search-0');
        const addNewChannel = document.getElementById('addNewChannel');
        const editChannelForm = document.getElementById('editChannelForm');
        const addSubmitBtn = addNewChannel.querySelector('button[type="submit"]');
        const editSubmitBtn = editChannelForm.querySelector('button[type="submit"]');
        let currentPage = 1;
        let lastPage = 1;
        let perPage = parseInt(perPageSelect.value);
        let searchQuery = '';

        function setLoading(btn, isLoading, defaultText) {
            if (isLoading) {
                btn.disabled = true;
                btn.innerHTML =
                    `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...`;
            } else {
                btn.disabled = false;
                btn.innerHTML = defaultText;
            }
        }

        function formatChannelType(str) {
            return str
                .toLowerCase()
                .split('_')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        }

        function fetchChannels(page = 1) {
            currentPage = page;
            tableBody.innerHTML = `<tr><td colspan="3" class="text-center">Loading...</td></tr>`;

            axios.get(`/superadmin/channels/data`, {
                    params: {
                        page: currentPage,
                        per_page: perPage,
                        search: searchQuery
                    }
                })
                .then(res => {
                    const {
                        data,
                        current_page,
                        last_page,
                        total
                    } = res.data;

                    if (data.length === 0) {
                        tableBody.innerHTML =
                            `<tr><td colspan="3" class="text-center">Channel tidak ditemukan.</td></tr>`;
                        pagination.innerHTML = '';
                        infoText.textContent = `Showing 0 to 0 of 0 entries`;
                        return;
                    }

                    lastPage = last_page;
                    infoText.textContent =
                        `Showing ${(current_page - 1) * perPage + 1} to ${Math.min(current_page * perPage, total)} of ${total} entries`;

                    tableBody.innerHTML = '';

                    data.forEach((customer, index) => {
                        let channelsHtml = '';

                        if (customer.channels.length === 0) {
                            channelsHtml = `<span class="text-muted fst-italic">Belum ada channel</span>`;
                        } else {
                            channelsHtml = customer.channels.map(channel => `
                                <div class="channel-card d-flex align-items-center justify-content-between mb-2 p-2 border rounded-2 shadow-sm">
                                    <div>
                                        <span class="badge bg-primary me-2">${channel.channel_code || '-'}</span>
                                        <span class="text-capitalize text-secondary">${formatChannelType(channel.channel_type)}</span>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-warning btn-edit" data-id="${channel.id}" data-customer="${customer.id}" title="Edit Channel">
                                            <i class="icon-base ti tabler-pencil"></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-delete" data-id="${channel.id}" data-customer="${customer.id}" title="Hapus Channel">
                                            <i class="icon-base ti tabler-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            `).join('');
                        }

                        tableBody.innerHTML += `
                            <tr class="align-middle">
                                <td>${(current_page - 1) * perPage + index + 1}</td>
                                <td><strong>${customer.name}</strong></td>
                                <td>${channelsHtml}</td>
                            </tr>
                        `;
                    });

                    renderPagination(current_page, last_page);
                    attachRowEventListeners();
                })
                .catch(err => {
                    tableBody.innerHTML =
                        `<tr><td colspan="3" class="text-center text-danger">Gagal memuat data.</td></tr>`;
                    pagination.innerHTML = '';
                    infoText.textContent = 'Showing 0 to 0 of 0 entries';
                    console.error(err);
                    Swal.fire('Error', 'Gagal memuat data channel.', 'error');
                });
        }


        function renderPagination(currentPage, lastPage) {
            let html = '';

            html += `
        <li class="page-item first ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link waves-effect" href="javascript:void(0);" data-page="first">
                <i class="icon-base ti tabler-chevrons-left icon-sm"></i>
            </a>
        </li>
        <li class="page-item prev ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link waves-effect" href="javascript:void(0);" data-page="prev">
                <i class="icon-base ti tabler-chevron-left icon-sm"></i>
            </a>
        </li>
    `;

            function addPage(i) {
                return `
            <li class="page-item ${i === currentPage ? 'active' : ''}">
                <a class="page-link waves-effect" href="javascript:void(0);" data-page="${i}">${i}</a>
            </li>
        `;
            }

            function addEllipsis() {
                return `
            <li class="page-item disabled">
                <span class="page-link">...</span>
            </li>
        `;
            }

            if (lastPage <= 7) {
                for (let i = 1; i <= lastPage; i++) {
                    html += addPage(i);
                }
            } else {
                for (let i = 1; i <= 3; i++) {
                    html += addPage(i);
                }

                let startMiddle = currentPage - 1;
                let endMiddle = currentPage + 1;

                if (startMiddle < 4) startMiddle = 4;
                if (endMiddle > lastPage - 3) endMiddle = lastPage - 3;

                if (startMiddle > 4) {
                    html += addEllipsis();
                }

                for (let i = startMiddle; i <= endMiddle; i++) {
                    if (i > 3 && i < lastPage - 2) {
                        html += addPage(i);
                    }
                }

                if (endMiddle < lastPage - 3) {
                    html += addEllipsis();
                }

                for (let i = lastPage - 2; i <= lastPage; i++) {
                    html += addPage(i);
                }
            }

            html += `
        <li class="page-item next ${currentPage === lastPage ? 'disabled' : ''}">
            <a class="page-link waves-effect" href="javascript:void(0);" data-page="next">
                <i class="icon-base ti tabler-chevron-right icon-sm"></i>
            </a>
        </li>
        <li class="page-item last ${currentPage === lastPage ? 'disabled' : ''}">
            <a class="page-link waves-effect" href="javascript:void(0);" data-page="last">
                <i class="icon-base ti tabler-chevrons-right icon-sm"></i>
            </a>
        </li>
    `;

            pagination.innerHTML = html;

            pagination.querySelectorAll('a.page-link').forEach(link => {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    let page = link.getAttribute('data-page');
                    if (page === 'first') page = 1;
                    else if (page === 'prev') page = currentPage > 1 ? currentPage - 1 : 1;
                    else if (page === 'next') page = currentPage < lastPage ? currentPage + 1 : lastPage;
                    else if (page === 'last') page = lastPage;
                    else page = parseInt(page);

                    if (page !== currentPage) {
                        fetchChannels(page);
                    }
                });
            });
        }



        function toggleChannelCodeInput(selectElement, codeInputWrapper) {
            const value = selectElement.value;
            if (value === 'transfer' || value === 'pulsa') {
                codeInputWrapper.style.display = 'none';
                codeInputWrapper.querySelector('input').value = '';
            } else {
                codeInputWrapper.style.display = 'block';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const addTypeSelect = document.getElementById('add-channel-type');
            const addCodeWrapper = document.getElementById('add-channel-code').closest('.mb-6');

            addTypeSelect.addEventListener('change', () => {
                toggleChannelCodeInput(addTypeSelect, addCodeWrapper);
            });
            toggleChannelCodeInput(addTypeSelect, addCodeWrapper);

            const editTypeSelect = document.getElementById('edit-channel-type');
            const editCodeWrapper = document.getElementById('edit-channel-code').closest('.mb-6');

            editTypeSelect.addEventListener('change', () => {
                toggleChannelCodeInput(editTypeSelect, editCodeWrapper);
            });
            toggleChannelCodeInput(editTypeSelect, editCodeWrapper);
        });


        function attachRowEventListeners() {
            document.querySelectorAll('.btn-edit').forEach(btn => {
                btn.addEventListener('click', e => {
                    const id = btn.getAttribute('data-id');
                    openEditOffcanvas(id);
                });
            });

            document.querySelectorAll('.btn-delete').forEach(btn => {
                btn.addEventListener('click', e => {
                    const id = btn.getAttribute('data-id');
                    deleteChannel(id);
                });
            });
        }

        function toggleChannelCodeInput(selectElement, codeInputWrapper) {
            const value = selectElement.value;
            if (value === 'transfer' || value === 'pulsa') {
                codeInputWrapper.style.display = 'none';
                codeInputWrapper.querySelector('input').value = '';
            } else {
                codeInputWrapper.style.display = 'block';
            }
        }

        function openEditOffcanvas(id) {
            axios.get(`/superadmin/channels/${id}/show`)
                .then(res => {
                    const channel = res.data;
                    document.getElementById('edit-channel-id').value = channel.id;
                    document.getElementById('edit-channel-customer').value = channel.provider_id;
                    document.getElementById('edit-channel-type').value = channel.channel_type;
                    document.getElementById('edit-channel-code').value = channel.channel_code || '';

                    const editTypeSelect = document.getElementById('edit-channel-type');
                    const editCodeWrapper = document.getElementById('edit-channel-code').closest('.mb-6');
                    toggleChannelCodeInput(editTypeSelect, editCodeWrapper);

                    const offcanvasEdit = new bootstrap.Offcanvas(document.getElementById('offcanvasEditChannel'));
                    offcanvasEdit.show();
                })
                .catch(err => {
                    Swal.fire('Error', 'Gagal memuat data channel.', 'error');
                    console.error(err);
                });
        }


        function deleteChannel(id) {
            Swal.fire({
                title: 'Apakah kamu yakin?',
                text: "Tindakan ini tidak dapat dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete(`/superadmin/channels/${id}`)
                        .then(() => {
                            Swal.fire('Deleted!', 'Channel telah dihapus.', 'success');
                            fetchChannels(currentPage);
                        })
                        .catch(() => {
                            Swal.fire('Error', 'Gagal menghapus channel.', 'error');
                        });
                }
            });
        }

        addNewChannel.addEventListener('submit', function(e) {
            e.preventDefault();

            setLoading(addSubmitBtn, true, '');

            const formData = new FormData(addNewChannel);

            axios.post('/superadmin/channels/store', formData)
                .then(() => {
                    Swal.fire('Success', 'Channel berhasil ditambahkan.', 'success');
                    addNewChannel.reset();

                    const offcanvasAdd = bootstrap.Offcanvas.getInstance(document.getElementById(
                        'offcanvasAddChannel'));
                    offcanvasAdd.hide();

                    fetchChannels(1);
                })
                .catch(err => {
                    if (err.response && err.response.data.errors) {
                        const errors = err.response.data.errors;
                        Object.keys(errors).forEach(key => {
                            const input = addNewChannel.querySelector(`[name="${key}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                input.nextElementSibling.textContent = errors[key][0];
                            }
                        });
                    } else {
                        Swal.fire('Error', 'Gagal menambahkan channel.', 'error');
                    }
                })
                .finally(() => {
                    setLoading(addSubmitBtn, false, '<span class="btn-text">Submit</span>');
                });
        });

        addNewChannel.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('input', () => {
                input.classList.remove('is-invalid');
                input.nextElementSibling.textContent = '';
            });
        });

        editChannelForm.addEventListener('submit', function(e) {
            e.preventDefault();

            setLoading(editSubmitBtn, true, '');

            const id = document.getElementById('edit-channel-id').value;
            const formData = new FormData(editChannelForm);

            if (!formData.get('channel_code')) {
                formData.delete('channel_code');
            }

            axios.post(`/superadmin/channels/${id}`, formData, {
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    }
                })
                .then(() => {
                    Swal.fire('Success', 'Channel berhasil diubah.', 'success');
                    editChannelForm.reset();

                    const offcanvasEdit = bootstrap.Offcanvas.getInstance(document.getElementById(
                        'offcanvasEditChannel'));
                    offcanvasEdit.hide();

                    fetchChannels(currentPage);
                })
                .catch(err => {
                    if (err.response && err.response.data.errors) {
                        const errors = err.response.data.errors;
                        Object.keys(errors).forEach(key => {
                            const input = editChannelForm.querySelector(`[name="${key}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                input.nextElementSibling.textContent = errors[key][0];
                            }
                        });
                    } else {
                        Swal.fire('Error', 'Gagal mengubah channel.', 'error');
                    }
                })
                .finally(() => {
                    setLoading(editSubmitBtn, false, '<span class="btn-text">Update</span>');
                });
        });

        editChannelForm.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('input', () => {
                input.classList.remove('is-invalid');
                input.nextElementSibling.textContent = '';
            });
        });

        perPageSelect.addEventListener('change', () => {
            perPage = parseInt(perPageSelect.value);
            fetchChannels(1);
        });

        let debounceTimeout = null;
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                searchQuery = searchInput.value.trim();
                fetchChannels(1);
            }, 500);
        });

        fetchChannels();
    </script>
@endpush
