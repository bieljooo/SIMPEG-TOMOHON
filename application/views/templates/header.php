<!DOCTYPE html>
<html lang="id">

<?php $shell_css_version = @filemtime(FCPATH . 'assets/css/simpeg-shell.css') ?: time(); ?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?> | SIMPEG DPMPTSPD</title>

    <script>
        (function () {
            try {
                if (localStorage.getItem('simpeg-theme') === 'dark') {
                    document.documentElement.classList.add('theme-dark');
                }
            } catch (error) {}
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/simpeg-shell.css?v=' . $shell_css_version) ?>">
    <script src="https://code.iconify.design/iconify-icon/2.2.0/iconify-icon.min.js"></script>
</head>

<body>

    <?php
    $role = $this->session->userdata('role');
    $display_role = ($role === 'kasubag')
        ? 'Kasubag'
        : (($role === 'admin')
            ? 'Admin'
            : (($role === 'kadis')
                ? 'Kadis'
                : (($role === 'sek') ? 'Sek' : 'Petugas')));
    $display_name = $this->session->userdata('nama') ?: $display_role;
    $profile_photo = $this->session->userdata('foto_profil');
    $profile_position = $this->session->userdata('foto_posisi') ?: 'center center';
    $is_petugas_dashboard = ($this->uri->segment(1) === 'dashboard_petugas');
    $is_draft_verifikasi = ($this->uri->segment(1) === 'pegawai' && $this->uri->segment(2) === 'draft_verifikasi');
    $is_akun_pegawai = ($this->uri->segment(1) === 'pegawai' && $this->uri->segment(2) === 'akun_pegawai');
    $is_pegawai_data = (($this->uri->segment(1) === 'pegawai' || $this->uri->segment(1) === '') && !in_array($this->uri->segment(2), array('draft_verifikasi', 'akun_pegawai'), TRUE) && !$is_petugas_dashboard);
    $is_petugas_pegawai_group = ($role === 'petugas' && ($is_pegawai_data || $is_draft_verifikasi || $is_akun_pegawai));
    $master_surat_segments = array('surat', 'master_surat');
    $is_master_surat_group = ($role === 'petugas' && in_array($this->uri->segment(1), $master_surat_segments, TRUE));
    $is_surat_masuk = ($this->uri->segment(1) === 'surat');
    $is_template_surat = ($this->uri->segment(1) === 'master_surat');
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
        'cuti_luar_negeri',
        'kenaikan_gaji_berkala',
        'proses_surat',
        'download_surat_rekomendasi',
    );
    $is_surat_sakit_menu = ($this->uri->segment(1) === 'pengajuan_surat' && in_array($pengajuan_segment, $surat_sakit_segments, TRUE));
    $is_surat_sakit_form = ($this->uri->segment(1) === 'pengajuan_surat' && ($pengajuan_segment === '' || $pengajuan_segment === 'surat_keterangan_sakit'));
    $is_surat_sakit_download = ($this->uri->segment(1) === 'pengajuan_surat' && in_array($pengajuan_segment, array('download_surat', 'download_surat_sakit'), TRUE));
    $is_rekomendasi_menu = ($this->uri->segment(1) === 'pengajuan_surat' && in_array($pengajuan_segment, $surat_rekomendasi_segments, TRUE));
    $is_kasubag_pegawai = ($this->uri->segment(1) === 'pegawai');
    $is_persetujuan_pegawai = ($this->uri->segment(1) === 'persetujuan_pegawai');
    $is_verifikasi_surat = ($this->uri->segment(1) === 'surat_pimpinan' && $this->uri->segment(2) === 'verifikasi_surat');
    ?>

    <div class="app-overlay" data-sidebar-overlay></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-shell">
            <div class="brand">
                <div class="brand-logo-box">
                    <img src="<?= base_url('assets/images/logo-tomohon.png') ?>" alt="Logo Kota Tomohon" class="brand-logo">
                </div>
                <div class="brand-copy">
                    <h1 class="brand-title">SIMPEG</h1>
                    <span class="brand-subtitle">DPMPTSPD Kota Tomohon</span>
                </div>
            </div>

            <div class="sidebar-scroll">
                <div class="nav-section">Menu Utama</div>
                <ul class="nav-menu">
                    <?php if ($role === 'petugas'): ?>
                    <li data-menu-search="dashboard ringkasan petugas" data-page-link-group>
                        <a href="<?= site_url('dashboard_petugas') ?>" class="<?= $is_petugas_dashboard ? 'active' : '' ?>">
                            <iconify-icon icon="mdi:view-dashboard-outline" class="app-icon"></iconify-icon>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if ($role === 'petugas'): ?>
                    <li data-menu-search="master data pegawai data pegawai akun pegawai draft verifikasi">
                        <a
                            class="nav-dropdown-toggle"
                            data-toggle="collapse"
                            href="#menuPetugasPegawai"
                            role="button"
                            aria-expanded="false"
                            aria-controls="menuPetugasPegawai">
                            <iconify-icon icon="mdi:account-group-outline" class="app-icon"></iconify-icon>
                            <span>Master Data</span>
                            <iconify-icon icon="mdi:chevron-down" class="menu-caret"></iconify-icon>
                        </a>
                        <div class="collapse" id="menuPetugasPegawai">
                            <ul class="nav-submenu">
                                <li data-menu-search="data pegawai utama">
                                    <a href="<?= site_url('pegawai') ?>" class="<?= $is_pegawai_data ? 'active' : '' ?>">
                                        <iconify-icon icon="mdi:account-multiple-outline" class="app-icon"></iconify-icon>
                                        <span>Data Pegawai</span>
                                    </a>
                                </li>
                                <li data-menu-search="akun pegawai">
                                    <a href="<?= site_url('pegawai/akun_pegawai') ?>" class="<?= $is_akun_pegawai ? 'active' : '' ?>">
                                        <iconify-icon icon="mdi:account-key-outline" class="app-icon"></iconify-icon>
                                        <span>Akun Pegawai</span>
                                    </a>
                                </li>
                                <li data-menu-search="draft verifikasi">
                                    <a href="<?= site_url('pegawai/draft_verifikasi') ?>" class="<?= $is_draft_verifikasi ? 'active' : '' ?>">
                                        <iconify-icon icon="mdi:file-document-check-outline" class="app-icon"></iconify-icon>
                                        <span>Draft Verifikasi</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <?php elseif ($role === 'kasubag'): ?>
                    <li data-menu-search="data diri profil pegawai">
                        <a href="<?= site_url('dashboard') ?>" class="<?= ($this->uri->segment(1) === 'dashboard') ? 'active' : '' ?>">
                            <iconify-icon icon="mdi:account-outline" class="app-icon"></iconify-icon>
                            <span>Data Diri</span>
                        </a>
                    </li>
                    <li data-menu-search="surat sakit buat surat download surat sakit">
                        <a
                            class="nav-dropdown-toggle"
                            data-toggle="collapse"
                            href="#menuKasubagSuratSakit"
                            role="button"
                            aria-expanded="false"
                            aria-controls="menuKasubagSuratSakit">
                            <iconify-icon icon="mdi:file-document-plus-outline" class="app-icon"></iconify-icon>
                            <span>Surat Sakit</span>
                            <iconify-icon icon="mdi:chevron-down" class="menu-caret"></iconify-icon>
                        </a>
                        <div class="collapse" id="menuKasubagSuratSakit">
                            <ul class="nav-submenu">
                                <li data-menu-search="buat surat surat sakit form">
                                    <a href="<?= site_url('pengajuan_surat/surat_keterangan_sakit') ?>" class="<?= $is_surat_sakit_form ? 'active' : '' ?>">
                                        <iconify-icon icon="mdi:file-document-edit-outline" class="app-icon"></iconify-icon>
                                        <span>Buat Surat</span>
                                    </a>
                                </li>
                                <li data-menu-search="download surat sakit word">
                                    <a href="<?= site_url('pengajuan_surat/download_surat_sakit') ?>" class="<?= $is_surat_sakit_download ? 'active' : '' ?>">
                                        <iconify-icon icon="mdi:download-outline" class="app-icon"></iconify-icon>
                                        <span>Download</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li data-menu-search="surat rekomendasi usulan kenaikan pangkat usulan cuti tahunan usulan cuti alasan penting usulan cuti luar negeri usulan kenaikan gaji berkala proses surat download">
                        <a
                            class="nav-dropdown-toggle"
                            data-toggle="collapse"
                            href="#menuKasubagSuratRekomendasi"
                            role="button"
                            aria-expanded="false"
                            aria-controls="menuKasubagSuratRekomendasi">
                            <iconify-icon icon="mdi:file-document-multiple-outline" class="app-icon"></iconify-icon>
                            <span>Surat Rekomendasi</span>
                            <iconify-icon icon="mdi:chevron-down" class="menu-caret"></iconify-icon>
                        </a>
                        <div class="collapse" id="menuKasubagSuratRekomendasi">
                            <ul class="nav-submenu">
                                <li data-menu-search="usulan kenaikan pangkat">
                                    <a href="<?= site_url('pengajuan_surat/cuti_kenaikan_pangkat') ?>" class="<?= ($pengajuan_segment === 'cuti_kenaikan_pangkat') ? 'active' : '' ?>">
                                        <span>Usulan Kenaikan Pangkat</span>
                                    </a>
                                </li>
                                <li data-menu-search="usulan cuti tahunan">
                                    <a href="<?= site_url('pengajuan_surat/pengajuan_cuti_tahun') ?>" class="<?= ($pengajuan_segment === 'pengajuan_cuti_tahun') ? 'active' : '' ?>">
                                        <span>Usulan Cuti Tahunan</span>
                                    </a>
                                </li>
                                <li data-menu-search="usulan cuti alasan penting">
                                    <a href="<?= site_url('pengajuan_surat/cuti_alasan_penting') ?>" class="<?= ($pengajuan_segment === 'cuti_alasan_penting') ? 'active' : '' ?>">
                                        <span>Usulan Cuti Alasan Penting</span>
                                    </a>
                                </li>
                                <li data-menu-search="usulan cuti luar negeri">
                                    <a href="<?= site_url('pengajuan_surat/cuti_luar_negeri') ?>" class="<?= ($pengajuan_segment === 'cuti_luar_negeri') ? 'active' : '' ?>">
                                        <span>Usulan Cuti Luar Negeri</span>
                                    </a>
                                </li>
                                <li data-menu-search="usulan kenaikan gaji berkala">
                                    <a href="<?= site_url('pengajuan_surat/kenaikan_gaji_berkala') ?>" class="<?= ($pengajuan_segment === 'kenaikan_gaji_berkala') ? 'active' : '' ?>">
                                        <span>Usulan Kenaikan Gaji Berkala</span>
                                    </a>
                                </li>
                                <li data-menu-search="proses surat rekomendasi">
                                    <a href="<?= site_url('pengajuan_surat/proses_surat') ?>" class="<?= ($pengajuan_segment === 'proses_surat') ? 'active' : '' ?>">
                                        <span>Proses Surat</span>
                                    </a>
                                </li>
                                <li data-menu-search="download surat rekomendasi">
                                    <a href="<?= site_url('pengajuan_surat/download_surat_rekomendasi') ?>" class="<?= ($pengajuan_segment === 'download_surat_rekomendasi') ? 'active' : '' ?>">
                                        <iconify-icon icon="mdi:download-outline" class="app-icon"></iconify-icon>
                                        <span>Download</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li data-menu-search="pegawai data pegawai">
                        <a href="<?= site_url('pegawai') ?>" class="<?= $is_kasubag_pegawai ? 'active' : '' ?>">
                            <iconify-icon icon="mdi:account-group-outline" class="app-icon"></iconify-icon>
                            <span>Pegawai</span>
                        </a>
                    </li>
                    <li data-menu-search="persetujuan data pegawai approval approve">
                        <a href="<?= site_url('persetujuan_pegawai') ?>" class="<?= $is_persetujuan_pegawai ? 'active' : '' ?>">
                            <iconify-icon icon="mdi:clipboard-check-outline" class="app-icon"></iconify-icon>
                            <span>Persetujuan Data Pegawai</span>
                        </a>
                    </li>
                    <li data-menu-search="verifikasi surat">
                        <a href="<?= site_url('surat_pimpinan/verifikasi_surat') ?>" class="<?= $is_verifikasi_surat ? 'active' : '' ?>">
                            <iconify-icon icon="mdi:file-check-outline" class="app-icon"></iconify-icon>
                            <span>Verifikasi Surat</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if ($role === 'petugas'): ?>
                    <li data-menu-search="master surat surat masuk template surat">
                        <a
                            class="nav-dropdown-toggle"
                            data-toggle="collapse"
                            href="#menuMasterSurat"
                            role="button"
                            aria-expanded="false"
                            aria-controls="menuMasterSurat">
                            <iconify-icon icon="mdi:folder-file-outline" class="app-icon"></iconify-icon>
                            <span>Master Surat</span>
                            <iconify-icon icon="mdi:chevron-down" class="menu-caret"></iconify-icon>
                        </a>
                        <div class="collapse" id="menuMasterSurat">
                            <ul class="nav-submenu">
                                <li data-menu-search="surat masuk">
                                    <a href="<?= site_url('surat') ?>" class="<?= $is_surat_masuk ? 'active' : '' ?>">
                                        <iconify-icon icon="mdi:email-open-outline" class="app-icon"></iconify-icon>
                                        <span>Surat Masuk</span>
                                    </a>
                                </li>
                                <li data-menu-search="template surat usulan kenaikan pangkat usulan cuti tahunan usulan cuti alasan penting usulan kenaikan gaji berkala">
                                    <a href="<?= site_url('master_surat/template_surat') ?>" class="<?= $is_template_surat ? 'active' : '' ?>">
                                        <iconify-icon icon="mdi:file-document-multiple-outline" class="app-icon"></iconify-icon>
                                        <span>Template Surat</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <?php elseif ($role !== 'kasubag'): ?>
                    <li data-menu-search="surat masuk nomor surat">
                        <a href="<?= site_url('surat') ?>" class="<?= ($this->uri->segment(1) == 'surat') ? 'active' : '' ?>">
                            <iconify-icon icon="mdi:email-open-outline" class="app-icon"></iconify-icon>
                            <span>Surat</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="sidebar-footer">
                <a href="<?= site_url('settings') ?>" class="sidebar-utility-link<?= ($this->uri->segment(1) == 'settings') ? ' active' : '' ?>">
                    <span class="sidebar-utility-label">
                        <iconify-icon icon="mdi:cog-outline"></iconify-icon>
                        <span>Pengaturan</span>
                    </span>
                </a>
                <a href="<?= site_url('auth/logout') ?>" class="sidebar-utility-link" data-logout-link>
                    <span class="sidebar-utility-label">
                        <iconify-icon icon="mdi:logout"></iconify-icon>
                        <span>Keluar</span>
                    </span>
                </a>
                <button type="button" class="theme-toggle" data-theme-toggle aria-pressed="false">
                    <span class="theme-toggle-label">
                        <iconify-icon icon="mdi:weather-night" data-theme-toggle-icon></iconify-icon>
                        <span data-theme-toggle-text>Dark Mode</span>
                    </span>
                    <span class="theme-toggle-switch">
                        <span class="theme-toggle-thumb"></span>
                    </span>
                </button>
            </div>
        </div>
    </aside>

    <nav class="main-navbar">
        <div class="navbar-left">
            <button class="toggle-btn" type="button" data-sidebar-toggle>
                <iconify-icon icon="mdi:menu"></iconify-icon>
            </button>
            <div class="page-heading">
                <span class="page-kicker">Panel Administrasi</span>
                <h1 class="page-title"><?= $title ?></h1>
            </div>
        </div>
        <div class="navbar-right">
            <div class="user-pill">
                <div class="user-pill-copy">
                    <strong><?= $display_name ?></strong>
                    <small><?= $display_role ?></small>
                </div>
                <?php if (!empty($profile_photo)): ?>
                <div class="user-avatar user-avatar-photo" style="background-image:url('<?= base_url($profile_photo) ?>');background-position:<?= htmlspecialchars($profile_position, ENT_QUOTES, 'UTF-8') ?>;"></div>
                <?php else: ?>
                <div class="user-avatar"><?= strtoupper(substr($display_name, 0, 1)) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="main-content">
