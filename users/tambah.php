<?php
include "../koneksi.php";

$error = '';
if (isset($_POST['submit'])) {
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Validasi input
    if (empty($nama) || empty($username) || empty($password) || empty($role)) {
        $error = 'Semua field wajib diisi!';
    } else {
        // Cek jika username sudah ada
        $check_stmt = mysqli_prepare($conn, "SELECT id_user FROM users WHERE username = ?");
        mysqli_stmt_bind_param($check_stmt, "s", $username);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);
        
        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = 'Username sudah terdaftar, silakan gunakan username lain.';
        } else {
            // Insert user baru dengan Prepared Statement
            $stmt = mysqli_prepare($conn, "INSERT INTO users (nama, username, password, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssss", $nama, $username, $password, $role);
            
            if (mysqli_stmt_execute($stmt)) {
                header("Location: index.php?status=sukses_tambah");
                exit;
            } else {
                $error = 'Gagal menyimpan data pengguna.';
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
        <h2>Tambah Pengguna Baru</h2>
        <p class="text-muted">Tambahkan admin baru atau petugas QC lapangan</p>
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
                    <label for="nama" class="form-label fw-medium">Nama Lengkap</label>
                    <input type="text" name="nama" id="nama" class="form-control" placeholder="Masukkan nama lengkap" required value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label fw-medium">Username</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-medium">Password</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password" required>
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label fw-medium">Role Hak Akses</label>
                    <select name="role" id="role" class="form-select" required>
                        <option value="" disabled selected>-- Pilih Role --</option>
                        <option value="admin" <?php echo (isset($_POST['role']) && $_POST['role'] == 'admin') ? 'selected' : ''; ?>>Admin / SPV QC</option>
                        <option value="petugas_qc" <?php echo (isset($_POST['role']) && $_POST['role'] == 'petugas_qc') ? 'selected' : ''; ?>>Petugas QC</option>
                    </select>
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
