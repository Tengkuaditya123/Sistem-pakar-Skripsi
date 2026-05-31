<?php
include "../koneksi.php";

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data gejala
$stmt_select = mysqli_prepare($conn, "SELECT * FROM gejala WHERE id_gejala = ?");
mysqli_stmt_bind_param($stmt_select, "i", $id);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
$gj = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt_select);

if (!$gj) {
    header("Location: index.php?status=gagal");
    exit;
}

$error = '';
if (isset($_POST['submit'])) {
    $kode_gejala = strtoupper(trim($_POST['kode_gejala']));
    $nama_gejala = trim($_POST['nama_gejala']);

    if (empty($kode_gejala) || empty($nama_gejala)) {
        $error = 'Kode gejala dan Nama gejala harus diisi!';
    } else {
        // Cek keunikan kode gejala
        $check_stmt = mysqli_prepare($conn, "SELECT id_gejala FROM gejala WHERE kode_gejala = ? AND id_gejala != ?");
        mysqli_stmt_bind_param($check_stmt, "si", $kode_gejala, $id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = 'Kode gejala sudah terdaftar pada gejala lain.';
        } else {
            // Update data gejala
            $stmt_update = mysqli_prepare($conn, "UPDATE gejala SET kode_gejala = ?, nama_gejala = ? WHERE id_gejala = ?");
            mysqli_stmt_bind_param($stmt_update, "ssi", $kode_gejala, $nama_gejala, $id);

            if (mysqli_stmt_execute($stmt_update)) {
                header("Location: index.php?status=sukses_edit");
                exit;
            } else {
                $error = 'Gagal memperbarui data gejala.';
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
        <h2>Edit Data Gejala</h2>
        <p class="text-muted">Perbarui gejala kerusakan rantis</p>
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
                    <label for="kode_gejala" class="form-label fw-medium">Kode Gejala</label>
                    <input type="text" name="kode_gejala" id="kode_gejala" class="form-control" placeholder="Contoh: G11" required value="<?php echo htmlspecialchars(isset($_POST['kode_gejala']) ? $_POST['kode_gejala'] : $gj['kode_gejala']); ?>">
                </div>

                <div class="mb-3">
                    <label for="nama_gejala" class="form-label fw-medium">Deskripsi Gejala Kerusakan</label>
                    <textarea name="nama_gejala" id="nama_gejala" class="form-control" rows="3" placeholder="Contoh: Deskripsi gejala..." required><?php echo htmlspecialchars(isset($_POST['nama_gejala']) ? $_POST['nama_gejala'] : $gj['nama_gejala']); ?></textarea>
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
