@extends('template.app')
@section('title', 'Data Laporan Akun')
@section('content')
    <div class="card">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Data Laporan Akun</h5>
            <div class="d-flex gap-2">
                <button id="exportSelectedBtn" class="btn btn-success">
                    Export Selected
                </button>
                <button id="exportAllBtn" class="btn btn-primary">
                    Export All
                </button>
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
                                <option value="all">Tampilkan Semua</option>
                            </select>
                            <label for="dt-length-0"></label>
                        </div>
                    </div>
                    <div
                        class="d-md-flex align-items-center dt-layout-end col-md-auto ms-auto d-flex gap-md-4 justify-content-md-between justify-content-center gap-2 flex-wrap">
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
                                <th>
                                    <input type="checkbox" id="selectAll">
                                </th>
                                <th>#</th>
                                <th>Website</th>
                                <th>URL</th>
                                <th>Tipe</th>
                                <th>Kode</th>
                                <th>Pemilik Akun</th>
                                <th>Nomor Akun</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tableBody">
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
        const exportSelectedBtn = document.getElementById('exportSelectedBtn');
        const exportAllBtn = document.getElementById('exportAllBtn');

        let currentPage = 1;
        let lastPage = 1;
        let perPage = parseInt(perPageSelect.value);
        let searchQuery = '';

        function formatChannelType(str) {
            return str
                .toLowerCase()
                .split('_')
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        }

        function fetchDeposits(page = 1) {
            currentPage = page;
            tableBody.innerHTML = '<tr><td colspan="12" class="text-center">Loading...</td></tr>';

            axios.get('/customer/gambling-reports/data', {
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
                            '<tr><td colspan="10" class="text-center">Data tidak ditemukan.</td></tr>';
                        pagination.innerHTML = '';
                        infoText.textContent = `Showing 0 to 0 of 0 entries`;
                        return;
                    }

                    lastPage = last_page;
                    infoText.textContent =
                        `Showing ${(current_page - 1) * perPage + 1} to ${Math.min(current_page * perPage, total)} of ${total} entries`;

                    tableBody.innerHTML = '';
                    data.forEach((item, index) => {
                        tableBody.innerHTML += `
                <tr>
                    <td>
                        <input type="checkbox" class="rowCheckbox" value="${item.id}">
                    </td>
                    <td>${(current_page - 1) * perPage + index + 1}</td>
                    <td>${item.website_name}</td>
                    <td><a href="${item.website_url}" target="_blank" rel="noopener" class="btn btn-sm btn-info">Lihat</a></td>
                    <td>${formatChannelType(item.channel.channel_type)}</td>
                    <td>${item.channel?.channel_code ?? '-'}</td>
                    <td>${item.account_name}</td>
                    <td>${item.account_number}</td>
                    <td>
                        <a class="btn btn-sm btn-info" href="/customer/gambling-deposits/${item.id}/detail">Detail</a>
                    </td>
                </tr>
            `;
                    });

                    renderPagination(current_page, last_page);

                    const selectAll = document.getElementById('selectAll');
                    selectAll.checked = false;
                    selectAll.addEventListener('change', function() {
                        const checkboxes = document.querySelectorAll('.rowCheckbox');
                        checkboxes.forEach(cb => cb.checked = this.checked);
                    });

                    document.querySelectorAll('.rowCheckbox').forEach(cb => {
                        cb.addEventListener('change', function() {
                            const allCheckboxes = document.querySelectorAll('.rowCheckbox');
                            const allChecked = Array.from(allCheckboxes).every(c => c.checked);
                            selectAll.checked = allChecked;
                        });
                    });
                })
                .catch(err => {
                    tableBody.innerHTML =
                        `<tr><td colspan="10" class="text-center text-danger">Gagal memuat data.</td></tr>`;
                    pagination.innerHTML = '';
                    infoText.textContent = 'Showing 0 to 0 of 0 entries';
                    console.error(err);
                    Swal.fire('Error', 'Gagal memuat data gambling deposits.', 'error');
                });
        }

        function getSelectedIds() {
            return Array.from(document.querySelectorAll('.rowCheckbox:checked')).map(cb => cb.value);
        }

        function renderPagination(currentPage, lastPage) {
            let html = '';

            html += `
        <li class="page-item first ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="first">«</a>
        </li>
        <li class="page-item prev ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="prev">‹</a>
        </li>
    `;

            for (let i = 1; i <= lastPage; i++) {
                html += `
        <li class="page-item ${i === currentPage ? 'active' : ''}">
            <a class="page-link" href="#" data-page="${i}">${i}</a>
        </li>
        `;
            }

            html += `
        <li class="page-item next ${currentPage === lastPage ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="next">›</a>
        </li>
        <li class="page-item last ${currentPage === lastPage ? 'disabled' : ''}">
            <a class="page-link" href="#" data-page="last">»</a>
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
        
        function downloadFile(blob, filename) {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            a.click();
            window.URL.revokeObjectURL(url);
        }

        function exportData(ids = [], exportAll = false) {
            const btn = exportAll ? exportAllBtn : exportSelectedBtn;
            if (!btn) return alert('Export button not found');

            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Loading...';

            axios.post('/customer/gambling-reports/export', {
                    ids,
                    export_all: exportAll,
                    search: searchQuery || '',
                    // is_solved: isSolvedFilter,
                    // start_date: startDate,
                    // end_date: endDate
                }, {
                    responseType: 'blob'
                })
                .then(res => {
                    downloadFile(res.data, `gambling_reports_${Date.now()}.xlsx`);
                })
                .catch(err => {
                    console.error(err);
                    Swal.fire('Error', 'Gagal mengekspor data.', 'error');
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.textContent = originalText;
                });
        }

        exportSelectedBtn.addEventListener('click', () => {
            const ids = getSelectedIds();
            if (ids.length === 0) {
                Swal.fire('Peringatan', 'Pilih minimal 1 data untuk diekspor.', 'warning');
                return;
            }
            exportData(ids, false);
        });

        exportAllBtn.addEventListener('click', () => {
            exportData([], true);
        });

        perPageSelect.addEventListener('change', () => {
            if (perPageSelect.value === 'all') {
                perPage = 'all';
            } else {
                perPage = parseInt(perPageSelect.value);
            }
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
