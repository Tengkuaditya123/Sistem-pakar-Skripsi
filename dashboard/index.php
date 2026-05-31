<?php
include "../koneksi.php";
include "../templates/header.php";

$id_user = $_SESSION['id_user'];
$role = $_SESSION['role'];

if ($role === 'admin') {
    // Query untuk menghitung total statistik admin
    $total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users"))['total'];
    $total_kendaraan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kendaraan"))['total'];
    $total_part = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM part_kendaraan"))['total'];
    $total_rule = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM rules"))['total'];
    $total_gejala = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM gejala"))['total'];

    // Query untuk menampilkan semua data user untuk DataTables
    $query_users = mysqli_query($conn, "SELECT * FROM users ORDER BY id_user DESC");
    ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold mb-1">Dashboard</h2>
            <p class="text-muted small">Selamat datang, Admin SPV QC</p>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-3 mb-4">
        <!-- Card Total Pengguna -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-2.4 col-xl">
            <div class="card stat-card h-100">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-box bg-icon-blue me-3">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small" style="font-size: 0.8rem;">Total Pengguna</div>
                            <h4 class="mb-0 fw-bold"><?php echo $total_users; ?></h4>
                        </div>
                    </div>
                    <div class="border-top pt-2">
                        <a href="../users/index.php" class="card-detail-link text-primary d-flex align-items-center justify-content-between">
                            <span>Lihat Detail</span> <i class="bi bi-arrow-right-short fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Total Kendaraan -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-2.4 col-xl">
            <div class="card stat-card h-100">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-box bg-icon-green me-3">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div>
                            <div class="text-muted small" style="font-size: 0.8rem;">Total Kendaraan</div>
                            <h4 class="mb-0 fw-bold"><?php echo $total_kendaraan; ?></h4>
                        </div>
                    </div>
                    <div class="border-top pt-2">
                        <a href="../kendaraan/index.php" class="card-detail-link text-success d-flex align-items-center justify-content-between">
                            <span>Lihat Detail</span> <i class="bi bi-arrow-right-short fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Total Part Kendaraan -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-2.4 col-xl">
            <div class="card stat-card h-100">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-box bg-icon-yellow me-3">
                            <i class="bi bi-gear-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small" style="font-size: 0.8rem;">Total Part Kendaraan</div>
                            <h4 class="mb-0 fw-bold"><?php echo $total_part; ?></h4>
                        </div>
                    </div>
                    <div class="border-top pt-2">
                        <a href="../part/index.php" class="card-detail-link text-warning d-flex align-items-center justify-content-between" style="color: #d39e00 !important;">
                            <span>Lihat Detail</span> <i class="bi bi-arrow-right-short fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Total Rule -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-2.4 col-xl">
            <div class="card stat-card h-100">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-box bg-icon-purple me-3">
                            <i class="bi bi-hammer"></i>
                        </div>
                        <div>
                            <div class="text-muted small" style="font-size: 0.8rem;">Total Rule</div>
                            <h4 class="mb-0 fw-bold"><?php echo $total_rule; ?></h4>
                        </div>
                    </div>
                    <div class="border-top pt-2">
                        <a href="../rules/index.php" class="card-detail-link text-purple d-flex align-items-center justify-content-between" style="color: #6f42c1 !important;">
                            <span>Lihat Detail</span> <i class="bi bi-arrow-right-short fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Total Gejala -->
        <div class="col-12 col-sm-6 col-md-4 col-lg-2.4 col-xl">
            <div class="card stat-card h-100">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-box bg-icon-cyan me-3">
                            <i class="bi bi-activity"></i>
                        </div>
                        <div>
                            <div class="text-muted small" style="font-size: 0.8rem;">Total Gejala</div>
                            <h4 class="mb-0 fw-bold"><?php echo $total_gejala; ?></h4>
                        </div>
                    </div>
                    <div class="border-top pt-2">
                        <a href="../gejala/index.php" class="card-detail-link text-info d-flex align-items-center justify-content-between">
                            <span>Lihat Detail</span> <i class="bi bi-arrow-right-short fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="panel-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0 fw-bold">Data Pengguna Terbaru</h5>
            <a href="../users/tambah.php" class="btn btn-primary btn-sm px-3 rounded d-flex align-items-center gap-1">
                <i class="bi bi-plus"></i> Tambah User
            </a>
        </div>

        <div class="table-responsive">
            <table class="datatable table table-hover align-middle table-borderless w-100">
                <thead>
                    <tr>
                        <th width="60px">No</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th>Terakhir Login</th>
                        <th width="120px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if (mysqli_num_rows($query_users) > 0) {
                        while ($user = mysqli_fetch_assoc($query_users)) {
                            $role_badge = ($user['role'] == 'admin') ? 'bg-primary' : 'bg-success';
                            $terakhir_login = $user['created_at'];
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td class="fw-semibold"><?php echo htmlspecialchars($user['nama']); ?></td>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><span class="badge <?php echo $role_badge; ?> text-capitalize"><?php echo htmlspecialchars($user['role']); ?></span></td>
                                <td class="text-muted small"><?php echo htmlspecialchars($terakhir_login); ?></td>
                                <td>
                                    <div class="btn-action-group">
                                        <?php if ($user['id_user'] != $_SESSION['id_user']): ?>
                                            <a href="../users/hapus.php?id=<?php echo $user['id_user']; ?>" class="btn btn-danger btn-sm p-1 px-2" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?');" title="Hapus">
                                                <i class="bi bi-trash-fill"></i>
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm p-1 px-2" disabled title="Tidak bisa menghapus akun sendiri">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <a href="../users/edit.php?id=<?php echo $user['id_user']; ?>" class="btn btn-primary btn-sm p-1 px-2" title="Edit">
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
} else if ($role === 'petugas_qc') {
    // Query untuk menghitung total statistik petugas
    $total_inspeksi = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM pemeriksaan WHERE id_user = $id_user"))['total'];
    $total_kendaraan = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM kendaraan"))['total'];
    $total_part = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM part_kendaraan"))['total'];

    // Query data riwayat pemeriksaan petugas untuk DataTables
    $query_riwayat = mysqli_query($conn, "
        SELECT p.*, k.kode_kendaraan, k.nama_kendaraan, pt.kode_part, pt.nama_part 
        FROM pemeriksaan p 
        JOIN kendaraan k ON p.id_kendaraan = k.id_kendaraan 
        JOIN part_kendaraan pt ON p.id_part = pt.id_part 
        WHERE p.id_user = $id_user 
        ORDER BY p.id_pemeriksaan DESC
    ");
    ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="fw-bold mb-1">Dashboard Petugas QC</h2>
            <p class="text-muted small">Selamat datang kembali, <?php echo htmlspecialchars($_SESSION['nama']); ?></p>
        </div>
    </div>

    <!-- Jumbotron Panel -->
    <div class="card shadow-sm border-0 mb-4 bg-light">
        <div class="card-body p-4 d-flex align-items-center gap-4">
            <div class="bg-success text-white p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                <i class="bi bi-shield-fill-check fs-1"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1 text-success">Sistem Pakar QC Kendaraan Rantis</h4>
                <p class="text-muted mb-0 small">Melakukan pengecekan kelayakan komponen kendaraan tempur menggunakan algoritma Forward Chaining logika OR.</p>
            </div>
        </div>
    </div>

    <!-- Statistik Cards Petugas -->
    <div class="row g-3 mb-4">
        <!-- Total Inspeksi Saya -->
        <div class="col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-box bg-icon-blue me-3">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div>
                            <div class="text-muted small" style="font-size: 0.8rem;">Pemeriksaan QC Saya</div>
                            <h4 class="mb-0 fw-bold"><?php echo $total_inspeksi; ?></h4>
                        </div>
                    </div>
                    <div class="border-top pt-2">
                        <a href="../pemeriksaan/riwayat.php" class="card-detail-link text-primary d-flex align-items-center justify-content-between">
                            <span>Lihat Riwayat</span> <i class="bi bi-arrow-right-short fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Kendaraan -->
        <div class="col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-box bg-icon-green me-3">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div>
                            <div class="text-muted small" style="font-size: 0.8rem;">Total Rantis Terdaftar</div>
                            <h4 class="mb-0 fw-bold"><?php echo $total_kendaraan; ?></h4>
                        </div>
                    </div>
                    <div class="border-top pt-2">
                        <a href="../pemeriksaan/index.php" class="card-detail-link text-success d-flex align-items-center justify-content-between">
                            <span>Mulai Periksa</span> <i class="bi bi-arrow-right-short fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Part -->
        <div class="col-md-4">
            <div class="card stat-card h-100">
                <div class="card-body p-3 d-flex flex-column justify-content-between">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stat-icon-box bg-icon-yellow me-3">
                            <i class="bi bi-gear-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small" style="font-size: 0.8rem;">Total Part Rantis</div>
                            <h4 class="mb-0 fw-bold"><?php echo $total_part; ?></h4>
                        </div>
                    </div>
                    <div class="border-top pt-2">
                        <span class="card-detail-link text-warning d-flex align-items-center justify-content-between" style="color: #d39e00 !important;">
                            <span>Siap Diuji</span> <i class="bi bi-shield-fill fs-5"></i>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="panel-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="mb-0 fw-bold">Riwayat Pemeriksaan Terakhir Anda</h5>
            <a href="../pemeriksaan/index.php" class="btn btn-success btn-sm px-3 rounded d-flex align-items-center gap-1">
                <i class="bi bi-play-circle-fill"></i> Mulai Pemeriksaan QC
            </a>
        </div>

        <div class="table-responsive">
            <table class="datatable table table-hover align-middle table-borderless w-100">
                <thead>
                    <tr>
                        <th width="60px">No</th>
                        <th>Kendaraan Rantis</th>
                        <th>Part Pilihan</th>
                        <th>Tanggal Periksa</th>
                        <th>Status QC</th>
                        <th width="120px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if (mysqli_num_rows($query_riwayat) > 0) {
                        while ($row = mysqli_fetch_assoc($query_riwayat)) {
                            $badge_class = ($row['status_qc'] == 'LOLOS') ? 'bg-success' : 'bg-danger';
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($row['kode_kendaraan']); ?></strong><br>
                                    <span class="text-muted small"><?php echo htmlspecialchars($row['nama_kendaraan']); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-dark"><?php echo htmlspecialchars($row['kode_part']); ?></span>
                                    <span class="fw-semibold ms-1"><?php echo htmlspecialchars($row['nama_part']); ?></span>
                                </td>
                                <td class="text-muted small"><?php echo htmlspecialchars($row['tanggal_pemeriksaan']); ?></td>
                                <td><span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($row['status_qc']); ?></span></td>
                                <td>
                                    <div class="btn-action-group justify-content-center">
                                        <a href="../pemeriksaan/print.php?id=<?php echo $row['id_pemeriksaan']; ?>" target="_blank" class="btn btn-secondary btn-sm p-1 px-2" title="Cetak Laporan">
                                            <i class="bi bi-printer-fill"></i>
                                        </a>
                                        <a href="../pemeriksaan/hasil.php?id_pemeriksaan=<?php echo $row['id_pemeriksaan']; ?>" class="btn btn-primary btn-sm p-1 px-2" title="Lihat Detail">
                                            <i class="bi bi-eye-fill"></i>
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
}

include "../templates/footer.php";
?>
