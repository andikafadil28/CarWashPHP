<?php
//session_start();
if (!empty($_SESSION["username_kantin"])) {
    header('location:home');
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Login aplikasi Carwash">
    <title>Login - Carwash</title>
    <link href="assets/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/theme-kantin.css" rel="stylesheet">
    <link href="sign-in.css" rel="stylesheet">
    <meta name="theme-color" content="#0B5ED7">
    <style>
        body.login-page {
            min-height: 100vh;
            background:
                radial-gradient(circle at top left, rgba(13, 110, 253, 0.18), transparent 32rem),
                linear-gradient(135deg, #e7f6ff 0%, #f8fbff 46%, #d9ecff 100%);
            color: #183153;
        }

        .login-shell {
            width: min(100%, 960px);
            padding: 1rem;
        }

        .login-card {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(320px, 380px);
            overflow: hidden;
            border: 0;
            border-radius: 1.25rem;
            box-shadow: 0 1.25rem 3rem rgba(24, 49, 83, 0.16);
        }

        .login-hero {
            position: relative;
            min-height: 480px;
            padding: 3rem;
            color: #fff;
            background:
                linear-gradient(135deg, rgba(11, 94, 215, 0.96), rgba(13, 202, 240, 0.88)),
                url("assets/brand/carwash-logo.svg") center 68% / 18rem no-repeat;
        }

        .login-hero::after {
            position: absolute;
            right: -4rem;
            bottom: -5rem;
            width: 17rem;
            height: 17rem;
            content: "";
            border: 2.5rem solid rgba(255, 255, 255, 0.14);
            border-radius: 999px;
        }

        .brand-mark {
            width: 72px;
            height: 72px;
            padding: 0.6rem;
            background: #fff;
            border-radius: 1.1rem;
            box-shadow: 0 1rem 2rem rgba(6, 58, 122, 0.18);
        }

        .login-form-panel {
            padding: 3rem 2.25rem;
            background: rgba(255, 255, 255, 0.96);
        }

        .form-signin {
            max-width: none;
            padding: 0;
        }

        .form-signin .form-floating {
            margin-bottom: 0.85rem;
        }

        .form-signin .form-control {
            min-height: 3.35rem;
            border-color: #d7e6f5;
            border-radius: 0.85rem;
        }

        .form-signin .form-control:focus {
            border-color: rgba(11, 94, 215, 0.45);
            box-shadow: 0 0 0 0.2rem rgba(11, 94, 215, 0.13);
        }

        .login-page .text-primary {
            color: #0b5ed7 !important;
        }

        .login-page .btn-primary {
            background-color: #0b5ed7 !important;
            border-color: #0b5ed7 !important;
        }

        .login-page .btn-primary:hover,
        .login-page .btn-primary:focus {
            background-color: #084db3 !important;
            border-color: #084db3 !important;
        }

        .login-page .form-check-input:checked {
            background-color: #0b5ed7;
            border-color: #0b5ed7;
        }

        .login-button {
            border-radius: 0.85rem;
            font-weight: 700;
        }

        .login-copy {
            max-width: 22rem;
            color: rgba(255, 255, 255, 0.82);
        }

        @media (max-width: 767.98px) {
            .login-card {
                display: block;
                border-radius: 1rem;
            }

            .login-hero {
                min-height: 220px;
                padding: 2rem;
                background-size: 10rem;
            }

            .login-form-panel {
                padding: 2rem 1.4rem;
            }
        }
    </style>
</head>

<body class="login-page d-flex align-items-center justify-content-center py-4">
    <main class="login-shell">
        <div class="login-card">
            <section class="login-hero d-flex flex-column justify-content-between">
                <div>
                    <img class="brand-mark mb-4" src="assets/brand/carwash-logo.svg" alt="Carwash">
                    <h1 class="display-6 fw-bold mb-3">Carwash</h1>
                    <p class="login-copy mb-0">Kelola transaksi, pelanggan, dan laporan pencucian kendaraan dari satu tempat.</p>
                </div>
                <p class="mb-0 small text-white-50">Aplikasi operasional carwash</p>
            </section>

            <section class="login-form-panel">
                <form class="form-signin needs-validation" novalidate action="validate/validate_login.php" method="post">
                    <img class="brand-mark d-md-none mb-4" src="assets/brand/carwash-logo.svg" alt="Carwash">
                    <p class="text-primary fw-bold mb-2">Selamat datang</p>
                    <h2 class="h3 mb-2 fw-bold">Masuk ke Carwash</h2>
                    <p class="text-body-secondary mb-4">Gunakan akun pegawai untuk melanjutkan.</p>

                    <div class="form-floating">
                        <input name="username" type="text" class="form-control" id="floatingInput" placeholder="ID Pegawai" required>
                        <label for="floatingInput">ID Pegawai</label>
                        <div class="invalid-feedback">
                            ID pegawai wajib diisi.
                        </div>
                    </div>

                    <div class="form-floating">
                        <input name="password" type="password" class="form-control" id="floatingPassword" placeholder="Password" required>
                        <label for="floatingPassword">Password</label>
                        <div class="invalid-feedback">
                            Password wajib diisi.
                        </div>
                    </div>

                    <div class="form-check text-start my-3">
                        <input class="form-check-input" type="checkbox" value="remember-me" id="checkDefault">
                        <label class="form-check-label" for="checkDefault">Ingat saya</label>
                    </div>

                    <button class="btn btn-primary login-button w-100 py-2" type="submit" name="submit_validate" value="isi">
                        Masuk
                    </button>

                    <p class="mt-5 mb-0 text-body-secondary small">&copy; <?php echo date('Y'); ?> Carwash</p>
                </form>
            </section>
        </div>
    </main>

    <script src="assets/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (() => {
            'use strict';

            const forms = document.querySelectorAll('.needs-validation');

            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>

</html>
