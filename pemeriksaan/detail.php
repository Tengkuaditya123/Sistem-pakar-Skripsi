<?php
include "../koneksi.php";
include "../templates/header.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard/index.php");
    exit;
}

$id_pemeriksaan = isset($_GET['id']) ? intval($_GET['id']) : 0;

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
    echo "<div class='alert alert-danger'>Data detail pemeriksaan tidak ditemukan!</div>";
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
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1">Detail QC Rantis</h2>
            <p class="text-muted small">Laporan lengkap sertifikasi kelayakan Rantis</p>
        </div>
        <div>
            <a href="laporan.php" class="btn btn-outline-secondary btn-sm px-3 rounded me-2">
                <i class="bi bi-chevron-left"></i> Kembali ke Laporan
            </a>
            <a href="print.php?id=<?php echo $id_pemeriksaan; ?>" target="_blank" class="btn btn-primary btn-sm px-3 rounded">
                <i class="bi bi-printer"></i> Cetak Laporan
            </a>
        </div>
    </div>
</div>

<div class="panel-container">
    <div class="row">
        <!-- Status Box -->
        <div class="col-md-12 mb-4">
            <?php if ($pem['status_qc'] == 'LOLOS'): ?>
                <div class="p-4 rounded-3 text-center border" style="background-color: #d1e7dd; border-color: #a3cfbb !important; color: #0f5132;">
                    <h5 class="fw-bold mb-1">HASIL SERTIFIKASI QUALITY CONTROL</h5>
                    <h1 class="fw-bold display-6 my-2 text-success">LOLOS QC</h1>
                    <p class="mb-0 small">Kendaraan taktis dinyatakan layak jalan berdasarkan penilaian part</p>
                </div>
            <?php else: ?>
                <div class="p-4 rounded-3 text-center border" style="background-color: #f8d7da; border-color: #f5c2c7 !important; color: #842029;">
                    <h5 class="fw-bold mb-1">HASIL SERTIFIKASI QUALITY CONTROL</h5>
                    <h1 class="fw-bold display-6 my-2 text-danger">TIDAK LOLOS QC</h1>
                    <p class="mb-0 small">Ditemukan indikator gejala kerusakan. Kendaraan wajib melakukan perbaikan.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Detail Table -->
        <div class="col-md-6 mb-4">
            <h5 class="fw-bold border-bottom pb-2">Informasi Rantis & Pemeriksa</h5>
            <table class="table table-borderless align-middle">
                <tr>
                    <th width="35%">Kode Rantis</th>
                    <td>: <strong class="text-primary"><?php echo htmlspecialchars($pem['kode_kendaraan']); ?></strong></td>
                </tr>
                <tr>
                    <th>Nama Rantis</th>
                    <td>: <?php echo htmlspecialchars($pem['nama_kendaraan']); ?></td>
                </tr>
                <tr>
                    <th>Bagian / Part</th>
                    <td>: <span class="badge bg-info text-dark"><?php echo htmlspecialchars($pem['kode_part']); ?></span> <strong><?php echo htmlspecialchars($pem['nama_part']); ?></strong></td>
                </tr>
                <tr>
                    <th>Tanggal Periksa</th>
                    <td>: <?php echo htmlspecialchars($pem['tanggal_pemeriksaan']); ?></td>
                </tr>
                <tr>
                    <th>Petugas QC</th>
                    <td>: <strong class="text-secondary"><?php echo htmlspecialchars($pem['nama_petugas']); ?></strong></td>
                </tr>
            </table>
        </div>

        <!-- Symptoms Box -->
        <div class="col-md-6 mb-4">
            <h5 class="fw-bold border-bottom pb-2">Gejala Terdeteksi</h5>
            <div class="p-3 bg-light rounded border" style="max-height: 250px; overflow-y: auto;">
                <?php if (mysqli_num_rows($query_gejala) > 0): ?>
                    <ul class="mb-0" style="padding-left: 20px;">
                        <?php while ($gj = mysqli_fetch_assoc($query_gejala)): ?>
                            <li class="mb-2">
                                <strong style="color: #6f42c1;"><?php echo htmlspecialchars($gj['kode_gejala']); ?></strong> - <?php echo htmlspecialchars($gj['nama_gejala']); ?>
                            </li>
                        <?php endwhile; ?>
                    </ul>
                <?php else: ?>
                    <p class="text-success fw-semibold mb-0"><i class="bi bi-shield-check-fill me-2"></i> Aman. Tidak ada gejala kerusakan terdeteksi.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Diagnosis Box -->
        <div class="col-md-12 mb-4">
            <h5 class="fw-bold border-bottom pb-2">Diagnosa Kerusakan (Sistem Pakar Forward Chaining)</h5>
            <div class="p-3 bg-white border rounded shadow-sm" style="white-space: pre-line;">
                <?php echo htmlspecialchars($pem['diagnosa']); ?>
            </div>
        </div>

        <!-- Solution Box -->
        <div class="col-md-12">
            <h5 class="fw-bold border-bottom pb-2">Rekomendasi Tindakan / Solusi</h5>
            <div class="p-3 bg-light border border-info-subtle rounded text-primary-emphasis" style="white-space: pre-line;">
                <?php echo htmlspecialchars($pem['solusi']); ?>
            </div>
        </div>
    </div>
</div>

<?php
include "../templates/footer.php";
?>
