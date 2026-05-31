<?php
include "../koneksi.php";

$error = '';
if (isset($_POST['submit'])) {
    $kode_kendaraan = strtoupper(trim($_POST['kode_kendaraan']));
    $nama_kendaraan = trim($_POST['nama_kendaraan']);
    $keterangan = trim($_POST['keterangan']);

    if (empty($kode_kendaraan) || empty($nama_kendaraan)) {
        $error = 'Kode kendaraan dan Nama kendaraan harus diisi!';
    } else {
        // Cek jika kode kendaraan sudah terdaftar
        $check_stmt = mysqli_prepare($conn, "SELECT id_kendaraan FROM kendaraan WHERE kode_kendaraan = ?");
        mysqli_stmt_bind_param($check_stmt, "s", $kode_kendaraan);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = 'Kode Kendaraan sudah digunakan, gunakan kode lain.';
        } else {
            // Simpan data kendaraan baru
            $stmt = mysqli_prepare($conn, "INSERT INTO kendaraan (kode_kendaraan, nama_kendaraan, keterangan) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $kode_kendaraan, $nama_kendaraan, $keterangan);

            if (mysqli_stmt_execute($stmt)) {
                header("Location: index.php?status=sukses_tambah");
                exit;
            } else {
                $error = 'Gagal menambahkan data kendaraan.';
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
        <h2>Tambah Kendaraan Baru</h2>
        <p class="text-muted">Tambahkan kendaraan taktis (Rantis) baru ke dalam sistem monitoring</p>
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
                    <label for="kode_kendaraan" class="form-label fw-medium">Kode Kendaraan (No. Registrasi / Lambung)</label>
                    <input type="text" name="kode_kendaraan" id="kode_kendaraan" class="form-control" placeholder="Contoh: ANOA-6X6-04" required value="<?php echo isset($_POST['kode_kendaraan']) ? htmlspecialchars($_POST['kode_kendaraan']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="nama_kendaraan" class="form-label fw-medium">Nama Kendaraan</label>
                    <input type="text" name="nama_kendaraan" id="nama_kendaraan" class="form-control" placeholder="Contoh: Panser Anoa APC 6x6" required value="<?php echo isset($_POST['nama_kendaraan']) ? htmlspecialchars($_POST['nama_kendaraan']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label fw-medium">Keterangan / Fungsi Kendaraan</label>
                    <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Masukkan keterangan singkat kendaraan..."><?php echo isset($_POST['keterangan']) ? htmlspecialchars($_POST['keterangan']) : ''; ?></textarea>
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
