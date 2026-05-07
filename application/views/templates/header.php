<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?> | SIMPEG DPMPTSPD</title>

    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://code.iconify.design/iconify-icon/2.2.0/iconify-icon.min.js"></script>

    <style>
        body {
            font-family: 'Source Sans Pro', sans-serif;
            font-size: 14px;
            background-color: #f4f6f9;
        }

        iconify-icon {
            display: inline-block;
            vertical-align: -0.125em;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 100vh;
            background: linear-gradient(180deg, #1e3a5f 0%, #2c5282 100%);
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s;
        }

        .sidebar .brand {
            padding: 18px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .sidebar .brand-icon {
            width: 38px;
            height: 38px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 18px;
        }

        .sidebar .brand-text {
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.2;
        }

        .sidebar .brand-text small {
            font-size: 11px;
            font-weight: 400;
            opacity: 0.7;
            display: block;
        }

        .sidebar .nav-section {
            padding: 15px 20px 8px;
            font-size: 11px;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.4);
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .sidebar .nav-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar .nav-menu li a {
            display: flex;
            align-items: center;
            padding: 11px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.2s;
            gap: 12px;
            font-size: 14px;
        }

        .sidebar .nav-menu li a:hover,
        .sidebar .nav-menu li a.active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        .sidebar .nav-menu li a.active {
            border-left: 3px solid #63b3ed;
        }

        .sidebar .nav-menu li a .app-icon {
            width: 20px;
            text-align: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        /* Navbar */
        .main-navbar {
            position: fixed;
            top: 0;
            left: 250px;
            right: 0;
            height: 58px;
            background: #fff;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            z-index: 999;
            display: flex;
            align-items: center;
            padding: 0 24px;
            justify-content: space-between;
        }

        .main-navbar .navbar-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .main-navbar .toggle-btn {
            background: none;
            border: none;
            font-size: 18px;
            color: #4a5568;
            cursor: pointer;
            padding: 5px;
            display: none;
        }

        .main-navbar .page-title {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
        }

        .main-navbar .navbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .main-navbar .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #4a5568;
            font-size: 13px;
        }

        .main-navbar .user-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #3182ce;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        /* Content */
        .main-content {
            margin-left: 250px;
            margin-top: 58px;
            padding: 24px;
            min-height: calc(100vh - 58px);
        }

        /* Breadcrumb */
        .breadcrumb-wrapper {
            margin-bottom: 20px;
        }

        .breadcrumb-wrapper .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 0;
            font-size: 13px;
        }

        /* Card */
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.06);
        }

        .card-header {
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 16px 20px;
            border-radius: 8px 8px 0 0 !important;
        }

        .card-header h3 {
            font-size: 16px;
            font-weight: 600;
            color: #2d3748;
            margin: 0;
        }

        .card-body {
            padding: 20px;
        }

        /* Table */
        .table thead th {
            background: #edf2f7;
            border-bottom: 2px solid #cbd5e0;
            color: #4a5568;
            font-weight: 600;
            font-size: 13px;
            white-space: nowrap;
            padding: 10px 12px;
        }

        .table tbody td {
            vertical-align: middle;
            padding: 10px 12px;
            font-size: 13px;
            color: #4a5568;
        }

        .table tbody tr:hover {
            background-color: #f7fafc;
        }

        /* Buttons */
        .btn-primary {
            background-color: #3182ce;
            border-color: #3182ce;
        }

        .btn-primary:hover {
            background-color: #2c5282;
            border-color: #2c5282;
        }

        .btn-info {
            background-color: #63b3ed;
            border-color: #63b3ed;
            color: #fff;
        }

        .btn-info:hover {
            background-color: #4299e1;
            border-color: #4299e1;
            color: #fff;
        }

        /* Badge */
        .badge-laki {
            background-color: #3182ce;
            color: #fff;
        }

        .badge-perempuan {
            background-color: #d53f8c;
            color: #fff;
        }

        /* Form */
        .form-control:focus {
            border-color: #63b3ed;
            box-shadow: 0 0 0 0.15rem rgba(66, 153, 225, 0.25);
        }

        .form-group label {
            font-weight: 600;
            color: #4a5568;
            font-size: 13px;
            margin-bottom: 6px;
        }

        /* Footer */
        .main-footer {
            margin-left: 250px;
            padding: 15px 24px;
            background: #fff;
            border-top: 1px solid #e2e8f0;
            font-size: 13px;
            color: #718096;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.active {
                transform: translateX(0);
            }

            .main-content,
            .main-navbar,
            .main-footer {
                margin-left: 0;
                left: 0;
            }

            .main-navbar .toggle-btn {
                display: block;
            }
        }
    </style>
</head>

<body>

    <?php $role = $this->session->userdata('role'); ?>

    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-icon">
                <iconify-icon icon="mdi:office-building-outline" class="app-icon"></iconify-icon>
            </div>
            <div class="brand-text">
                SIMPEG
                <small>DPMPTSPD Kota Tomohon</small>
            </div>
        </div>

        <div class="nav-section">Menu Utama</div>
        <ul class="nav-menu">
            <li>
                <a href="<?= site_url('pegawai') ?>" class="<?= ($this->uri->segment(1) == 'pegawai' || $this->uri->segment(1) == '') ? 'active' : '' ?>">
                    <iconify-icon icon="mdi:account-group-outline" class="app-icon"></iconify-icon>
                    <span><?= ($role === 'kasubag') ? 'Pegawai' : 'Data Pegawai' ?></span>
                </a>
            </li>
            <?php if ($role === 'kasubag'): ?>
            <li>
                <a href="<?= site_url('persetujuan_pegawai') ?>" class="<?= ($this->uri->segment(1) == 'persetujuan_pegawai') ? 'active' : '' ?>">
                    <iconify-icon icon="mdi:clipboard-check-outline" class="app-icon"></iconify-icon>
                    <span>Persetujuan Data Pegawai</span>
                </a>
            </li>
            <?php else: ?>
            <li>
                <a href="<?= site_url('surat') ?>" class="<?= ($this->uri->segment(1) == 'surat') ? 'active' : '' ?>">
                    <iconify-icon icon="mdi:email-open-outline" class="app-icon"></iconify-icon>
                    <span>Surat</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>

        <div class="nav-section">Pengaturan</div>
        <ul class="nav-menu">
            <li>
                <a href="<?= site_url('auth/logout') ?>">
                    <iconify-icon icon="mdi:logout" class="app-icon"></iconify-icon>
                    <span>Keluar</span>
                </a>
            </li>
        </ul>
    </aside>

    <!-- Navbar -->
    <nav class="main-navbar">
        <div class="navbar-left">
            <button class="toggle-btn" onclick="document.getElementById('sidebar').classList.toggle('active')">
                <iconify-icon icon="mdi:menu" class="app-icon"></iconify-icon>
            </button>
            <h1 class="page-title"><?= $title ?></h1>
        </div>
        <div class="navbar-right">
            <div class="user-info">
                <span><?= $this->session->userdata('nama') ?: 'Petugas' ?></span>
                <div class="user-avatar"><?= strtoupper(substr($this->session->userdata('nama') ?: 'Petugas', 0, 1)) ?></div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="main-content">
