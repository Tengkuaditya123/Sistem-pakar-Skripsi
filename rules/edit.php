<?php
include "../koneksi.php";

$kode_rule = isset($_GET['kode']) ? trim($_GET['kode']) : '';

// Ambil data-data rule berdasarkan kode_rule
$stmt_select = mysqli_prepare($conn, "SELECT * FROM rules WHERE kode_rule = ?");
mysqli_stmt_bind_param($stmt_select, "s", $kode_rule);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);

$existing_symptoms = [];
$rule_meta = null;

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        if ($rule_meta === null) {
            $rule_meta = $row; // Ambil row pertama untuk meta data part, keputusan, status
        }
        $existing_symptoms[] = $row['id_gejala'];
    }
}
mysqli_stmt_close($stmt_select);

if (!$rule_meta) {
    header("Location: index.php?status=gagal");
    exit;
}

// Ambil list gejala untuk checkboxes
$gejala_list = mysqli_query($conn, "SELECT * FROM gejala ORDER BY kode_gejala ASC");

// Ambil list part untuk dropdown
$part_list = mysqli_query($conn, "SELECT * FROM part_kendaraan ORDER BY kode_part ASC");

$error = '';
if (isset($_POST['submit'])) {
    $id_part = intval($_POST['id_part']);
    $keputusan = trim($_POST['keputusan']);
    $status = $_POST['status'];
    $selected_gejala = isset($_POST['gejala']) ? $_POST['gejala'] : []; // Array of id_gejala

    if ($id_part <= 0 || empty($keputusan) || empty($status) || empty($selected_gejala)) {
        $error = 'Semua field wajib diisi dan minimal 1 gejala harus dipilih!';
    } else {
        // Hapus rule lama dengan kode_rule ini terlebih dahulu
        $stmt_del = mysqli_prepare($conn, "DELETE FROM rules WHERE kode_rule = ?");
        mysqli_stmt_bind_param($stmt_del, "s", $kode_rule);
        mysqli_stmt_execute($stmt_del);
        mysqli_stmt_close($stmt_del);

        // Simpan rule terupdate (satu baris untuk setiap gejala terpilih)
        $success = true;
        $stmt_insert = mysqli_prepare($conn, "INSERT INTO rules (kode_rule, id_gejala, id_part, keputusan, status) VALUES (?, ?, ?, ?, ?)");
        
        foreach ($selected_gejala as $id_g) {
            $id_g_int = intval($id_g);
            mysqli_stmt_bind_param($stmt_insert, "siiss", $kode_rule, $id_g_int, $id_part, $keputusan, $status);
            if (!mysqli_stmt_execute($stmt_insert)) {
                $success = false;
            }
        }
        mysqli_stmt_close($stmt_insert);

        if ($success) {
            header("Location: index.php?status=sukses_edit");
            exit;
        } else {
            $error = 'Gagal memperbarui data rule.';
        }
    }
}

include "../templates/header.php";
?>

<div class="row mb-4">
    <div class="col-md-8 mx-auto">
        <a href="index.php" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Daftar
        </a>
        <h2>Edit Rule: <?php echo htmlspecialchars($kode_rule); ?></h2>
        <p class="text-muted">Perbarui pemetaan gejala, part kendaraan, keputusan diagnosa, atau status uji kelayakan</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0 p-4">
            <?php if ($error != ''): ?>
                <div class="alert alert-danger" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Kode Rule</label>
                    <input type="text" class="form-control bg-light" value="<?php echo htmlspecialchars($kode_rule); ?>" disabled readonly>
                </div>

                <div class="mb-3">
                    <label for="id_part" class="form-label fw-semibold">Pilih Part Kendaraan Terkait (THEN)</label>
                    <select name="id_part" id="id_part" class="form-select" required>
                        <?php $active_part = isset($_POST['id_part']) ? intval($_POST['id_part']) : $rule_meta['id_part']; ?>
                        <?php while ($pt = mysqli_fetch_assoc($part_list)): ?>
                            <option value="<?php echo $pt['id_part']; ?>" <?php echo ($active_part == $pt['id_part']) ? 'selected' : ''; ?>>
                                [<?php echo htmlspecialchars($pt['kode_part']); ?>] <?php echo htmlspecialchars($pt['nama_part']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label fw-semibold">Status Uji Kelayakan</label>
                    <select name="status" id="status" class="form-select" required>
                        <?php $active_status = isset($_POST['status']) ? $_POST['status'] : $rule_meta['status']; ?>
                        <option value="TIDAK LOLOS" <?php echo ($active_status == 'TIDAK LOLOS') ? 'selected' : ''; ?>>TIDAK LOLOS</option>
                        <option value="LOLOS" <?php echo ($active_status == 'LOLOS') ? 'selected' : ''; ?>>LOLOS</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="keputusan" class="form-label fw-semibold">Diagnosa Kerusakan</label>
                    <textarea name="keputusan" id="keputusan" class="form-control" rows="3" required><?php echo htmlspecialchars(isset($_POST['keputusan']) ? $_POST['keputusan'] : $rule_meta['keputusan']); ?></textarea>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold d-block">Pilih Gejala Kerusakan (IF - Logika OR)</label>
                    <p class="text-muted small">Pilih gejala apa saja yang jika muncul akan memicu rule ini.</p>
                    <div class="border rounded p-3 bg-light" style="max-height: 250px; overflow-y: auto;">
                        <?php if (mysqli_num_rows($gejala_list) > 0): ?>
                            <?php while ($gj = mysqli_fetch_assoc($gejala_list)): ?>
                                <?php 
                                $is_checked = false;
                                if (isset($_POST['gejala'])) {
                                    $is_checked = in_array($gj['id_gejala'], $_POST['gejala']);
                                } else {
                                    $is_checked = in_array($gj['id_gejala'], $existing_symptoms);
                                }
                                ?>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="gejala[]" value="<?php echo $gj['id_gejala']; ?>" id="gj_<?php echo $gj['id_gejala']; ?>" <?php echo $is_checked ? 'checked' : ''; ?>>
                                    <label class="form-check-label text-dark fw-medium small" style="cursor: pointer;" for="gj_<?php echo $gj['id_gejala']; ?>">
                                        <span class="badge bg-warning text-dark me-1"><?php echo htmlspecialchars($gj['kode_gejala']); ?></span> - <?php echo htmlspecialchars($gj['nama_gejala']); ?>
                                    </label>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-muted mb-0 small">Belum ada data gejala terdaftar.</p>
                        <?php endif; ?>
                    </div>
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
