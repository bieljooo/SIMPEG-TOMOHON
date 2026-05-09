<!-- Breadcrumb -->
<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('pegawai') ?>">Home</a></li>
        <li class="breadcrumb-item active">Data Pegawai</li>
    </ol>
</nav>

<?php $is_kasubag = ($this->session->userdata('role') === 'kasubag'); ?>

<!-- Data Table -->
<div class="card card-flat-shell pegawai-table-fit">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3><iconify-icon icon="mdi:table-large" class="mr-2"></iconify-icon>Daftar Pegawai</h3>
        <?php if (!$is_kasubag): ?>
        <a href="<?= site_url('pegawai/tambah') ?>" class="btn btn-primary btn-sm">
            <iconify-icon icon="mdi:plus" class="mr-1"></iconify-icon> Tambah Pegawai
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="table-shell-full">
            <table id="dataTable" class="table table-bordered table-hover table-pegawai-full" style="width:100%" data-dt-search="true" data-dt-preserve-order="true">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Pangkat/Gol</th>
                        <th>Jabatan</th>
                        <th>L/P</th>
                        <th>Pendidikan</th>
                        <th style="width:180px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1;
                    foreach ($pegawai as $p): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td>
                                <strong><?= $p->nama ?></strong>
                                <?php if ($p->tempat_lahir || $p->tanggal_lahir): ?>
                                <br><small class="text-muted"><?= $p->tempat_lahir ?>, <?= $p->tanggal_lahir ? date('d/m/Y', strtotime($p->tanggal_lahir)) : '-' ?></small>
                                <?php endif; ?>
                            </td>
                            <td><code><?= $p->nip ?></code></td>
                            <td><?= $p->pangkat_terakhir ?></td>
                            <td><?= $p->jabatan ?></td>
                            <td class="text-center">
                                <span class="gender-code">
                                    <?= ($p->jenis_kelamin == 'L') ? 'L' : 'P' ?>
                                </span>
                            </td>
                            <td><?= $p->tingkat_pendidikan ?></td>
                            <td class="text-center">
                                <div class="table-action-group">
                                    <a href="<?= site_url('pegawai/detail/' . $p->nip) ?>" class="btn btn-success btn-sm" title="Detail">
                                        <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                                    </a>
                                    <?php if (!$is_kasubag): ?>
                                    <a href="<?= site_url('pegawai/edit/' . $p->nip) ?>" class="btn btn-info btn-sm" title="Edit">
                                        <iconify-icon icon="mdi:pencil-outline"></iconify-icon>
                                    </a>
                                    <button onclick="confirmDelete('<?= site_url('pegawai/hapus/' . $p->nip) ?>', '<?= addslashes($p->nama) ?>')" class="btn btn-danger btn-sm" title="Hapus">
                                        <iconify-icon icon="mdi:trash-can-outline"></iconify-icon>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

