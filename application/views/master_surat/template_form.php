<!-- Breadcrumb -->
<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('dashboard_petugas') ?>">Home</a></li>
        <li class="breadcrumb-item">Master Surat</li>
        <li class="breadcrumb-item"><a href="<?= site_url('master_surat/template_surat') ?>">Template Surat</a></li>
        <li class="breadcrumb-item active"><?= ($form_mode === 'upload') ? 'Upload' : 'Edit' ?></li>
    </ol>
</nav>

<div class="card card-flat-shell">
    <div class="card-header">
        <h3>
            <iconify-icon icon="<?= ($form_mode === 'upload') ? 'mdi:upload-outline' : 'mdi:pencil-outline' ?>" class="mr-2"></iconify-icon>
            <?= $title ?>
        </h3>
    </div>
    <form action="" method="post" enctype="multipart/form-data">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Template</label>
                        <?php if ($form_mode === 'edit'): ?>
                            <input type="text" name="nama_template" class="form-control" value="<?= htmlspecialchars($template->nama_template, ENT_QUOTES, 'UTF-8') ?>" required>
                        <?php else: ?>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($template->nama_template, ENT_QUOTES, 'UTF-8') ?>" readonly>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Jenis Surat</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($template->sub_menu, ENT_QUOTES, 'UTF-8') ?>" readonly>
                    </div>
                </div>
            </div>

            <?php if (!empty($template->file_path)): ?>
            <div class="alert alert-light border">
                <strong>File aktif:</strong>
                <a href="<?= base_url($template->file_path) ?>" target="_blank" rel="noopener">
                    <?= htmlspecialchars($template->file_original_name, ENT_QUOTES, 'UTF-8') ?>
                </a>
            </div>
            <?php endif; ?>

            <div class="form-group">
                <label><?= ($form_mode === 'upload') ? 'File Template Word' : 'Ganti File Template Word' ?></label>
                <input type="file" name="template_file" class="form-control-file" accept=".doc,.docx" <?= ($form_mode === 'upload') ? 'required' : '' ?>>
                <small class="form-text text-muted">
                    Format file hanya <strong>.doc</strong> atau <strong>.docx</strong>.
                    <?= ($form_mode === 'edit') ? 'Kosongkan jika file tidak diganti.' : '' ?>
                </small>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-between">
            <a href="<?= site_url('master_surat/template_surat') ?>" class="btn btn-secondary">
                <iconify-icon icon="mdi:arrow-left" class="mr-1"></iconify-icon>Kembali
            </a>
            <button type="submit" class="btn btn-primary">
                <iconify-icon icon="mdi:content-save-outline" class="mr-1"></iconify-icon>Simpan
            </button>
        </div>
    </form>
</div>
