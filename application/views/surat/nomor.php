<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('dashboard_petugas') ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('surat') ?>">Surat Masuk</a></li>
        <li class="breadcrumb-item active">Nomor Surat</li>
    </ol>
</nav>

<?php $s = $surat; ?>

<div class="card">
    <div class="card-header">
        <h3><iconify-icon icon="mdi:format-list-numbered" class="mr-2"></iconify-icon>Input Nomor Surat</h3>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center mb-4 p-3" style="background:#f7fafc;border-radius:8px;border-left:4px solid #3182ce">
            <div style="width:52px;height:52px;border-radius:50%;background:#3182ce;color:#fff;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;margin-right:16px">
                <?= strtoupper(substr($s->nama, 0, 1)) ?>
            </div>
            <div>
                <h5 style="margin:0;color:#2d3748;font-weight:700"><?= $s->nama ?></h5>
                <span style="color:#718096;font-size:14px">NIP: <?= $s->nip ?></span>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-4">
                <label style="color:#718096;font-weight:600">Jenis Surat</label>
                <div class="form-control" style="background:#edf2f7"><?= $jenis_surat_label ?></div>
            </div>
            <div class="col-md-4">
                <label style="color:#718096;font-weight:600">Pangkat</label>
                <div class="form-control" style="background:#edf2f7"><?= $s->pangkat ?: '-' ?></div>
            </div>
            <div class="col-md-4">
                <label style="color:#718096;font-weight:600">Status</label>
                <div class="form-control" style="background:#edf2f7"><?= $status_label ?></div>
            </div>
        </div>

        <form action="<?= site_url('surat/update_nomor/' . $s->id) ?>" method="POST">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Nomor Surat <span class="text-danger">*</span></label>
                    <input type="text" name="nomor_surat" class="form-control" value="<?= htmlspecialchars((string) $s->nomor_surat, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Sifat <span class="text-danger">*</span></label>
                    <input type="text" name="sifat" class="form-control" value="<?= htmlspecialchars($default_sifat, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Lampiran <span class="text-danger">*</span></label>
                    <input type="text" name="lampiran" class="form-control" value="<?= htmlspecialchars($default_lampiran, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Perihal <span class="text-danger">*</span></label>
                    <input type="text" name="perihal" class="form-control" value="<?= htmlspecialchars($default_perihal, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <?php if (!empty($show_template_pemohon_fields)): ?>
                <div class="form-group col-md-4">
                    <label>No <span class="text-danger">*</span></label>
                    <input type="text" name="template_no" class="form-control" value="<?= htmlspecialchars($default_template_no, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Nama <span class="text-danger">*</span></label>
                    <input type="text" name="template_nama" class="form-control" value="<?= htmlspecialchars($default_template_nama, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="form-group col-md-4">
                    <label>NIP <span class="text-danger">*</span></label>
                    <input type="text" name="template_nip" class="form-control" value="<?= htmlspecialchars($default_template_nip, ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <?php endif; ?>
            </div>

            <hr>
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('surat') ?>" class="btn btn-cancel-action">
                    <iconify-icon icon="mdi:arrow-left" class="mr-1"></iconify-icon> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <iconify-icon icon="mdi:content-save-outline" class="mr-1"></iconify-icon> Simpan Nomor
                </button>
            </div>
        </form>
    </div>
</div>
