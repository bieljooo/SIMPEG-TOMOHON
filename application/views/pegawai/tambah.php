<!-- Breadcrumb -->
<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('pegawai') ?>">Home</a></li>
        <li class="breadcrumb-item"><a href="<?= site_url('pegawai') ?>">Data Pegawai</a></li>
        <li class="breadcrumb-item active">Tambah Pegawai</li>
    </ol>
</nav>

<div class="card card-flat-shell pegawai-form-shell">
    <div class="card-header">
        <h3><iconify-icon icon="mdi:account-plus-outline" class="mr-2"></iconify-icon>Form Tambah Pegawai</h3>
    </div>
    <div class="card-body">
        <form action="<?= site_url('pegawai/simpan') ?>" method="POST">

            <!-- Data Pribadi -->
            <div class="mb-4">
                <h5 style="color:#3182ce;font-weight:700;border-bottom:2px solid #ebf4ff;padding-bottom:8px">
                    <iconify-icon icon="mdi:account-outline" class="mr-2"></iconify-icon>Data Pribadi
                </h5>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="nama" class="form-control" placeholder="Nama lengkap beserta gelar" required>
                </div>
                <div class="form-group col-md-3">
                    <label>Tempat Lahir</label>
                    <input type="text" name="tempat_lahir" class="form-control" placeholder="Tempat lahir">
                </div>
                <div class="form-group col-md-3">
                    <label>Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Jenis Kelamin <span class="text-danger">*</span></label>
                    <select name="jenis_kelamin" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Status Kawin</label>
                    <select name="status_kawin" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Kawin">Kawin</option>
                        <option value="Belum Kawin">Belum Kawin</option>
                        <option value="Cerai Hidup">Cerai Hidup</option>
                        <option value="Cerai Mati">Cerai Mati</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Agama</label>
                    <select name="agama" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Kristen">Kristen</option>
                        <option value="Katolik">Katolik</option>
                        <option value="Islam">Islam</option>
                        <option value="Hindu">Hindu</option>
                        <option value="Buddha">Buddha</option>
                        <option value="Konghucu">Konghucu</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>No. Telepon</label>
                    <input type="text" name="no_telp" class="form-control" placeholder="08xxxxxxxxxx">
                </div>
            </div>

            <div class="form-group">
                <label>Alamat Tempat Tinggal</label>
                <textarea name="alamat" class="form-control" rows="2" placeholder="Alamat lengkap"></textarea>
            </div>

            <!-- Data Kepegawaian -->
            <div class="mb-4 mt-4">
                <h5 style="color:#3182ce;font-weight:700;border-bottom:2px solid #ebf4ff;padding-bottom:8px">
                    <iconify-icon icon="mdi:card-account-details-outline" class="mr-2"></iconify-icon>Data Kepegawaian
                </h5>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>NIP <span class="text-danger">*</span></label>
                    <input type="text" name="nip" class="form-control" placeholder="19xxxxxx xxxxxx x xxx" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Gol. Ruang CPNS</label>
                    <select name="gol_ruang_cpns" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="Ia">Ia</option>
                        <option value="Ib">Ib</option>
                        <option value="Ic">Ic</option>
                        <option value="Id">Id</option>
                        <option value="IIa">IIa</option>
                        <option value="IIb">IIb</option>
                        <option value="IIc">IIc</option>
                        <option value="IId">IId</option>
                        <option value="IIIa">IIIa</option>
                        <option value="IIIb">IIIb</option>
                        <option value="IIIc">IIIc</option>
                        <option value="IIId">IIId</option>
                        <option value="IVa">IVa</option>
                        <option value="IVb">IVb</option>
                        <option value="IVc">IVc</option>
                        <option value="IVd">IVd</option>
                        <option value="IVe">IVe</option>
                        <option value="IX">IX</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>TMT CPNS</label>
                    <input type="date" name="tmt_cpns" class="form-control">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Pangkat / Gol. Ruang Terakhir</label>
                    <input type="text" name="pangkat_terakhir" class="form-control" placeholder="Contoh: Pembina Utama Muda/ IVc">
                </div>
                <div class="form-group col-md-6">
                    <label>Jabatan</label>
                    <input type="text" name="jabatan" class="form-control" placeholder="Jabatan saat ini">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Eselon</label>
                    <select name="eselon" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="I">I</option>
                        <option value="II">II</option>
                        <option value="IIIa">IIIa</option>
                        <option value="IIIb">IIIb</option>
                        <option value="IVa">IVa</option>
                        <option value="IVb">IVb</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Diklat Penjenjangan</label>
                    <select name="diklat_penjenjangan" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="PIM I">PIM I</option>
                        <option value="PIM II">PIM II</option>
                        <option value="PIM III">PIM III</option>
                        <option value="PIM IV">PIM IV</option>
                        <option value="Pra Jabatan">Pra Jabatan</option>
                        <option value="Latsar CPNS">Latsar CPNS</option>
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Instansi Pembayar</label>
                    <input type="text" name="instansi_pembayar" class="form-control" placeholder="Contoh: DPMPTSPD" value="DPMPTSPD">
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
                    <select name="tingkat_pendidikan" class="form-control">
                        <option value="">-- Pilih --</option>
                        <option value="SD">SD</option>
                        <option value="SMP">SMP</option>
                        <option value="SMA">SMA</option>
                        <option value="D-1">D-1</option>
                        <option value="D-2">D-2</option>
                        <option value="D-3">D-3</option>
                        <option value="D-4">D-4</option>
                        <option value="S-1">S-1</option>
                        <option value="S-2">S-2</option>
                        <option value="S-3">S-3</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Jurusan</label>
                    <input type="text" name="jurusan" class="form-control" placeholder="Jurusan pendidikan">
                </div>
                <div class="form-group col-md-3">
                    <label>Tahun Lulus</label>
                    <input type="number" name="tahun_lulus" class="form-control" placeholder="Tahun" min="1970" max="2030">
                </div>
                <div class="form-group col-md-3">
                    <label>Alumni (Universitas)</label>
                    <input type="text" name="alumni" class="form-control" placeholder="Nama universitas">
                </div>
            </div>

            <!-- Keterangan -->
            <div class="form-group">
                <label>Keterangan</label>
                <textarea name="keterangan" class="form-control" rows="2" placeholder="Keterangan tambahan (opsional)"></textarea>
            </div>

            <hr>
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('pegawai') ?>" class="btn btn-secondary">
                    <iconify-icon icon="mdi:arrow-left" class="mr-1"></iconify-icon> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <iconify-icon icon="mdi:content-save-outline" class="mr-1"></iconify-icon> Simpan Data
                </button>
            </div>

        </form>
    </div>
</div>

