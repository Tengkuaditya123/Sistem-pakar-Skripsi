<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Proteksi session login (admin & petugas_qc)
if (!isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}

// Deteksi halaman aktif berdasarkan folder
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$current_file = basename($_SERVER['PHP_SELF']);

// Path helper karena file berada di subfolder
$base_url = "../";

// Proteksi hak akses role admin untuk folder master data
$admin_dirs = ['users', 'kendaraan', 'part', 'gejala', 'rules'];
if (in_array($current_dir, $admin_dirs) && $_SESSION['role'] !== 'admin') {
    header("Location: " . $base_url . "dashboard/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pakar QC Check - Admin</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- DataTables Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f6f9;
            overflow-x: hidden;
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styling */
        #sidebar {
            width: 250px;
            background-color: #f1f3f5;
            border-right: 1px solid #dee2e6;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-header {
            padding: 22px 20px;
            font-weight: 700;
            font-size: 1.15rem;
            letter-spacing: 1px;
            color: #212529;
            border-bottom: 1px solid #dee2e6;
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 15px 10px;
            margin: 0;
            flex-grow: 1;
        }
        
        .sidebar-menu li {
            margin-bottom: 8px;
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #495057;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .sidebar-menu a i {
            font-size: 1.25rem;
            margin-right: 12px;
        }
        
        .sidebar-menu a:hover {
            background-color: #e9ecef;
            color: #212529;
        }
        
        .sidebar-menu a.active {
            background-color: #0d6efd;
            color: #fff;
            font-weight: 600;
        }
        
        /* Main Content Styling */
        #content {
            margin-left: 250px;
            flex-grow: 1;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        /* Top Navbar */
        .navbar-custom {
            height: 70px;
            background: linear-gradient(90deg, #cfe2ff 0%, #b8d6fe 100%);
            border-bottom: 1px solid #9ec5fe;
            padding: 0 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .navbar-brand-title {
            font-weight: 600;
            color: #084298;
            margin: 0;
            font-size: 1.25rem;
        }
        
        .user-profile {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .user-avatar {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background-color: #084298;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1.2rem;
            border: 2px solid #fff;
        }
        
        .user-name {
            font-weight: 600;
            color: #084298;
            font-size: 0.95rem;
        }
        
        /* Dashboard Cards */
        .stat-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            background: #fff;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        }
        
        .stat-icon-box {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            color: #fff;
        }
        
        /* Card Colors matching the image */
        .bg-icon-blue { background-color: #0d6efd; }
        .bg-icon-green { background-color: #198754; }
        .bg-icon-yellow { background-color: #ffc107; }
        .bg-icon-purple { background-color: #6f42c1; }
        .bg-icon-cyan { background-color: #0dcaf0; }
        
        .card-detail-link {
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        /* Table and Panel styling */
        .panel-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 24px;
            border: 1px solid #e9ecef;
            margin-bottom: 24px;
        }
        
        /* DataTables Customizations to match image */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter {
            margin-bottom: 15px;
        }
        .dataTables_wrapper .dataTables_filter input {
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 5px 10px;
            outline: none;
        }
            .dataTables_wrapper .dataTables_length select {
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 5px;
            outline: none;
            width: 70px !important;
            min-width: 70px !important;
            display: inline-block;
        }
        table.dataTable {
            border-collapse: collapse !important;
        }
        table.dataTable thead th {
            background-color: #f8f9fa !important;
            border-bottom: 1px solid #dee2e6 !important;
            font-weight: 600;
            color: #495057;
            padding: 12px 10px !important;
        }
        table.dataTable tbody td {
            border-bottom: 1px solid #f1f3f5 !important;
            padding: 12px 10px !important;
        }
        
        /* Footer styling */
        footer {
            background-color: #fff;
            border-top: 1px solid #dee2e6;
            padding: 15px 0;
            text-align: center;
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: auto;
        }
        
        /* Action buttons spacing */
        .btn-action-group {
            display: flex;
            gap: 5px;
            justify-content: center;
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div id="sidebar">
        <div class="sidebar-header">
            DASHBOARD
        </div>
        <ul class="sidebar-menu">
            <li>
                <a href="<?php echo $base_url; ?>dashboard/index.php" class="<?php echo ($current_dir == 'dashboard') ? 'active' : ''; ?>">
                    <i class="bi bi-house-door-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <li>
                    <a href="<?php echo $base_url; ?>users/index.php" class="<?php echo ($current_dir == 'users') ? 'active' : ''; ?>">
                        <i class="bi bi-person-fill"></i>
                        <span>Kelola Data Pengguna</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>kendaraan/index.php" class="<?php echo ($current_dir == 'kendaraan') ? 'active' : ''; ?>">
                        <i class="bi bi-truck"></i>
                        <span>Kelola Data Kendaraan</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>part/index.php" class="<?php echo ($current_dir == 'part') ? 'active' : ''; ?>">
                        <i class="bi bi-gear-fill"></i>
                        <span>Kelola Data Part</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>gejala/index.php" class="<?php echo ($current_dir == 'gejala') ? 'active' : ''; ?>">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <span>Kelola Gejala</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>rules/index.php" class="<?php echo ($current_dir == 'rules') ? 'active' : ''; ?>">
                        <i class="bi bi-hammer"></i>
                        <span>Kelola Rule</span>
                    </a>
                </li>

            <?php elseif ($_SESSION['role'] == 'petugas_qc'): ?>
                <li>
                    <a href="<?php echo $base_url; ?>pemeriksaan/index.php" class="<?php echo ($current_dir == 'pemeriksaan' && ($current_file == 'index.php' || $current_file == 'pilih_part.php' || $current_file == 'proses.php' || $current_file == 'hasil.php')) ? 'active' : ''; ?>">
                        <i class="bi bi-shield-fill-check"></i>
                        <span>Pemeriksaan QC</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_url; ?>pemeriksaan/riwayat.php" class="<?php echo ($current_dir == 'pemeriksaan' && $current_file == 'riwayat.php') ? 'active' : ''; ?>">
                        <i class="bi bi-printer-fill"></i>
                        <span>Cetak Hasil Pemeriksaan</span>
                    </a>
                </li>
            <?php endif; ?>
 

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
                <div class="user-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>
                <span class="user-name d-none d-md-inline"><?php echo isset($_SESSION['nama']) ? htmlspecialchars($_SESSION['nama']) : 'User'; ?></span>
                <a href="#" data-bs-toggle="modal" data-bs-target="#logoutModal" class="btn btn-primary btn-sm px-3 rounded">
                    Logout
                </a>
            </div>
        </nav>
        
        <!-- Main Wrapper -->
        <div class="container-fluid p-4">
