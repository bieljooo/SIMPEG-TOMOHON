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

        .sidebar .nav-menu li a .menu-caret {
            width: auto;
            margin-left: auto;
            font-size: 16px;
            transition: transform 0.2s;
        }

        .sidebar .nav-dropdown-toggle[aria-expanded="true"] .menu-caret,
        .sidebar .nav-dropdown-toggle.active .menu-caret {
            transform: rotate(180deg);
        }

        .sidebar .nav-submenu {
            list-style: none;
            padding: 0;
            margin: 0;
            background: rgba(0, 0, 0, 0.08);
        }

        .sidebar .nav-submenu li a {
            padding-left: 52px;
            font-size: 13px;
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

        /* Buttons */
        .btn-primary {
            background-color: #3182ce;
            border-color: #3182ce;
        }

        .btn-primary:hover {
            background-color: #2c5282;
            border-color: #2c5282;
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

    <?php
    $pengajuan_segment = $this->uri->segment(1) === 'pengajuan_surat' ? $this->uri->segment(2) : '';
    $surat_sakit_segments = array(
        '',
        'surat_keterangan_sakit',
        'download_surat',
        'download_surat_sakit',
    );
    $surat_rekomendasi_segments = array(
        'cuti_kenaikan_pangkat',
        'pengajuan_cuti_tahun',
        'cuti_alasan_penting',
        'kenaikan_gaji_berkala',
        'download_surat_rekomendasi',
    );
    $is_surat_sakit_menu = ($this->uri->segment(1) === 'pengajuan_surat' && in_array($pengajuan_segment, $surat_sakit_segments, TRUE));
    $is_surat_sakit_form = ($this->uri->segment(1) === 'pengajuan_surat' && ($pengajuan_segment === '' || $pengajuan_segment === 'surat_keterangan_sakit'));
    $is_surat_sakit_download = ($this->uri->segment(1) === 'pengajuan_surat' && in_array($pengajuan_segment, array('download_surat', 'download_surat_sakit'), TRUE));
    $is_rekomendasi_menu = ($this->uri->segment(1) === 'pengajuan_surat' && in_array($pengajuan_segment, $surat_rekomendasi_segments, TRUE));
    ?>

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
                <a href="<?= site_url('dashboard') ?>" class="<?= ($this->uri->segment(1) == 'dashboard' && $this->uri->segment(2) == '') ? 'active' : '' ?>">
                    <iconify-icon icon="mdi:account-outline" class="app-icon"></iconify-icon>
                    <span>Data Diri</span>
                </a>
            </li>
            <li>
                <a
                    class="nav-dropdown-toggle <?= $is_surat_sakit_menu ? 'active' : '' ?>"
                    data-toggle="collapse"
                    href="#menuSuratSakit"
                    role="button"
                    aria-expanded="<?= $is_surat_sakit_menu ? 'true' : 'false' ?>"
                    aria-controls="menuSuratSakit">
                    <iconify-icon icon="mdi:file-document-plus-outline" class="app-icon"></iconify-icon>
                    <span>Surat Sakit</span>
                    <iconify-icon icon="mdi:chevron-down" class="menu-caret"></iconify-icon>
                </a>
                <div class="collapse <?= $is_surat_sakit_menu ? 'show' : '' ?>" id="menuSuratSakit">
                    <ul class="nav-submenu">
                        <li>
                            <a href="<?= site_url('pengajuan_surat/surat_keterangan_sakit') ?>" class="<?= $is_surat_sakit_form ? 'active' : '' ?>">
                                <iconify-icon icon="mdi:file-document-edit-outline" class="app-icon"></iconify-icon>
                                <span>Buat Surat</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= site_url('pengajuan_surat/download_surat_sakit') ?>" class="<?= $is_surat_sakit_download ? 'active' : '' ?>">
                                <iconify-icon icon="mdi:download-outline" class="app-icon"></iconify-icon>
                                <span>Download</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
            <li>
                <a
                    class="nav-dropdown-toggle <?= $is_rekomendasi_menu ? 'active' : '' ?>"
                    data-toggle="collapse"
                    href="#menuSuratRekomendasi"
                    role="button"
                    aria-expanded="<?= $is_rekomendasi_menu ? 'true' : 'false' ?>"
                    aria-controls="menuSuratRekomendasi">
                    <iconify-icon icon="mdi:file-document-multiple-outline" class="app-icon"></iconify-icon>
                    <span>Surat Rekomendasi</span>
                    <iconify-icon icon="mdi:chevron-down" class="menu-caret"></iconify-icon>
                </a>
                <div class="collapse <?= $is_rekomendasi_menu ? 'show' : '' ?>" id="menuSuratRekomendasi">
                    <ul class="nav-submenu">
                        <li>
                            <a href="<?= site_url('pengajuan_surat/cuti_kenaikan_pangkat') ?>" class="<?= ($pengajuan_segment === 'cuti_kenaikan_pangkat') ? 'active' : '' ?>">
                                <span>Usulan Kenaikan Pangkat</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= site_url('pengajuan_surat/pengajuan_cuti_tahun') ?>" class="<?= ($pengajuan_segment === 'pengajuan_cuti_tahun') ? 'active' : '' ?>">
                                <span>Usulan Cuti Tahun</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= site_url('pengajuan_surat/cuti_alasan_penting') ?>" class="<?= ($pengajuan_segment === 'cuti_alasan_penting') ? 'active' : '' ?>">
                                <span>Usulan Alasan Penting</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= site_url('pengajuan_surat/kenaikan_gaji_berkala') ?>" class="<?= ($pengajuan_segment === 'kenaikan_gaji_berkala') ? 'active' : '' ?>">
                                <span>Usulan Kenaikan Gaji Berkala</span>
                            </a>
                        </li>
                        <li>
                            <a href="<?= site_url('pengajuan_surat/download_surat_rekomendasi') ?>" class="<?= ($pengajuan_segment === 'download_surat_rekomendasi') ? 'active' : '' ?>">
                                <iconify-icon icon="mdi:download-outline" class="app-icon"></iconify-icon>
                                <span>Download</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>
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
                <span><?= $this->session->userdata('nama') ?></span>
                <div class="user-avatar"><?= strtoupper(substr($this->session->userdata('nama'), 0, 1)) ?></div>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="main-content">
