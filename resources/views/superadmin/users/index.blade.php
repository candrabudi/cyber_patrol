@extends('template.app')
@section('title', 'Data Pelanggan')
@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Data Pelanggan</h5>
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
                                placeholder="Search User" aria-controls="DataTables_Table_0"><label
                                for="dt-search-0"></label>
                        </div>
                        <div class="dt-buttons btn-group flex-wrap d-flex gap-4 mb-md-0 mb-4">
                            <button class="btn add-new btn-primary" tabindex="0" aria-controls="DataTables_Table_0"
                                type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUser">
                                <span>
                                    <span class="d-flex align-items-center gap-2">
                                        <i class="icon-base ti tabler-plus icon-xs"></i>
                                        <span class="d-none d-sm-inline-block">Tambah Pengguna</span>
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
                                <th>#</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Tanggal Daftar</th>
                                <th>Aksi</th>
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

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddUser" aria-labelledby="offcanvasAddUserLabel">
        <div class="offcanvas-header border-bottom">
            <h5 id="offcanvasAddUserLabel" class="offcanvas-title">Tambah User</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
            <form id="addNewUserForm" novalidate>
                <div class="mb-6 form-control-validation fv-plugins-icon-container">
                    <label class="form-label" for="add-user-email">Email</label>
                    <input type="email" id="add-user-email" class="form-control" name="email"
                        placeholder="john.doe@example.com" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-6 form-control-validation fv-plugins-icon-container">
                    <label class="form-label" for="add-user-username">Username</label>
                    <input type="text" id="add-user-username" class="form-control" name="username" placeholder="johndoe"
                        required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-6 form-control-validation fv-plugins-icon-container">
                    <label class="form-label" for="add-user-password">Password</label>
                    <input type="password" id="add-user-password" class="form-control" name="password"
                        placeholder="********" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-6 form-control-validation fv-plugins-icon-container">
                    <label class="form-label" for="add-user-role">Role</label>
                    <select id="add-user-role" name="role" class="form-select" required>
                        <option value="">Pilih Role</option>
                        <option value="superadmin">Superadmin</option>
                        <option value="admin">Admin</option>
                        <option value="reviewer">Reviewer</option>
                    </select>
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

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasEditUser" aria-labelledby="offcanvasEditUserLabel">
        <div class="offcanvas-header border-bottom">
            <h5 id="offcanvasEditUserLabel" class="offcanvas-title">Edit User</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0 p-6 h-100">
            <form id="editUserForm" novalidate>
                <input type="hidden" id="edit-user-id" name="id" />
                <div class="mb-6 form-control-validation fv-plugins-icon-container">
                    <label class="form-label" for="edit-user-email">Email</label>
                    <input type="email" id="edit-user-email" class="form-control" name="email"
                        placeholder="john.doe@example.com" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-6 form-control-validation fv-plugins-icon-container">
                    <label class="form-label" for="edit-user-username">Username</label>
                    <input type="text" id="edit-user-username" class="form-control" name="username"
                        placeholder="johndoe" required>
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-6 form-control-validation fv-plugins-icon-container">
                    <label class="form-label" for="edit-user-password">Password <small>(kosongkan jika tidak
                            dirubah)</small></label>
                    <input type="password" id="edit-user-password" class="form-control" name="password"
                        placeholder="********">
                    <div class="invalid-feedback"></div>
                </div>
                <div class="mb-6 form-control-validation fv-plugins-icon-container">
                    <label class="form-label" for="edit-user-role">Role</label>
                    <select id="edit-user-role" name="role" class="form-select" required>
                        <option value="">Pilih Role</option>
                        <option value="superadmin">Superadmin</option>
                        <option value="admin">Admin</option>
                        <option value="reviewer">Reviewer</option>
                    </select>
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
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        axios.defaults.headers.common['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]').getAttribute(
            'content');

        const tableBody = document.querySelector('#DataTables_Table_0 tbody');
        const pagination = document.querySelector('#pagination');
        const infoText = document.getElementById('DataTables_Table_0_info');
        const perPageSelect = document.getElementById('dt-length-0');
        const searchInput = document.getElementById('dt-search-0');
        const addNewUserForm = document.getElementById('addNewUserForm');
        const editUserForm = document.getElementById('editUserForm');
        const addSubmitBtn = addNewUserForm.querySelector('button[type="submit"]');
        const editSubmitBtn = editUserForm.querySelector('button[type="submit"]');
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

        function roleBadge(role) {
            switch (role.toLowerCase()) {
                case 'admin':
                    return `<span class="badge bg-danger">Admin</span>`;
                case 'reviewer':
                    return `<span class="badge bg-primary">Reviewer</span>`;
                case 'superadmin':
                    return `<span class="badge bg-success">Superadmin</span>`;
                default:
                    return `<span class="badge bg-secondary">${role}</span>`;
            }
        }

        function fetchUsers(page = 1) {
            currentPage = page;
            tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Loading...</td></tr>';

            axios.get(`/superadmin/users/data`, {
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
                        tableBody.innerHTML = '<tr><td colspan="6" class="text-center">Pengguna tidak ditemukan.</td></tr>';
                        pagination.innerHTML = '';
                        infoText.textContent = `Showing 0 to 0 of 0 entries`;
                        return;
                    }

                    lastPage = last_page;
                    infoText.textContent =
                        `Showing ${(current_page - 1) * perPage + 1} to ${Math.min(current_page * perPage, total)} of ${total} entries`;

                    tableBody.innerHTML = '';
                    data.forEach((user, index) => {
                        tableBody.innerHTML += `
                        <tr>
                            <td>${(current_page - 1) * perPage + index + 1}</td>
                            <td>${user.username}</td>
                            <td>${user.email}</td>
                            <td>${roleBadge(user.role)}</td>
                            <td>${user.register_at ?? '-'}</td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-edit" data-id="${user.id}">Edit</button>
                                <button class="btn btn-sm btn-danger btn-delete" data-id="${user.id}">Delete</button>
                            </td>
                        </tr>
                    `;
                    });

                    renderPagination(current_page, last_page);
                    attachRowEventListeners();
                })
                .catch(err => {
                    tableBody.innerHTML =
                        `<tr><td colspan="6" class="text-center text-danger">Gagal memuat data.</td></tr>`;
                    pagination.innerHTML = '';
                    infoText.textContent = 'Showing 0 to 0 of 0 entries';
                    console.error(err);
                    Swal.fire('Error', 'Gagal memuat data pengguna.', 'error');
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
        </li>`;

            for (let i = 1; i <= lastPage; i++) {
                html += `
        <li class="page-item ${i === currentPage ? 'active' : ''}">
            <a class="page-link waves-effect" href="javascript:void(0);" data-page="${i}">${i}</a>
        </li>`;
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
        </li>`;

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
                        fetchUsers(page);
                    }
                });
            });
        }

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
                    deleteUser(id);
                });
            });
        }

        function openEditOffcanvas(id) {
            axios.get(`/superadmin/users/${id}/show`)
                .then(res => {
                    const user = res.data;
                    document.getElementById('edit-user-id').value = user.id;
                    document.getElementById('edit-user-email').value = user.email;
                    document.getElementById('edit-user-username').value = user.username;
                    document.getElementById('edit-user-password').value = '';

                    const roleSelect = document.getElementById('edit-user-role');
                    if (roleSelect) {
                        roleSelect.value = user.role;
                    }

                    const offcanvasEdit = new bootstrap.Offcanvas(document.getElementById('offcanvasEditUser'));
                    offcanvasEdit.show();
                })
                .catch(err => {
                    Swal.fire('Error', 'Gagal memuat data.', 'error');
                    console.error(err);
                });
        }

        function deleteUser(id) {
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
                    axios.delete(`/superadmin/users/${id}`)
                        .then(() => {
                            Swal.fire('Deleted!', 'Pengguna telah dihapus.', 'success');
                            fetchUsers(currentPage);
                        })
                        .catch(() => {
                            Swal.fire('Error', 'Gagal menghapus pengguna.', 'error');
                        });
                }
            });
        }

        addNewUserForm.addEventListener('submit', function(e) {
            e.preventDefault();

            setLoading(addSubmitBtn, true, '');

            const formData = new FormData(addNewUserForm);

            axios.post('/superadmin/users/store', formData)
                .then(() => {
                    Swal.fire('Success', 'Pengguna berhasil ditambahkan.', 'success');
                    addNewUserForm.reset();

                    const offcanvasAdd = bootstrap.Offcanvas.getInstance(document.getElementById(
                        'offcanvasAddUser'));
                    offcanvasAdd.hide();

                    fetchUsers(1);
                })
                .catch(err => {
                    if (err.response && err.response.data.errors) {
                        const errors = err.response.data.errors;
                        Object.keys(errors).forEach(key => {
                            const input = addNewUserForm.querySelector(`[name="${key}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                input.nextElementSibling.textContent = errors[key][0];
                            }
                        });
                    } else {
                        Swal.fire('Error', 'Gagal menambahkan pengguna.', 'error');
                    }
                })
                .finally(() => {
                    setLoading(addSubmitBtn, false, '<span class="btn-text">Submit</span>');
                });
        });

        addNewUserForm.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('input', () => {
                input.classList.remove('is-invalid');
                input.nextElementSibling.textContent = '';
            });
        });

        editUserForm.addEventListener('submit', function(e) {
            e.preventDefault();

            setLoading(editSubmitBtn, true, '');

            const id = document.getElementById('edit-user-id').value;
            const formData = new FormData(editUserForm);

            if (!formData.get('password')) {
                formData.delete('password');
            }

            axios.post(`/superadmin/users/${id}`, formData, {
                    headers: {
                        'X-HTTP-Method-Override': 'PUT'
                    }
                })
                .then(() => {
                    Swal.fire('Success', 'Pengguna berhasil diubah.', 'success');
                    editUserForm.reset();

                    const offcanvasEdit = bootstrap.Offcanvas.getInstance(document.getElementById(
                        'offcanvasEditUser'));
                    offcanvasEdit.hide();

                    fetchUsers(currentPage);
                })
                .catch(err => {
                    if (err.response && err.response.data.errors) {
                        const errors = err.response.data.errors;
                        Object.keys(errors).forEach(key => {
                            const input = editUserForm.querySelector(`[name="${key}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                input.nextElementSibling.textContent = errors[key][0];
                            }
                        });
                    } else {
                        Swal.fire('Error', 'Gagal mengubah pengguna.', 'error');
                    }
                })
                .finally(() => {
                    setLoading(editSubmitBtn, false, '<span class="btn-text">Update</span>');
                });
        });

        editUserForm.querySelectorAll('input, select').forEach(input => {
            input.addEventListener('input', () => {
                input.classList.remove('is-invalid');
                input.nextElementSibling.textContent = '';
            });
        });

        perPageSelect.addEventListener('change', () => {
            perPage = parseInt(perPageSelect.value);
            fetchUsers(1);
        });

        let debounceTimeout = null;
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                searchQuery = searchInput.value.trim();
                fetchUsers(1);
            }, 500);
        });

        fetchUsers();
    </script>
@endpush
