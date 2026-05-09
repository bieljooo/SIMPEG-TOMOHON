<!-- Breadcrumb -->
<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('dashboard_petugas') ?>">Home</a></li>
        <li class="breadcrumb-item">Master Surat</li>
        <li class="breadcrumb-item active">Template Surat</li>
    </ol>
</nav>

<div class="card card-flat-shell pegawai-table-fit">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3><iconify-icon icon="mdi:file-document-multiple-outline" class="mr-2"></iconify-icon>Template Surat</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-shell-full">
            <table id="dataTable" class="table table-bordered table-hover table-pegawai-full" style="width:100%" data-dt-search="true" data-dt-preserve-order="true">
                <thead>
                    <tr>
                        <th style="width:40px">No</th>
                        <th>Template</th>
                        <th>File Word</th>
                        <th style="width:190px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; foreach ($templates as $item): ?>
                        <tr>
                            <td class="text-center"><?= $no++ ?></td>
                            <td>
                                <strong><?= htmlspecialchars($item->nama_template, ENT_QUOTES, 'UTF-8') ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($item->sub_menu, ENT_QUOTES, 'UTF-8') ?></small>
                            </td>
                            <td>
                                <?php if (!empty($item->file_path)): ?>
                                    <a href="<?= base_url($item->file_path) ?>" target="_blank" rel="noopener">
                                        <iconify-icon icon="mdi:file-word-outline" class="mr-1"></iconify-icon><?= htmlspecialchars($item->file_original_name, ENT_QUOTES, 'UTF-8') ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">Belum ada file</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="table-action-group">
                                    <a href="<?= site_url('master_surat/upload/' . $item->id) ?>" class="btn btn-secondary btn-sm" title="Upload">
                                        <iconify-icon icon="mdi:upload-outline"></iconify-icon>
                                    </a>
                                    <a href="<?= site_url('master_surat/edit/' . $item->id) ?>" class="btn btn-info btn-sm" title="Edit">
                                        <iconify-icon icon="mdi:pencil-outline"></iconify-icon>
                                    </a>
                                    <button type="button" onclick="confirmDelete('<?= site_url('master_surat/delete/' . $item->id) ?>', '<?= addslashes($item->nama_template) ?>')" class="btn btn-danger btn-sm" title="Hapus">
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
