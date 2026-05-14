<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
        <li class="breadcrumb-item">Surat Rekomendasi</li>
        <li class="breadcrumb-item active">View</li>
    </ol>
</nav>

<?php $s = $surat; ?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3><iconify-icon icon="mdi:file-document-outline" class="mr-2"></iconify-icon>View Surat Rekomendasi</h3>
        <div class="table-action-group">
            <?php if ($s->status === 'approved'): ?>
                <a href="<?= site_url('pengajuan_surat/unduh_surat_rekomendasi/' . $s->id) ?>" class="btn btn-action-read btn-sm" title="Unduh Word" data-no-transition>
                    <iconify-icon icon="mdi:file-word-outline" class="mr-1"></iconify-icon> Word
                </a>
            <?php endif; ?>
            <a href="<?= $back_url ?>" class="btn btn-cancel-action btn-sm">
                <iconify-icon icon="mdi:arrow-left" class="mr-1"></iconify-icon> Kembali
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center mb-4 p-3" style="background:#f7fafc;border-radius:8px;border-left:4px solid #3182ce">
            <div style="width:60px;height:60px;border-radius:50%;background:#3182ce;color:#fff;display:flex;align-items:center;justify-content:center;font-size:24px;font-weight:700;margin-right:16px">
                <?= strtoupper(substr($s->nama, 0, 1)) ?>
            </div>
            <div>
                <h4 style="margin:0;color:#2d3748;font-weight:700"><?= htmlspecialchars($s->nama, ENT_QUOTES, 'UTF-8') ?></h4>
                <span style="color:#718096;font-size:14px">NIP: <?= htmlspecialchars($s->nip, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td style="width:190px;color:#a0aec0;font-weight:600">Jenis Surat</td>
                        <td style="color:#2d3748"><?= htmlspecialchars($jenis_surat_label, ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Nama</td>
                        <td style="color:#2d3748"><?= htmlspecialchars($s->nama, ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">NIP</td>
                        <td style="color:#2d3748"><?= htmlspecialchars($s->nip, ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Pangkat</td>
                        <td style="color:#2d3748"><?= !empty($s->pangkat) ? htmlspecialchars($s->pangkat, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Jabatan</td>
                        <td style="color:#2d3748"><?= !empty($s->jabatan) ? htmlspecialchars($s->jabatan, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Status</td>
                        <td style="color:#2d3748"><?= htmlspecialchars($status_label, ENT_QUOTES, 'UTF-8') ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td style="width:190px;color:#a0aec0;font-weight:600">Tanggal Pengajuan</td>
                        <td style="color:#2d3748"><?= !empty($s->created_at) ? date('d/m/Y H:i', strtotime($s->created_at)) : '-' ?></td>
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
                    <?php if (in_array($s->jenis_surat, array('usulan_cuti_tahun', 'cuti_alasan_penting', 'usulan_alasan_penting', 'usulan_cuti_luar_negeri'), TRUE)): ?>
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
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</div>
