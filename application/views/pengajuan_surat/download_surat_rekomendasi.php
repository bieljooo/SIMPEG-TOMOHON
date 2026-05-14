<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
        <li class="breadcrumb-item">Surat Rekomendasi</li>
        <li class="breadcrumb-item active">Download</li>
    </ol>
</nav>

<?php $total_surat = count($surat_rekomendasi); ?>
<?php
$jenis_surat_labels = array(
    'usulan_kenaikan_pangkat' => 'Usulan Kenaikan Pangkat',
    'usulan_kenaikan_gaji_berkala' => 'Usulan Kenaikan Gaji Berkala',
    'usulan_cuti_tahun' => 'Usulan Cuti Tahunan',
    'usulan_alasan_penting' => 'Usulan Cuti Alasan Penting',
    'cuti_alasan_penting' => 'Usulan Cuti Alasan Penting',
    'usulan_cuti_luar_negeri' => 'Usulan Cuti Luar Negeri',
);
?>
<iframe name="suratRekomendasiDownloadFrame" style="display:none" aria-hidden="true"></iframe>

<div class="row mb-4">
    <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <div style="width:45px;height:45px;border-radius:10px;background:#ebf4ff;display:flex;align-items:center;justify-content:center;margin-right:14px">
                    <iconify-icon icon="mdi:file-word-box" style="font-size:20px;color:#3182ce"></iconify-icon>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:#2d3748"><?= $total_surat ?></div>
                    <div style="font-size:12px;color:#a0aec0;font-weight:500">Total Surat Rekomendasi</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card table-page-fit">
    <div class="card-header">
        <h3><iconify-icon icon="mdi:download-outline" class="mr-2"></iconify-icon>Download Surat Rekomendasi</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Jenis Surat</th>
                        <th>Nomor Surat</th>
                        <th>Sifat</th>
                        <th>Lampiran</th>
                        <th>Perihal</th>
                        <th>Validasi Kadis</th>
                        <th>Divalidasi</th>
                        <th style="width:210px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($surat_rekomendasi)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">Belum ada surat rekomendasi yang selesai divalidasi kadis.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($surat_rekomendasi as $item): ?>
                            <tr>
                                <td class="text-center"><?= $no++ ?></td>
                                <td>
                                    <strong><?= isset($jenis_surat_labels[$item->jenis_surat]) ? $jenis_surat_labels[$item->jenis_surat] : ucfirst(str_replace('_', ' ', $item->jenis_surat)) ?></strong>
                                    <br><small class="text-muted"><code><?= htmlspecialchars($item->nip, ENT_QUOTES, 'UTF-8') ?></code></small>
                                </td>
                                <td><?= !empty($item->nomor_surat) ? htmlspecialchars($item->nomor_surat, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                <td><?= !empty($item->sifat) ? htmlspecialchars($item->sifat, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                <td><?= ($item->lampiran !== NULL && $item->lampiran !== '') ? htmlspecialchars($item->lampiran, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                <td><?= !empty($item->perihal) ? htmlspecialchars($item->perihal, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                                <td><?= !empty($item->kadis_nama) ? htmlspecialchars($item->kadis_nama, ENT_QUOTES, 'UTF-8') : 'Kadis' ?></td>
                                <td><?= !empty($item->kadis_at) ? date('d/m/Y H:i', strtotime($item->kadis_at)) : '-' ?></td>
                                <td class="text-center">
                                    <div class="table-action-group">
                                        <a href="<?= site_url('pengajuan_surat/view_surat_rekomendasi/' . $item->id . '?source=download') ?>" class="btn btn-action-read btn-sm" title="View">
                                            <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                                        </a>
                                        <a href="<?= site_url('pengajuan_surat/unduh_surat_rekomendasi/' . $item->id) ?>" class="btn btn-action-read btn-sm" title="Unduh Word" data-no-transition target="suratRekomendasiDownloadFrame">
                                            <iconify-icon icon="mdi:file-word-outline"></iconify-icon>
                                        </a>
                                        <button
                                            type="button"
                                            class="btn btn-action-delete btn-sm"
                                            title="Hapus"
                                            onclick="confirmDeleteSurat('<?= site_url('pengajuan_surat/hapus_surat_rekomendasi/' . $item->id) ?>', 'Surat rekomendasi <?= isset($jenis_surat_labels[$item->jenis_surat]) ? $jenis_surat_labels[$item->jenis_surat] : ucfirst(str_replace('_', ' ', $item->jenis_surat)) ?>')">
                                            <iconify-icon icon="mdi:trash-can-outline"></iconify-icon>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
