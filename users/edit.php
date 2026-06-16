<?php
include "../koneksi.php";

// Ambil ID dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data user saat ini
$stmt_select = mysqli_prepare($conn, "SELECT * FROM users WHERE id_user = ?");
mysqli_stmt_bind_param($stmt_select, "i", $id);
mysqli_stmt_execute($stmt_select);
$result = mysqli_stmt_get_result($stmt_select);
$user_data = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt_select);

if (!$user_data) {
    header("Location: index.php?status=gagal");
    exit;
}

$error = '';
if (isset($_POST['submit'])) {
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = $_POST['password']; // opsional jika ganti
    $role = $_POST['role'];

    if (empty($nama) || empty($username) || empty($role)) {
        $error = 'Nama, Username, dan Role harus diisi!';
    } else {
        // Cek username unik di luar dirinya sendiri
        $check_stmt = mysqli_prepare($conn, "SELECT id_user FROM users WHERE username = ? AND id_user != ?");
        mysqli_stmt_bind_param($check_stmt, "si", $username, $id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $error = 'Username sudah digunakan oleh pengguna lain.';
        } else {
            if (!empty($password)) {
                // Update dengan password baru
                $stmt_update = mysqli_prepare($conn, "UPDATE users SET nama = ?, username = ?, password = ?, role = ? WHERE id_user = ?");
                mysqli_stmt_bind_param($stmt_update, "ssssi", $nama, $username, $password, $role, $id);
            } else {
                // Update tanpa password baru
                $stmt_update = mysqli_prepare($conn, "UPDATE users SET nama = ?, username = ?, role = ? WHERE id_user = ?");
                mysqli_stmt_bind_param($stmt_update, "sssi", $nama, $username, $role, $id);
            }

            if (mysqli_stmt_execute($stmt_update)) {
                header("Location: index.php?status=sukses_edit");
                exit;
            } else {
                $error = 'Gagal memperbarui data pengguna.';
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
        <h2>Edit Data Pengguna</h2>
        <p class="text-muted">Perbarui Data User</p>
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
                    <input type="text" name="nama" id="nama" class="form-control" placeholder="Masukkan nama lengkap" required value="<?php echo htmlspecialchars(isset($_POST['nama']) ? $_POST['nama'] : $user_data['nama']); ?>">
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label fw-medium">Username</label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username" required value="<?php echo htmlspecialchars(isset($_POST['username']) ? $_POST['username'] : $user_data['username']); ?>">
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label fw-medium">Password (Kosongkan jika tidak ingin diubah)</label>
                    <input type="password" name="password" id="password" class="form-control" placeholder="Masukkan password baru">
                </div>

                <div class="mb-3">
                    <label for="role" class="form-label fw-medium">Role Hak Akses</label>
                    <select name="role" id="role" class="form-select" required>
                        <?php $selected_role = isset($_POST['role']) ? $_POST['role'] : $user_data['role']; ?>
                        <option value="admin" <?php echo ($selected_role == 'admin') ? 'selected' : ''; ?>>Admin / SPV QC</option>
                        <option value="petugas_qc" <?php echo ($selected_role == 'petugas_qc') ? 'selected' : ''; ?>>Petugas QC</option>
                    </select>
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
