<!-- Breadcrumb -->
<?php $home_url = ($this->session->userdata('role') === 'petugas') ? site_url('dashboard_petugas') : site_url('pegawai'); ?>
<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $home_url ?>">Home</a></li>
        <li class="breadcrumb-item">Master Surat</li>
        <li class="breadcrumb-item active">Surat Masuk</li>
    </ol>
</nav>

<?php
$total_surat = count($surat_masuk);
$belum_dinomori = 0;
$sudah_dinomori = 0;

foreach ($surat_masuk as $item) {
    if (empty($item->nomor_surat)) {
        $belum_dinomori++;
    } else {
        $sudah_dinomori++;
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
                    <div style="font-size:22px;font-weight:700;color:#2d3748"><?= $belum_dinomori ?></div>
                    <div style="font-size:12px;color:#a0aec0;font-weight:500">Belum Dinomori</div>
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
                    <div style="font-size:22px;font-weight:700;color:#2d3748"><?= $sudah_dinomori ?></div>
                    <div style="font-size:12px;color:#a0aec0;font-weight:500">Sudah Dinomori</div>
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
                        <th>Tanggal Masuk</th>
                        <th>Jenis Surat</th>
                        <th>Pegawai</th>
                        <th>Tanggal Izin</th>
                        <th>Penandatangan</th>
                        <th>Nomor Surat</th>
                        <th style="width:120px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($surat_masuk as $item): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $item->created_at ? date('d/m/Y H:i', strtotime($item->created_at)) : '-' ?></td>
                            <td>
                                <strong>Surat Keterangan Sakit</strong>
                                <br><small class="text-muted text-capitalize"><?= $item->jenis ?></small>
                            </td>
                            <td>
                                <strong><?= $item->nama ?: '-' ?></strong>
                                <br><small class="text-muted"><code><?= $item->nip ?></code></small>
                            </td>
                            <td><?= $item->tanggal_izin ? date('d/m/Y', strtotime($item->tanggal_izin)) : '-' ?></td>
                            <td><?= $item->penandatangan ?></td>
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
                            <td class="text-center">
                                <a href="<?= site_url('surat/nomor/' . $item->id) ?>" class="btn btn-secondary btn-sm" title="<?= empty($item->nomor_surat) ? 'Beri Nomor' : 'Ubah Nomor' ?>">
                                    <iconify-icon icon="mdi:format-list-numbered"></iconify-icon>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
