<?php
include "../koneksi.php";
include "../templates/header.php";

$id_user = $_SESSION['id_user'];
$role = $_SESSION['role'];

// Query riwayat pemeriksaan berdasarkan role
// Petugas QC hanya melihat hasil pemeriksaannya sendiri
if ($role == 'admin') {
    $query_riwayat = mysqli_query($conn, "
        SELECT p.*, k.kode_kendaraan, k.nama_kendaraan, pt.kode_part, pt.nama_part, u.nama as nama_petugas
        FROM pemeriksaan p 
        JOIN kendaraan k ON p.id_kendaraan = k.id_kendaraan 
        JOIN part_kendaraan pt ON p.id_part = pt.id_part 
        JOIN users u ON p.id_user = u.id_user 
        ORDER BY p.id_pemeriksaan DESC
    ");
} else {
    $query_riwayat = mysqli_query($conn, "
        SELECT p.*, k.kode_kendaraan, k.nama_kendaraan, pt.kode_part, pt.nama_part, u.nama as nama_petugas
        FROM pemeriksaan p 
        JOIN kendaraan k ON p.id_kendaraan = k.id_kendaraan 
        JOIN part_kendaraan pt ON p.id_part = pt.id_part 
        JOIN users u ON p.id_user = u.id_user 
        WHERE p.id_user = $id_user 
        ORDER BY p.id_pemeriksaan DESC
    ");
}
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="fw-bold mb-1">Cetak Hasil Pemeriksaan</h2>
        <p class="text-muted small">Daftar riwayat hasil pengujian Quality Control Kendaraan Rantis Anda</p>
    </div>
</div>

<!-- Table Card -->
<div class="panel-container">
    <div class="table-responsive">
        <table class="datatable table table-hover align-middle table-borderless w-100">
            <thead>
                <tr>
                    <th width="60px">No</th>
                    <th>Kendaraan Rantis</th>
                    <th>Part Teruji</th>
                    <th>Tanggal Pengecekan</th>
                    <th width="150px">Status QC</th>
                    <th width="140px" class="text-center">Aksi</th>
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
                            <td><span class="badge <?php echo $badge_class; ?> px-3 py-2 fs-7 text-uppercase"><?php echo htmlspecialchars($row['status_qc']); ?></span></td>
                            <td class="text-center">
                                <div class="btn-action-group justify-content-center">
                                    <!-- Print -->
                                    <a href="print.php?id=<?php echo $row['id_pemeriksaan']; ?>" target="_blank" class="btn btn-secondary btn-sm p-1 px-2" title="Cetak Hasil">
                                        <i class="bi bi-printer-fill"></i>
                                    </a>
                                    <!-- Detail -->
                                    <a href="hasil.php?id_pemeriksaan=<?php echo $row['id_pemeriksaan']; ?>" class="btn btn-primary btn-sm p-1 px-2" title="Lihat Detail">
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
include "../templates/footer.php";
?>
