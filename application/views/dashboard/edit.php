<!-- Breadcrumb -->
<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Data Diri</a></li>
        <li class="breadcrumb-item active">Edit Data Diri</li>
    </ol>
</nav>

<?php $p = $pegawai; ?>

<div class="card">
    <div class="card-header">
        <h3><iconify-icon icon="mdi:account-edit-outline" class="mr-2"></iconify-icon>Edit Data Diri</h3>
    </div>
    <div class="card-body">
        <form action="<?= site_url('dashboard/update') ?>" method="POST">

            <!-- Data Pribadi -->
            <div class="mb-4">
                <h5 style="color:#3182ce;font-weight:700;border-bottom:2px solid #ebf4ff;padding-bottom:8px">
                    <iconify-icon icon="mdi:account-outline" class="mr-2"></iconify-icon>Data Pribadi
                </h5>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" value="<?= $p->nama ?>" required>
                </div>
                <div class="form-group col-md-3">
                    <label>Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" value="<?= $p->tempat_lahir ?>">
                </div>
                <div class="form-group col-md-3">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control" value="<?= $p->tanggal_lahir ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Jenis Kelamin <span class="text-danger">*</span></label>
                    <select name="jenis_kelamin" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="L" <?= ($p->jenis_kelamin == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                        <option value="P" <?= ($p->jenis_kelamin == 'P') ? 'selected' : '' ?>>Perempuan</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Status Kawin</label>
                    <select name="status_kawin" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Kawin" <?= ($p->status_kawin == 'Kawin') ? 'selected' : '' ?>>Kawin</option>
                        <option value="Belum Kawin" <?= ($p->status_kawin == 'Belum Kawin') ? 'selected' : '' ?>>Belum Kawin</option>
                        <option value="Cerai Hidup" <?= ($p->status_kawin == 'Cerai Hidup') ? 'selected' : '' ?>>Cerai Hidup</option>
                        <option value="Cerai Mati" <?= ($p->status_kawin == 'Cerai Mati') ? 'selected' : '' ?>>Cerai Mati</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Agama</label>
                    <select name="agama" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Kristen" <?= ($p->agama == 'Kristen') ? 'selected' : '' ?>>Kristen</option>
                        <option value="Katolik" <?= ($p->agama == 'Katolik') ? 'selected' : '' ?>>Katolik</option>
                        <option value="Islam" <?= ($p->agama == 'Islam') ? 'selected' : '' ?>>Islam</option>
                        <option value="Hindu" <?= ($p->agama == 'Hindu') ? 'selected' : '' ?>>Hindu</option>
                        <option value="Buddha" <?= ($p->agama == 'Buddha') ? 'selected' : '' ?>>Buddha</option>
                        <option value="Konghucu" <?= ($p->agama == 'Konghucu') ? 'selected' : '' ?>>Konghucu</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>No. Telepon</label>
                    <input type="text" name="no_telp" class="form-control" value="<?= $p->no_telp ?>">
                </div>
            </div>

            <div class="form-group">
                <label>Alamat Tempat Tinggal</label>
                <textarea name="alamat" class="form-control" rows="2"><?= $p->alamat ?></textarea>
            </div>

            <!-- Data Kepegawaian -->
            <div class="mb-4 mt-4">
                <h5 style="color:#3182ce;font-weight:700;border-bottom:2px solid #ebf4ff;padding-bottom:8px">
                    <iconify-icon icon="mdi:card-account-details-outline" class="mr-2"></iconify-icon>Data Kepegawaian
                </h5>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>NIP</label>
                    <input type="text" class="form-control" value="<?= $p->nip ?>" readonly style="background:#edf2f7">
                </div>
                <div class="form-group col-md-4">
                    <label>Gol. Ruang CPNS</label>
                    <?php
                        $gol_options = array('Ia','Ib','Ic','Id','IIa','IIb','IIc','IId','IIIa','IIIb','IIIc','IIId','IVa','IVb','IVc','IVd','IVe','IX');
                    ?>
                    <select name="gol_ruang_cpns" class="form-control">
                        <option value="">-- Pilih --</option>
                        <?php foreach($gol_options as $gol): ?>
                        <option value="<?= $gol ?>" <?= ($p->gol_ruang_cpns == $gol) ? 'selected' : '' ?>><?= $gol ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>TMT CPNS</label>
                    <input type="date" name="tmt_cpns" class="form-control" value="<?= $p->tmt_cpns ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Pangkat / Gol. Ruang Terakhir</label>
                    <input type="text" name="pangkat_terakhir" class="form-control" value="<?= $p->pangkat_terakhir ?>">
                </div>
                <div class="form-group col-md-6">
                    <label>Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" value="<?= $p->jabatan ?>">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Eselon</label>
                    <?php
                        $eselon_options = array('I','II','IIIa','IIIb','IVa','IVb');
                    ?>
                    <select name="eselon" class="form-control">
                        <option value="">-- Pilih --</option>
                        <?php foreach($eselon_options as $es): ?>
                        <option value="<?= $es ?>" <?= ($p->eselon == $es) ? 'selected' : '' ?>><?= $es ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Diklat Penjenjangan</label>
                    <?php
                        $diklat_options = array('PIM I','PIM II','PIM III','PIM IV','Pra Jabatan','Latsar CPNS');
                    ?>
                    <select name="diklat_penjenjangan" class="form-control">
                        <option value="">-- Pilih --</option>
                        <?php foreach($diklat_options as $dk): ?>
                        <option value="<?= $dk ?>" <?= ($p->diklat_penjenjangan == $dk) ? 'selected' : '' ?>><?= $dk ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Instansi Pembayar</label>
                    <input type="text" name="instansi_pembayar" class="form-control" value="<?= $p->instansi_pembayar ?>">
                </div>
            </div>

            <!-- Data Pendidikan -->
            <div class="mb-4 mt-4">
                <h5 style="color:#3182ce;font-weight:700;border-bottom:2px solid #ebf4ff;padding-bottom:8px">
                    <iconify-icon icon="mdi:school-outline" class="mr-2"></iconify-icon>Data Pendidikan
                </h5>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Tingkat Pendidikan</label>
                    <?php
                        $pendidikan_options = array('SD','SMP','SMA','D-1','D-2','D-3','D-4','S-1','S-2','S-3');
                    ?>
                    <select name="tingkat_pendidikan" class="form-control">
                        <option value="">-- Pilih --</option>
                        <?php foreach($pendidikan_options as $pnd): ?>
                        <option value="<?= $pnd ?>" <?= ($p->tingkat_pendidikan == $pnd) ? 'selected' : '' ?>><?= $pnd ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Jurusan</label>
                    <input type="text" name="jurusan" class="form-control" value="<?= $p->jurusan ?>">
                </div>
                <div class="form-group col-md-3">
                    <label>Tahun Lulus</label>
                    <input type="number" name="tahun_lulus" class="form-control" value="<?= $p->tahun_lulus ?>" min="1970" max="2030">
                </div>
                <div class="form-group col-md-3">
                    <label>Alumni (Universitas)</label>
                    <input type="text" name="alumni" class="form-control" value="<?= $p->alumni ?>">
                </div>
            </div>

            <!-- Keterangan -->
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="2"><?= $p->keterangan ?></textarea>
            </div>

            <hr>
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('dashboard') ?>" class="btn btn-secondary">
                    <iconify-icon icon="mdi:arrow-left" class="mr-1"></iconify-icon> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <iconify-icon icon="mdi:content-save-outline" class="mr-1"></iconify-icon> Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>

