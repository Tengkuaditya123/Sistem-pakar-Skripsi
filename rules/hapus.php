<?php
session_start();
include "../koneksi.php";

// Proteksi session login dan role admin
if (!isset($_SESSION['login']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id > 0) {
    $stmt = mysqli_prepare($conn, "DELETE FROM rules WHERE id_rule = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    
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
