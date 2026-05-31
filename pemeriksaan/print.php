<?php
include "../koneksi.php";
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit;
}

$id_pemeriksaan = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Query detail hasil pemeriksaan
$stmt_pem = mysqli_prepare($conn, "
    SELECT p.*, k.kode_kendaraan, k.nama_kendaraan, pt.kode_part, pt.nama_part, u.nama as nama_petugas
    FROM pemeriksaan p 
    JOIN kendaraan k ON p.id_kendaraan = k.id_kendaraan 
    JOIN part_kendaraan pt ON p.id_part = pt.id_part 
    JOIN users u ON p.id_user = u.id_user 
    WHERE p.id_pemeriksaan = ?
");
mysqli_stmt_bind_param($stmt_pem, "i", $id_pemeriksaan);
mysqli_stmt_execute($stmt_pem);
$result_pem = mysqli_stmt_get_result($stmt_pem);
$pem = mysqli_fetch_assoc($result_pem);
mysqli_stmt_close($stmt_pem);

if (!$pem) {
    die("Data pemeriksaan tidak ditemukan!");
}

// Query gejala yang terdeteksi pada pemeriksaan ini
$query_gejala = mysqli_query($conn, "
    SELECT g.* 
    FROM pemeriksaan_gejala pg 
    JOIN gejala g ON pg.id_gejala = g.id_gejala 
    WHERE pg.id_pemeriksaan = $id_pemeriksaan 
    ORDER BY g.kode_gejala ASC
");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil QC - <?php echo htmlspecialchars($pem['kode_kendaraan']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.5;
            padding: 30px;
        }
        .header-kop {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 30px;
        }
        .header-kop h2 {
            margin: 0;
            font-size: 1.6rem;
            text-transform: uppercase;
        }
        .header-kop p {
            margin: 5px 0 0 0;
            font-size: 0.9rem;
            color: #666;
        }
        .doc-title {
            text-align: center;
            font-size: 1.3rem;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 25px;
            text-transform: uppercase;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
        }
        .info-table th, .info-table td {
            padding: 8px 12px;
            text-align: left;
            border: 1px solid #ddd;
        }
        .info-table th {
            background-color: #f2f2f2;
            width: 30%;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 15px;
            font-weight: bold;
            font-size: 1.1rem;
            border: 2px solid;
            border-radius: 4px;
        }
        .status-lolos {
            color: #198754;
            border-color: #198754;
            background-color: #e8f5e9;
        }
        .status-tidak-lolos {
            color: #dc3545;
            border-color: #dc3545;
            background-color: #fdf2f2;
        }
        .section-title {
            font-size: 1.1rem;
            font-weight: bold;
            margin-top: 25px;
            margin-bottom: 10px;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
        }
        .box-content {
            border: 1px solid #ddd;
            background-color: #fafafa;
            padding: 15px;
            border-radius: 4px;
            white-space: pre-line;
        }
        .signature-container {
            margin-top: 50px;
            display: flex;
            justify-content: flex-end;
        }
        .signature-box {
            text-align: center;
            width: 250px;
        }
        .signature-space {
            height: 80px;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print();" style="padding: 10px 20px; font-weight: bold; background-color: #0d6efd; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Cetak Dokumen
        </button>
    </div>

    <!-- Kop Surat Perusahaan Rantis -->
    <div class="header-kop">
        <h2>PT Sentra Surya Ekajaya</h2>
        <p>Quality Control Division - Military Vehicles and Defense Equipment Manufacturer</p>
        <p style="font-size: 0.8rem;">Jl. Raya SSE No. 1, Tangerang, Banten, Indonesia</p>
    </div>

    <div class="doc-title">
        Laporan Sertifikasi Quality Control Rantis
    </div>

    <table class="info-table">
        <tr>
            <th>Kode Rantis</th>
            <td><?php echo htmlspecialchars($pem['kode_kendaraan']); ?></td>
        </tr>
        <tr>
            <th>Nama Rantis</th>
            <td><?php echo htmlspecialchars($pem['nama_kendaraan']); ?></td>
        </tr>
        <tr>
            <th>Bagian / Part Pemeriksaan</th>
            <td><strong><?php echo htmlspecialchars($pem['kode_part']); ?> - <?php echo htmlspecialchars($pem['nama_part']); ?></strong></td>
        </tr>
        <tr>
            <th>Tanggal QC</th>
            <td><?php echo htmlspecialchars($pem['tanggal_pemeriksaan']); ?></td>
        </tr>
        <tr>
            <th>Petugas QC</th>
            <td><?php echo htmlspecialchars($pem['nama_petugas']); ?></td>
        </tr>
        <tr>
            <th>Status Kelayakan (QC)</th>
            <td>
                <?php if ($pem['status_qc'] == 'LOLOS'): ?>
                    <span class="status-badge status-lolos">LOLOS QC / LAYAK</span>
                <?php else: ?>
                    <span class="status-badge status-tidak-lolos">TIDAK LOLOS QC / PERBAIKAN</span>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <div class="section-title">Indikator / Gejala Kerusakan Terdeteksi</div>
    <div style="border: 1px solid #ddd; padding: 12px; background-color: #fafafa; border-radius: 4px;">
        <?php if (mysqli_num_rows($query_gejala) > 0): ?>
            <ul style="margin: 0; padding-left: 20px;">
                <?php while ($gj = mysqli_fetch_assoc($query_gejala)): ?>
                    <li style="margin-bottom: 5px;">
                        <strong>[<?php echo htmlspecialchars($gj['kode_gejala']); ?>]</strong> <?php echo htmlspecialchars($gj['nama_gejala']); ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p style="margin: 0; color: #198754; font-weight: bold;">Tidak ada gejala kerusakan yang ditemukan pada part ini.</p>
        <?php endif; ?>
    </div>

    <div class="section-title">Diagnosa Kerusakan (Sistem Pakar)</div>
    <div class="box-content">
        <?php echo htmlspecialchars($pem['diagnosa']); ?>
    </div>

    <div class="section-title">Rekomendasi Tindakan / Solusi</div>
    <div class="box-content" style="background-color: #eef7ff; border-color: #bfe3ff; color: #084298;">
        <?php echo htmlspecialchars($pem['solusi']); ?>
    </div>

    <!-- Tanda Tangan SPV/Petugas -->
    <div class="signature-container">
        <div class="signature-box">
            <p>Tangerang, <?php echo date('d F Y', strtotime($pem['tanggal_pemeriksaan'])); ?></p>
            <p>Supervisor / Division Head QC</p>
            <div class="signature-space"></div>
            <p style="text-decoration: underline; font-weight: bold;">( ______________________ )</p>
            <p style="font-size: 0.85rem; color: #666;">PT Sentra Surya Ekajaya</p>
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            // Auto trigger print dialog if target is printing
            <?php if (isset($_GET['print_auto'])): ?>
                window.print();
            <?php endif; ?>
        });
    </script>
</body>
</html>
