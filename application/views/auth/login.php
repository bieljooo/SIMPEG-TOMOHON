<!DOCTYPE html>
<html lang="id">

<?php $shell_css_version = @filemtime(FCPATH . 'assets/css/simpeg-shell.css') ?: time(); ?>
<?php $login_bg_version = @filemtime(FCPATH . 'assets/images/tomohonberdayasaing.png') ?: time(); ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | SIMPEG DPMPTSPD</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@500;600;700&family=Manrope:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Sora:wght@500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/simpeg-shell.css?v=' . $shell_css_version) ?>">
    <script src="https://code.iconify.design/iconify-icon/2.2.0/iconify-icon.min.js"></script>
    <style>
        body.auth-screen {
            min-height: 100vh;
            margin: 0;
            padding: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            background:
                linear-gradient(180deg, rgba(129, 33, 23, 0.38) 0%, rgba(90, 34, 26, 0.18) 18%, rgba(17, 25, 38, 0.24) 100%),
                url('<?= base_url('assets/images/tomohonberdayasaing.png?v=' . $login_bg_version) ?>') center center / cover no-repeat;
        }

        body.auth-screen::before {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(180deg, rgba(73, 18, 14, 0.18) 0%, rgba(12, 18, 29, 0.08) 58%, rgba(12, 18, 29, 0.22) 100%);
            pointer-events: none;
        }

        body.auth-screen .auth-shell-login {
            width: 100%;
            max-width: 342px;
            position: relative;
            z-index: 1;
        }

        body.auth-screen .auth-card-login {
            padding: 22px 20px 18px;
            border-radius: 22px;
            border: 1px solid #f0f1f7;
            background: #ffffff;
            box-shadow: 0 22px 52px rgba(16, 24, 40, 0.2);
            position: relative;
            overflow: hidden;
            isolation: isolate;
        }

        body.auth-screen .auth-card-login::before {
            display: none;
        }

        body.auth-screen .auth-card-login::after {
            display: none;
        }

        body.auth-screen .auth-card-login>* {
            position: relative;
            z-index: 1;
        }

        body.auth-screen .auth-login-top {
            text-align: center;
        }

        body.auth-screen .auth-login-logo {
            width: 42px !important;
            height: 42px !important;
            margin: 0 auto 10px;
        }

        body.auth-screen .auth-login-logo img {
            width: 42px !important;
            height: 42px !important;
        }

        body.auth-screen .auth-kicker {
            display: inline-block;
            margin-bottom: 8px;
            color: rgba(18, 18, 18, 0.72);
            font-family: 'Instrument Sans', sans-serif;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
        }

        body.auth-screen .auth-card-header h2 {
            margin: 0;
            color: rgba(12, 12, 12, 0.92);
            font-family: 'Sora', sans-serif;
            font-size: 23px;
            font-weight: 700;
            letter-spacing: -0.05em;
        }

        body.auth-screen .auth-card-header p {
            margin: 7px 0 0;
            color: rgba(32, 32, 32, 0.66);
            font-family: 'Manrope', sans-serif;
            font-size: 12px;
            line-height: 1.55;
        }

        body.auth-screen .auth-form {
            margin-top: 18px;
        }

        body.auth-screen .auth-form .form-group {
            margin-bottom: 12px;
        }

        body.auth-screen .auth-password-group {
            position: relative;
        }

        body.auth-screen .auth-form label {
            display: block;
            margin-bottom: 6px;
            color: rgba(18, 18, 18, 0.86);
            font-family: 'Instrument Sans', sans-serif;
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        body.auth-screen .auth-form label iconify-icon {
            color: rgba(18, 18, 18, 0.82);
        }

        body.auth-screen .auth-form .form-control {
            height: 42px;
            border-radius: 0;
            border: 0;
            border-bottom: 1px solid rgba(20, 20, 20, 0.48);
            background: #ffffff;
            color: rgba(15, 15, 15, 0.92);
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 12px;
            padding: 0 4px;
            box-shadow: none;
        }

        body.auth-screen .auth-password-group .form-control {
            padding-right: 36px;
        }

        body.auth-screen .auth-form .form-control::placeholder {
            color: rgba(34, 34, 34, 0.42);
        }

        body.auth-screen .auth-form .form-control:focus {
            border-bottom-color: #6d5efc;
            background: #ffffff;
            color: rgba(15, 15, 15, 0.95);
            box-shadow: none;
        }

        body.auth-screen .auth-password-toggle {
            position: absolute;
            right: 0;
            bottom: 8px;
            width: 28px;
            height: 28px;
            border: 0;
            background: transparent;
            color: rgba(33, 33, 33, 0.56);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            cursor: pointer;
        }

        body.auth-screen .auth-password-toggle:hover,
        body.auth-screen .auth-password-toggle:focus {
            color: #6d5efc;
            outline: none;
        }

        body.auth-screen .auth-password-toggle iconify-icon {
            font-size: 18px;
        }

        body.auth-screen .auth-submit {
            width: 100%;
            min-height: 42px;
            margin-top: 4px;
            border-radius: 999px;
            border: 0;
            background: linear-gradient(90deg, #6d5efc 0%, #5e4ff2 100%);
            color: #ffffff;
            font-family: 'Sora', sans-serif;
            font-size: 12px;
            font-weight: 600;
            box-shadow: 0 14px 28px rgba(109, 94, 252, 0.26);
        }

        body.auth-screen .auth-submit:hover,
        body.auth-screen .auth-submit:focus {
            background: linear-gradient(90deg, #5e4ff2 0%, #5243eb 100%);
            color: #ffffff;
        }

        body.auth-screen .auth-footer-note {
            margin-top: 12px;
            color: rgba(28, 28, 28, 0.62);
            font-family: 'Manrope', sans-serif;
            font-size: 10px;
            line-height: 1.55;
            text-align: center;
            letter-spacing: 0.04em;
        }

        @media (max-width: 575px) {
            body.auth-screen {
                padding: 18px;
            }

            body.auth-screen .auth-card-login {
                padding: 20px 18px 16px;
                border-radius: 18px;
            }
        }
    </style>
</head>

<body class="auth-screen">

    <div class="auth-shell auth-shell-login">
        <section class="auth-card auth-card-login">
            <div class="auth-login-top">
                <div class="auth-login-logo">
                    <img src="<?= base_url('assets/images/logo-tomohon.png') ?>" alt="Logo Kota Tomohon">
                </div>
                <span class="auth-kicker">Sistem Informasi Data Kepegawaian</span>
                <div class="auth-card-header">
                    <h2>Masuk ke sistem</h2>
                    <p>Gunakan NIP atau username dan Password untuk melanjutkan.</p>
                </div>
            </div>

            <form action="<?= site_url('auth/login') ?>" method="POST" class="auth-form" id="loginForm">
                <div class="form-group">
                    <label><iconify-icon icon="mdi:card-account-details-outline" class="mr-1"></iconify-icon> NIP / Username</label>
                    <input type="text" name="nip" class="form-control" placeholder="Masukkan NIP atau Username" required autofocus>
                </div>
                <div class="form-group">
                    <label><iconify-icon icon="mdi:lock-outline" class="mr-1"></iconify-icon> Password</label>
                    <div class="auth-password-group">
                        <input type="password" name="password" id="loginPassword" class="form-control" placeholder="Masukkan Password" required>
                        <button type="button" class="auth-password-toggle" id="loginPasswordToggle" aria-label="Tampilkan password" aria-pressed="false">
                            <iconify-icon icon="lucide:eye"></iconify-icon>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary auth-submit" id="loginSubmitButton">
                    <iconify-icon icon="mdi:login-variant"></iconify-icon>
                    <span>Masuk</span>
                </button>
            </form>

            <div class="auth-footer-note">Akses hanya untuk akun resmi internal DPMPTSPD Kota Tomohon.</div>
        </section>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php
        $login_flash_notifications = array(
            'success' => array('icon' => 'success', 'title' => 'Berhasil!'),
            'error' => array('icon' => 'error', 'title' => 'Login Gagal'),
            'warning' => array('icon' => 'warning', 'title' => 'Perhatian!'),
            'info' => array('icon' => 'info', 'title' => 'Informasi'),
        );
        foreach ($login_flash_notifications as $flash_key => $flash_config):
            if (!$this->session->flashdata($flash_key)) {
                continue;
            }
        ?>
            Swal.fire({
                icon: '<?= $flash_config['icon'] ?>',
                title: '<?= $flash_config['title'] ?>',
                text: '<?= $this->session->flashdata($flash_key) ?>',
                showConfirmButton: true
            });
        <?php endforeach; ?>

        (function () {
            var loginForm = document.getElementById('loginForm');
            var loginSubmitButton = document.getElementById('loginSubmitButton');
            var passwordInput = document.getElementById('loginPassword');
            var toggleButton = document.getElementById('loginPasswordToggle');

            if (loginForm) {
                loginForm.dataset.submitting = '0';

                loginForm.addEventListener('submit', function (event) {
                    if (loginForm.dataset.submitting === '1') {
                        event.preventDefault();
                        return;
                    }

                    loginForm.dataset.submitting = '1';

                    if (loginSubmitButton) {
                        loginSubmitButton.disabled = true;
                    }

                    if (window.Swal && typeof window.Swal.fire === 'function') {
                        Swal.fire({
                            title: 'Sedang masuk...',
                            text: 'Mohon tunggu sebentar.',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            showConfirmButton: false,
                            didOpen: function () {
                                Swal.showLoading();
                            }
                        });
                    }
                });
            }

            if (!passwordInput || !toggleButton) {
                return;
            }

            toggleButton.addEventListener('click', function () {
                var isPassword = passwordInput.getAttribute('type') === 'password';

                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                toggleButton.setAttribute('aria-pressed', isPassword ? 'true' : 'false');
                toggleButton.setAttribute('aria-label', isPassword ? 'Sembunyikan password' : 'Tampilkan password');
                toggleButton.innerHTML = '<iconify-icon icon="' + (isPassword ? 'lucide:eye-off' : 'lucide:eye') + '"></iconify-icon>';
            });
        })();
    </script>

</body>

</html>
