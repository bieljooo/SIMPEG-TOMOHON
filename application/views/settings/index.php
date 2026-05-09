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
                        <div class="settings-photo-overlay" aria-hidden="true">
                            <span class="settings-photo-guide settings-photo-guide-v-one"></span>
                            <span class="settings-photo-guide settings-photo-guide-v-two"></span>
                            <span class="settings-photo-guide settings-photo-guide-h-one"></span>
                            <span class="settings-photo-guide settings-photo-guide-h-two"></span>
                            <span class="settings-photo-overlay-copy">Drag untuk menyesuaikan foto</span>
                        </div>
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

                    <input type="hidden" name="foto_posisi" id="foto_posisi" value="<?= htmlspecialchars($current_position, ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
        </div>
    </div>

    <div class="card settings-card">
        <div class="card-header">
            <h3><iconify-icon icon="lucide:shield-check" class="mr-2"></iconify-icon>Keamanan &amp; Password</h3>
            <p>Pastikan Anda menggunakan password yang kuat dan unik untuk menjaga keamanan akun.</p>
        </div>
        <div class="card-body">
            <div class="settings-form-grid">
                <div class="settings-form-col-span">
                    <div class="form-group mb-0">
                        <label for="current_password"><iconify-icon icon="lucide:key-round" class="mr-1"></iconify-icon>Password Saat Ini</label>
                        <input type="password" name="current_password" id="current_password" class="form-control" placeholder="Masukkan password saat ini">
                    </div>
                </div>
                <div>
                    <div class="form-group mb-0">
                        <label for="new_password"><iconify-icon icon="lucide:lock-keyhole" class="mr-1"></iconify-icon>Password Baru</label>
                        <input type="password" name="new_password" id="new_password" class="form-control" placeholder="Minimal 8 karakter">
                        <div class="settings-form-note">Password harus mengandung kombinasi huruf dan angka.</div>
                    </div>
                </div>
                <div>
                    <div class="form-group mb-0">
                        <label for="confirm_password"><iconify-icon icon="lucide:check-check" class="mr-1"></iconify-icon>Konfirmasi Password Baru</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Ulangi password baru">
                    </div>
                </div>
            </div>

            <div class="settings-actions">
                <button type="submit" class="btn btn-primary">
                    <iconify-icon icon="lucide:save"></iconify-icon>
                    <span>Simpan Perubahan</span>
                </button>
                <a href="<?= $home_url ?>" class="btn btn-secondary">Batal</a>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var form = document.getElementById('settingsForm');
        var fileInput = document.getElementById('foto_profil');
        var removeButton = document.getElementById('removePhotoButton');
        var removeInput = document.getElementById('hapus_foto');
        var preview = document.getElementById('photoPreview');
        var previewText = document.getElementById('photoInitial');
        var fileName = document.getElementById('photoFileName');
        var positionInput = document.getElementById('foto_posisi');
        var defaultLetter = preview.getAttribute('data-default-letter') || 'U';
        var dragState = {
            active: false,
            pointerId: null
        };

        function clamp(value, min, max) {
            return Math.min(max, Math.max(min, value));
        }

        function formatPercent(value) {
            return (Math.round(value * 100) / 100).toString().replace(/\.0+$/, '').replace(/(\.\d*[1-9])0+$/, '$1') + '%';
        }

        function parsePhotoPosition(value) {
            var normalized = (value || '').toString().trim().toLowerCase();
            var legacyMap = {
                'left top': {
                    x: 0,
                    y: 0
                },
                'center top': {
                    x: 50,
                    y: 0
                },
                'right top': {
                    x: 100,
                    y: 0
                },
                'left center': {
                    x: 0,
                    y: 50
                },
                'center center': {
                    x: 50,
                    y: 50
                },
                'right center': {
                    x: 100,
                    y: 50
                },
                'left bottom': {
                    x: 0,
                    y: 100
                },
                'center bottom': {
                    x: 50,
                    y: 100
                },
                'right bottom': {
                    x: 100,
                    y: 100
                }
            };

            if (legacyMap[normalized]) {
                return legacyMap[normalized];
            }

            var match = normalized.match(/^(\d{1,3}(?:\.\d+)?)%\s+(\d{1,3}(?:\.\d+)?)%$/);
            if (match) {
                return {
                    x: clamp(parseFloat(match[1]), 0, 100),
                    y: clamp(parseFloat(match[2]), 0, 100)
                };
            }

            return {
                x: 50,
                y: 50
            };
        }

        function updatePhotoPosition(x, y) {
            var posX = clamp(x, 0, 100);
            var posY = clamp(y, 0, 100);
            var cssPosition = formatPercent(posX) + ' ' + formatPercent(posY);

            if (positionInput) {
                positionInput.value = cssPosition;
            }

            preview.style.backgroundPosition = cssPosition;

        }

        function setPreviewImage(imageUrl) {
            preview.classList.add('has-image');
            preview.style.backgroundImage = 'url("' + imageUrl + '")';
            preview.setAttribute('data-has-photo', '1');
            updatePhotoPosition(50, 50);
            previewText.style.display = 'none';
            if (removeButton) {
                removeButton.style.display = '';
            }
        }

        function resetPreview() {
            preview.classList.remove('has-image');
            preview.style.backgroundImage = '';
            preview.setAttribute('data-has-photo', '0');
            updatePhotoPosition(50, 50);
            previewText.textContent = defaultLetter;
            previewText.style.display = '';
            fileName.textContent = 'Belum ada foto profil.';
            if (removeButton) {
                removeButton.style.display = 'none';
            }
        }

        function movePhotoFromPointer(event) {
            var rect = preview.getBoundingClientRect();
            var posX = ((event.clientX - rect.left) / rect.width) * 100;
            var posY = ((event.clientY - rect.top) / rect.height) * 100;

            updatePhotoPosition(posX, posY);
        }

        function stopPhotoDrag() {
            if (!dragState.active) {
                return;
            }

            dragState.active = false;
            preview.classList.remove('is-dragging');

            if (dragState.pointerId !== null && preview.releasePointerCapture) {
                try {
                    preview.releasePointerCapture(dragState.pointerId);
                } catch (error) {}
            }

            dragState.pointerId = null;
        }

        if (preview) {
            preview.addEventListener('pointerdown', function(event) {
                if (!preview.classList.contains('has-image')) {
                    return;
                }

                dragState.active = true;
                dragState.pointerId = event.pointerId;
                preview.classList.add('is-dragging');

                if (preview.setPointerCapture) {
                    preview.setPointerCapture(event.pointerId);
                }

                movePhotoFromPointer(event);
            });

            preview.addEventListener('pointermove', function(event) {
                if (!dragState.active) {
                    return;
                }

                movePhotoFromPointer(event);
            });

            preview.addEventListener('pointerup', stopPhotoDrag);
            preview.addEventListener('pointercancel', stopPhotoDrag);
            preview.addEventListener('lostpointercapture', stopPhotoDrag);
        }

        if (fileInput) {
            fileInput.addEventListener('change', function() {
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
                reader.onload = function(event) {
                    setPreviewImage(event.target.result);
                };
                reader.readAsDataURL(file);
            });
        }

        if (removeButton) {
            removeButton.addEventListener('click', function() {
                if (fileInput) {
                    fileInput.value = '';
                }

                removeInput.value = '1';
                resetPreview();
            });
        }

        var initialPosition = parsePhotoPosition(positionInput ? positionInput.value : '50% 50%');
        updatePhotoPosition(initialPosition.x, initialPosition.y);

        if (form) {
            form.addEventListener('submit', function(event) {
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
                }).then(function(result) {
                    if (result.isConfirmed) {
                        form.dataset.confirmed = '1';
                        form.submit();
                    }
                });
            });
        }
    });
</script>