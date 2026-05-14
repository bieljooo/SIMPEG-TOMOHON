<?php $home_url = site_url('dashboard_petugas'); ?>

<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $home_url ?>">Home</a></li>
        <li class="breadcrumb-item">Master Surat</li>
        <li class="breadcrumb-item active">Surat Masuk</li>
    </ol>
</nav>

<?php
$total_surat = count($surat_masuk);
$menunggu_nomor = 0;
$selesai = 0;

foreach ($surat_masuk as $item) {
    if ($item->status === 'pending_petugas') {
        $menunggu_nomor++;
    }

    if ($item->status === 'approved') {
        $selesai++;
    }
}
?>

<div class="row mb-4">
    <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <div style="width:45px;height:45px;border-radius:10px;background:#ebf4ff;display:flex;align-items:center;justify-content:center;margin-right:14px">
                    <iconify-icon icon="mdi:email-open-outline" style="font-size:20px;color:#3182ce"></iconify-icon>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:#2d3748"><?= $total_surat ?></div>
                    <div style="font-size:12px;color:#a0aec0;font-weight:500">Total Surat Masuk</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <div style="width:45px;height:45px;border-radius:10px;background:#fffaf0;display:flex;align-items:center;justify-content:center;margin-right:14px">
                    <iconify-icon icon="mdi:timer-sand" style="font-size:20px;color:#dd6b20"></iconify-icon>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:#2d3748"><?= $menunggu_nomor ?></div>
                    <div style="font-size:12px;color:#a0aec0;font-weight:500">Menunggu Nomor</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
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
        <h3><iconify-icon icon="mdi:inbox-arrow-down-outline" class="mr-2"></iconify-icon>Daftar Surat Masuk</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-hover" style="width:100%" data-dt-search="true" data-dt-preserve-order="true">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Jenis Surat</th>
                        <th>Pemohon</th>
                        <th>Pangkat</th>
                        <th>Nomor Surat</th>
                        <th>Status</th>
                        <th style="width:160px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($surat_masuk as $item): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $item->created_at ? date('d/m/Y H:i', strtotime($item->created_at)) : '-' ?></td>
                            <td><?= $item->jenis_surat_label ?></td>
                            <td>
                                <strong><?= $item->nama ?: '-' ?></strong>
                                <br><small class="text-muted"><?= $item->nip ?></small>
                            </td>
                            <td><?= $item->pangkat ?: '-' ?></td>
                            <td>
                                <?php if (!empty($item->nomor_surat)): ?>
                                    <strong><?= $item->nomor_surat ?></strong>
                                    <?php if (!empty($item->nomor_surat_at)): ?>
                                        <br><small class="text-muted"><?= date('d/m/Y H:i', strtotime($item->nomor_surat_at)) ?></small>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge" style="background:#fff3cd;color:#7a5a00">Belum Dinomori</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                $status_badge = 'color:#4a5568;font-weight:600;';
                                if ($item->status === 'pending_petugas') {
                                    $status_badge = 'color:#2c5282;font-weight:600;';
                                } elseif ($item->status === 'pending_kasubag' || $item->status === 'pending_sek' || $item->status === 'pending_kadis') {
                                    $status_badge = 'color:#975a16;font-weight:600;';
                                } elseif ($item->status === 'approved') {
                                    $status_badge = 'color:#1f8f56;font-weight:600;';
                                } elseif (strpos($item->status, 'rejected_') === 0) {
                                    $status_badge = 'color:#c53030;font-weight:600;';
                                }
                                ?>
                                <span style="<?= $status_badge ?>"><?= $item->status_label ?></span>
                            </td>
                            <td class="text-center">
                                <div class="table-action-group">
                                    <?php if ($item->status === 'pending_petugas'): ?>
                                    <a href="<?= site_url('surat/nomor/' . $item->id) ?>" class="btn btn-secondary btn-sm" title="Nomor">
                                        <iconify-icon icon="mdi:format-list-numbered"></iconify-icon>
                                    </a>
                                    <?php endif; ?>
                                    <button
                                        type="button"
                                        class="btn btn-action-delete btn-sm"
                                        title="Hapus"
                                        onclick="confirmDelete('<?= site_url('surat/hapus/' . $item->id) ?>', '<?= addslashes($item->jenis_surat_label . ' - ' . $item->nama) ?>')">
                                        <iconify-icon icon="mdi:trash-can-outline"></iconify-icon>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
