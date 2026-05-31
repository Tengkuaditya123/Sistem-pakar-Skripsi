<?php
include "koneksi.php";

$sql1 = "CREATE TABLE IF NOT EXISTS `pemeriksaan` (
  `id_pemeriksaan` int(11) NOT NULL AUTO_INCREMENT,
  `id_kendaraan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_part` int(11) NOT NULL,
  `tanggal_pemeriksaan` datetime NOT NULL,
  `status_qc` enum('LOLOS','TIDAK LOLOS') NOT NULL,
  `diagnosa` text DEFAULT NULL,
  `solusi` text DEFAULT NULL,
  PRIMARY KEY (`id_pemeriksaan`),
  KEY `id_kendaraan` (`id_kendaraan`),
  KEY `id_user` (`id_user`),
  KEY `id_part` (`id_part`),
  CONSTRAINT `pemeriksaan_ibfk_1` FOREIGN KEY (`id_kendaraan`) REFERENCES `kendaraan` (`id_kendaraan`) ON DELETE CASCADE,
  CONSTRAINT `pemeriksaan_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE,
  CONSTRAINT `pemeriksaan_ibfk_3` FOREIGN KEY (`id_part`) REFERENCES `part_kendaraan` (`id_part`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

$sql2 = "CREATE TABLE IF NOT EXISTS `pemeriksaan_gejala` (
  `id_pemeriksaan` int(11) NOT NULL,
  `id_gejala` int(11) NOT NULL,
  PRIMARY KEY (`id_pemeriksaan`,`id_gejala`),
  KEY `id_gejala` (`id_gejala`),
  CONSTRAINT `pemeriksaan_gejala_ibfk_1` FOREIGN KEY (`id_pemeriksaan`) REFERENCES `pemeriksaan` (`id_pemeriksaan`) ON DELETE CASCADE,
  CONSTRAINT `pemeriksaan_gejala_ibfk_2` FOREIGN KEY (`id_gejala`) REFERENCES `gejala` (`id_gejala`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if (mysqli_query($conn, $sql1)) {
    echo "Tabel pemeriksaan berhasil dibuat atau sudah ada.\n";
} else {
    echo "Gagal membuat tabel pemeriksaan: " . mysqli_error($conn) . "\n";
}

if (mysqli_query($conn, $sql2)) {
    echo "Tabel pemeriksaan_gejala berhasil dibuat atau sudah ada.\n";
} else {
    echo "Gagal membuat tabel pemeriksaan_gejala: " . mysqli_error($conn) . "\n";
}
?>
