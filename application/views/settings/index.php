<?php
$account_name = isset($account->nama) ? $account->nama : $account->nama_lengkap;
$profile_photo = !empty($account->foto_profil) ? base_url($account->foto_profil) : '';
$initial_letter = strtoupper(substr($account_name, 0, 1));
?>

<nav class="breadcrumb-wrapper">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= $home_url ?>">Home</a></li>
        <li class="breadcrumb-item active">Pengaturan</li>
    </ol>
</nav>

<form action="<?= site_url('settings/update') ?>" method="POST" enctype="multipart/form-data" id="settingsForm" class="settings-layout">
    <div class="card settings-card">
        <div class="card-header">
            <h3><iconify-icon icon="mdi:account-circle-outline" class="mr-2"></iconify-icon>Foto Profil</h3>
            <p>Perbarui foto profil Anda agar mudah dikenali oleh anggota tim lain.</p>
        </div>
        <div class="card-body">
            <div class="settings-profile-row">
                <div class="settings-photo-preview-wrap">
                    <div
                        id="photoPreview"
                        class="settings-photo-preview <?= $profile_photo ? 'has-image' : '' ?>"
                        data-default-letter="<?= $initial_letter ?>"
                        data-has-photo="<?= $profile_photo ? '1' : '0' ?>"
                        style="<?= $profile_photo ? "background-image:url('{$profile_photo}');background-position:" . htmlspecialchars($current_position, ENT_QUOTES, 'UTF-8') . ";" : '' ?>">
                        <span id="photoInitial" <?= $profile_photo ? 'style="display:none"' : '' ?>><?= $initial_letter ?></span>
                        <span class="settings-photo-grid"></span>
                    </div>
                </div>
                <div>
                    <div class="settings-photo-actions">
                        <label class="btn btn-secondary btn-sm mb-0">
                            <iconify-icon icon="mdi:upload-outline"></iconify-icon>
                            <span>Unggah Foto Baru</span>
                            <input type="file" name="foto_profil" id="foto_profil" accept=".jpg,.jpeg,.png,.gif" hidden>
                        </label>
                        <button type="button" class="settings-remove-btn" id="removePhotoButton" <?= $profile_photo ? '' : 'style="display:none"' ?>>Hapus</button>
                        <input type="hidden" name="hapus_foto" id="hapus_foto" value="0">
                    </div>
                    <div class="settings-photo-meta">
                        <div id="photoFileName"><?= $profile_photo ? 'Foto profil aktif.' : 'Belum ada foto profil.' ?></div>
                        <div>Gunakan format JPG, JPEG, GIF, atau PNG. Ukuran maksimal 1MB.</div>
                    </div>

                    <div class="settings-position-block">
                        <strong>Posisi Foto</strong>
                        <div class="settings-position-grid">
                            <?php foreach ($position_options as $position): ?>
                                <label class="settings-position-option" title="<?= ucwords($position) ?>">
                                    <input type="radio" name="foto_posisi" value="<?= $position ?>" <?= ($current_position === $position) ? 'checked' : '' ?>>
                                    <span></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card settings-card">
        <div class="card-header">
            <h3><iconify-icon icon="mdi:shield-lock-outline" class="mr-2"></iconify-icon>Keamanan &amp; Password</h3>
            <p>Pastikan Anda menggunakan password yang kuat dan unik untuk menjaga keamanan akun.</p>
        </div>
        <div class="card-body">
            <div class="settings-form-grid">
                <div class="settings-form-col-span">
                    <div class="form-group mb-0">
                        <label for="current_password">Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Masukkan password saat ini">
                    </div>
                </div>
                <div>
                    <div class="form-group mb-0">
                        <label for="new_password">Password Baru</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Minimal 8 karakter">
                        <div class="settings-form-note">Password harus mengandung kombinasi huruf dan angka.</div>
                    </div>
                </div>
                <div>
                    <div class="form-group mb-0">
                        <label for="confirm_password">Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Ulangi password baru">
                    </div>
                </div>
            </div>

            <div class="settings-actions">
                <button type="submit" class="btn btn-primary">
                    <iconify-icon icon="mdi:content-save-outline"></iconify-icon>
                    <span>Simpan Perubahan</span>
                </button>
                <a href="<?= $home_url ?>" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var form = document.getElementById('settingsForm');
    var fileInput = document.getElementById('foto_profil');
    var removeButton = document.getElementById('removePhotoButton');
    var removeInput = document.getElementById('hapus_foto');
    var preview = document.getElementById('photoPreview');
    var previewText = document.getElementById('photoInitial');
    var fileName = document.getElementById('photoFileName');
    var defaultLetter = preview.getAttribute('data-default-letter') || 'U';

    function setPreviewImage(imageUrl) {
        preview.classList.add('has-image');
        preview.style.backgroundImage = 'url("' + imageUrl + '")';
        previewText.style.display = 'none';
        if (removeButton) {
            removeButton.style.display = '';
        }
    }

    function resetPreview() {
        preview.classList.remove('has-image');
        preview.style.backgroundImage = '';
        previewText.textContent = defaultLetter;
        previewText.style.display = '';
        fileName.textContent = 'Belum ada foto profil.';
        if (removeButton) {
            removeButton.style.display = 'none';
        }
    }

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            var file = this.files && this.files[0] ? this.files[0] : null;

            if (!file) {
                return;
            }

            if (file.size > 1048576) {
                this.value = '';
                Swal.fire({
                    icon: 'warning',
                    title: 'File Terlalu Besar',
                    text: 'Ukuran foto profil maksimal 1 MB.'
                });
                return;
            }

            removeInput.value = '0';
            fileName.textContent = file.name;

            var reader = new FileReader();
            reader.onload = function (event) {
                setPreviewImage(event.target.result);
            };
            reader.readAsDataURL(file);
        });
    }

    if (removeButton) {
        removeButton.addEventListener('click', function () {
            if (fileInput) {
                fileInput.value = '';
            }

            removeInput.value = '1';
            resetPreview();
        });
    }

    document.querySelectorAll('input[name="foto_posisi"]').forEach(function (radio) {
        radio.addEventListener('change', function () {
            preview.style.backgroundPosition = this.value;
        });
    });

    if (form) {
        form.addEventListener('submit', function (event) {
            var currentPassword = document.getElementById('current_password').value.trim();
            var newPassword = document.getElementById('new_password').value.trim();
            var confirmPassword = document.getElementById('confirm_password').value.trim();
            var passwordWillChange = currentPassword !== '' || newPassword !== '' || confirmPassword !== '';

            if (!passwordWillChange || form.dataset.confirmed === '1') {
                return;
            }

            event.preventDefault();

            Swal.fire({
                icon: 'question',
                title: 'Anda Yakin?',
                text: 'Password akun akan langsung diperbarui.',
                showCancelButton: true,
                confirmButtonText: 'Ya, Ubah Password',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#5b6475',
                cancelButtonColor: '#9ca3af'
            }).then(function (result) {
                if (result.isConfirmed) {
                    form.dataset.confirmed = '1';
                    form.submit();
                }
            });
        });
    }
});
</script>
