<?php
include "../koneksi.php";

// Ambil list gejala untuk dropdown
$gejala_list = mysqli_query($conn, "SELECT * FROM gejala ORDER BY kode_gejala ASC");

// Ambil list part untuk dropdown
$part_list = mysqli_query($conn, "SELECT * FROM part_kendaraan ORDER BY kode_part ASC");

$error = '';
if (isset($_POST['submit'])) {
    $id_gejala = intval($_POST['id_gejala']);
    $id_part = intval($_POST['id_part']);
    $keputusan = trim($_POST['keputusan']);

    if ($id_gejala <= 0 || $id_part <= 0 || empty($keputusan)) {
        $error = 'Semua field wajib dipilih/diisi!';
    } else {
        // Cek jika rule pemetaan ini sudah ada (opsional, tapi disarankan)
        $check_stmt = mysqli_prepare($conn, "SELECT id_rule FROM rules WHERE id_gejala = ? AND id_part = ?");
        mysqli_stmt_bind_param($check_stmt, "ii", $id_gejala, $id_part);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = 'Kombinasi Gejala dan Part ini sudah terdaftar sebagai Rule. Silakan edit rule yang sudah ada.';
        } else {
            // Simpan rule baru
            $stmt = mysqli_prepare($conn, "INSERT INTO rules (id_gejala, id_part, keputusan) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "iis", $id_gejala, $id_part, $keputusan);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: index.php?status=sukses_tambah");
                exit;
            } else {
                $error = 'Gagal menyimpan data rule.';
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_stmt_close($check_stmt);
    }
}

include "../templates/header.php";
?>

<div class="row mb-4">
    <div class="col-md-8 mx-auto">
        <a href="index.php" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
        <h2>Tambah Rule Baru</h2>
        <p class="text-muted">Buat pemetaan diagnosis forward chaining baru (IF Gejala THEN Part + Keputusan)</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0 p-4">
            <?php if ($error != ''): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="id_gejala" class="form-label fw-medium">Pilih Gejala (IF)</label>
                    <select name="id_gejala" id="id_gejala" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Gejala --</option>
                        <?php while ($gj = mysqli_fetch_assoc($gejala_list)): ?>
                            <option value="<?php echo $gj['id_gejala']; ?>" <?php echo (isset($_POST['id_gejala']) && $_POST['id_gejala'] == $gj['id_gejala']) ? 'selected' : ''; ?>>
                                [<?php echo htmlspecialchars($gj['kode_gejala']); ?>] <?php echo htmlspecialchars($gj['nama_gejala']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="id_part" class="form-label fw-medium">Pilih Part Kendaraan Terkait (THEN)</label>
                    <select name="id_part" id="id_part" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Part Kendaraan --</option>
                        <?php while ($pt = mysqli_fetch_assoc($part_list)): ?>
                            <option value="<?php echo $pt['id_part']; ?>" <?php echo (isset($_POST['id_part']) && $_POST['id_part'] == $pt['id_part']) ? 'selected' : ''; ?>>
                                [<?php echo htmlspecialchars($pt['kode_part']); ?>] <?php echo htmlspecialchars($pt['nama_part']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="keputusan" class="form-label fw-medium">Keputusan / Diagnosis Hasil Pemeriksaan</label>
                    <textarea name="keputusan" id="keputusan" class="form-control" rows="3" placeholder="Contoh: Terjadi kebocoran minyak rem atau ada udara di sistem pengereman" required><?php echo isset($_POST['keputusan']) ? htmlspecialchars($_POST['keputusan']) : ''; ?></textarea>
                </div>

                <div class="mt-4 text-end">
                    <button type="reset" class="btn btn-light me-2">Reset</button>
                    <button type="submit" name="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save"></i> Simpan Rule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include "../templates/footer.php";
?>
