<?php
include "../koneksi.php";
include "../templates/header.php";

$id_pemeriksaan = isset($_GET['id_pemeriksaan']) ? intval($_GET['id_pemeriksaan']) : 0;

// Query detail hasil pemeriksaan
$stmt_pem = mysqli_prepare($conn, "
    SELECT p.*, k.kode_kendaraan, k.nama_kendaraan, pt.kode_part, pt.nama_part, u.nama as nama_petugas
    FROM pemeriksaan p 
    JOIN kendaraan k ON p.id_kendaraan = k.id_kendaraan 
    JOIN part_kendaraan pt ON p.id_part = pt.id_part 
    JOIN users u ON p.id_user = u.id_user 
    WHERE p.id_pemeriksaan = ?
");
mysqli_stmt_bind_param($stmt_pem, "i", $id_pemeriksaan);
mysqli_stmt_execute($stmt_pem);
$result_pem = mysqli_stmt_get_result($stmt_pem);
$pem = mysqli_fetch_assoc($result_pem);
mysqli_stmt_close($stmt_pem);

if (!$pem) {
    echo "<div class='alert alert-danger'>Data hasil pemeriksaan tidak ditemukan!</div>";
    include "../templates/footer.php";
    exit;
}

// Query gejala yang terdeteksi pada pemeriksaan ini
$query_gejala = mysqli_query($conn, "
    SELECT g.* 
    FROM pemeriksaan_gejala pg 
    JOIN gejala g ON pg.id_gejala = g.id_gejala 
    WHERE pg.id_pemeriksaan = $id_pemeriksaan 
    ORDER BY g.kode_gejala ASC
");
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="fw-bold mb-1">Hasil Diagnosa QC</h2>
        <p class="text-muted small">Hasil uji kelayakan part rantis oleh sistem pakar</p>
    </div>
</div>

<div class="row">
    <!-- Status Box -->
    <div class="col-md-12 mb-4">
        <?php if ($pem['status_qc'] == 'LOLOS'): ?>
            <div class="p-4 rounded-3 text-center border" style="background-color: #d1e7dd; border-color: #a3cfbb !important; color: #0f5132;">
                <h5 class="fw-bold mb-1">HASIL SERTIFIKASI QUALITY CONTROL</h5>
                <h1 class="fw-bold display-5 my-2 text-success"><i class="bi bi-patch-check-fill me-2"></i> LOLOS QC</h1>
                <p class="mb-0 small fw-medium">Sistem menyatakan part/komponen kendaraan rantis sesuai standart.</p>
            </div>
        <?php else: ?>
            <div class="p-4 rounded-3 text-center border" style="background-color: #f8d7da; border-color: #f5c2c7 !important; color: #842029;">
                <h5 class="fw-bold mb-1">HASIL SERTIFIKASI QUALITY CONTROL</h5>
                <h1 class="fw-bold display-5 my-2 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i> TIDAK LOLOS QC</h1>
                <p class="mb-0 small fw-medium">Ditemukan indikator gejala kerusakan. Sistem menyarankan tindakan perbaikan segera.</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Info Detail Rantis -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-dark text-white py-3">
                <h6 class="card-title mb-0 fw-bold"><i class="bi bi-info-circle me-2"></i> Informasi Rantis & Penguji</h6>
            </div>
            <div class="card-body p-4">
                <table class="table table-borderless align-middle mb-0">
                    <tr>
                        <th>Tipe Kendaraan</th>
                        <td>: <?php echo htmlspecialchars($pem['nama_kendaraan']); ?></td>
                    </tr>
                    <tr>
                        <th>Part Teruji</th>
                        <td>: <span class="badge bg-info text-dark"><?php echo htmlspecialchars($pem['kode_part']); ?></span> <strong><?php echo htmlspecialchars($pem['nama_part']); ?></strong></td>
                    </tr>
                    <tr>
                        <th>Tanggal Pengujian</th>
                        <td>: <?php echo htmlspecialchars($pem['tanggal_pemeriksaan']); ?></td>
                    </tr>
                    <tr>
                        <th>Petugas Penguji</th>
                        <td>: <strong class="text-secondary"><?php echo htmlspecialchars($pem['nama_petugas']); ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Info Gejala Terdeteksi -->
    <div class="col-md-6 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-primary text-white py-3">
                <h6 class="card-title mb-0 fw-bold"><i class="bi bi-list-check me-2"></i> Gejala Terpilih</h6>
            </div>
            <div class="card-body p-4 bg-light">
                <div style="max-height: 180px; overflow-y: auto;">
                    <?php if (mysqli_num_rows($query_gejala) > 0): ?>
                        <ul class="mb-0 py-1" style="padding-left: 20px;">
                            <?php while ($gj = mysqli_fetch_assoc($query_gejala)): ?>
                                <li class="mb-2">
                                    <strong style="color: #6f42c1;"><?php echo htmlspecialchars($gj['kode_gejala']); ?></strong> - <?php echo htmlspecialchars($gj['nama_gejala']); ?>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-success fw-bold mb-0 py-2"><i class="bi bi-shield-check-fill me-1"></i> Tidak ada gejala yang terdeteksi pada part kendaraan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Diagnosa Box -->
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-secondary text-white py-3">
                <h6 class="card-title mb-0 fw-bold"><i class="bi bi-cpu me-2"></i> Kesimpulan Diagnosa Sistem</h6>
            </div>
            <div class="card-body p-4 bg-white" style="white-space: pre-line; line-height: 1.6; font-weight: 500;">
                <?php echo htmlspecialchars($pem['diagnosa']); ?>
            </div>
        </div>
    </div>

    <!-- Solusi Box -->
    <div class="col-md-12 mb-4">
        <div class="card shadow-sm border-0 border-start border-3 border-info">
            <div class="card-header bg-info-subtle text-primary-emphasis py-3 border-0">
                <h6 class="card-title mb-0 fw-bold"><i class="bi bi-wrench me-2"></i> Rekomendasi Solusi </h6>
            </div>
            <div class="card-body p-4 bg-white text-primary-emphasis" style="white-space: pre-line; line-height: 1.6;">
                <?php echo htmlspecialchars($pem['solusi']); ?>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mt-3">
    <a href="index.php" class="btn btn-outline-primary fw-semibold">
        <i class="bi bi-arrow-repeat me-1"></i> Pemeriksaan Baru
    </a>
    <div>
        <a href="print.php?id=<?php echo $id_pemeriksaan; ?>" target="_blank" class="btn btn-secondary fw-semibold me-2">
            <i class="bi bi-printer-fill me-1"></i> Cetak Laporan
        </a>
        <a href="riwayat.php" class="btn btn-primary fw-semibold">
            <i class="bi bi-clock-history me-1"></i> Riwayat QC Saya
        </a>
    </div>
</div>

<?php
include "../templates/footer.php";
?>
