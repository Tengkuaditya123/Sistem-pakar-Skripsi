<?php
include "../koneksi.php";
include "../templates/header.php";

// Ambil semua data gejala untuk DataTables
$query_gejala = mysqli_query($conn, "SELECT * FROM gejala ORDER BY id_gejala DESC");
?>

<div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1">Kelola Data Gejala</h2>
            <p class="text-muted small">Manajemen gejala kerusakan rantis dalam sistem pakar</p>
        </div>
        <a href="tambah.php" class="btn btn-primary btn-sm px-3 rounded d-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i> Tambah Gejala
        </a>
    </div>
</div>

<!-- Alert Success/Error -->
<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] == 'sukses_tambah'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Data gejala berhasil ditambahkan!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($_GET['status'] == 'sukses_edit'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Data gejala berhasil diperbarui!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($_GET['status'] == 'sukses_hapus'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Data gejala berhasil dihapus!
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
                    <th width="150px">Kode Gejala</th>
                    <th>Nama Gejala</th>
                    <th width="120px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                if (mysqli_num_rows($query_gejala) > 0) {
                    while ($gj = mysqli_fetch_assoc($query_gejala)) {
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td class="fw-bold text-primary"><?php echo htmlspecialchars($gj['kode_gejala']); ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($gj['nama_gejala']); ?></td>
                            <td>
                                <div class="btn-action-group">
                                    <!-- Aksi: Hapus di kiri, Edit di kanan -->
                                    <a href="hapus.php?id=<?php echo $gj['id_gejala']; ?>" class="btn btn-danger btn-sm p-1 px-2" onclick="return confirm('Apakah Anda yakin ingin menghapus gejala ini? Semua rule terkait akan ikut terhapus.');" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                    
                                    <a href="edit.php?id=<?php echo $gj['id_gejala']; ?>" class="btn btn-primary btn-sm p-1 px-2" title="Edit">
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
