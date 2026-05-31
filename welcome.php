<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}
?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow border-0 text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-shield-fill-check text-success" style="font-size: 5rem;"></i>
                </div>
                <h2 class="mb-3">Selamat Datang, <?php echo htmlspecialchars($_SESSION['nama']); ?>!</h2>
                <p class="text-muted mb-4">Anda berhasil login ke Sistem Pakar Quality Control Kendaraan Rantis (Forward Chaining) sebagai <strong>Petugas QC Lapangan</strong>.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="pemeriksaan/index.php" class="btn btn-primary btn-lg px-4 shadow-sm">
                        <i class="bi bi-play-circle-fill me-2"></i> Mulai Pemeriksaan Rantis
                    </a>
                    <a href="pemeriksaan/riwayat.php" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="bi bi-clock-history me-2"></i> Riwayat QC Saya
                    </a>
                    <a href="logout.php" class="btn btn-danger btn-lg px-4">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
