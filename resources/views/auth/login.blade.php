<!doctype html>
<html lang="en" class="layout-wide customizer-hide" dir="ltr" data-skin="default"
    data-assets-path="{{ asset('template/assets/') }}" data-template="horizontal-menu-template" data-bs-theme="dark">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>Login | Cyber Patrol</title>
    <meta name="description" content="" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

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
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/libs/@form-validation/form-validation.css') }}" />
    <link rel="stylesheet" href="{{ asset('template/assets/vendor/css/pages/page-auth.css') }}" />
    <script src="{{ asset('template/assets/vendor/js/helpers.js') }}"></script>
    {{-- <script src="{{ asset('template/assets/vendor/js/template-customizer.js') }}"></script> --}}
    <script src="{{ asset('template/assets/js/config.js') }}"></script>
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner py-6">
                <div class="card">
                    <div class="card-body">
                        <div class="app-brand justify-content-center mb-6">
                            <a href="/" class="app-brand-link d-flex align-items-center justify-content-center">
                                <img src="https://cdn-icons-png.flaticon.com/128/6601/6601019.png" alt="CyberPatrol Logo" width="40"
                                    height="40" style="margin-right: 10px;" />
                                <span class="app-brand-text demo text-heading fw-bold"
                                    style="font-size: 1.5rem; color: #007bff;">CyberPatrol</span>
                            </a>
                        </div>
                        <h4 class="mb-1 text-center">Selamat Datang di CyberPatrol!</h4>
                        <p class="mb-6 text-center">Lindungi diri Anda dari situs ilegal dengan sistem pengawasan kami.</p>

                        <form id="formAuthentication" class="mb-4" action="{{ route('login') }}" method="POST">
                            @csrf
                            <div class="mb-6 form-control-validation">
                                <label for="login" class="form-label">Email or Username</label>
                                <input type="text" class="form-control" id="login" name="login"
                                    placeholder="Masukkan email atau username" autofocus />
                            </div>
                            <div class="mb-6 form-password-toggle form-control-validation">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="••••••••••••" aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i
                                            class="icon-base ti tabler-eye-off"></i></span>
                                </div>
                            </div>
                            <div class="mb-6">
                                <button id="btnLogin" class="btn btn-primary d-grid w-100"
                                    type="button">Login</button>
                            </div>
                        </form>


                    </div>
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
    <script src="{{ asset('template/assets/vendor/libs/@form-validation/popular.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/@form-validation/bootstrap5.js') }}"></script>
    <script src="{{ asset('template/assets/vendor/libs/@form-validation/auto-focus.js') }}"></script>
    <script src="{{ asset('template/assets/js/main.js') }}"></script>
    <script src="{{ asset('template/assets/js/pages-auth.js') }}"></script>

    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const form = document.getElementById('formAuthentication');
        const btnLogin = document.getElementById('btnLogin');

        btnLogin.addEventListener('click', function() {
            const formData = new FormData(form);
            btnLogin.disabled = true;
            btnLogin.innerHTML = 'Loading...';
            axios.post(form.action, formData)
                .then(response => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: 'Login berhasil, mengarahkan...',
                        timer: 2000,
                        showConfirmButton: true
                    }).then(() => {
                        window.location.href = response.data.redirect_url;
                    });
                })
                .catch(error => {
                    if (error.response) {
                        if (error.response.status === 422) {
                            const errors = error.response.data.errors;
                            let pesanError = '';
                            for (const key in errors) {
                                pesanError += errors[key].join(' ') + '\n';
                            }
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal',
                                text: pesanError
                            });
                        } else if (error.response.status === 401) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal Login',
                                text: 'Email/Username atau password salah'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Terjadi Kesalahan',
                                text: 'Silakan coba lagi nanti.'
                            });
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi Kesalahan',
                            text: 'Tidak dapat terhubung ke server.'
                        });
                    }
                })
                .finally(() => {
                    btnLogin.disabled = false;
                    btnLogin.innerHTML = 'Login';
                });
        });
    </script>

</body>

</html>
