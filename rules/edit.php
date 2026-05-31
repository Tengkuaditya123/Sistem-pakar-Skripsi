<?php
include "../koneksi.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data rule saat ini
$stmt_select = mysqli_prepare($conn, "SELECT * FROM rules WHERE id_rule = ?");
mysqli_stmt_bind_param($stmt_select, "i", $id);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
$rule_data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt_select);

if (!$rule_data) {
    header("Location: index.php?status=gagal");
    exit;
}

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
        // Cek keunikan rule di luar dirinya sendiri
        $check_stmt = mysqli_prepare($conn, "SELECT id_rule FROM rules WHERE id_gejala = ? AND id_part = ? AND id_rule != ?");
        mysqli_stmt_bind_param($check_stmt, "iii", $id_gejala, $id_part, $id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = 'Kombinasi Gejala dan Part ini sudah digunakan pada rule lain.';
        } else {
            // Update rule
            $stmt_update = mysqli_prepare($conn, "UPDATE rules SET id_gejala = ?, id_part = ?, keputusan = ? WHERE id_rule = ?");
            mysqli_stmt_bind_param($stmt_update, "iiis", $id_gejala, $id_part, $keputusan, $id);

            if (mysqli_stmt_execute($stmt_update)) {
                header("Location: index.php?status=sukses_edit");
                exit;
            } else {
                $error = 'Gagal memperbarui data rule.';
            }
            mysqli_stmt_close($stmt_update);
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
        <h2>Edit Rule</h2>
        <p class="text-muted">Perbarui pemetaan gejala, part kendaraan, atau keputusan diagnosis</p>
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
                        <?php $selected_gejala = isset($_POST['id_gejala']) ? $_POST['id_gejala'] : $rule_data['id_gejala']; ?>
                        <?php while ($gj = mysqli_fetch_assoc($gejala_list)): ?>
                            <option value="<?php echo $gj['id_gejala']; ?>" <?php echo ($selected_gejala == $gj['id_gejala']) ? 'selected' : ''; ?>>
                                [<?php echo htmlspecialchars($gj['kode_gejala']); ?>] <?php echo htmlspecialchars($gj['nama_gejala']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="id_part" class="form-label fw-medium">Pilih Part Kendaraan Terkait (THEN)</label>
                    <select name="id_part" id="id_part" class="form-select" required>
                        <?php $selected_part = isset($_POST['id_part']) ? $_POST['id_part'] : $rule_data['id_part']; ?>
                        <?php while ($pt = mysqli_fetch_assoc($part_list)): ?>
                            <option value="<?php echo $pt['id_part']; ?>" <?php echo ($selected_part == $pt['id_part']) ? 'selected' : ''; ?>>
                                [<?php echo htmlspecialchars($pt['kode_part']); ?>] <?php echo htmlspecialchars($pt['nama_part']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="keputusan" class="form-label fw-medium">Keputusan / Diagnosis Hasil Pemeriksaan</label>
                    <textarea name="keputusan" id="keputusan" class="form-control" rows="3" placeholder="Masukkan keputusan..." required><?php echo htmlspecialchars(isset($_POST['keputusan']) ? $_POST['keputusan'] : $rule_data['keputusan']); ?></textarea>
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" name="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save"></i> Perbarui Rule
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include "../templates/footer.php";
?>
