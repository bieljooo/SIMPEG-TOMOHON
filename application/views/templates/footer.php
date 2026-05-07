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
<!-- DataTables -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>
<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Init DataTables
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "language": {
                "search": "Cari:",
                "lengthMenu": "Tampilkan _MENU_ data",
                "info": "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                "infoEmpty": "Tidak ada data",
                "infoFiltered": "(difilter dari _MAX_ total data)",
                "zeroRecords": "Data tidak ditemukan",
                "paginate": {
                    "first": "Awal",
                    "last": "Akhir",
                    "next": "Selanjutnya",
                    "previous": "Sebelumnya"
                }
            },
            "pageLength": 10,
            "responsive": true
        });
    });

    // SweetAlert Delete Confirmation
    function confirmDelete(url, nama) {
        Swal.fire({
            title: 'Hapus Data?',
            html: 'Anda yakin ingin menghapus data <strong>' + nama + '</strong>?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e53e3e',
            cancelButtonColor: '#a0aec0',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    function confirmApproval(formId, nama) {
        Swal.fire({
            title: 'Setujui Data?',
            html: 'Setujui data pegawai <strong>' + nama + '</strong>?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#38a169',
            cancelButtonColor: '#a0aec0',
            confirmButtonText: 'Ya, Setujui!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById(formId).submit();
            }
        });
    }

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
