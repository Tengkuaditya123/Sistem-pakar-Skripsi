<?php
include "../koneksi.php";
include "../templates/header.php";

// Ambil semua data user untuk DataTables
$query_users = mysqli_query($conn, "SELECT * FROM users ORDER BY id_user DESC");
?>

<div class="row mb-4">
    <div class="col-md-12 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1">Kelola Data Pengguna</h2>
            <p class="text-muted small">Manajemen Data User Pengguna</p>
        </div>
        <a href="tambah.php" class="btn btn-primary btn-sm px-3 rounded d-flex align-items-center gap-1">
            <i class="bi bi-plus-lg"></i> Tambah Pengguna
        </a>
    </div>
</div>

<!-- Alert Success/Error -->
<?php if (isset($_GET['status'])): ?>
    <?php if ($_GET['status'] == 'sukses_tambah'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Data pengguna berhasil ditambahkan!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($_GET['status'] == 'sukses_edit'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Data pengguna berhasil diperbarui!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($_GET['status'] == 'sukses_hapus'): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> Data pengguna berhasil dihapus!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif ($_GET['status'] == 'gagal'): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> Terjadi kesalahan saat memproses data.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
<?php endif; ?>

<!-- Table Section -->
<div class="panel-container">
    <div class="table-responsive">
        <table class="datatable table table-hover align-middle table-borderless w-100">
            <thead>
                <tr>
                    <th width="60px">No</th>
                    <th>Nama Lengkap</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Dibuat Pada</th>
                    <th width="120px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                if (mysqli_num_rows($query_users) > 0) {
                    while ($user = mysqli_fetch_assoc($query_users)) {
                        $role_badge = ($user['role'] == 'admin') ? 'bg-primary' : 'bg-success';
                        ?>
                        <tr>
                            <td><?php echo $no++; ?></td>
                            <td class="fw-semibold"><?php echo htmlspecialchars($user['nama']); ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><span class="badge <?php echo $role_badge; ?> text-capitalize"><?php echo htmlspecialchars($user['role']); ?></span></td>
                            <td class="text-muted small"><?php echo htmlspecialchars($user['created_at']); ?></td>
                            <td>
                                <div class="btn-action-group">
                                    <!-- Aksi: Hapus di kiri (merah), Edit di kanan (biru) sesuai mockup -->
                                    <?php if ($user['id_user'] != $_SESSION['id_user']): ?>
                                        <a href="hapus.php?id=<?php echo $user['id_user']; ?>" class="btn btn-danger btn-sm p-1 px-2 btn-confirm-delete" data-message="Yakin ingin menghapus data ini?" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                    <?php else: ?>
                                        <button class="btn btn-secondary btn-sm p-1 px-2" disabled title="Tidak bisa menghapus akun sendiri">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <a href="edit.php?id=<?php echo $user['id_user']; ?>" class="btn btn-primary btn-sm p-1 px-2" title="Edit">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include "../templates/footer.php";
?>
