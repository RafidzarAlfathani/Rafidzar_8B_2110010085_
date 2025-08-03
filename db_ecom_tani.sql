-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2025 at 07:50 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ecom_tani`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `level` varchar(20) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `foto` varchar(255) NOT NULL,
  `status` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `nama`, `level`, `username`, `password`, `foto`, `status`) VALUES
(1, 'Admin Utama', 'Admin', 'admin', 'admin', 'admin.png', 'Aktif'),
(2, 'Kepala Balai', 'Pimpinan', 'pimpinan', 'pimpinan', 'user.png', 'Aktif');

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id_detail` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga_saat_pesan` decimal(10,0) NOT NULL,
  `sub_total` decimal(12,0) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail`, `id_pesanan`, `id_produk`, `jumlah`, `harga_saat_pesan`, `sub_total`) VALUES
(10, 7, 1, 10, '10000', '100000'),
(11, 7, 2, 5, '22000', '110000'),
(12, 7, 3, 3, '12000', '36000'),
(13, 7, 5, 5, '11000', '55000');

-- --------------------------------------------------------

--
-- Table structure for table `kategori_produk`
--

CREATE TABLE `kategori_produk` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kategori_produk`
--

INSERT INTO `kategori_produk` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Sayuran Daun'),
(2, 'Buah-buahan'),
(3, 'Umbi-umbian'),
(4, 'Rempah-rempah'),
(5, 'Padi & Palawija');

-- --------------------------------------------------------

--
-- Table structure for table `kurir`
--

CREATE TABLE `kurir` (
  `id_kurir` int(11) NOT NULL,
  `nama_kurir` varchar(100) NOT NULL,
  `telp` varchar(20) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('Tersedia','Bertugas','Tidak Aktif') NOT NULL DEFAULT 'Tersedia'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kurir`
--

INSERT INTO `kurir` (`id_kurir`, `nama_kurir`, `telp`, `username`, `password`, `status`) VALUES
(1, 'Bambang Sutejo', '081234567890', 'bambang', 'kurir123', 'Tersedia'),
(2, 'Agus Setiawan', '081234567891', 'agus', 'kurir123', 'Tersedia'),
(3, 'Siti Lestari', '081234567892', 'siti', 'kurir123', 'Bertugas'),
(4, 'Joko Haryono', '081234567893', 'joko', 'kurir123', 'Tidak Aktif'),
(5, 'Eko Prasetyo', '081234567894', 'eko', 'kurir123', 'Bertugas');

-- --------------------------------------------------------

--
-- Table structure for table `meta`
--

CREATE TABLE `meta` (
  `id_meta` int(11) NOT NULL,
  `instansi` varchar(255) DEFAULT NULL,
  `telp` varchar(50) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `pimpinan` varchar(255) DEFAULT NULL,
  `singkat` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `meta`
--

INSERT INTO `meta` (`id_meta`, `instansi`, `telp`, `email`, `alamat`, `logo`, `pimpinan`, `singkat`) VALUES
(1, 'Balai Pertanian Kecamatan Lokpaikat', '(0517) 123456', 'bp.lokpaikat@tapinkab.go.id', 'Jl. Raya Lokpaikat, Kecamatan Lokpaikat, Kabupaten Tapin, Kalimantan Selatan', 'logo.png', 'Ahmad Syahroni, S.P.', 'E-Tani Lokpaikat');

-- --------------------------------------------------------

--
-- Table structure for table `pembeli`
--

CREATE TABLE `pembeli` (
  `id_pembeli` int(11) NOT NULL,
  `nama_pembeli` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telp` varchar(20) NOT NULL,
  `foto_pembeli` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pembeli`
--

INSERT INTO `pembeli` (`id_pembeli`, `nama_pembeli`, `email`, `password`, `telp`, `foto_pembeli`) VALUES
(1, 'Anisa Rahmawati', 'anisa@example.com', 'pembeli123', '0895329695138', 'default.png'),
(2, 'Budi Santoso', 'budi@example.com', 'pembeli123', '0895329695138', 'default.png'),
(3, 'Citra Kirana', 'citra@example.com', 'pembeli123', '0895329695138', 'default.png'),
(4, 'Dewi Lestari', 'dewi@example.com', 'pembeli123', '0895329695138', 'default.png'),
(5, 'Eka Kurniawan', 'eka@example.com', 'pembeli123', '0895329695138', 'default.png');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id_pesanan` int(11) NOT NULL,
  `kode_invoice` varchar(50) NOT NULL,
  `id_pembeli` int(11) NOT NULL,
  `id_kurir` int(11) DEFAULT NULL,
  `alamat_pengiriman` text NOT NULL,
  `total_bayar` decimal(12,0) NOT NULL,
  `ongkir` decimal(10,0) NOT NULL,
  `tgl_pesan` timestamp NOT NULL DEFAULT current_timestamp(),
  `status_pesanan` enum('Menunggu Pembayaran','Menunggu Verifikasi','Diproses','Dikirim','Selesai','Dibatalkan') NOT NULL DEFAULT 'Menunggu Pembayaran',
  `metode_pembayaran` varchar(50) DEFAULT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `tgl_bayar` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `kode_invoice`, `id_pembeli`, `id_kurir`, `alamat_pengiriman`, `total_bayar`, `ongkir`, `tgl_pesan`, `status_pesanan`, `metode_pembayaran`, `bukti_bayar`, `tgl_bayar`) VALUES
(7, 'INV-20250615-130650-4', 4, 2, 'A paragraph generator is a tool, often powered by AI, that helps users create paragraphs of text based on given prompts or inputs. It can be used to quickly generate content for various purposes, such as blog posts, marketing materials, or even academic writing. These generators can save time and effort, especially when dealing with writer\'s block or tight deadlines. ', '316000', '15000', '2025-06-15 05:06:50', 'Selesai', 'Transfer Bank', 'INV-20250615-130650-4_1749964078.png', '2025-06-15 13:07:58');

-- --------------------------------------------------------

--
-- Table structure for table `petani`
--

CREATE TABLE `petani` (
  `id_petani` int(11) NOT NULL,
  `nama_petani` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `telp` varchar(20) NOT NULL,
  `alamat_petani` text NOT NULL,
  `foto_petani` varchar(255) DEFAULT 'default.png',
  `status_akun` enum('Aktif','Non-Aktif','Menunggu Verifikasi') NOT NULL DEFAULT 'Menunggu Verifikasi'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `petani`
--

INSERT INTO `petani` (`id_petani`, `nama_petani`, `email`, `password`, `telp`, `alamat_petani`, `foto_petani`, `status_akun`) VALUES
(1, 'Suparman', 'suparman@petani.com', 'petani123', '0811500111', 'Desa Binderang, Lokpaikat', 'default.png', 'Aktif'),
(2, 'Paijo', 'paijo@petani.com', 'petani123', '0811500222', 'Desa Ayunan Papan, Lokpaikat', 'default.png', 'Aktif'),
(3, 'Slamet Riyadi', 'slamet@petani.com', 'petani123', '0811500333', 'Desa Bitahan, Lokpaikat', 'default.png', 'Aktif'),
(4, 'Joko Susilo', 'jokos@petani.com', 'petani123', '0811500444', 'Desa Bataratat, Lokpaikat', 'default.png', 'Aktif'),
(5, 'Siti Aminah', 'sitia@petani.com', 'petani123', '0811500555', 'Desa Parandakan, Lokpaikat', 'default.png', 'Menunggu Verifikasi');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id_produk` int(11) NOT NULL,
  `id_petani` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `nama_produk` varchar(255) NOT NULL,
  `deskripsi` text NOT NULL,
  `harga` decimal(10,0) NOT NULL,
  `satuan` varchar(20) NOT NULL COMMENT 'Contoh: kg, ikat, buah',
  `stok` int(11) NOT NULL,
  `foto_produk` varchar(255) NOT NULL,
  `status_produk` enum('Tersedia','Habis') NOT NULL DEFAULT 'Tersedia',
  `tgl_upload` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id_produk`, `id_petani`, `id_kategori`, `nama_produk`, `deskripsi`, `harga`, `satuan`, `stok`, `foto_produk`, `status_produk`, `tgl_upload`) VALUES
(1, 1, 1, 'Bayam Segar Organik', 'Bayam segar baru petik dari kebun, tanpa pestisida. Cocok untuk masakan bening.', '10000', 'ikat', 37, 'bayam organis.jpg', 'Tersedia', '2025-06-15 04:04:32'),
(2, 2, 2, 'Mangga Harum Manis Super', 'Mangga Harum Manis pilihan, rasa manis dan legit. Berat rata-rata 0.5 kg per buah.', '22000', 'kg', 92, 'mangga harum manis.jpg', 'Tersedia', '2025-06-15 04:04:32'),
(3, 3, 3, 'Singkong Mentega', 'Singkong jenis mentega, pulen dan cocok untuk direbus atau digoreng.', '12000', 'kg', 77, 'singkong mentega.jpg', 'Tersedia', '2025-06-15 04:04:32'),
(4, 1, 4, 'Jahe Merah Asli', 'Jahe merah asli dari pegunungan, cocok untuk minuman kesehatan.', '15000', 'kg', 30, 'jahe merah.jpg', 'Tersedia', '2025-06-15 04:04:32'),
(5, 4, 5, 'Beras Lokal Siam Unus', 'Beras unus asli dari sawah tadah hujan, pulen dan wangi.', '11000', 'kg', 195, 'beras siam.jpg', 'Tersedia', '2025-06-15 04:04:32');

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_harga`
--

CREATE TABLE `riwayat_harga` (
  `id_riwayat` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `harga_lama` decimal(10,0) NOT NULL,
  `harga_baru` decimal(10,0) NOT NULL,
  `tgl_perubahan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `riwayat_harga`
--

INSERT INTO `riwayat_harga` (`id_riwayat`, `id_produk`, `harga_lama`, `harga_baru`, `tgl_perubahan`) VALUES
(1, 2, '20000', '22000', '2025-06-10 09:00:00'),
(2, 1, '4500', '5000', '2025-06-11 09:00:00'),
(3, 3, '10000', '12000', '2025-06-12 09:00:00'),
(4, 5, '10000', '11000', '2025-06-13 09:00:00'),
(5, 4, '13000', '15000', '2025-06-14 09:00:00'),
(6, 1, '5000', '8000', '2025-06-15 00:55:04'),
(7, 1, '8000', '10000', '2025-06-15 01:03:46');

-- --------------------------------------------------------

--
-- Table structure for table `tracking_pengiriman`
--

CREATE TABLE `tracking_pengiriman` (
  `id_tracking` int(11) NOT NULL,
  `id_pesanan` int(11) NOT NULL,
  `latitude` varchar(50) NOT NULL,
  `longitude` varchar(50) NOT NULL,
  `keterangan` text NOT NULL,
  `waktu_update` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tracking_pengiriman`
--

INSERT INTO `tracking_pengiriman` (`id_tracking`, `id_pesanan`, `latitude`, `longitude`, `keterangan`, `waktu_update`) VALUES
(8, 7, '-2.977756', '115.267883', 'Dijemput', '2025-06-15 05:41:00'),
(9, 7, '-2.968156', '115.277550', 'Disortir', '2025-06-15 05:41:49'),
(11, 7, '-2.960886', '115.276605', 'Pesanana diterima pembeli ', '2025-06-15 05:42:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `kategori_produk`
--
ALTER TABLE `kategori_produk`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `kurir`
--
ALTER TABLE `kurir`
  ADD PRIMARY KEY (`id_kurir`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `meta`
--
ALTER TABLE `meta`
  ADD PRIMARY KEY (`id_meta`);

--
-- Indexes for table `pembeli`
--
ALTER TABLE `pembeli`
  ADD PRIMARY KEY (`id_pembeli`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD UNIQUE KEY `kode_invoice` (`kode_invoice`),
  ADD KEY `id_pembeli` (`id_pembeli`),
  ADD KEY `id_kurir` (`id_kurir`);

--
-- Indexes for table `petani`
--
ALTER TABLE `petani`
  ADD PRIMARY KEY (`id_petani`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `id_petani` (`id_petani`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `riwayat_harga`
--
ALTER TABLE `riwayat_harga`
  ADD PRIMARY KEY (`id_riwayat`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indexes for table `tracking_pengiriman`
--
ALTER TABLE `tracking_pengiriman`
  ADD PRIMARY KEY (`id_tracking`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `kategori_produk`
--
ALTER TABLE `kategori_produk`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `kurir`
--
ALTER TABLE `kurir`
  MODIFY `id_kurir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `meta`
--
ALTER TABLE `meta`
  MODIFY `id_meta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pembeli`
--
ALTER TABLE `pembeli`
  MODIFY `id_pembeli` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `petani`
--
ALTER TABLE `petani`
  MODIFY `id_petani` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `riwayat_harga`
--
ALTER TABLE `riwayat_harga`
  MODIFY `id_riwayat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tracking_pengiriman`
--
ALTER TABLE `tracking_pengiriman`
  MODIFY `id_tracking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE;

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_pembeli`) REFERENCES `pembeli` (`id_pembeli`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`id_kurir`) REFERENCES `kurir` (`id_kurir`) ON DELETE SET NULL;

--
-- Constraints for table `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_petani`) REFERENCES `petani` (`id_petani`) ON DELETE CASCADE,
  ADD CONSTRAINT `produk_ibfk_2` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_produk` (`id_kategori`) ON DELETE CASCADE;

--
-- Constraints for table `riwayat_harga`
--
ALTER TABLE `riwayat_harga`
  ADD CONSTRAINT `riwayat_harga_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE;

--
-- Constraints for table `tracking_pengiriman`
--
ALTER TABLE `tracking_pengiriman`
  ADD CONSTRAINT `tracking_pengiriman_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
