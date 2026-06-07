<?php
session_start();
include "../koneksi.php";

// Proteksi session login dan role admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$kode = isset($_GET['kode']) ? trim($_GET['kode']) : '';

if (!empty($kode)) {
    $stmt = mysqli_prepare($conn, "DELETE FROM rules WHERE kode_rule = ?");
    mysqli_stmt_bind_param($stmt, "s", $kode);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: index.php?status=sukses_hapus");
    } else {
        header("Location: index.php?status=gagal");
    }
    mysqli_stmt_close($stmt);
} else {
    header("Location: index.php?status=gagal");
}
exit;
?>
