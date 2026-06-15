<?php
include "../koneksi.php";

$error = '';
if (isset($_POST['submit'])) {
    $kode_part = strtoupper(trim($_POST['kode_part']));
    $nama_part = trim($_POST['nama_part']);

    if (empty($kode_part) || empty($nama_part)) {
        $error = 'Kode part dan Nama part harus diisi!';
    } else {
        // Cek jika kode part sudah terdaftar
        $check_stmt = mysqli_prepare($conn, "SELECT id_part FROM part_kendaraan WHERE kode_part = ?");
        mysqli_stmt_bind_param($check_stmt, "s", $kode_part);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = 'Kode Part sudah terdaftar, silakan gunakan kode lain.';
        } else {
            // Simpan data part baru
            $stmt = mysqli_prepare($conn, "INSERT INTO part_kendaraan (kode_part, nama_part) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ss", $kode_part, $nama_part);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: index.php?status=sukses_tambah");
                exit;
            } else {
                $error = 'Gagal menyimpan data part.';
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
        <h2>Tambah Part Kendaraan Baru</h2>
        <p class="text-muted">Tambahkan part kendaraan baru</p>
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
                    <input type="text" name="kode_part" id="kode_part" class="form-control" placeholder="Contoh: P06" required value="<?php echo isset($_POST['kode_part']) ? htmlspecialchars($_POST['kode_part']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="nama_part" class="form-label fw-medium">Nama Part / Sistem</label>
                    <input type="text" name="nama_part" id="nama_part" class="form-control" placeholder="Contoh: Sistem Suspensi Hidrolik" required value="<?php echo isset($_POST['nama_part']) ? htmlspecialchars($_POST['nama_part']) : ''; ?>">
                </div>

                <div class="mt-4 text-end">
                    <button type="reset" class="btn btn-light me-2">Reset</button>
                    <button type="submit" name="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
include "../templates/footer.php";
?>
