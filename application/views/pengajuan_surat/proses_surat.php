<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
        <li class="breadcrumb-item">Surat Rekomendasi</li>
        <li class="breadcrumb-item active">Proses Surat</li>
    </ol>
</nav>

<?php
$total_surat = count($surat_rekomendasi);
$selesai = 0;
$jenis_surat_labels = array(
    'usulan_kenaikan_pangkat' => 'Usulan Kenaikan Pangkat',
    'usulan_kenaikan_gaji_berkala' => 'Usulan Kenaikan Gaji Berkala',
    'usulan_cuti_tahun' => 'Usulan Cuti Tahunan',
    'usulan_alasan_penting' => 'Usulan Cuti Alasan Penting',
    'cuti_alasan_penting' => 'Usulan Cuti Alasan Penting',
    'usulan_cuti_luar_negeri' => 'Usulan Cuti Luar Negeri',
);

foreach ($surat_rekomendasi as $item) {
    if ($item->status === 'approved') {
        $selesai++;
    }
}
?>

<div class="row mb-4">
    <div class="col-lg-6 col-md-6 col-sm-6 mb-3">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <div style="width:45px;height:45px;border-radius:10px;background:#ebf4ff;display:flex;align-items:center;justify-content:center;margin-right:14px">
                    <iconify-icon icon="mdi:file-document-outline" style="font-size:20px;color:#3182ce"></iconify-icon>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:#2d3748"><?= $total_surat ?></div>
                    <div style="font-size:12px;color:#a0aec0;font-weight:500">Total Usulan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 mb-3">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <div style="width:45px;height:45px;border-radius:10px;background:#f0fff4;display:flex;align-items:center;justify-content:center;margin-right:14px">
                    <iconify-icon icon="mdi:check-circle-outline" style="font-size:20px;color:#38a169"></iconify-icon>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:#2d3748"><?= $selesai ?></div>
                    <div style="font-size:12px;color:#a0aec0;font-weight:500">Selesai</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card table-page-fit">
    <div class="card-header">
        <h3><iconify-icon icon="mdi:progress-check" class="mr-2"></iconify-icon>Riwayat Proses Surat</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-hover" style="width:100%" data-dt-search="true" data-dt-preserve-order="true">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Jenis Surat</th>
                        <th>Pangkat</th>
                        <th>Nomor Surat</th>
                        <th>Status</th>
                        <th style="width:90px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($surat_rekomendasi as $item): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $item->created_at ? date('d/m/Y H:i', strtotime($item->created_at)) : '-' ?></td>
                            <td><?= isset($jenis_surat_labels[$item->jenis_surat]) ? $jenis_surat_labels[$item->jenis_surat] : ucfirst(str_replace('_', ' ', $item->jenis_surat)) ?></td>
                            <td><?= $item->pangkat ?: '-' ?></td>
                            <td>
                                <?php if (!empty($item->nomor_surat)): ?>
                                    <?= htmlspecialchars($item->nomor_surat, ENT_QUOTES, 'UTF-8') ?>
                                <?php else: ?>
                                    <span class="badge" style="background:#fff3cd;color:#7a5a00">Belum Dinomori</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $status_badge = 'background:#edf2f7;color:#4a5568';
                                if ($item->status === 'pending_petugas') {
                                    $status_badge = 'background:#ebf8ff;color:#2c5282';
                                } elseif ($item->status === 'pending_kasubag' || $item->status === 'pending_sek' || $item->status === 'pending_kadis') {
                                    $status_badge = 'background:#fffaf0;color:#975a16';
                                } elseif ($item->status === 'approved') {
                                    $status_badge = 'background:#f0fff4;color:#1f8f56';
                                } elseif (strpos($item->status, 'rejected_') === 0) {
                                    $status_badge = 'background:#fff5f5;color:#c53030';
                                }
                                ?>
                                <span class="badge" style="<?= $status_badge ?>">
                                    <?php
                                    $map = array(
                                        'pending_petugas' => 'Menunggu penomoran petugas',
                                        'pending_kasubag' => 'Menunggu verifikasi kasubag',
                                        'pending_sek' => 'Menunggu verifikasi sek',
                                        'pending_kadis' => 'Menunggu validasi kadis',
                                        'approved' => 'Selesai',
                                        'rejected_kasubag' => 'Ditolak kasubag',
                                        'rejected_sek' => 'Ditolak sek',
                                        'rejected_kadis' => 'Ditolak kadis',
                                    );
                                    echo isset($map[$item->status]) ? $map[$item->status] : ucfirst(str_replace('_', ' ', $item->status));
                                    ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="<?= site_url('pengajuan_surat/view_surat_rekomendasi/' . $item->id . '?source=proses') ?>" class="btn btn-action-read btn-sm" title="View">
                                    <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
