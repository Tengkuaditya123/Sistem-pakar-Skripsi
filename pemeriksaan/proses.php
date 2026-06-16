<?php
include "../koneksi.php";

$id_kendaraan = isset($_GET['id_kendaraan']) ? intval($_GET['id_kendaraan']) : 0;
$id_part = isset($_GET['id_part']) ? intval($_GET['id_part']) : 0;

// Query detail kendaraan
$stmt_kdr = mysqli_prepare($conn, "SELECT * FROM kendaraan WHERE id_kendaraan = ?");
mysqli_stmt_bind_param($stmt_kdr, "i", $id_kendaraan);
mysqli_stmt_execute($stmt_kdr);
$result_kdr = mysqli_stmt_get_result($stmt_kdr);
$kdr = mysqli_fetch_assoc($result_kdr);
mysqli_stmt_close($stmt_kdr);

// Query detail part
$stmt_part = mysqli_prepare($conn, "SELECT * FROM part_kendaraan WHERE id_part = ?");
mysqli_stmt_bind_param($stmt_part, "i", $id_part);
mysqli_stmt_execute($stmt_part);
$result_part = mysqli_stmt_get_result($stmt_part);
$part = mysqli_fetch_assoc($result_part);
mysqli_stmt_close($stmt_part);

if (!$kdr || !$part) {
    include "../templates/header.php";
    echo "<div class='alert alert-danger'>Data kendaraan atau part tidak ditemukan!</div>";
    include "../templates/footer.php";
    exit;
}

// Ambil daftar gejala yang terhubung dengan part ini melalui tabel rules (Forward Chaining mapping)
$query_gejala = mysqli_query($conn, "
    SELECT DISTINCT g.* 
    FROM rules r 
    JOIN gejala g ON r.id_gejala = g.id_gejala 
    WHERE r.id_part = $id_part 
    ORDER BY g.kode_gejala ASC
");

$error = '';
// PROSES JALANKAN FORWARD CHAINING & SIMPAN OTOMATIS
if (isset($_POST['proses_diagnosa'])) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    $id_user = $_SESSION['id_user'];
    $selected_gejala = isset($_POST['gejala']) ? $_POST['gejala'] : []; // Array id_gejala

    // Konsep Forward Chaining Logika OR:
    // Jika ada minimal 1 gejala yang dipilih -> TIDAK LOLOS
    // Jika tidak ada gejala -> LOLOS
    if (count($selected_gejala) > 0) {
        $status_qc = 'TIDAK LOLOS';
        
        // Membaca tabel rule untuk menentukan diagnosa
        $diagnosa_arr = [];
        
        foreach ($selected_gejala as $id_g) {
            $id_g_int = intval($id_g);
            $query_rule = mysqli_query($conn, "SELECT keputusan FROM rules WHERE id_part = $id_part AND id_gejala = $id_g_int");
            if ($r_data = mysqli_fetch_assoc($query_rule)) {
                $diagnosa_arr[] = $r_data['keputusan'];
            }
        }
        
        if (empty($diagnosa_arr)) {
            $diagnosa = "Terdeteksi kerusakan pada sistem " . $part['nama_part'] . " berdasarkan gejala yang dipilih.";
        } else {
            $diagnosa = implode(";\n", array_unique($diagnosa_arr));
        }
        
        $solusi = "Lakukan perbaikan yang sesuai dengan gejala pada part " . $part['nama_part'] . ", dan koordinasi dengan divisi produksi yang terkait.";
    } else {
        $status_qc = 'LOLOS';
        $diagnosa = "Seluruh indikator pada part " . $part['nama_part'] . "  dalam kondisi normal.";
        $solusi = "Lakukan pemeliharaan ringan dan rutin untuk menjaga kondisi fisik dan fungsi pada part.";
    }

    // Simpan otomatis ke tabel pemeriksaan
    $stmt_insert = mysqli_prepare($conn, "INSERT INTO pemeriksaan (id_kendaraan, id_user, id_part, tanggal_pemeriksaan, status_qc, diagnosa, solusi) VALUES (?, ?, ?, NOW(), ?, ?, ?)");
    mysqli_stmt_bind_param($stmt_insert, "iiisss", $id_kendaraan, $id_user, $id_part, $status_qc, $diagnosa, $solusi);
    
    if (mysqli_stmt_execute($stmt_insert)) {
        $id_pemeriksaan = mysqli_insert_id($conn);
        mysqli_stmt_close($stmt_insert);
        
        // Simpan gejala terpilih ke tabel pemeriksaan_gejala
        if (count($selected_gejala) > 0) {
            foreach ($selected_gejala as $id_g) {
                $id_g_int = intval($id_g);
                mysqli_query($conn, "INSERT INTO pemeriksaan_gejala (id_pemeriksaan, id_gejala) VALUES ($id_pemeriksaan, $id_g_int)");
            }
        }
        
        // Redirect ke halaman hasil
        header("Location: hasil.php?id_pemeriksaan=" . $id_pemeriksaan);
        exit;
    } else {
        $error = 'Gagal menyimpan hasil pemeriksaan ke database. Silakan coba kembali.';
    }
}

include "../templates/header.php";
?>

<div class="row mb-4">
    <div class="col-md-12">
        <h2 class="fw-bold mb-1">Checklist Gejala Kerusakan</h2>
        <p class="text-muted small">Tahap 3: Centang Gejala Pada Part Kendaraan</p>
    </div>
</div>

<!-- Progress/Detail Info Card -->
<div class="card shadow-sm border-0 mb-4 bg-light border-start border-success border-3">
    <div class="card-body p-3">
        <div class="row">
            <div class="col-md-6">
                <span class="text-muted small">KENDARAAN RANTIS</span>
                <p class="fw-bold mb-0 text-dark"><?php echo htmlspecialchars($kdr['kode_kendaraan']); ?> - <?php echo htmlspecialchars($kdr['nama_kendaraan']); ?></p>
            </div>
            <div class="col-md-6 border-start">
                <span class="text-muted small">PART KENDARAAN TERUJI</span>
                <p class="fw-bold mb-0 text-success"><?php echo htmlspecialchars($part['kode_part']); ?> - <?php echo htmlspecialchars($part['nama_part']); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Gejala Checklist Card -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-primary text-white py-3">
        <h5 class="card-title mb-0 fw-bold"><i class="bi bi-patch-question me-2"></i> Indikator Gejala Pilihan</h5>
    </div>
    <div class="card-body p-4">
        <p class="text-muted small mb-4">Centang pada kotak di samping gejala kerusakan yang teramati. Jika komponen normal (tidak ada kerusakan sama sekali), kosongkan seluruh pilihan dan langsung tekan tombol <strong>Proses QC & Diagnosa</strong>.</p>
        
        <?php if ($error != ''): ?>
            <div class="alert alert-danger" role="alert"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <?php if (mysqli_num_rows($query_gejala) > 0): ?>
                <div class="list-group mb-4 shadow-sm rounded">
                    <?php while ($gj = mysqli_fetch_assoc($query_gejala)): ?>
                        <label class="list-group-item list-group-item-action d-flex align-items-start gap-3 py-3" style="cursor: pointer;">
                            <input class="form-check-input flex-shrink-0" type="checkbox" name="gejala[]" value="<?php echo $gj['id_gejala']; ?>" style="width: 1.25rem; height: 1.25rem; margin-top: 0.15rem;">
                            <div>
                                <span class="badge bg-warning text-dark me-2"><?php echo htmlspecialchars($gj['kode_gejala']); ?></span>
                                <span class="fw-medium text-dark"><?php echo htmlspecialchars($gj['nama_gejala']); ?></span>
                            </div>
                        </label>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning border-0" role="alert">
                    <i class="bi bi-info-circle-fill me-2"></i> Belum ada rule gejala kerusakan yang terdaftar untuk part ini. Sistem pakar akan menyimpulkan part ini **LOLOS QC** secara default.
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-between align-items-center mt-4">
                <a href="pilih_part.php?id_kendaraan=<?php echo $id_kendaraan; ?>" class="btn btn-outline-secondary">
                    <i class="bi bi-chevron-left me-1"></i> Kembali
                </a>
                <button type="submit" name="proses_diagnosa" class="btn btn-primary px-4 fw-semibold">
                    <i class="bi bi-cpu-fill me-2"></i> Proses QC & Diagnosa
                </button>
            </div>
        </form>
    </div>
</div>

<?php
include "../templates/footer.php";
?>
