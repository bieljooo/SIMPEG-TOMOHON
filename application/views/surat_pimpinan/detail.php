<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= ($current_role === 'kadis') ? site_url('surat_pimpinan/validasi_surat') : site_url('surat_pimpinan/verifikasi_surat') ?>"><?= ($current_role === 'kadis') ? 'Validasi Surat' : 'Verifikasi Surat' ?></a></li>
        <li class="breadcrumb-item active">Detail Surat</li>
    </ol>
</nav>

<?php $s = $surat; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3><iconify-icon icon="mdi:file-document-outline" class="mr-2"></iconify-icon>Detail Surat</h3>
        <a href="<?= ($current_role === 'kadis') ? site_url('surat_pimpinan/validasi_surat') : site_url('surat_pimpinan/verifikasi_surat') ?>" class="btn btn-cancel-action btn-sm">
            <iconify-icon icon="mdi:arrow-left" class="mr-1"></iconify-icon> Kembali
        </a>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center mb-4 p-3" style="background:#f7fafc;border-radius:8px;border-left:4px solid #3182ce">
            <div style="width:60px;height:60px;border-radius:50%;background:#3182ce;color:#fff;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;margin-right:16px">
                <?= strtoupper(substr($s->nama, 0, 1)) ?>
            </div>
            <div>
                <h4 style="margin:0;color:#2d3748;font-weight:700"><?= $s->nama ?></h4>
                <span style="color:#718096;font-size:14px">NIP: <?= $s->nip ?></span>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td style="width:180px;color:#a0aec0;font-weight:600">Jenis Surat</td>
                        <td style="color:#2d3748"><?= $jenis_surat_label ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Pangkat</td>
                        <td style="color:#2d3748"><?= $s->pangkat ?: '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Jabatan</td>
                        <td style="color:#2d3748"><?= $s->jabatan ?: '-' ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td style="width:180px;color:#a0aec0;font-weight:600">Tanggal Pengajuan</td>
                        <td style="color:#2d3748"><?= $s->created_at ? date('d/m/Y H:i', strtotime($s->created_at)) : '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Nomor Surat</td>
                        <td style="color:#2d3748"><?= !empty($s->nomor_surat) ? htmlspecialchars($s->nomor_surat, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Sifat</td>
                        <td style="color:#2d3748"><?= !empty($s->sifat) ? htmlspecialchars($s->sifat, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Lampiran</td>
                        <td style="color:#2d3748"><?= ($s->lampiran !== NULL && $s->lampiran !== '') ? htmlspecialchars($s->lampiran, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Perihal</td>
                        <td style="color:#2d3748"><?= !empty($s->perihal) ? htmlspecialchars($s->perihal, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                    </tr>
                    <?php if (in_array($s->jenis_surat, array('usulan_cuti_tahun', 'cuti_alasan_penting', 'usulan_alasan_penting'), TRUE)): ?>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Mulai Tanggal</td>
                        <td style="color:#2d3748"><?= !empty($s->tanggal_mulai) ? date('d/m/Y', strtotime($s->tanggal_mulai)) : '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Sampai Dengan</td>
                        <td style="color:#2d3748"><?= !empty($s->tanggal_selesai) ? date('d/m/Y', strtotime($s->tanggal_selesai)) : '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Keterangan</td>
                        <td style="color:#2d3748"><?= !empty($s->keterangan) ? htmlspecialchars($s->keterangan, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($s->jenis_surat === 'usulan_cuti_luar_negeri'): ?>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Negara Tujuan</td>
                        <td style="color:#2d3748"><?= !empty($s->negara_tujuan) ? htmlspecialchars($s->negara_tujuan, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Mulai Tanggal</td>
                        <td style="color:#2d3748"><?= !empty($s->tanggal_mulai) ? date('d/m/Y', strtotime($s->tanggal_mulai)) : '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Sampai Dengan</td>
                        <td style="color:#2d3748"><?= !empty($s->tanggal_selesai) ? date('d/m/Y', strtotime($s->tanggal_selesai)) : '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Keterangan</td>
                        <td style="color:#2d3748"><?= !empty($s->keterangan) ? htmlspecialchars($s->keterangan, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Status</td>
                        <td style="color:#2d3748"><?= $status_label ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="table-action-group">
            <form id="approve-form-<?= $s->id ?>" action="<?= site_url('surat_pimpinan/setujui/' . $s->id) ?>" method="POST" style="display:inline">
                <button type="button" onclick="confirmApproval('approve-form-<?= $s->id ?>', '<?= addslashes($s->nama) ?>')" class="btn btn-action-approve btn-sm">
                    <iconify-icon icon="mdi:check"></iconify-icon>
                    <span><?= ($current_role === 'kadis') ? 'Validasi' : 'Setujui' ?></span>
                </button>
            </form>
            <form id="reject-form-<?= $s->id ?>" action="<?= site_url('surat_pimpinan/tolak/' . $s->id) ?>" method="POST" style="display:inline">
                <button type="button" onclick="confirmRejection('reject-form-<?= $s->id ?>', '<?= addslashes($s->nama) ?>')" class="btn btn-danger btn-sm">
                    <iconify-icon icon="mdi:close-thick"></iconify-icon>
                    <span>Tolak</span>
                </button>
            </form>
        </div>
    </div>
</div>
