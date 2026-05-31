<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "spfc_rantis_qc";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi database gagal: " . mysqli_connect_error());
}
?>
