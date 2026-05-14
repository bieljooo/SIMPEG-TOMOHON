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
    $display_role = ($role === 'kadis')
        ? 'Kadis'
        : (($role === 'sek')
            ? 'Sek'
            : (($role === 'kasubag') ? 'Kasubag' : 'Pegawai'));
    $display_name = $this->session->userdata('nama') ?: 'Pegawai';
    $profile_photo = $this->session->userdata('foto_profil');
    $profile_position = $this->session->userdata('foto_posisi') ?: 'center center';
    $pengajuan_segment = $this->uri->segment(1) === 'pengajuan_surat' ? $this->uri->segment(2) : '';
    $pengajuan_view_source = strtolower(trim((string) $this->input->get('source', TRUE)));
    $is_validasi_surat = ($this->uri->segment(1) === 'surat_pimpinan' && $this->uri->segment(2) === 'validasi_surat');
    $is_verifikasi_surat = ($this->uri->segment(1) === 'surat_pimpinan' && $this->uri->segment(2) === 'verifikasi_surat');
    $is_kasubag_pegawai = ($this->uri->segment(1) === 'pegawai');
    $is_persetujuan_pegawai = ($this->uri->segment(1) === 'persetujuan_pegawai');
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
        'view_surat_rekomendasi',
    );
    $is_surat_sakit_menu = ($this->uri->segment(1) === 'pengajuan_surat' && in_array($pengajuan_segment, $surat_sakit_segments, TRUE));
    $is_surat_sakit_form = ($this->uri->segment(1) === 'pengajuan_surat' && ($pengajuan_segment === '' || $pengajuan_segment === 'surat_keterangan_sakit'));
    $is_surat_sakit_download = ($this->uri->segment(1) === 'pengajuan_surat' && in_array($pengajuan_segment, array('download_surat', 'download_surat_sakit'), TRUE));
    $is_rekomendasi_menu = ($this->uri->segment(1) === 'pengajuan_surat' && in_array($pengajuan_segment, $surat_rekomendasi_segments, TRUE));
    $is_rekomendasi_view = ($this->uri->segment(1) === 'pengajuan_surat' && $pengajuan_segment === 'view_surat_rekomendasi');
    $is_rekomendasi_proses = ($pengajuan_segment === 'proses_surat') || ($is_rekomendasi_view && $pengajuan_view_source !== 'download');
    $is_rekomendasi_download = ($pengajuan_segment === 'download_surat_rekomendasi') || ($is_rekomendasi_view && $pengajuan_view_source === 'download');
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
                    <li data-menu-search="data diri profil pegawai">
                        <a href="<?= site_url('dashboard') ?>" class="<?= ($this->uri->segment(1) == 'dashboard' && $this->uri->segment(2) == '') ? 'active' : '' ?>">
                            <iconify-icon icon="mdi:account-outline" class="app-icon"></iconify-icon>
                            <span>Data Diri</span>
                        </a>
                    </li>
                    <?php if (in_array($role, array('pegawai', 'kasubag', 'sek'), TRUE)): ?>
                    <li data-menu-search="surat sakit buat surat download surat sakit">
                        <a
                            class="nav-dropdown-toggle"
                            data-toggle="collapse"
                            href="#menuSuratSakit"
                            role="button"
                            aria-expanded="false"
                            aria-controls="menuSuratSakit">
                            <iconify-icon icon="mdi:file-document-plus-outline" class="app-icon"></iconify-icon>
                            <span>Surat Sakit</span>
                            <iconify-icon icon="mdi:chevron-down" class="menu-caret"></iconify-icon>
                        </a>
                        <div class="collapse" id="menuSuratSakit">
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
                            href="#menuSuratRekomendasi"
                            role="button"
                            aria-expanded="false"
                            aria-controls="menuSuratRekomendasi">
                            <iconify-icon icon="mdi:file-document-multiple-outline" class="app-icon"></iconify-icon>
                            <span>Surat Rekomendasi</span>
                            <iconify-icon icon="mdi:chevron-down" class="menu-caret"></iconify-icon>
                        </a>
                        <div class="collapse" id="menuSuratRekomendasi">
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
                                    <a href="<?= site_url('pengajuan_surat/proses_surat') ?>" class="<?= $is_rekomendasi_proses ? 'active' : '' ?>">
                                        <span>Proses Surat</span>
                                    </a>
                                </li>
                                <li data-menu-search="download surat rekomendasi">
                                    <a href="<?= site_url('pengajuan_surat/download_surat_rekomendasi') ?>" class="<?= $is_rekomendasi_download ? 'active' : '' ?>">
                                        <iconify-icon icon="mdi:download-outline" class="app-icon"></iconify-icon>
                                        <span>Download</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <?php if ($role === 'kasubag'): ?>
                    <li data-menu-search="pegawai data pegawai">
                        <a href="<?= site_url('pegawai') ?>" class="<?= $is_kasubag_pegawai ? 'active' : '' ?>">
                            <iconify-icon icon="mdi:account-group-outline" class="app-icon"></iconify-icon>
                            <span>Pegawai</span>
                        </a>
                    </li>
                    <li data-menu-search="persetujuan data pegawai">
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
                    <?php if ($role === 'sek'): ?>
                    <li data-menu-search="verifikasi surat">
                        <a href="<?= site_url('surat_pimpinan/verifikasi_surat') ?>" class="<?= $is_verifikasi_surat ? 'active' : '' ?>">
                            <iconify-icon icon="mdi:file-check-outline" class="app-icon"></iconify-icon>
                            <span>Verifikasi Surat</span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php elseif ($role === 'kadis'): ?>
                    <li data-menu-search="validasi surat">
                        <a href="<?= site_url('surat_pimpinan/validasi_surat') ?>" class="<?= $is_validasi_surat ? 'active' : '' ?>">
                            <iconify-icon icon="mdi:clipboard-check-outline" class="app-icon"></iconify-icon>
                            <span>Validasi Surat</span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="sidebar-footer">
                <a href="<?= site_url('settings') ?>" class="sidebar-utility-link<?= ($this->uri->segment(1) == 'settings' || ($this->uri->segment(1) == 'dashboard' && $this->uri->segment(2) == 'settings')) ? ' active' : '' ?>">
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
                <span class="page-kicker">Workspace <?= $display_role ?></span>
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
