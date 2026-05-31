<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Proteksi session login (admin dan petugas_qc boleh akses)
if (!isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}

// Deteksi halaman aktif
$current_file = basename($_SERVER['PHP_SELF']);
$base_url = "../";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pemeriksaan QC Rantis - Sistem Pakar</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons for styling sidebar icons in native CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- CSS Native -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Sidebar Petugas QC / Admin -->
    <div id="sidebar">
        <div class="sidebar-header">
            QC PETUGAS
        </div>
        <ul class="sidebar-menu">
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <li>
                    <a href="<?php echo $base_url; ?>dashboard/index.php">
                        <i class="bi bi-arrow-left-circle-fill"></i>
                        <span>Kembali ke Admin</span>
                    </a>
                </li>
            <?php endif; ?>
            <li>
                <a href="index.php" class="<?php echo ($current_file == 'index.php' || $current_file == 'pilih_part.php' || $current_file == 'proses.php' || $current_file == 'hasil.php') ? 'active' : ''; ?>">
                    <i class="bi bi-shield-fill-check"></i>
                    <span>Mulai Pemeriksaan</span>
                </a>
            </li>
            <li>
                <a href="riwayat.php" class="<?php echo ($current_file == 'riwayat.php') ? 'active' : ''; ?>">
                    <i class="bi bi-clock-history"></i>
                    <span>Riwayat QC Saya</span>
                </a>
            </li>
            <li style="margin-top: auto; padding-top: 20px; border-top: 1px solid #dee2e6;">
                <a href="<?php echo $base_url; ?>logout.php" style="color: #dc3545;">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>

    <!-- Main Content -->
    <div id="content">
        <!-- Top Navbar -->
        <nav class="navbar-custom">
            <div class="navbar-brand-title">
                Sistem Pakar QC Check
            </div>
            <div class="user-profile">
                <span class="user-name"><?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                <span class="badge-role"><?php echo htmlspecialchars($_SESSION['role']); ?></span>
                <a href="<?php echo $base_url; ?>logout.php" class="btn-logout">
                    Logout
                </a>
            </div>
        </nav>
        
        <!-- Wrapper -->
        <div class="container-pemeriksaan">
