</div><!-- /.main-content -->

<!-- Footer -->
<footer class="main-footer">
    <div class="d-flex justify-content-between align-items-center">
        <span>&copy; <?= date('Y') ?> SIMPEG - Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu Kota Tomohon</span>
        <span>v1.0</span>
    </div>
</footer>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- Bootstrap 4 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    <?php
    $flash_notifications = array(
        'success' => array('icon' => 'success', 'title' => 'Berhasil!', 'timer' => 2000),
        'error' => array('icon' => 'error', 'title' => 'Gagal!', 'timer' => 2500),
        'warning' => array('icon' => 'warning', 'title' => 'Perhatian!', 'timer' => 2500),
        'info' => array('icon' => 'info', 'title' => 'Informasi', 'timer' => 2500),
    );
    ?>
    <?php foreach ($flash_notifications as $flash_key => $flash_config): ?>
        <?php if ($this->session->flashdata($flash_key)): ?>
            Swal.fire({
                icon: '<?= $flash_config['icon'] ?>',
                title: '<?= $flash_config['title'] ?>',
                text: '<?= $this->session->flashdata($flash_key) ?>',
                showConfirmButton: false,
                timer: <?= $flash_config['timer'] ?>,
                toast: true,
                position: 'top-end'
            });
        <?php endif; ?>
    <?php endforeach; ?>
</script>

</body>

</html>
