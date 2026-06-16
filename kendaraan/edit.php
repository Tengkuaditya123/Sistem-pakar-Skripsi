<?php
include "../koneksi.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data kendaraan
$stmt_select = mysqli_prepare($conn, "SELECT * FROM kendaraan WHERE id_kendaraan = ?");
mysqli_stmt_bind_param($stmt_select, "i", $id);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
$kdr = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt_select);

if (!$kdr) {
    header("Location: index.php?status=gagal");
    exit;
}

$error = '';
if (isset($_POST['submit'])) {
    $kode_kendaraan = strtoupper(trim($_POST['kode_kendaraan']));
    $nama_kendaraan = trim($_POST['nama_kendaraan']);
    $keterangan = trim($_POST['keterangan']);

    if (empty($kode_kendaraan) || empty($nama_kendaraan)) {
        $error = 'Kode kendaraan dan Nama kendaraan harus diisi!';
    } else {
        // Cek keunikan kode kendaraan
        $check_stmt = mysqli_prepare($conn, "SELECT id_kendaraan FROM kendaraan WHERE kode_kendaraan = ? AND id_kendaraan != ?");
        mysqli_stmt_bind_param($check_stmt, "si", $kode_kendaraan, $id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = 'Kode kendaraan sudah digunakan oleh kendaraan lain.';
        } else {
            // Update data kendaraan
            $stmt_update = mysqli_prepare($conn, "UPDATE kendaraan SET kode_kendaraan = ?, nama_kendaraan = ?, keterangan = ? WHERE id_kendaraan = ?");
            mysqli_stmt_bind_param($stmt_update, "sssi", $kode_kendaraan, $nama_kendaraan, $keterangan, $id);

            if (mysqli_stmt_execute($stmt_update)) {
                header("Location: index.php?status=sukses_edit");
                exit;
            } else {
                $error = 'Gagal memperbarui data kendaraan.';
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
        <h2>Edit Data Kendaraan</h2>
        <p class="text-muted">Perbarui Data Kendaraan</p>
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
                    <input type="text" name="kode_kendaraan" id="kode_kendaraan" class="form-control" placeholder="Contoh: ANOA-6X6-04" required value="<?php echo htmlspecialchars(isset($_POST['kode_kendaraan']) ? $_POST['kode_kendaraan'] : $kdr['kode_kendaraan']); ?>">
                </div>

                <div class="mb-3">
                    <label for="nama_kendaraan" class="form-label fw-medium">Nama Kendaraan</label>
                    <input type="text" name="nama_kendaraan" id="nama_kendaraan" class="form-control" placeholder="Contoh: Panser Anoa APC 6x6" required value="<?php echo htmlspecialchars(isset($_POST['nama_kendaraan']) ? $_POST['nama_kendaraan'] : $kdr['nama_kendaraan']); ?>">
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label fw-medium">Keterangan / Fungsi Kendaraan</label>
                    <textarea name="keterangan" id="keterangan" class="form-control" rows="3" placeholder="Masukkan keterangan singkat kendaraan..."><?php echo htmlspecialchars(isset($_POST['keterangan']) ? $_POST['keterangan'] : $kdr['keterangan']); ?></textarea>
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
