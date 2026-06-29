<?php
include "../koneksi.php";
include "../templates/header.php";

if ($_SESSION['role'] !== 'admin') {
    header("Location: ../dashboard/index.php");
    exit;
}

// Query semua riwayat pemeriksaan untuk Laporan
$query_laporan = mysqli_query($conn, "
    SELECT p.*, k.kode_kendaraan, k.nama_kendaraan, pt.kode_part, pt.nama_part, u.nama as nama_petugas
    FROM pemeriksaan p 
    JOIN kendaraan k ON p.id_kendaraan = k.id_kendaraan 
    JOIN part_kendaraan pt ON p.id_part = pt.id_part 
    JOIN users u ON p.id_user = u.id_user 
    ORDER BY p.id_pemeriksaan DESC
");

// Logika Hapus Laporan
if (isset($_GET['action']) && $_GET['action'] == 'hapus') {
    $id_del = intval($_GET['id']);
    if ($id_del > 0) {
        $stmt_del = mysqli_prepare($conn, "DELETE FROM pemeriksaan WHERE id_pemeriksaan = ?");
        mysqli_stmt_bind_param($stmt_del, "i", $id_del);
        if (mysqli_stmt_execute($stmt_del)) {
            echo "<script>alert('Laporan pemeriksaan berhasil dihapus!'); window.location.href='laporan.php';</script>";
            exit;
        } else {
            echo "<script>alert('Gagal menghapus laporan pemeriksaan.'); window.location.href='laporan.php';</script>";
            exit;
        }
        mysqli_stmt_close($stmt_del);
    }
}
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="fw-bold mb-1">Laporan Pemeriksaan QC</h2>
        <p class="text-muted small">Semua riwayat hasil Quality Control kendaraan rantis oleh seluruh petugas</p>
    </div>
</div>

<div class="panel-container">
    <div class="table-responsive">
        <table class="datatable table table-hover align-middle table-borderless w-100">
            <thead>
                <tr>
                    <th width="60px">No</th>
                    <th>Kendaraan Rantis</th>
                    <th>Part Kendaraan</th>
                    <th>Pemeriksa (Petugas)</th>
                    <th>Tanggal Periksa</th>
                    <th>Status QC</th>
                    <th width="160px" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                if (mysqli_num_rows($query_laporan) > 0): 
                    while ($row = mysqli_fetch_assoc($query_laporan)): 
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
                            <td class="fw-semibold text-secondary"><?php echo htmlspecialchars($row['nama_petugas']); ?></td>
                            <td class="text-muted small"><?php echo htmlspecialchars($row['tanggal_pemeriksaan']); ?></td>
                            <td>
                                <span class="badge <?php echo $badge_class; ?>">
                                    <?php echo htmlspecialchars($row['status_qc']); ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-action-group justify-content-center">
                                    <!-- Aksi: Hapus Laporan -->
                                    <a href="laporan.php?action=hapus&id=<?php echo $row['id_pemeriksaan']; ?>" class="btn btn-danger btn-sm p-1 px-2 btn-confirm-delete" data-message="Yakin ingin menghapus data ini?" title="Hapus">
                                        <i class="bi bi-trash-fill"></i>
                                    </a>
                                    <!-- Detail/Cetak -->
                                    <a href="print.php?id=<?php echo $row['id_pemeriksaan']; ?>" target="_blank" class="btn btn-secondary btn-sm p-1 px-2" title="Cetak Laporan">
                                        <i class="bi bi-printer-fill"></i>
                                    </a>
                                    <a href="detail.php?id=<?php echo $row['id_pemeriksaan']; ?>" class="btn btn-primary btn-sm p-1 px-2" title="Lihat Detail">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php 
                    endwhile; 
                endif; 
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php
include "../templates/footer.php";
?>
