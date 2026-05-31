<?php
include "../koneksi.php";
include "../templates/header.php";

// Query data rule dengan JOIN ke gejala dan part_kendaraan untuk DataTables
$query_rules = mysqli_query($conn, "
    SELECT r.*, g.kode_gejala, g.nama_gejala, p.kode_part, p.nama_part 
    FROM rules r 
    JOIN gejala g ON r.id_gejala = g.id_gejala 
    JOIN part_kendaraan p ON r.id_part = p.id_part 
    ORDER BY r.id_rule DESC
");
?>

<div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1">Kelola Rule Forward Chaining</h2>
            <p class="text-muted small">Manajemen aturan pemetaan gejala kerusakan ke part rantis terkait</p>
        </div>
        <a href="tambah.php" class="btn btn-primary btn-sm px-3 rounded d-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i> Tambah Rule
        </a>
    </div>
</div>

<!-- Alert Success/Error -->
<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] == 'sukses_tambah'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Rule berhasil disimpan!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($_GET['status'] == 'sukses_edit'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Rule berhasil diperbarui!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($_GET['status'] == 'sukses_hapus'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Rule berhasil dihapus!
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
                    <th>Gejala (IF)</th>
                    <th>Part Terkait (THEN)</th>
                    <th>Keputusan / Diagnosis</th>
                    <th width="120px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                if (mysqli_num_rows($query_rules) > 0) {
                    while ($rule = mysqli_fetch_assoc($query_rules)) {
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td>
                                <span class="badge bg-warning text-dark me-1"><?php echo htmlspecialchars($rule['kode_gejala']); ?></span>
                                <?php echo htmlspecialchars($rule['nama_gejala']); ?>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark me-1"><?php echo htmlspecialchars($rule['kode_part']); ?></span>
                                <span class="fw-semibold"><?php echo htmlspecialchars($rule['nama_part']); ?></span>
                            </td>
                            <td class="fw-semibold text-danger"><?php echo htmlspecialchars($rule['keputusan']); ?></td>
                            <td>
                                <div class="btn-action-group">
                                    <!-- Aksi: Hapus di kiri (merah), Edit di kanan (biru) sesuai mockup -->
                                    <a href="hapus.php?id=<?php echo $rule['id_rule']; ?>" class="btn btn-danger btn-sm p-1 px-2" onclick="return confirm('Apakah Anda yakin ingin menghapus rule ini?');" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                    
                                    <a href="edit.php?id=<?php echo $rule['id_rule']; ?>" class="btn btn-primary btn-sm p-1 px-2" title="Edit">
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
