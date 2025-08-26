@extends('template.app')
@section('title', 'Data Permintaan Rekening')
@section('content')
    <div class="card">
        <div class="card-header border-bottom">
            <h5 class="card-title mb-0">Data Permintaan Rekening</h5>
            <div class="d-flex justify-content-between align-items-center row pt-4 gap-4 gap-md-0">
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
            </div>
        </div>
        <div class="card-datatable">
            <div class="row m-3 my-0 justify-content-between">
                <div class="d-md-flex justify-content-between align-items-center dt-layout-start col-md-auto me-auto">
                    <div class="dt-length mb-md-6 mb-0">
                        <select class="form-select" id="dt-length-0">
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                        </select>
                    </div>
                </div>
                <div
                    class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
                    <div class="dt-search">
                        <input type="search" class="form-control" id="dt-search-0"
                            placeholder="Cari website / customer / creator">
                    </div>
                </div>
            </div>

            <div class="table-responsive m-3">
                <table class="table table-bordered table-striped">
                    <thead class="border-top">
                        <tr>
                            <th>#</th>
                            <th>Website</th>
                            <th>Channel</th>
                            <th>Customer</th>
                            <th>Alasan</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="deposit-table-body">
                        <tr>
                            <td colspan="10" class="text-center">Loading...</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center m-3">
                <div id="table-info" class="dataTables_info"></div>
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

        const tableBody = document.querySelector('#deposit-table-body');
        const pagination = document.querySelector('#pagination');
        const infoText = document.getElementById('table-info');
        const perPageSelect = document.getElementById('dt-length-0');
        const searchInput = document.getElementById('dt-search-0');

        let currentPage = 1;
        let lastPage = 1;
        let perPage = parseInt(perPageSelect.value);
        let searchQuery = '';

        function getStatusBadge(status) {
            switch (status) {
                case 'completed':
                    return `<span class="badge bg-success">Selesai</span>`;
                case 'process':
                    return `<span class="badge bg-info">Proses</span>`;
                case 'rejected':
                    return `<span class="badge bg-danger">Ditolak</span>`;
                case 'pending':
                default:
                    return `<span class="badge bg-warning">Pending</span>`;
            }
        }

        function fetchDeposits(page = 1) {
            currentPage = page;
            tableBody.innerHTML = '<tr><td colspan="10" class="text-center">Loading...</td></tr>';

            axios.get('/admin/request-gambling-deposits/data', {
                params: {
                    page: currentPage,
                    per_page: perPage,
                    search: searchQuery
                }
            }).then(res => {
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
                    infoText.textContent = `Menampilkan 0 dari 0 data`;
                    return;
                }

                lastPage = last_page;
                infoText.textContent =
                    `Menampilkan ${(current_page - 1) * perPage + 1} - ${Math.min(current_page * perPage, total)} dari ${total} data`;

                tableBody.innerHTML = '';
                data.forEach((item, index) => {
                    tableBody.innerHTML += `
                    <tr>
                        <td>${(current_page - 1) * perPage + index + 1}</td>
                        <td><a href="${item.website?.website_url}" target="_blank">${item.website?.website_name ?? '-'}</a></td>
                        <td>${item.channel?.channel_name ?? '-'}</td>
                        <td>${item.channel?.customer?.full_name ?? '-'}</td>
                        <td>${item.reason ?? '-'}</td>
                        <td>${getStatusBadge(item.status)}</td>
                        <td>${new Date(item.created_at).toLocaleDateString('id-ID')}</td>
                        <td>
                            <a href="/admin/request-gambling-deposits/${item.id}/detail" class="btn btn-sm btn-info">Detail</a>
                        </td>
                    </tr>
                `;
                });

                renderPagination(current_page, last_page);
            }).catch(err => {
                tableBody.innerHTML =
                    `<tr><td colspan="10" class="text-center text-danger">Gagal memuat data.</td></tr>`;
                pagination.innerHTML = '';
                infoText.textContent = '';
                Swal.fire('Error', 'Gagal memuat data request.', 'error');
            });
        }

        function renderPagination(currentPage, lastPage) {
            let html = `
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0);" data-page="1">&laquo;</a>
            </li>
        `;

            for (let i = 1; i <= lastPage; i++) {
                html += `
                <li class="page-item ${i === currentPage ? 'active' : ''}">
                    <a class="page-link" href="javascript:void(0);" data-page="${i}">${i}</a>
                </li>
            `;
            }

            html += `
            <li class="page-item ${currentPage === lastPage ? 'disabled' : ''}">
                <a class="page-link" href="javascript:void(0);" data-page="${lastPage}">&raquo;</a>
            </li>
        `;

            pagination.innerHTML = html;
            pagination.querySelectorAll('a.page-link').forEach(link => {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    const page = parseInt(link.getAttribute('data-page'));
                    if (page !== currentPage) fetchDeposits(page);
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

        fetchDeposits();
    </script>
@endpush
