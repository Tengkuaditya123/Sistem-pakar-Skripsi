-- SQL Script untuk Database Sistem Pakar Quality Control Kendaraan Rantis
-- Metode: Forward Chaining
-- Siap di-import langsung melalui phpMyAdmin

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `spfc_rantis_qc`
--
CREATE DATABASE IF NOT EXISTS `spfc_rantis_qc` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `spfc_rantis_qc`;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','petugas_qc') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nama`, `username`, `password`, `role`) VALUES
(1, 'Administrator SPV', 'admin', 'admin123', 'admin'),
(2, 'Petugas QC Lapangan', 'petugas', 'petugas123', 'petugas_qc');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kendaraan`
--

CREATE TABLE `kendaraan` (
  `id_kendaraan` int(11) NOT NULL AUTO_INCREMENT,
  `kode_kendaraan` varchar(50) NOT NULL,
  `nama_kendaraan` varchar(100) NOT NULL,
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id_kendaraan`),
  UNIQUE KEY `kode_kendaraan` (`kode_kendaraan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kendaraan`
--

INSERT INTO `kendaraan` (`id_kendaraan`, `kode_kendaraan`, `nama_kendaraan`, `keterangan`) VALUES
(1, 'ANOA-6X6-01', 'Panser Anoa 6x6 APC', 'Kendaraan Angkut Personel Sedang'),
(2, 'KMD-4X4-02', 'Rantis Komodo 4x4 Recon', 'Kendaraan Taktis Pengintai'),
(3, 'MNG-4X4-03', 'Rantis Maung 4x4', 'Kendaraan Taktis Ringan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `gejala`
--

CREATE TABLE `gejala` (
  `id_gejala` int(11) NOT NULL AUTO_INCREMENT,
  `kode_gejala` varchar(10) NOT NULL,
  `nama_gejala` text NOT NULL,
  PRIMARY KEY (`id_gejala`),
  UNIQUE KEY `kode_gejala` (`kode_gejala`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `gejala`
--

INSERT INTO `gejala` (`id_gejala`, `kode_gejala`, `nama_gejala`) VALUES
(1, 'G01', 'Kemudi terasa berat saat dibelokkan'),
(2, 'G02', 'Terdengar suara dengung/bising dari roda depan saat berbelok'),
(3, 'G03', 'Pedal rem terasa terlalu empuk atau amblas saat ditekan'),
(4, 'G04', 'Jarak pengereman terlalu jauh (rem kurang pakem)'),
(5, 'G05', 'Gigi transmisi sulit dipindahkan (keras)'),
(6, 'G06', 'Transmisi sering slip atau menyentak saat berpindah gigi'),
(7, 'G07', 'Mesin sulit dinyalakan saat kondisi dingin'),
(8, 'G08', 'Keluar asap tebal berwarna putih dari knalpot secara terus-menerus'),
(9, 'G09', 'Lampu indikator pengisian aki menyala terus di dashboard'),
(10, 'G10', 'Lampu utama rantis redup atau tidak menyala sama sekali');

-- --------------------------------------------------------

--
-- Struktur dari tabel `part_kendaraan`
--

CREATE TABLE `part_kendaraan` (
  `id_part` int(11) NOT NULL AUTO_INCREMENT,
  `kode_part` varchar(10) NOT NULL,
  `nama_part` varchar(100) NOT NULL,
  PRIMARY KEY (`id_part`),
  UNIQUE KEY `kode_part` (`kode_part`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `part_kendaraan`
--

INSERT INTO `part_kendaraan` (`id_part`, `kode_part`, `nama_part`) VALUES
(1, 'P01', 'Sistem Kemudi (Steering)'),
(2, 'P02', 'Sistem Pengereman (Brake)'),
(3, 'P03', 'Sistem Transmisi (Transmission)'),
(4, 'P04', 'Sistem Mesin (Engine)'),
(5, 'P05', 'Sistem Kelistrikan (Electrical)');

-- --------------------------------------------------------

--
-- Struktur dari tabel `rules`
--

CREATE TABLE `rules` (
  `id_rule` int(11) NOT NULL AUTO_INCREMENT,
  `id_gejala` int(11) NOT NULL,
  `id_part` int(11) NOT NULL,
  `keputusan` varchar(255) NOT NULL,
  PRIMARY KEY (`id_rule`),
  KEY `id_gejala` (`id_gejala`),
  KEY `id_part` (`id_part`),
  CONSTRAINT `rules_ibfk_1` FOREIGN KEY (`id_gejala`) REFERENCES `gejala` (`id_gejala`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `rules_ibfk_2` FOREIGN KEY (`id_part`) REFERENCES `part_kendaraan` (`id_part`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `rules`
--

INSERT INTO `rules` (`id_rule`, `id_gejala`, `id_part`, `keputusan`) VALUES
(1, 1, 1, 'Kerusakan pada pompa power steering atau terjadi kebocoran fluida steering'),
(2, 2, 1, 'Keausan pada steering gear box atau bearing roda depan'),
(3, 3, 2, 'Kebocoran minyak rem atau terdapat udara masuk di sistem hidrolik rem'),
(4, 4, 2, 'Kampas rem aus/tipis atau permukaan cakram rem tidak rata'),
(5, 5, 3, 'Kampas kopling tipis atau selang booster kopling mengalami kebocoran'),
(6, 6, 3, 'Solenoid transmisi rusak atau kualitas oli transmisi menurun/kotor'),
(7, 7, 4, 'Busi pijar (glow plug) rusak atau suplai bahan bakar dari supply pump terhambat'),
(8, 8, 4, 'Ring piston aus atau terjadi kebocoran oli mesin ke ruang bakar'),
(9, 9, 5, 'Kerusakan alternator atau sistem pengisian aki (undercharge)'),
(10, 10, 5, 'Kabel konektor sekering lampu kendor atau relay lampu utama putus');

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
