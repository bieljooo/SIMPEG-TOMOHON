<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
        <li class="breadcrumb-item">Surat Rekomendasi</li>
        <li class="breadcrumb-item active">Usulan Cuti Luar Negeri</li>
    </ol>
</nav>

<?php $p = $pegawai; ?>

<div class="card">
    <div class="card-header">
        <h3><iconify-icon icon="mdi:file-document-edit-outline" class="mr-2"></iconify-icon>Form Usulan Cuti Luar Negeri</h3>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center mb-4 p-3" style="background:#f7fafc;border-radius:8px;border-left:4px solid #3182ce">
            <div style="width:52px;height:52px;border-radius:50%;background:#3182ce;color:#fff;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;margin-right:16px">
                <?= strtoupper(substr($p->nama, 0, 1)) ?>
            </div>
            <div>
                <h5 style="margin:0;color:#2d3748;font-weight:700"><?= $p->nama ?></h5>
                <span style="color:#718096;font-size:14px">NIP: <?= $p->nip ?></span>
            </div>
        </div>

        <div class="mb-4 p-3" style="background:#ebf8ff;border-radius:8px;border-left:4px solid #63b3ed;color:#2c5282">
            Data surat akan otomatis dikirim ke petugas untuk penomoran sebelum masuk ke jalur verifikasi atasan langsung.
        </div>

        <form action="<?= site_url('pengajuan_surat/simpan_cuti_luar_negeri') ?>" method="POST" id="formCutiLuarNegeri">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Nama</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($p->nama, ENT_QUOTES, 'UTF-8') ?>" readonly style="background:#edf2f7">
                </div>
                <div class="form-group col-md-4">
                    <label>NIP</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($p->nip, ENT_QUOTES, 'UTF-8') ?>" readonly style="background:#edf2f7">
                </div>
                <div class="form-group col-md-4">
                    <label>Pangkat</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars((string) $p->pangkat_terakhir, ENT_QUOTES, 'UTF-8') ?>" readonly style="background:#edf2f7">
                </div>
                <div class="form-group col-md-12">
                    <label>Negara Tujuan</label>
                    <input type="text" name="negara_tujuan" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Mulai Tanggal</label>
                    <input type="date" name="tanggal_mulai" class="form-control" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Sampai Dengan</label>
                    <input type="date" name="tanggal_selesai" class="form-control" required>
                </div>
                <div class="form-group col-12">
                    <label>Keterangan</label>
                    <textarea name="keterangan" class="form-control" rows="4" required></textarea>
                </div>
            </div>

            <hr>
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('dashboard') ?>" class="btn btn-cancel-action">
                    <iconify-icon icon="mdi:arrow-left" class="mr-1"></iconify-icon> Kembali
                </a>
                <button type="button" class="btn btn-primary" onclick="confirmSubmitUsulan('formCutiLuarNegeri', 'Usulan Cuti Luar Negeri')">
                    <iconify-icon icon="mdi:send-outline" class="mr-1"></iconify-icon> Kirim Usulan
                </button>
            </div>
        </form>
    </div>
</div>
