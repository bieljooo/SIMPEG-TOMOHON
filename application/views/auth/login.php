<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login | SIMPEG DPMPTSPD</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.iconify.design/iconify-icon/2.2.0/iconify-icon.min.js"></script>
    <style>
        body {
            font-family: 'Source Sans Pro', sans-serif;
            background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 40px 36px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header .icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #1e3a5f, #2c5282);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 26px;
            margin-bottom: 14px;
        }
        iconify-icon {
            display: inline-block;
            vertical-align: -0.125em;
        }
        .login-header h4 {
            font-weight: 700;
            color: #2d3748;
            margin: 0;
        }
        .login-header p {
            color: #718096;
            font-size: 13px;
            margin-top: 4px;
        }
        .form-group label {
            font-weight: 600;
            color: #4a5568;
            font-size: 13px;
        }
        .form-control {
            border-radius: 8px;
            padding: 10px 14px;
        }
        .form-control:focus {
            border-color: #63b3ed;
            box-shadow: 0 0 0 0.15rem rgba(66,153,225,0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #1e3a5f, #2c5282);
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-weight: 600;
            font-size: 15px;
            width: 100%;
            color: #fff;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #2c5282, #1e3a5f);
            color: #fff;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="login-header">
        <div class="icon"><iconify-icon icon="mdi:office-building-outline"></iconify-icon></div>
        <h4>SIMPEG DPMPTSPD</h4>
        <p>Dinas Penanaman Modal & Pelayanan Terpadu Satu Pintu</p>
    </div>

    <form action="<?= site_url('auth/login') ?>" method="POST">
        <div class="form-group">
            <label><iconify-icon icon="mdi:card-account-details-outline" class="mr-1"></iconify-icon> NIP / Username</label>
            <input type="text" name="nip" class="form-control" placeholder="Masukkan NIP atau Username" required autofocus>
        </div>
        <div class="form-group">
            <label><iconify-icon icon="mdi:lock-outline" class="mr-1"></iconify-icon> Password</label>
            <input type="password" name="password" class="form-control" placeholder="Masukkan Password" required>
        </div>
        <button type="submit" class="btn btn-login mt-2">
            <iconify-icon icon="mdi:login-variant" class="mr-1"></iconify-icon> Masuk
        </button>
    </form>
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
</script>

</body>
</html>
