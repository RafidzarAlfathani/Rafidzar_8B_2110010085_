-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 06 Agu 2025 pada 09.05
-- Versi server: 10.4.11-MariaDB
-- Versi PHP: 7.4.4

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
-- Struktur dari tabel `admin`
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
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `nama`, `level`, `username`, `password`, `foto`, `status`) VALUES
(1, 'Admin Utama', 'Admin', 'admin', 'admin', 'admin.png', 'Aktif'),
(2, 'Kepala Balai', 'Pimpinan', 'pimpinan', 'pimpinan', 'user.png', 'Aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `detail_pesanan`
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
-- Dumping data untuk tabel `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id_detail`, `id_pesanan`, `id_produk`, `jumlah`, `harga_saat_pesan`, `sub_total`) VALUES
(10, 7, 1, 10, '10000', '100000'),
(11, 7, 2, 5, '22000', '110000'),
(12, 7, 3, 3, '12000', '36000'),
(13, 7, 5, 5, '11000', '55000'),
(14, 8, 1, 7, '10000', '70000'),
(22, 16, 4, 6, '15000', '90000'),
(24, 18, 4, 2, '15000', '30000'),
(25, 19, 6, 1, '4500', '4500'),
(31, 25, 8, 5, '6600', '33000'),
(32, 26, 7, 3, '8000', '24000'),
(33, 27, 7, 3, '9000', '27000'),
(34, 28, 2, 5, '25000', '125000');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori_produk`
--

CREATE TABLE `kategori_produk` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `kategori_produk`
--

INSERT INTO `kategori_produk` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Sayuran Daun'),
(2, 'Buah-buahan'),
(3, 'Umbi-umbian'),
(4, 'Rempah-rempah'),
(5, 'Padi & Palawija'),
(6, 'Biji-Bijian');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kurir`
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
-- Dumping data untuk tabel `kurir`
--

INSERT INTO `kurir` (`id_kurir`, `nama_kurir`, `telp`, `username`, `password`, `status`) VALUES
(1, 'Bambang Sutejo', '081234567890', 'bambang', 'kurir123', 'Tersedia'),
(2, 'Agus Setiawan', '085156317868', 'agus', 'kurir123', 'Tersedia'),
(3, 'Siti Lestari', '085787126487', 'siti', 'kurir123', 'Bertugas'),
(4, 'Joko Haryono', '081234567893', 'joko', 'kurir123', 'Tidak Aktif'),
(5, 'Eko Prasetyo', '081234567894', 'eko', 'kurir123', 'Bertugas');

-- --------------------------------------------------------

--
-- Struktur dari tabel `meta`
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
-- Dumping data untuk tabel `meta`
--

INSERT INTO `meta` (`id_meta`, `instansi`, `telp`, `email`, `alamat`, `logo`, `pimpinan`, `singkat`) VALUES
(1, 'Balai Pertanian Kecamatan Lokpaikat', '(0517) 123456', 'bp.lokpaikat@tapinkab.go.id', 'Jl. Raya Lokpaikat, Kecamatan Lokpaikat, Kabupaten Tapin, Kalimantan Selatan', 'logo.png', 'Tata Suryana, S.P.', 'E-Tani Lokpaikat');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembeli`
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
-- Dumping data untuk tabel `pembeli`
--

INSERT INTO `pembeli` (`id_pembeli`, `nama_pembeli`, `email`, `password`, `telp`, `foto_pembeli`) VALUES
(1, 'Alfath hani', 'muh.hani21@gmail.com', 'pembeli123', '085787126487', 'default.png'),
(2, 'Budi Santoso', 'budi@example.com', 'pembeli123', '085787126487', 'default.png'),
(3, 'Citra Kirana', 'citra@example.com', 'pembeli123', '085787126487', 'default.png'),
(4, 'Dewi Lestari', 'dewi@example.com', 'pembeli123', '085787126487', 'default.png'),
(5, 'Eka Kurniawan', 'eka@example.com', 'pembeli123', '085787126487', 'default.png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan_dana_kurir`
--

CREATE TABLE `pengajuan_dana_kurir` (
  `id_pengajuan` int(11) NOT NULL,
  `id_kurir` int(11) NOT NULL,
  `jumlah_dana` decimal(15,2) NOT NULL,
  `metode` varchar(50) NOT NULL,
  `catatan` text DEFAULT NULL,
  `status` enum('Menunggu','Disetujui','Ditolak') DEFAULT 'Menunggu',
  `tanggal_verifikasi` datetime DEFAULT NULL,
  `diverifikasi_oleh` varchar(100) DEFAULT NULL,
  `tanggal_pengajuan` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `pengajuan_dana_kurir`
--

INSERT INTO `pengajuan_dana_kurir` (`id_pengajuan`, `id_kurir`, `jumlah_dana`, `metode`, `catatan`, `status`, `tanggal_verifikasi`, `diverifikasi_oleh`, `tanggal_pengajuan`) VALUES
(1, 3, '8000.00', 'Cash di Balai', 'beli bensin', 'Disetujui', '2025-08-06 08:17:43', 'Admin Utama', '2025-08-06 07:43:11');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan_dana_petani`
--

CREATE TABLE `pengajuan_dana_petani` (
  `id_pengajuan` int(11) NOT NULL,
  `id_petani` int(11) NOT NULL,
  `jumlah_dana` bigint(20) NOT NULL,
  `metode` varchar(20) NOT NULL,
  `catatan` text DEFAULT NULL,
  `status` enum('Menunggu','Disetujui','Ditolak') DEFAULT 'Menunggu',
  `tanggal_pengajuan` datetime DEFAULT current_timestamp(),
  `tanggal_verifikasi` datetime DEFAULT NULL,
  `diverifikasi_oleh` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `pengajuan_dana_petani`
--

INSERT INTO `pengajuan_dana_petani` (`id_pengajuan`, `id_petani`, `jumlah_dana`, `metode`, `catatan`, `status`, `tanggal_pengajuan`, `tanggal_verifikasi`, `diverifikasi_oleh`) VALUES
(1, 1, 100000, 'Cash di Balai', 'beli pupuk', 'Disetujui', '2025-08-05 00:00:00', '2025-08-06 00:00:00', 1),
(4, 1, 10000, 'Cash di Balai', 'beli beli', 'Ditolak', '2025-08-06 00:00:00', '2025-08-06 00:00:00', 1),
(5, 1, 15000, 'Cash di Balai', 'aaaaaa', 'Disetujui', '2025-08-06 00:00:00', '2025-08-06 00:00:00', 1),
(6, 1, 3000, 'Cash di Balai', 'bbbb', 'Disetujui', '2025-08-06 00:00:00', '2025-08-06 00:00:00', 1),
(7, 1, 20000, 'Transfer', 'beli kuda', 'Disetujui', '2025-08-06 00:00:00', '2025-08-06 00:00:00', 1),
(8, 1, 12000, 'Cash di Balai', 'ambil cash di balai', 'Disetujui', '2025-08-06 00:00:00', '2025-08-06 00:00:00', 1),
(9, 1, 5000, 'Cash di Balai', 'hhhhh', 'Disetujui', '2025-08-06 00:00:00', '2025-08-06 00:00:00', 1),
(10, 2, 20000, 'Cash di Balai', 'untuk beli pupuk', 'Ditolak', '2025-08-06 00:00:00', '2025-08-06 00:00:00', 1),
(11, 2, 30000, 'Cash di Balai', 'beli bibit baru', 'Disetujui', '2025-08-06 00:00:00', '2025-08-06 00:00:00', 1),
(12, 1, 23000, 'Transfer', 'beli pupuk dan bibit baru boss', 'Disetujui', '2025-08-06 00:00:00', '2025-08-06 08:23:55', 0),
(13, 1, 10500, 'Transfer', 'cek lagi boss', 'Disetujui', '2025-08-06 00:00:00', '2025-08-06 08:26:47', 0),
(14, 1, 3000, 'Transfer', 'cekkkkkk', 'Disetujui', '2025-08-06 00:00:00', '2025-08-06 08:28:47', 0),
(15, 1, 1000, 'Cash di Balai', 'cek.......', 'Disetujui', '2025-08-06 00:00:00', '2025-08-06 08:31:03', 0),
(16, 1, 16, 'Transfer', 'cek hapus bos', 'Ditolak', '2025-08-06 00:00:00', '2025-08-06 08:44:22', 0);

-- --------------------------------------------------------

--
-- Struktur dari tabel `pesanan`
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
  `tgl_bayar` datetime DEFAULT NULL,
  `bukti_sampai` varchar(255) DEFAULT NULL,
  `biaya_admin` int(11) NOT NULL COMMENT 'Biaya admin layanan untuk penyedia layanan'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `pesanan`
--

INSERT INTO `pesanan` (`id_pesanan`, `kode_invoice`, `id_pembeli`, `id_kurir`, `alamat_pengiriman`, `total_bayar`, `ongkir`, `tgl_pesan`, `status_pesanan`, `metode_pembayaran`, `bukti_bayar`, `tgl_bayar`, `bukti_sampai`, `biaya_admin`) VALUES
(7, 'INV-20250615-130650-4', 4, 2, 'A paragraph generator is a tool, often powered by AI, that helps users create paragraphs of text based on given prompts or inputs. It can be used to quickly generate content for various purposes, such as blog posts, marketing materials, or even academic writing. These generators can save time and effort, especially when dealing with writer\'s block or tight deadlines. ', '316000', '15000', '2025-06-15 05:06:50', 'Selesai', 'Transfer Bank', 'INV-20250615-130650-4_1749964078.png', '2025-06-15 13:07:58', NULL, 0),
(8, 'INV-20250705-143327-3', 3, 1, 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.', '85000', '15000', '2025-07-05 06:33:27', 'Selesai', 'Transfer Bank', 'INV-20250705-143327-3_1751697233.jpg', '2025-07-05 14:33:53', NULL, 0),
(16, 'INV-20250721-122911-2', 2, 3, 'lokpakat lagi', '105000', '15000', '2025-07-21 04:29:11', 'Selesai', 'Transfer Bank', 'INV-20250721-122911-2_1753072306.jpg', '2025-07-21 12:31:46', NULL, 0),
(18, 'INV-20250721-125019-2', 2, NULL, 'azml', '45000', '15000', '2025-07-21 04:50:19', 'Selesai', 'Transfer Bank', 'INV-20250721-125019-2_1753073580.jpg', '2025-07-21 12:53:00', NULL, 0),
(19, 'INV-20250802-022544-2', 2, NULL, 'aaaaaaaaaa', '19500', '15000', '2025-08-01 18:25:44', 'Selesai', 'Transfer Bank', 'INV-20250802-022544-2_1754188683.jpg', '2025-08-03 10:38:03', NULL, 0),
(25, 'INV-20250805-104405-2', 2, 3, 'handil bakti', '48000', '15000', '2025-08-05 02:44:05', 'Selesai', 'Transfer Bank', 'INV-20250805-104405-2_1754361865.jpg', '2025-08-05 10:44:25', 'bukti_sampai_25_1754362128.jpg', 0),
(26, 'INV-20250805-190416-2', 2, NULL, 'binderanggg', '39000', '15000', '2025-08-05 11:04:16', 'Selesai', 'Transfer Bank', NULL, NULL, NULL, 0),
(27, 'INV-20250805-202639-2', 2, NULL, 'hhhhh', '42000', '15000', '2025-08-05 12:26:39', 'Selesai', 'Transfer Bank', 'INV-20250805-202639-2_1754396856.jpg', '2025-08-05 20:27:36', NULL, 0),
(28, 'INV-20250806-145430-2', 2, NULL, 'binderang lokapikat', '141500', '15000', '2025-08-06 06:54:30', 'Menunggu Pembayaran', 'Transfer Bank', NULL, NULL, NULL, 1500);

-- --------------------------------------------------------

--
-- Struktur dari tabel `petani`
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
-- Dumping data untuk tabel `petani`
--

INSERT INTO `petani` (`id_petani`, `nama_petani`, `email`, `password`, `telp`, `alamat_petani`, `foto_petani`, `status_akun`) VALUES
(1, 'Suparman', 'muh.hani21@gmail.com', 'petani123', '085787126487', 'Desa Binderang, Lokpaikat', 'avatar-2.jpg', 'Aktif'),
(2, 'Paijo', 'paijo@petani.com', 'petani123', '085787126487', 'Desa Ayunan Papan, Lokpaikat', 'default.png', 'Aktif'),
(3, 'Slamet Riyadi', 'slamet@petani.com', 'petani123', '085787126487', 'Desa Bitahan, Lokpaikat', 'default.png', 'Aktif'),
(4, 'Joko Susilo', 'jokos@petani.com', 'petani123', '085787126487', 'Desa Bataratat, Lokpaikat', 'default.png', 'Aktif'),
(5, 'Siti Aminah', 'sitia@petani.com', 'petani123', '085787126487', 'Desa Parandakan, Lokpaikat', 'default.png', 'Non-Aktif');

-- --------------------------------------------------------

--
-- Struktur dari tabel `produk`
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
  `tgl_upload` timestamp NULL DEFAULT current_timestamp(),
  `minimum_pembelian` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `produk`
--

INSERT INTO `produk` (`id_produk`, `id_petani`, `id_kategori`, `nama_produk`, `deskripsi`, `harga`, `satuan`, `stok`, `foto_produk`, `status_produk`, `tgl_upload`, `minimum_pembelian`) VALUES
(1, 1, 1, 'Bayam Segar Organik', 'Bayam segar baru petik dari kebun, tanpa pestisida. Cocok untuk masakan bening.', '10000', 'ikat', 30, 'bayam organis.jpg', 'Tersedia', '2025-06-15 04:04:32', 1),
(2, 2, 2, 'Mangga Harum Manis Super', 'Mangga Harum Manis pilihan, rasa manis dan legit. Berat rata-rata 0.5 kg per buah.', '25000', 'kg', 86, 'mangga harum manis.jpg', 'Tersedia', '2025-06-15 04:04:32', 1),
(3, 3, 3, 'Singkong Mentega', 'Singkong jenis mentega, pulen dan cocok untuk direbus atau digoreng.', '12000', 'kg', 77, 'singkong mentega.jpg', 'Tersedia', '2025-06-15 04:04:32', 1),
(4, 1, 4, 'Jahe Merah Asli', 'Jahe merah asli dari pegunungan, cocok untuk minuman kesehatan.', '15000', 'kg', 0, 'jahe merah.jpg', 'Tersedia', '2025-06-15 04:04:32', 1),
(5, 4, 5, 'Beras Lokal Siam Unus', 'Beras unus asli dari sawah tadah hujan, pulen dan wangi.', '10500', 'kg', 195, 'beras siam.jpg', 'Tersedia', '2025-06-15 04:04:32', 1),
(6, 1, 2, 'Buah Naga', 'Buah naga (Inggris: pitaya) adalah buah dari beberapa jenis kaktus dari genus Hylocereus dan Selenicereus. Buah ini berasal dari Meksiko, Amerika Tengah dan Amerika Selatan namun sekarang juga dibudidayakan di negara-negara Asia seperti Taiwan, Vietnam, Filipina, Indonesia dan Malaysia. Buah ini juga dapat ditemui di Okinawa, Palestina, Australia utara dan Tiongkok selatan. Hylocereus hanya mekar pada malam hari.', '4500', 'Kg', 27, 'buah naga.jpg', 'Tersedia', '2025-07-05 06:53:55', 2),
(7, 1, 2, 'Semangka', 'Semangka adalah buah yang populer, dikenal karena rasanya yang manis dan menyegarkan, serta kandungan airnya yang tinggi. Buah ini kaya akan nutrisi dan memiliki berbagai manfaat kesehatan, termasuk menjaga hidrasi, mendukung kesehatan jantung, dan berpotensi mengurangi risiko kanker. Semangka juga mengandung vitamin, mineral, dan antioksidan seperti likopen dan citrulline yang bermanfaat bagi tubuh. ', '9000', 'Kg', 83, 'sem.jpg', 'Tersedia', '2025-07-05 06:59:08', 1),
(8, 1, 2, 'Mangga Segar', 'Mangga Segar Dan Manis ', '6700', 'buah', 13, 'product12.jpg', 'Tersedia', '2025-07-21 04:20:23', 5);

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat_harga`
--

CREATE TABLE `riwayat_harga` (
  `id_riwayat` int(11) NOT NULL,
  `id_produk` int(11) NOT NULL,
  `harga_lama` decimal(10,0) NOT NULL,
  `harga_baru` decimal(10,0) NOT NULL,
  `tgl_perubahan` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `riwayat_harga`
--

INSERT INTO `riwayat_harga` (`id_riwayat`, `id_produk`, `harga_lama`, `harga_baru`, `tgl_perubahan`) VALUES
(1, 2, '20000', '22000', '2025-06-10 09:00:00'),
(2, 1, '4500', '5000', '2025-06-11 09:00:00'),
(3, 3, '10000', '12000', '2025-06-12 09:00:00'),
(4, 5, '10000', '11000', '2025-06-13 09:00:00'),
(5, 4, '13000', '15000', '2025-06-14 09:00:00'),
(6, 1, '5000', '8000', '2025-06-15 00:55:04'),
(7, 1, '8000', '10000', '2025-06-15 01:03:46'),
(8, 2, '22000', '25000', '2025-07-05 10:29:46'),
(9, 8, '5000', '6000', '2025-08-01 18:44:01'),
(10, 7, '0', '0', '2025-08-01 19:24:11'),
(11, 7, '0', '0', '2025-08-01 19:24:11'),
(12, 7, '5000', '5500', '2025-08-01 19:24:22'),
(13, 7, '5000', '5500', '2025-08-01 19:24:22'),
(14, 7, '0', '0', '2025-08-01 19:24:22'),
(15, 8, '0', '0', '2025-08-01 19:25:09'),
(16, 8, '0', '0', '2025-08-01 19:25:09'),
(17, 8, '0', '0', '2025-08-01 19:28:22'),
(18, 8, '0', '0', '2025-08-01 19:28:22'),
(19, 8, '0', '0', '2025-08-01 19:30:25'),
(20, 8, '0', '0', '2025-08-01 19:30:25'),
(21, 8, '0', '0', '2025-08-01 19:33:15'),
(22, 8, '0', '0', '2025-08-01 19:33:15'),
(23, 8, '0', '0', '2025-08-01 19:33:15'),
(24, 8, '6000', '6600', '2025-08-01 19:33:24'),
(25, 8, '6000', '6600', '2025-08-01 19:33:24'),
(26, 8, '0', '0', '2025-08-01 19:33:24'),
(27, 8, '0', '0', '2025-08-01 19:33:25'),
(28, 7, '0', '0', '2025-08-01 19:34:41'),
(29, 7, '0', '0', '2025-08-01 19:34:41'),
(30, 7, '0', '0', '2025-08-01 19:34:42'),
(31, 7, '5500', '7500', '2025-08-01 19:36:26'),
(32, 8, '6600', '6700', '2025-08-05 02:55:18'),
(33, 7, '7500', '8000', '2025-08-05 02:55:56'),
(34, 5, '11000', '10500', '2025-08-05 02:56:14'),
(35, 7, '8000', '9000', '2025-08-05 12:25:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `riwayat_penarikan_dana`
--

CREATE TABLE `riwayat_penarikan_dana` (
  `id_riwayat` int(11) NOT NULL,
  `id_pengajuan` int(11) NOT NULL,
  `id_petani` int(11) NOT NULL,
  `jumlah_dana` decimal(12,2) NOT NULL,
  `metode_penarikan` varchar(50) NOT NULL,
  `status_penarikan` varchar(20) NOT NULL DEFAULT 'diproses',
  `tanggal_penarikan` datetime DEFAULT current_timestamp(),
  `catatan_admin` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `riwayat_penarikan_dana`
--

INSERT INTO `riwayat_penarikan_dana` (`id_riwayat`, `id_pengajuan`, `id_petani`, `jumlah_dana`, `metode_penarikan`, `status_penarikan`, `tanggal_penarikan`, `catatan_admin`) VALUES
(1, 5, 1, '15000.00', '', 'Disetujui', '2025-08-06 00:00:00', ''),
(2, 6, 1, '3000.00', '', 'Disetujui', '2025-08-06 00:00:00', ''),
(3, 7, 1, '20000.00', 'transfer', 'Disetujui', '2025-08-06 00:00:00', ''),
(4, 8, 1, '12000.00', 'Cash di Balai', 'Disetujui', '2025-08-06 00:00:00', ''),
(5, 9, 1, '5000.00', 'Cash di Balai', 'Disetujui', '2025-08-06 00:00:00', ''),
(6, 11, 2, '30000.00', 'Cash di Balai', 'Disetujui', '2025-08-06 00:00:00', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tracking_pengiriman`
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
-- Dumping data untuk tabel `tracking_pengiriman`
--

INSERT INTO `tracking_pengiriman` (`id_tracking`, `id_pesanan`, `latitude`, `longitude`, `keterangan`, `waktu_update`) VALUES
(8, 7, '-2.977756', '115.267883', 'Dijemput', '2025-06-15 05:41:00'),
(9, 7, '-2.968156', '115.277550', 'Disortir', '2025-06-15 05:41:49'),
(11, 7, '-2.960886', '115.276605', 'Pesanana diterima pembeli ', '2025-06-15 05:42:54'),
(12, 8, '-2.980842', '115.265786', 'Dalam Pengantran', '2025-07-05 07:22:18'),
(13, 8, '-2.962670', '115.271969', 'Pos ', '2025-07-05 07:22:30'),
(14, 8, '-2.953702', '115.264377', 'Pesanan diterima', '2025-07-05 07:22:45'),
(25, 25, '-2.910879', '115.212619', 'Menjempu pesanan', '2025-08-05 02:47:12'),
(26, 25, '-2.945030', '115.166623', 'sampai di tujuan ', '2025-08-05 02:48:12');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indeks untuk tabel `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id_detail`),
  ADD KEY `id_pesanan` (`id_pesanan`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indeks untuk tabel `kategori_produk`
--
ALTER TABLE `kategori_produk`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `kurir`
--
ALTER TABLE `kurir`
  ADD PRIMARY KEY (`id_kurir`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `meta`
--
ALTER TABLE `meta`
  ADD PRIMARY KEY (`id_meta`);

--
-- Indeks untuk tabel `pembeli`
--
ALTER TABLE `pembeli`
  ADD PRIMARY KEY (`id_pembeli`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `pengajuan_dana_kurir`
--
ALTER TABLE `pengajuan_dana_kurir`
  ADD PRIMARY KEY (`id_pengajuan`),
  ADD KEY `id_kurir` (`id_kurir`);

--
-- Indeks untuk tabel `pengajuan_dana_petani`
--
ALTER TABLE `pengajuan_dana_petani`
  ADD PRIMARY KEY (`id_pengajuan`),
  ADD KEY `id_petani` (`id_petani`);

--
-- Indeks untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id_pesanan`),
  ADD UNIQUE KEY `kode_invoice` (`kode_invoice`),
  ADD KEY `id_pembeli` (`id_pembeli`),
  ADD KEY `id_kurir` (`id_kurir`);

--
-- Indeks untuk tabel `petani`
--
ALTER TABLE `petani`
  ADD PRIMARY KEY (`id_petani`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id_produk`),
  ADD KEY `id_petani` (`id_petani`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `riwayat_harga`
--
ALTER TABLE `riwayat_harga`
  ADD PRIMARY KEY (`id_riwayat`),
  ADD KEY `id_produk` (`id_produk`);

--
-- Indeks untuk tabel `riwayat_penarikan_dana`
--
ALTER TABLE `riwayat_penarikan_dana`
  ADD PRIMARY KEY (`id_riwayat`),
  ADD KEY `id_pengajuan` (`id_pengajuan`),
  ADD KEY `id_petani` (`id_petani`);

--
-- Indeks untuk tabel `tracking_pengiriman`
--
ALTER TABLE `tracking_pengiriman`
  ADD PRIMARY KEY (`id_tracking`),
  ADD KEY `id_pesanan` (`id_pesanan`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT untuk tabel `kategori_produk`
--
ALTER TABLE `kategori_produk`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `kurir`
--
ALTER TABLE `kurir`
  MODIFY `id_kurir` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `meta`
--
ALTER TABLE `meta`
  MODIFY `id_meta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pembeli`
--
ALTER TABLE `pembeli`
  MODIFY `id_pembeli` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `pengajuan_dana_kurir`
--
ALTER TABLE `pengajuan_dana_kurir`
  MODIFY `id_pengajuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `pengajuan_dana_petani`
--
ALTER TABLE `pengajuan_dana_petani`
  MODIFY `id_pengajuan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id_pesanan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT untuk tabel `petani`
--
ALTER TABLE `petani`
  MODIFY `id_petani` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `produk`
--
ALTER TABLE `produk`
  MODIFY `id_produk` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `riwayat_harga`
--
ALTER TABLE `riwayat_harga`
  MODIFY `id_riwayat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `riwayat_penarikan_dana`
--
ALTER TABLE `riwayat_penarikan_dana`
  MODIFY `id_riwayat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `tracking_pengiriman`
--
ALTER TABLE `tracking_pengiriman`
  MODIFY `id_tracking` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengajuan_dana_kurir`
--
ALTER TABLE `pengajuan_dana_kurir`
  ADD CONSTRAINT `pengajuan_dana_kurir_ibfk_1` FOREIGN KEY (`id_kurir`) REFERENCES `kurir` (`id_kurir`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pengajuan_dana_petani`
--
ALTER TABLE `pengajuan_dana_petani`
  ADD CONSTRAINT `pengajuan_dana_petani_ibfk_1` FOREIGN KEY (`id_petani`) REFERENCES `petani` (`id_petani`);

--
-- Ketidakleluasaan untuk tabel `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`id_pembeli`) REFERENCES `pembeli` (`id_pembeli`) ON DELETE CASCADE,
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`id_kurir`) REFERENCES `kurir` (`id_kurir`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `produk`
--
ALTER TABLE `produk`
  ADD CONSTRAINT `produk_ibfk_1` FOREIGN KEY (`id_petani`) REFERENCES `petani` (`id_petani`) ON DELETE CASCADE,
  ADD CONSTRAINT `produk_ibfk_2` FOREIGN KEY (`id_kategori`) REFERENCES `kategori_produk` (`id_kategori`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `riwayat_harga`
--
ALTER TABLE `riwayat_harga`
  ADD CONSTRAINT `riwayat_harga_ibfk_1` FOREIGN KEY (`id_produk`) REFERENCES `produk` (`id_produk`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `riwayat_penarikan_dana`
--
ALTER TABLE `riwayat_penarikan_dana`
  ADD CONSTRAINT `riwayat_penarikan_dana_ibfk_1` FOREIGN KEY (`id_pengajuan`) REFERENCES `pengajuan_dana_petani` (`id_pengajuan`) ON DELETE CASCADE,
  ADD CONSTRAINT `riwayat_penarikan_dana_ibfk_2` FOREIGN KEY (`id_petani`) REFERENCES `petani` (`id_petani`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `tracking_pengiriman`
--
ALTER TABLE `tracking_pengiriman`
  ADD CONSTRAINT `tracking_pengiriman_ibfk_1` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
