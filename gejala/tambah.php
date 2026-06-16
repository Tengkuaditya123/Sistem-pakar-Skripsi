<?php
include "../koneksi.php";

$error = '';
if (isset($_POST['submit'])) {
    $kode_gejala = strtoupper(trim($_POST['kode_gejala']));
    $nama_gejala = trim($_POST['nama_gejala']);

    if (empty($kode_gejala) || empty($nama_gejala)) {
        $error = 'Kode gejala dan Nama gejala harus diisi!';
    } else {
        // Cek jika kode gejala sudah terdaftar
        $check_stmt = mysqli_prepare($conn, "SELECT id_gejala FROM gejala WHERE kode_gejala = ?");
        mysqli_stmt_bind_param($check_stmt, "s", $kode_gejala);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = 'Kode Gejala sudah terdaftar, silakan gunakan kode lain.';
        } else {
            // Simpan data gejala baru
            $stmt = mysqli_prepare($conn, "INSERT INTO gejala (kode_gejala, nama_gejala) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ss", $kode_gejala, $nama_gejala);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: index.php?status=sukses_tambah");
                exit;
            } else {
                $error = 'Gagal menyimpan data gejala.';
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
        <h2>Tambah Gejala</h2>
        <p class="text-muted">Tambahkan gejala baru</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow-sm border-0 p-4">
            <?php if ($error != ''): ?>
                <div class="alert alert-danger animate__animated animate__fadeIn" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="mb-3">
                    <label for="kode_gejala" class="form-label fw-medium">Kode Gejala</label>
                    <input type="text" name="kode_gejala" id="kode_gejala" class="form-control" <?php echo isset($_POST['kode_gejala']) ? htmlspecialchars($_POST['kode_gejala']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="nama_gejala" class="form-label fw-medium">Deskripsi Gejala Kerusakan</label>
                    <textarea name="nama_gejala" id="nama_gejala" class="form-control" rows="3" placeholder="masukan keterangan gejala baru..." required><?php echo isset($_POST['nama_gejala']) ? htmlspecialchars($_POST['nama_gejala']) : ''; ?></textarea>
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
