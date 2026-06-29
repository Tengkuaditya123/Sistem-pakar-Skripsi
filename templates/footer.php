        </div> <!-- End of container-fluid -->
        
        <!-- Footer -->
        <footer>
            <div class="container-fluid">
                <span>&copy; <?php echo date('Y'); ?> T.M. Aditya Ramadhan - Sistem Pakar QC Check.</span>
            </div>
        </footer>
    </div> <!-- End of content -->

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="logoutModalLabel">Logout</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-0">Yakin ingin keluar dari sistem?</p>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-end gap-2 pb-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                <a href="<?php echo $base_url; ?>logout.php" class="btn btn-primary px-4 fw-semibold">Logout</a>
            </div>
        </div>
    </div>
</div>

<!-- Global Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold" id="confirmDeleteModalLabel">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-0" id="confirmDeleteMessage">Yakin ingin menghapus data ini?</p>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-end gap-2 pb-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-primary px-4 fw-semibold">Hapus</a>
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
                var message = $(this).attr('data-message') || 'Yakin ingin menghapus data ini?';
                
                $('#confirmDeleteMessage').text(message);
                $('#confirmDeleteBtn').attr('href', href);
                
                var deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
                deleteModal.show();
            });
        });
    </script>
</body>
</html>
