<!-- Breadcrumb -->
<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('dashboard_petugas') ?>">Home</a></li>
        <li class="breadcrumb-item">Master Data</li>
        <li class="breadcrumb-item"><a href="<?= site_url('pegawai/akun_pegawai') ?>">Akun Pegawai</a></li>
        <li class="breadcrumb-item active">Update Password</li>
    </ol>
</nav>

<div class="card card-flat-shell pegawai-form-shell">
    <div class="card-header">
        <h3><iconify-icon icon="mdi:key-outline" class="mr-2"></iconify-icon>Update Password Akun Pegawai</h3>
    </div>
    <div class="card-body">
        <form action="<?= site_url('pegawai/simpan_password_akun/' . $akun->nip) ?>" method="POST">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Nama Pegawai</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($akun->nama, ENT_QUOTES, 'UTF-8') ?>" readonly style="background:#edf2f7">
                </div>
                <div class="form-group col-md-6">
                    <label>NIP</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($akun->nip, ENT_QUOTES, 'UTF-8') ?>" readonly style="background:#edf2f7">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Password Baru <span class="text-danger">*</span></label>
                    <input type="password" name="new_password" class="form-control" placeholder="Minimal 8 karakter" required>
                </div>
                <div class="form-group col-md-6">
                    <label>Konfirmasi Password Baru <span class="text-danger">*</span></label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Ulangi password baru" required>
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <a href="<?= site_url('pegawai/akun_pegawai') ?>" class="btn btn-cancel-action">
                    <iconify-icon icon="mdi:arrow-left" class="mr-1"></iconify-icon> Kembali
                </a>
                <button type="submit" class="btn btn-action-save">
                    <iconify-icon icon="mdi:content-save-outline" class="mr-1"></iconify-icon> Update Password
                </button>
            </div>
        </form>
    </div>
</div>
