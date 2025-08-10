<!doctype html>

<html lang="en" class="layout-navbar-fixed layout-menu-fixed layout-compact" dir="ltr" data-skin="default"
    data-assets-path="{{ asset('template/assets/') }}" data-template="horizontal-menu-template" data-bs-theme="dark">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title') | Cyber Patrol</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <meta name="description" content="" />

    <link rel="icon" type="image/x-icon" href="{{ asset('template/assets/img/favicon/favicon.ico') }}" />

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet" />

    <link rel="stylesheet" href="{{ asset('template/assets/vendor/fonts/iconify-icons.css') }}" />

    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/node-waves/node-waves.css') }}" />

    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/pickr/pickr-themes.css') }}" />

    <link rel="stylesheet" href="{{ asset('template/assets/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/css/demo.css') }}" />

    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />
    <link rel="stylesheet"
        href="{{ asset('template/assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/@form-validation/form-validation.css') }}" />

    <script src="{{ asset('template/assets/vendor/js/helpers.js') }}"></script>

    <script src="{{ asset('template/assets/js/config.js') }}"></script>
    <style>
        .swal2-container {
            z-index: 9999999 !important;
        }
    </style>
</head>

<body>
    <div class="layout-wrapper layout-navbar-full layout-horizontal layout-without-menu">
        <div class="layout-container">
            <nav class="layout-navbar navbar navbar-expand-xl align-items-center" id="layout-navbar">
                <div class="container-xxl">
                    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4 ms-0">
                        <a href="" class="app-brand-link d-flex align-items-center justify-content-center">
                            <img src="https://cdn-icons-png.flaticon.com/128/6601/6601019.png" alt="CyberPatrol Logo"
                                width="40" height="40" style="margin-right: 10px;" />
                            <span class="app-brand-text demo text-heading fw-bold"
                                style="font-size: 1.5rem; color: #007bff;">CyberPatrol</span>
                        </a>

                        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
                            <i
                                class="icon-base ti tabler-x icon-sm d-flex align-items-center justify-content-center"></i>
                        </a>
                    </div>

                    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
                        <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                            <i class="icon-base ti tabler-menu-2 icon-md"></i>
                        </a>
                    </div>

                    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
                        <ul class="navbar-nav flex-row align-items-center ms-md-auto">
                            <li class="nav-item d-flex align-items-center gap-3">
                                <div class="avatar avatar-online">
                                    <img src="{{ asset('template/assets/img/avatars/1.png') }}" alt="Avatar"
                                        class="rounded-circle" />
                                </div>
                                <div class="d-flex flex-column">
                                    @php
                                        $username = Auth::user()->username;
                                        $pascalCaseUsername = str_replace(
                                            ' ',
                                            '',
                                            ucwords(str_replace(['-', '_'], ' ', $username)),
                                        );
                                    @endphp
                                    <span class="fw-semibold">{{ $pascalCaseUsername }}</span>

                                    <small class="text-muted">{{ Auth::user()->role }}</small>
                                </div>
                                <a href="javascript:void(0)" class="btn btn-sm btn-danger d-flex align-items-center"
                                    id="logoutBtn">
                                    <small class="align-middle me-2">Logout</small>
                                    <i class="icon-base ti tabler-logout icon-14px"></i>
                                </a>

                                <script>
                                    document.getElementById('logoutBtn').addEventListener('click', function() {
                                        document.getElementById('logoutForm').submit();
                                    });
                                </script>

                                <form id="logoutForm" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>

                            </li>

                        </ul>
                    </div>
                </div>
            </nav>

            <div class="layout-page">
                <div class="content-wrapper">
                    <aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu flex-grow-0">
                        <div class="container-xxl d-flex h-100">
                            @if (Auth::user()->role == 'superadmin')
                                @include('template.partials.menu_superadmin')
                            @elseif (Auth::user()->role == 'admin')
                                @include('template.partials.menu_admin')
                            @elseif (Auth::user()->role == 'reviewer')
                                @include('template.partials.menu_reviewer')
                            @elseif (Auth::user()->role == 'customer')
                                @include('template.partials.menu_customer')
                            @endif
                        </div>
                    </aside>


                    <div class="container-xxl flex-grow-1 container-p-y">
                        @yield('content')
                    </div>

                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl">
                            <div
                                class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
                                <div class="text-body">
                                    ©
                                    <script>
                                        document.write(new Date().getFullYear());
                                    </script>
                                    , made with ❤️ by <a href="#" class="footer-link">CyberPatrol</a>
                                </div>
                            </div>
                        </div>
                    </footer>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('template/assets/vendor/libs/jquery/jquery.js') }}"></script>

    <script src="{{ asset('template/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/node-waves/node-waves.js') }}"></script>

    <script src="{{ asset('template/assets/vendor/libs/@algolia/autocomplete-js.js') }}"></script>

    <script src="{{ asset('template/assets/vendor/libs/pickr/pickr.js') }}"></script>

    <script src="{{ asset('template/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

    <script src="{{ asset('template/assets/vendor/libs/hammer/hammer.js') }}"></script>

    <script src="{{ asset('template/assets/vendor/libs/i18n/i18n.js') }}"></script>

    <script src="{{ asset('template/assets/vendor/js/menu.js') }}"></script>

    <script src="{{ asset('template/assets/vendor/libs/datatables-bs5/datatables-bootstrap5.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/select2/select2.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>

    <script src="{{ asset('template/assets/js/main.js') }}"></script>

    @stack('scripts')
</body>

</html>
