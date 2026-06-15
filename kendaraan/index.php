<?php
include "../koneksi.php";
include "../templates/header.php";

// Ambil semua data kendaraan untuk DataTables
$query_kendaraan = mysqli_query($conn, "SELECT * FROM kendaraan ORDER BY id_kendaraan DESC");
?>

<div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1">Kelola Data Kendaraan</h2>
            <p class="text-muted small">Manajemen kendaraan taktis (Rantis) yang terdaftar</p>
        </div>
        <a href="tambah.php" class="btn btn-primary btn-sm px-3 rounded d-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i> Tambah Kendaraan
        </a>
    </div>
</div>

<!-- Alert Success/Error -->
<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] == 'sukses_tambah'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Data kendaraan berhasil ditambahkan!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($_GET['status'] == 'sukses_edit'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Data kendaraan berhasil diperbarui!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($_GET['status'] == 'sukses_hapus'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Data kendaraan berhasil dihapus!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($_GET['status'] == 'gagal'): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Terjadi kesalahan saat memproses data.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Table Section -->
<div class="panel-container">
    <div class="table-responsive">
        <table class="datatable table table-hover align-middle table-borderless w-100">
            <thead>
                <tr>
                    <th width="60px">No</th>
                    <th>Kode Kendaraan</th>
                    <th>Nama Kendaraan</th>
                    <th>Keterangan / Fungsi</th>
                    <th width="120px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                if (mysqli_num_rows($query_kendaraan) > 0) {
                    while ($kdr = mysqli_fetch_assoc($query_kendaraan)) {
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td class="fw-bold text-primary"><?php echo htmlspecialchars($kdr['kode_kendaraan']); ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($kdr['nama_kendaraan']); ?></td>
                            <td><?php echo htmlspecialchars($kdr['keterangan'] ?: '-'); ?></td>
                            <td>
                                <div class="btn-action-group">
                                    <!-- Aksi: Hapus di kiri (merah), Edit di kanan (biru) sesuai mockup -->
                                    <a href="hapus.php?id=<?php echo $kdr['id_kendaraan']; ?>" class="btn btn-danger btn-sm p-1 px-2 btn-confirm-delete" data-message="Apakah Anda yakin ingin menghapus kendaraan ini?" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                    
                                    <a href="edit.php?id=<?php echo $kdr['id_kendaraan']; ?>" class="btn btn-primary btn-sm p-1 px-2" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include "../templates/footer.php";
?>
