<!-- Breadcrumb -->
<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('pegawai') ?>">Home</a></li>
        <li class="breadcrumb-item active">Persetujuan Data Pegawai</li>
    </ol>
</nav>

<?php $total_pengajuan = count($pengajuan); ?>

<div class="row mb-4">
    <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <div style="width:45px;height:45px;border-radius:10px;background:#ebf4ff;display:flex;align-items:center;justify-content:center;margin-right:14px">
                    <iconify-icon icon="mdi:account-clock-outline" style="font-size:20px;color:#3182ce"></iconify-icon>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:#2d3748"><?= $total_pengajuan ?></div>
                    <div style="font-size:12px;color:#a0aec0;font-weight:500">Menunggu Persetujuan</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 col-md-6 col-sm-6 mb-3">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <div style="width:45px;height:45px;border-radius:10px;background:#f0fff4;display:flex;align-items:center;justify-content:center;margin-right:14px">
                    <iconify-icon icon="mdi:account-check-outline" style="font-size:20px;color:#38a169"></iconify-icon>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:#2d3748"><?= $this->session->userdata('nama') ?></div>
                    <div style="font-size:12px;color:#a0aec0;font-weight:500">Kasubag Pemeriksa</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card table-page-fit">
    <div class="card-header">
        <h3><iconify-icon icon="mdi:clipboard-check-outline" class="mr-2"></iconify-icon>Daftar Persetujuan Data Pegawai</h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-hover" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Jabatan</th>
                        <th>Pendidikan</th>
                        <th style="width:220px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($pengajuan as $item): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $item->created_at ? date('d/m/Y H:i', strtotime($item->created_at)) : '-' ?></td>
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
                                <div class="table-action-group">
                                    <a href="<?= site_url('persetujuan_pegawai/detail/' . $item->id) ?>" class="btn btn-info btn-sm" title="View">
                                        <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                                    </a>
                                    <form id="approve-form-<?= $item->id ?>" action="<?= site_url('persetujuan_pegawai/setujui/' . $item->id) ?>" method="POST" style="display:inline">
                                        <button type="button" onclick="confirmApproval('approve-form-<?= $item->id ?>', '<?= addslashes($item->nama) ?>')" class="btn btn-success btn-sm" title="Setujui">
                                            <iconify-icon icon="mdi:check" class="mr-1"></iconify-icon> Setujui
                                        </button>
                                    </form>
                                    <form id="reject-form-<?= $item->id ?>" action="<?= site_url('persetujuan_pegawai/tolak/' . $item->id) ?>" method="POST" style="display:inline">
                                        <button type="button" onclick="confirmRejection('reject-form-<?= $item->id ?>', '<?= addslashes($item->nama) ?>')" class="btn btn-danger btn-sm" title="Tolak">
                                            <iconify-icon icon="mdi:close-thick" class="mr-1"></iconify-icon> Tolak
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
