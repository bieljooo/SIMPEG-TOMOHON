<!-- Breadcrumb -->
<?php $home_url = ($this->session->userdata('role') === 'petugas') ? site_url('dashboard_petugas') : site_url('pegawai'); ?>
<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $home_url ?>">Home</a></li>
        <li class="breadcrumb-item">Master Surat</li>
        <li class="breadcrumb-item"><a href="<?= site_url('surat') ?>">Surat Masuk</a></li>
        <li class="breadcrumb-item active">Beri Nomor Surat</li>
    </ol>
</nav>

<?php $s = $surat; ?>

<div class="card">
    <div class="card-header">
        <h3><iconify-icon icon="mdi:pound" class="mr-2"></iconify-icon>Beri Nomor Surat</h3>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td style="width:170px;color:#a0aec0;font-weight:600">Jenis Surat</td>
                        <td style="color:#2d3748">Surat Keterangan Sakit</td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Nama Pegawai</td>
                        <td style="color:#2d3748"><?= $s->nama ?: '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">NIP</td>
                        <td style="color:#2d3748"><code><?= $s->nip ?></code></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Jenis</td>
                        <td style="color:#2d3748" class="text-capitalize"><?= $s->jenis ?></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <table class="table table-borderless table-sm">
                    <tr>
                        <td style="width:170px;color:#a0aec0;font-weight:600">Tanggal Surat</td>
                        <td style="color:#2d3748"><?= $s->tanggal_surat ? date('d F Y', strtotime($s->tanggal_surat)) : '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Tanggal Izin</td>
                        <td style="color:#2d3748"><?= $s->tanggal_izin ? date('d F Y', strtotime($s->tanggal_izin)) : '-' ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Penandatangan</td>
                        <td style="color:#2d3748"><?= $s->penandatangan ?></td>
                    </tr>
                    <tr>
                        <td style="color:#a0aec0;font-weight:600">Diajukan</td>
                        <td style="color:#2d3748"><?= $s->created_at ? date('d F Y H:i', strtotime($s->created_at)) : '-' ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="mb-4">
            <h5 style="color:#3182ce;font-weight:700;border-bottom:2px solid #ebf4ff;padding-bottom:8px">
                <iconify-icon icon="mdi:note-text-outline" class="mr-2"></iconify-icon>Alasan / Keterangan
            </h5>
            <p style="color:#4a5568;margin:12px 0 0"><?= nl2br(htmlspecialchars($s->alasan, ENT_QUOTES, 'UTF-8')) ?></p>
        </div>

        <form action="<?= site_url('surat/update_nomor/' . $s->id) ?>" method="POST">
            <div class="form-group col-md-6 px-0">
                <label>Nomor Surat <span class="text-danger">*</span></label>
                <input type="text" name="nomor_surat" class="form-control" value="<?= htmlspecialchars($s->nomor_surat ?: '', ENT_QUOTES, 'UTF-8') ?>" required>
            </div>

            <hr>
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('surat') ?>" class="btn btn-secondary">
                    <iconify-icon icon="mdi:arrow-left" class="mr-1"></iconify-icon> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <iconify-icon icon="mdi:content-save-outline" class="mr-1"></iconify-icon> Simpan Nomor Surat
                </button>
            </div>
        </form>
    </div>
</div>
