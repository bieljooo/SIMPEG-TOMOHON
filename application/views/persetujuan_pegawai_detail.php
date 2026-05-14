<!-- Breadcrumb -->
<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('pegawai') ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('persetujuan_pegawai') ?>">Persetujuan Data Pegawai</a></li>
        <li class="breadcrumb-item active">Detail Pengajuan Pegawai</li>
    </ol>
</nav>

<?php $p = $pegawai; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3><iconify-icon icon="mdi:account-outline" class="mr-2"></iconify-icon>Detail Pengajuan Pegawai</h3>
        <div>
            <a href="<?= site_url('persetujuan_pegawai') ?>" class="btn btn-cancel-action btn-sm">
                <iconify-icon icon="mdi:arrow-left" class="mr-1"></iconify-icon> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">

        <div class="d-flex align-items-center mb-4 p-3" style="background:#f7fafc;border-radius:8px;border-left:4px solid #3182ce">
            <div style="width:60px;height:60px;border-radius:50%;background:#3182ce;color:#fff;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;margin-right:16px">
                <?= strtoupper(substr($p->nama, 0, 1)) ?>
            </div>
            <div>
                <h4 style="margin:0;color:#2d3748;font-weight:700"><?= $p->nama ?></h4>
                <span style="color:#718096;font-size:14px">NIP: <?= $p->nip ?></span>
                <span class="ml-3 gender-label" style="font-size:12px">
                    <?= ($p->jenis_kelamin == 'L') ? 'Laki-laki' : 'Perempuan' ?>
                </span>
                <span class="ml-2" style="color:#718096;font-size:12px">
                    Jenis: <?= (!empty($p->jenis) && strtolower((string) $p->jenis) === 'update') ? 'Update Data' : 'Data Baru' ?>
                </span>
            </div>
        </div>

        <div class="mb-3">
            <h5 style="color:#3182ce;font-weight:700;border-bottom:2px solid #ebf4ff;padding-bottom:8px">
                <iconify-icon icon="mdi:account-outline" class="mr-2"></iconify-icon>Data Pribadi
            </h5>
        </div>
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td style="width:170px;color:#a0aec0;font-weight:600">Nama Lengkap</td>
                        <td style="color:#2d3748"><?= $p->nama ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Tempat, Tgl. Lahir</td>
                        <td style="color:#2d3748"><?= $p->tempat_lahir ?>, <?= $p->tanggal_lahir ? date('d F Y', strtotime($p->tanggal_lahir)) : '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Jenis Kelamin</td>
                        <td style="color:#2d3748"><?= ($p->jenis_kelamin == 'L') ? 'Laki-laki' : 'Perempuan' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Status Kawin</td>
                        <td style="color:#2d3748"><?= $p->status_kawin ?: '-' ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td style="width:170px;color:#a0aec0;font-weight:600">Agama</td>
                        <td style="color:#2d3748"><?= $p->agama ?: '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">No. Telepon</td>
                        <td style="color:#2d3748"><?= $p->no_telp ?: '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Alamat</td>
                        <td style="color:#2d3748"><?= $p->alamat ?: '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="mb-3">
            <h5 style="color:#3182ce;font-weight:700;border-bottom:2px solid #ebf4ff;padding-bottom:8px">
                <iconify-icon icon="mdi:card-account-details-outline" class="mr-2"></iconify-icon>Data Kepegawaian
            </h5>
        </div>
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td style="width:170px;color:#a0aec0;font-weight:600">NIP</td>
                        <td style="color:#2d3748"><code><?= $p->nip ?></code></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Gol. Ruang CPNS</td>
                        <td style="color:#2d3748"><?= $p->gol_ruang_cpns ?: '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">TMT CPNS</td>
                        <td style="color:#2d3748"><?= $p->tmt_cpns ? date('d F Y', strtotime($p->tmt_cpns)) : '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Pangkat Terakhir</td>
                        <td style="color:#2d3748"><?= $p->pangkat_terakhir ?: '-' ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td style="width:170px;color:#a0aec0;font-weight:600">Jabatan</td>
                        <td style="color:#2d3748"><?= $p->jabatan ?: '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Eselon</td>
                        <td style="color:#2d3748"><?= $p->eselon ?: '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Diklat Penjenjangan</td>
                        <td style="color:#2d3748"><?= $p->diklat_penjenjangan ?: '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Instansi Pembayar</td>
                        <td style="color:#2d3748"><?= $p->instansi_pembayar ?: '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="mb-3">
            <h5 style="color:#3182ce;font-weight:700;border-bottom:2px solid #ebf4ff;padding-bottom:8px">
                <iconify-icon icon="mdi:school-outline" class="mr-2"></iconify-icon>Data Pendidikan
            </h5>
        </div>
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td style="width:170px;color:#a0aec0;font-weight:600">Tingkat Pendidikan</td>
                        <td style="color:#2d3748"><?= $p->tingkat_pendidikan ?: '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Jurusan</td>
                        <td style="color:#2d3748"><?= $p->jurusan ?: '-' ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td style="width:170px;color:#a0aec0;font-weight:600">Tahun Lulus</td>
                        <td style="color:#2d3748"><?= $p->tahun_lulus ?: '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Alumni</td>
                        <td style="color:#2d3748"><?= $p->alumni ?: '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <?php if ($p->keterangan): ?>
        <div class="mb-3">
            <h5 style="color:#3182ce;font-weight:700;border-bottom:2px solid #ebf4ff;padding-bottom:8px">
                <iconify-icon icon="mdi:note-text-outline" class="mr-2"></iconify-icon>Keterangan
            </h5>
        </div>
        <p style="color:#4a5568"><?= $p->keterangan ?></p>
        <?php endif; ?>

    </div>
</div>
