<?php
include "../koneksi.php";
include "../templates/header.php";

$id_kendaraan = isset($_GET['id_kendaraan']) ? intval($_GET['id_kendaraan']) : 0;

// Query detail kendaraan
$stmt_kdr = mysqli_prepare($conn, "SELECT * FROM kendaraan WHERE id_kendaraan = ?");
mysqli_stmt_bind_param($stmt_kdr, "i", $id_kendaraan);
mysqli_stmt_execute($stmt_kdr);
$result_kdr = mysqli_stmt_get_result($stmt_kdr);
$kdr = mysqli_fetch_assoc($result_kdr);
mysqli_stmt_close($stmt_kdr);

if (!$kdr) {
    echo "<div class='alert alert-danger'>Data kendaraan tidak ditemukan!</div>";
    include "../templates/footer.php";
    exit;
}

// Ambil semua data part kendaraan
$query_parts = mysqli_query($conn, "SELECT * FROM part_kendaraan ORDER BY kode_part ASC");
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="fw-bold mb-1">Pilih Part Kendaraan</h2>
        <p class="text-muted small">Tahap 2: Pilih Part Kendaraan Yang Akan Diperiksa</p>
    </div>
</div>

<!-- Info Rantis Card -->
<div class="card shadow-sm border-0 mb-4 bg-light border-start border-primary border-3">
    <div class="card-body p-3">
        <h5 class="fw-bold mb-2 text-primary"><i class="bi bi-info-circle-fill"></i> Detail Kendaraan:</h5>
        <div class="row">
            <div class="col-md-3">
                <span class="text-muted small">Kode Rantis</span>
                <p class="fw-bold mb-0 text-primary-emphasis"><?php echo htmlspecialchars($kdr['kode_kendaraan']); ?></p>
            </div>
            <div class="col-md-4">
                <span class="text-muted small">Nama Rantis</span>
                <p class="fw-semibold mb-0"><?php echo htmlspecialchars($kdr['nama_kendaraan']); ?></p>
            </div>
            <div class="col-md-5">
                <span class="text-muted small">Keterangan</span>
                <p class="text-muted mb-0 small"><?php echo htmlspecialchars($kdr['keterangan'] ?: '-'); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Parts Table -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white py-3">
        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-gear-wide-connected me-2"></i> Daftar Komponen Utama</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th width="80px" class="ps-4">No</th>
                        <th width="150px">Kode Part</th>
                        <th>Nama Part</th>
                        <th width="200px" class="text-center pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if (mysqli_num_rows($query_parts) > 0):
                        while ($part = mysqli_fetch_assoc($query_parts)): 
                            ?>
                            <tr>
                                <td class="ps-4"><?php echo $no++; ?></td>
                                <td><span class="badge bg-secondary px-2 py-1"><?php echo htmlspecialchars($part['kode_part']); ?></span></td>
                                <td class="fw-bold"><?php echo htmlspecialchars($part['nama_part']); ?></td>
                                <td class="text-center pe-4">
                                    <a href="proses.php?id_kendaraan=<?php echo $id_kendaraan; ?>&id_part=<?php echo $part['id_part']; ?>" class="btn btn-success btn-sm px-3 rounded d-inline-flex align-items-center gap-1">
                                        Mulai Uji <i class="bi bi-play-fill"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php 
                        endwhile; 
                    else:
                        ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted p-4">Tidak ada data part kendaraan terdaftar.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="index.php" class="btn btn-outline-secondary">
        <i class="bi bi-chevron-left me-1"></i> Kembali ke Pilih Kendaraan
    </a>
</div>

<?php
include "../templates/footer.php";
?>
