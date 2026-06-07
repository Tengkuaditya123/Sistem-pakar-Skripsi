<?php
include "koneksi.php";

// Cek dan tambah kolom `kode_rule` jika belum ada
$result = mysqli_query($conn, "SHOW COLUMNS FROM `rules` LIKE 'kode_rule'");
if (mysqli_num_rows($result) == 0) {
    mysqli_query($conn, "ALTER TABLE `rules` ADD COLUMN `kode_rule` VARCHAR(50) DEFAULT NULL AFTER `id_rule`");
    echo "Kolom kode_rule berhasil ditambahkan.\n";
} else {
    echo "Kolom kode_rule sudah ada.\n";
}

// Cek dan tambah kolom `status` jika belum ada
$result_status = mysqli_query($conn, "SHOW COLUMNS FROM `rules` LIKE 'status'");
if (mysqli_num_rows($result_status) == 0) {
    mysqli_query($conn, "ALTER TABLE `rules` ADD COLUMN `status` VARCHAR(50) DEFAULT 'TIDAK LOLOS' AFTER `keputusan`");
    echo "Kolom status berhasil ditambahkan.\n";
} else {
    echo "Kolom status sudah ada.\n";
}

// Update data rule lama agar punya kode_rule default (misal R1, R2, dst)
mysqli_query($conn, "UPDATE `rules` SET `kode_rule` = CONCAT('R', id_rule) WHERE `kode_rule` IS NULL");
echo "Data rule lama berhasil diperbarui dengan kode default.\n";

?>
