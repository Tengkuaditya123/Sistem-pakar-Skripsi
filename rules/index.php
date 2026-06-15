    <?php
    include "../koneksi.php";
    include "../templates/header.php";

    // Query data rule dengan JOIN ke gejala dan part_kendaraan
    $query_rules = mysqli_query($conn, "
        SELECT r.*, g.kode_gejala, g.nama_gejala, p.kode_part, p.nama_part 
        FROM rules r 
        JOIN gejala g ON r.id_gejala = g.id_gejala 
        JOIN part_kendaraan p ON r.id_part = p.id_part 
        ORDER BY r.kode_rule ASC, g.kode_gejala ASC
    ");

    // Grouping data rule berdasarkan kode_rule di PHP
    $grouped_rules = [];
    if (mysqli_num_rows($query_rules) > 0) {
        while ($row = mysqli_fetch_assoc($query_rules)) {
            $kode = $row['kode_rule'];
            if (!isset($grouped_rules[$kode])) {
                $grouped_rules[$kode] = [
                    'kode_rule' => $row['kode_rule'],
                    'nama_part' => $row['nama_part'],
                    'kode_part' => $row['kode_part'],
                    'status' => $row['status'] ?: 'TIDAK LOLOS',
                    'gejala' => []
                ];
            }
            $grouped_rules[$kode]['gejala'][] = $row['kode_gejala'];
        }
    }
    ?>

    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold mb-1">Kelola Rule</h2>
                <p class="text-muted small">Basis aturan logika</p>
            </div>
            <a href="tambah.php" class="btn btn-primary btn-sm px-3 rounded d-flex align-items-center gap-1">
                <i class="bi bi-plus-lg"></i> Tambah Rule
            </a>
        </div>
    </div>

    <!-- Alert Success/Error -->
    <?php if (isset($_GET['status'])): ?>
        <?php if ($_GET['status'] == 'sukses_tambah'): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> Rule baru berhasil disimpan!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['status'] == 'sukses_edit'): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> Rule berhasil diperbarui!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php elseif ($_GET['status'] == 'sukses_hapus'): ?>
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> Rule berhasil dihapus!
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
                        <th width="80px">No</th>
                        <th width="120px">RULE</th>
                        <th>IF (gabungan gejala OR)</th>
                        <th>THEN (part)</th>
                       <!-- <th width="150px">STATUS</th> -->   
                        <th width="120px" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    if (count($grouped_rules) > 0) {
                        foreach ($grouped_rules as $rule) {
                            $badge_class = ($rule['status'] == 'LOLOS') ? 'bg-success' : 'bg-danger';
                            
                            // Buat format OR: G01 OR G02 OR G03
                            $if_symptoms = implode(' <span class="text-muted fw-bold">OR</span> ', array_map(function($g) {
                                return '<span class="badge bg-warning text-dark">' . htmlspecialchars($g) . '</span>';
                            }, $rule['gejala']));
                            ?>
                            <tr>
                                <td><?php echo $no++; ?></td>
                                <td class="fw-bold text-primary"><?php echo htmlspecialchars($rule['kode_rule']); ?></td>
                                <td><?php echo $if_symptoms; ?></td>
                                <td>
                                    <span class="badge bg-info text-dark me-1"><?php echo htmlspecialchars($rule['kode_part']); ?></span>
                                    <span class="fw-semibold"><?php echo htmlspecialchars($rule['nama_part']); ?></span>
                                </td>
                                <!-- <td>
                                    <span class="badge <?php echo $badge_class; ?> px-3 py-2 text-uppercase"><?php echo htmlspecialchars($rule['status']); ?></span>
                                </td> -->
                                <td>
                                    <div class="btn-action-group">
                                        <!-- Aksi: Hapus (kode_rule) & Edit (kode_rule) -->
                                        <a href="hapus.php?kode=<?php echo urlencode($rule['kode_rule']); ?>" class="btn btn-danger btn-sm p-1 px-2 btn-confirm-delete" data-message="Apakah Anda yakin ingin menghapus rule <?php echo htmlspecialchars($rule['kode_rule']); ?>?" title="Hapus">
                                            <i class="bi bi-trash-fill"></i>
                                        </a>
                                        
                                        <a href="edit.php?kode=<?php echo urlencode($rule['kode_rule']); ?>" class="btn btn-primary btn-sm p-1 px-2" title="Edit">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='5' class='text-center text-muted p-4'>Tidak ada data rule ditemukan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    include "../templates/footer.php";
    ?>
