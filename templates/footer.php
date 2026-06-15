        </div> <!-- End of container-fluid -->
        
        <!-- Footer -->
        <footer>
            <div class="container-fluid">
                <span>&copy; <?php echo date('Y'); ?> Sistem Pakar QC Check - All rights reserved.</span>
            </div>
        </footer>
    </div> <!-- End of content -->

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="logoutModalLabel"><i class="bi bi-box-arrow-right me-2"></i>Konfirmasi Logout</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-question-circle text-warning display-4 mb-3 d-block"></i>
                <h5 class="fw-bold mb-2">Apakah Anda yakin ingin logout?</h5>
                <p class="text-muted small mb-0">Sesi Anda akan berakhir dan Anda harus masuk kembali untuk menggunakan sistem.</p>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <a href="<?php echo $base_url; ?>logout.php" class="btn btn-danger px-4 fw-semibold">Ya, Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- Global Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold" id="confirmDeleteModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="bi bi-trash text-danger display-4 mb-3 d-block"></i>
                <h5 class="fw-bold mb-2" id="confirmDeleteMessage">Apakah Anda yakin ingin menghapus data ini?</h5>
                <p class="text-muted small mb-0">Tindakan ini tidak dapat dibatalkan setelah data berhasil dihapus.</p>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-center gap-2 pb-4">
                <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger px-4 fw-semibold">Ya, Hapus</a>
            </div>
        </div>
    </div>
</div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <!-- Bootstrap Bundle with Popper JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>

    <!-- Auto-init DataTables -->
    <script>
        $(document).ready(function() {
            if ($('.datatable').length) {
                $('.datatable').DataTable({
                    "language": {
                        "lengthMenu": "Show _MENU_ entries",
                        "search": "Search:",
                        "zeroRecords": "Tidak ada data ditemukan",
                        "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                        "infoEmpty": "Showing 0 to 0 of 0 entries",
                        "infoFiltered": "(filtered from _MAX_ total entries)",
                        "paginate": {
                            "first": "First",
                            "last": "Last",
                            "next": "Next",
                            "previous": "Previous"
                        }
                    },
                    "pageLength": 10,
                    "responsive": true
                });
            }

            // Interceptor for delete buttons using Bootstrap 5 Modal
            $(document).on('click', '.btn-confirm-delete', function(e) {
                e.preventDefault();
                var href = $(this).attr('href');
                var message = $(this).attr('data-message') || 'Apakah Anda yakin ingin menghapus data ini?';
                
                $('#confirmDeleteMessage').text(message);
                $('#confirmDeleteBtn').attr('href', href);
                
                var deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                deleteModal.show();
            });
        });
    </script>
</body>
</html>
