<!-- Breadcrumb -->
<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= site_url('dashboard') ?>">Home</a></li>
        <li class="breadcrumb-item active">Surat Sakit</li>
    </ol>
</nav>

<?php $p = $pegawai; ?>
<?php $ttd = $penandatangan; ?>

<div class="card">
    <div class="card-header">
        <h3><iconify-icon icon="mdi:file-document-plus-outline" class="mr-2"></iconify-icon>Form Surat Keterangan Sakit</h3>
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
            Setelah surat dibuat, file Word bisa langsung diunduh pada menu <strong>Download Surat</strong>.
        </div>

        <form action="<?= site_url('pengajuan_surat/simpan_surat_keterangan_sakit') ?>" method="POST">
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Jenis <span class="text-danger">*</span></label>
                    <select name="jenis" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <option value="pagi">Pagi</option>
                        <option value="sore">Sore</option>
                        <option value="1 hari">1 Hari</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-3">
                    <label>Hari Surat</label>
                    <input type="text" id="hari_surat" class="form-control" readonly style="background:#edf2f7">
                </div>
                <div class="form-group col-md-3">
                    <label>Tanggal Surat <span class="text-danger">*</span></label>
                    <input type="date" id="tanggal_surat" name="tanggal_surat" class="form-control" required>
                </div>
                <div class="form-group col-md-3">
                    <label>Hari Izin</label>
                    <input type="text" id="hari_izin" class="form-control" readonly style="background:#edf2f7">
                </div>
                <div class="form-group col-md-3">
                    <label>Tanggal Izin <span class="text-danger">*</span></label>
                    <input type="date" id="tanggal_izin" name="tanggal_izin" class="form-control" required>
                </div>
            </div>

            <div class="form-group">
                <label>Alasan / Keterangan <span class="text-danger">*</span></label>
                <textarea name="alasan" class="form-control" rows="4" required></textarea>
            </div>

            <div class="form-group">
                <label>Penandatangan <span class="text-danger">*</span></label>
                <input type="hidden" name="penandatangan_nip" value="<?= htmlspecialchars($ttd->nip, ENT_QUOTES, 'UTF-8') ?>">
                <input type="text" class="form-control" value="<?= htmlspecialchars($ttd->nama, ENT_QUOTES, 'UTF-8') ?>" readonly style="background:#edf2f7">
            </div>

            <hr>
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('dashboard') ?>" class="btn btn-cancel-action">
                    <iconify-icon icon="mdi:arrow-left" class="mr-1"></iconify-icon> Kembali
                </a>
                <button type="submit" class="btn btn-primary">
                    <iconify-icon icon="mdi:file-word-box" class="mr-1"></iconify-icon> Buat Surat
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        function bindHariField(tanggalId, hariId) {
            var tanggalInput = document.getElementById(tanggalId);
            var hariInput = document.getElementById(hariId);

            if (!tanggalInput || !hariInput) {
                return;
            }

            function updateHari() {
                if (!tanggalInput.value) {
                    hariInput.value = '';
                    return;
                }

                var tanggal = new Date(tanggalInput.value + 'T00:00:00');

                if (isNaN(tanggal.getTime())) {
                    hariInput.value = '';
                    return;
                }

                hariInput.value = namaHari[tanggal.getDay()];
            }

            tanggalInput.addEventListener('change', updateHari);
            updateHari();
        }

        bindHariField('tanggal_surat', 'hari_surat');
        bindHariField('tanggal_izin', 'hari_izin');
    });
</script>
