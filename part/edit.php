<?php
include "../koneksi.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data part
$stmt_select = mysqli_prepare($conn, "SELECT * FROM part_kendaraan WHERE id_part = ?");
mysqli_stmt_bind_param($stmt_select, "i", $id);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
$part = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt_select);

if (!$part) {
    header("Location: index.php?status=gagal");
    exit;
}

$error = '';
if (isset($_POST['submit'])) {
    $kode_part = strtoupper(trim($_POST['kode_part']));
    $nama_part = trim($_POST['nama_part']);

    if (empty($kode_part) || empty($nama_part)) {
        $error = 'Kode part dan Nama part harus diisi!';
    } else {
        // Cek keunikan kode part
        $check_stmt = mysqli_prepare($conn, "SELECT id_part FROM part_kendaraan WHERE kode_part = ? AND id_part != ?");
        mysqli_stmt_bind_param($check_stmt, "si", $kode_part, $id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = 'Kode part sudah terdaftar pada part lain.';
        } else {
            // Update data part
            $stmt_update = mysqli_prepare($conn, "UPDATE part_kendaraan SET kode_part = ?, nama_part = ? WHERE id_part = ?");
            mysqli_stmt_bind_param($stmt_update, "ssi", $kode_part, $nama_part, $id);

            if (mysqli_stmt_execute($stmt_update)) {
                header("Location: index.php?status=sukses_edit");
                exit;
            } else {
                $error = 'Gagal memperbarui data part.';
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
        <h2>Edit Part Kendaraan</h2>
        <p class="text-muted">Perbarui data part atau sub-sistem kendaraan taktis (Rantis)</p>
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
                    <label for="kode_part" class="form-label fw-medium">Kode Part</label>
                    <input type="text" name="kode_part" id="kode_part" class="form-control" placeholder="Contoh: P06" required value="<?php echo htmlspecialchars(isset($_POST['kode_part']) ? $_POST['kode_part'] : $part['kode_part']); ?>">
                </div>

                <div class="mb-3">
                    <label for="nama_part" class="form-label fw-medium">Nama Part / Sistem</label>
                    <input type="text" name="nama_part" id="nama_part" class="form-control" placeholder="Contoh: Sistem Suspensi Hidrolik" required value="<?php echo htmlspecialchars(isset($_POST['nama_part']) ? $_POST['nama_part'] : $part['nama_part']); ?>">
                </div>

                <div class="mt-4 text-end">
                    <button type="submit" name="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save"></i> Perbarui
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include "../templates/footer.php";
?>
