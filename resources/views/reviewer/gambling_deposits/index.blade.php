@extends('template.app')

@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Data Akun Penampung</h5>
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

                        <!-- Filter Status -->
                        <div class="dt-status">
                            <select class="form-select" id="status-filter">
                                <option value="all" selected>Semua</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Disetujui</option>
                                <option value="rejected">Ditolak</option>
                            </select>
                        </div>

                        <div class="dt-search">
                            <input type="search" class="form-control" id="dt-search-0" placeholder="Cari channel"
                                aria-controls="DataTables_Table_0">
                            <label for="dt-search-0"></label>
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
                                <th>Website</th>
                                <th>URL</th>
                                <th>Tipe</th>
                                <th>Customer</th>
                                <th>Pemilik Akun</th>
                                <th>Nomor Akun</th>
                                <th>DiInput</th>
                                <th>DiUpdate</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="12" class="dt-empty text-center">Loading...</td>
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
        const statusSelect = document.getElementById('status-filter');

        let currentPage = 1;
        let lastPage = 1;
        let perPage = parseInt(perPageSelect.value);
        let searchQuery = '';
        let statusFilter = 'all';

        function formatChannelType(str) {
            return str
                .toLowerCase()
                .split('_')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        }

        function getStatusBadge(status) {
            switch (status) {
                case 'approved':
                    return `<span class="badge bg-success text-white">Disetujui</span>`;
                case 'rejected':
                    return `<span class="badge bg-danger text-white">Ditolak</span>`;
                case 'pending':
                default:
                    return `<span class="badge bg-warning text-white">Pending</span>`;
            }
        }

        function fetchDeposits(page = 1) {
            currentPage = page;
            tableBody.innerHTML = '<tr><td colspan="12" class="text-center">Loading...</td></tr>';

            axios.get('/reviewer/gambling-deposits/data', {
                    params: {
                        page: currentPage,
                        per_page: perPage,
                        search: searchQuery,
                        status: statusFilter
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
                        '<tr><td colspan="10" class="text-center">Data tidak ditemukan.</td></tr>';
                        pagination.innerHTML = '';
                        infoText.textContent = `Menampilkan 0 sampai 0 dari 0 data`;
                        return;
                    }

                    lastPage = last_page;
                    infoText.textContent =
                        `Menampilkan ${(current_page - 1) * perPage + 1} sampai ${Math.min(current_page * perPage, total)} dari ${total} data`;

                    tableBody.innerHTML = '';
                    data.forEach((item, index) => {
                        const customerName = item.channel?.customer?.full_name || item.channel_name || '-';
                        const nonMemberFlag = item.is_non_member ? '<span class="badge bg-warning text-white">Non Member</span>' : '';
                        const channelType = item.channel?.channel_type || '-';

                        tableBody.innerHTML += `
                            <tr>
                                <td>${(current_page - 1) * perPage + index + 1}</td>
                                <td>${item.website_name}</td>
                                <td><a href="${item.website_url}" target="_blank" rel="noopener">${item.website_url}</a></td>
                                <td>${formatChannelType(channelType)}</td>
                                <td>${customerName} ${nonMemberFlag}</td>
                                <td>${item.account_name}</td>
                                <td>${item.account_number}</td>
                                <td>${item.creator?.username ?? 'Unknown'}</td>
                                <td>${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
                                <td>${new Date(item.updated_at).toLocaleDateString('id-ID')}</td>
                                <td>${getStatusBadge(item.report_status)}</td>
                                <td>
                                    <a class="btn btn-sm btn-warning" href="/reviewer/gambling-deposits/${item.id}/edit">Edit</a>
                                </td>
                            </tr>
                        `;
                    });

                    renderPagination(current_page, last_page);
                })
                .catch(err => {
                    tableBody.innerHTML =
                        `<tr><td colspan="10" class="text-center text-danger">Gagal memuat data.</td></tr>`;
                    pagination.innerHTML = '';
                    infoText.textContent = 'Menampilkan 0 sampai 0 dari 0 data';
                    console.error(err);
                    Swal.fire('Error', 'Gagal memuat data gambling deposits.', 'error');
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

            for (let i = 1; i <= lastPage; i++) {
                html += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link waves-effect" href="javascript:void(0);" data-page="${i}">${i}</a>
                </li>
            `;
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
                        fetchDeposits(page);
                    }
                });
            });
        }

        perPageSelect.addEventListener('change', () => {
            perPage = parseInt(perPageSelect.value);
            fetchDeposits(1);
        });

        let debounceTimeout = null;
        searchInput.addEventListener('input', () => {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(() => {
                searchQuery = searchInput.value.trim();
                fetchDeposits(1);
            }, 500);
        });

        statusSelect.addEventListener('change', () => {
            statusFilter = statusSelect.value;
            fetchDeposits(1);
        });

        fetchDeposits();
    </script>
@endpush
