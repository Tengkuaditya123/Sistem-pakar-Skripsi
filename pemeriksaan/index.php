<?php
include "../koneksi.php";
include "../templates/header.php";

// Ambil data kendaraan
$query_kendaraan = mysqli_query($conn, "SELECT * FROM kendaraan ORDER BY nama_kendaraan ASC");
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="fw-bold mb-1">Pemeriksaan QC</h2>
        <p class="text-muted small">Mulai pengujian kelayakan kendaraan taktis rantis</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="card-title mb-0 fw-bold"><i class="bi bi-truck me-2"></i> Pilih Kendaraan Rantis</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted small mb-4">Silakan pilih unit kendaraan taktis (Rantis) dari daftar di bawah ini untuk memulai audit komponen / part.</p>
                
                <form method="GET" action="pilih_part.php">
                    <div class="mb-4">
                        <label for="id_kendaraan" class="form-label fw-semibold">Pilih Unit Rantis</label>
                        <select name="id_kendaraan" id="id_kendaraan" class="form-select form-select-lg" required>
                            <option value="" disabled selected>-- Pilih Unit Rantis --</option>
                            <?php while ($kdr = mysqli_fetch_assoc($query_kendaraan)): ?>
                                <option value="<?php echo $kdr['id_kendaraan']; ?>">
                                    [<?php echo htmlspecialchars($kdr['kode_kendaraan']); ?>] <?php echo htmlspecialchars($kdr['nama_kendaraan']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4 py-2 fw-semibold">
                            Lanjutkan <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include "../templates/footer.php";
?>
