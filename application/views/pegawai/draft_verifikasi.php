<!-- Breadcrumb -->
<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('dashboard_petugas') ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('pegawai') ?>">Data Pegawai</a></li>
        <li class="breadcrumb-item active">Draft Verifikasi</li>
    </ol>
</nav>

<div class="card table-page-fit">
    <div class="card-header">
        <h3><iconify-icon icon="mdi:file-document-check-outline" class="mr-2"></iconify-icon>Draft Verifikasi</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Tanggal Verifikasi</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Jabatan</th>
                        <th>Pendidikan</th>
                        <th style="width:140px">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($draft_verifikasi as $item): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $item->created_at ? date('d/m/Y H:i', strtotime($item->created_at)) : '-' ?></td>
                            <td><?= $item->approved_at ? date('d/m/Y H:i', strtotime($item->approved_at)) : '-' ?></td>
                            <td>
                                <strong><?= $item->nama ?></strong>
                                <?php if ($item->tempat_lahir || $item->tanggal_lahir): ?>
                                <br><small class="text-muted"><?= $item->tempat_lahir ?>, <?= $item->tanggal_lahir ? date('d/m/Y', strtotime($item->tanggal_lahir)) : '-' ?></small>
                                <?php endif; ?>
                            </td>
                            <td><code><?= $item->nip ?></code></td>
                            <td><?= $item->jabatan ?: '-' ?></td>
                            <td><?= $item->tingkat_pendidikan ?: '-' ?></td>
                            <td class="text-center">
                                <?php if ($item->status === 'approved'): ?>
                                    <span class="badge" style="background:#dff7ea;color:#1f8f56">Disetujui</span>
                                <?php else: ?>
                                    <span class="badge" style="background:#ffe1e6;color:#c74b65">Ditolak</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
