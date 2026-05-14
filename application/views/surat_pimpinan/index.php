<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
        <li class="breadcrumb-item active"><?= $title ?></li>
    </ol>
</nav>

<?php $total_surat = count($surat_list); ?>

<div class="row mb-4">
    <div class="col-lg-6 col-md-6 col-sm-6 mb-3">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <div style="width:45px;height:45px;border-radius:10px;background:#ebf4ff;display:flex;align-items:center;justify-content:center;margin-right:14px">
                    <iconify-icon icon="mdi:file-document-check-outline" style="font-size:20px;color:#3182ce"></iconify-icon>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:#2d3748"><?= $total_surat ?></div>
                    <div style="font-size:12px;color:#a0aec0;font-weight:500">Menunggu Diproses</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-6 col-md-6 col-sm-6 mb-3">
        <div class="card">
            <div class="card-body d-flex align-items-center">
                <div style="width:45px;height:45px;border-radius:10px;background:#f0fff4;display:flex;align-items:center;justify-content:center;margin-right:14px">
                    <iconify-icon icon="mdi:account-check-outline" style="font-size:20px;color:#38a169"></iconify-icon>
                </div>
                <div>
                    <div style="font-size:22px;font-weight:700;color:#2d3748"><?= htmlspecialchars($this->session->userdata('nama'), ENT_QUOTES, 'UTF-8') ?></div>
                    <div style="font-size:12px;color:#a0aec0;font-weight:500"><?= ucfirst($current_role) ?> Pemeriksa</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card table-page-fit">
    <div class="card-header">
        <h3><iconify-icon icon="mdi:file-document-check-outline" class="mr-2"></iconify-icon><?= $title ?></h3>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-hover" style="width:100%" data-dt-search="true" data-dt-preserve-order="true">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Jenis Surat</th>
                        <th>Nama</th>
                        <th>NIP</th>
                        <th>Pangkat</th>
                        <th>Nomor Surat</th>
                        <th style="width:220px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($surat_list as $item): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td><?= $item->created_at ? date('d/m/Y H:i', strtotime($item->created_at)) : '-' ?></td>
                            <td><?= $item->jenis_surat_label ?></td>
                            <td><?= $item->nama ?: '-' ?></td>
                            <td><code><?= $item->nip ?></code></td>
                            <td><?= $item->pangkat ?: '-' ?></td>
                            <td><?= !empty($item->nomor_surat) ? htmlspecialchars($item->nomor_surat, ENT_QUOTES, 'UTF-8') : '-' ?></td>
                            <td class="text-center">
                                <div class="table-action-group">
                                    <a href="<?= site_url('surat_pimpinan/detail/' . $item->id) ?>" class="btn btn-action-read btn-sm" title="View">
                                        <iconify-icon icon="mdi:eye-outline"></iconify-icon>
                                    </a>
                                    <form id="approve-form-<?= $item->id ?>" action="<?= site_url('surat_pimpinan/setujui/' . $item->id) ?>" method="POST" style="display:inline">
                                        <button type="button" onclick="confirmApproval('approve-form-<?= $item->id ?>', '<?= addslashes($item->nama) ?>')" class="btn btn-action-approve btn-sm" title="Setujui">
                                            <iconify-icon icon="mdi:check"></iconify-icon>
                                            <span><?= $primary_action_label ?></span>
                                        </button>
                                    </form>
                                    <form id="reject-form-<?= $item->id ?>" action="<?= site_url('surat_pimpinan/tolak/' . $item->id) ?>" method="POST" style="display:inline">
                                        <button type="button" onclick="confirmRejection('reject-form-<?= $item->id ?>', '<?= addslashes($item->nama) ?>')" class="btn btn-danger btn-sm" title="Tolak">
                                            <iconify-icon icon="mdi:close-thick"></iconify-icon>
                                            <span>Tolak</span>
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
