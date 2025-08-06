<?php
include "inc/koneksi.php";
require_once("inc/tanggal.php");
session_start();

// Cek apakah user login dan levelnya Petani
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'Petani') {
    echo "<script>alert('Akses ditolak! Halaman khusus petani.'); window.location='index.php';</script>";
    exit;
}

$id_petani = $_SESSION['user_id'];
$nama_petani = $_SESSION['user_nama'] ?? 'Petani'; // fallback jika $admin['nama'] tidak tersedia

// Hitung total pendapatan dari penjualan produk oleh petani ini (pesanan selesai)
$q_pendapatan = mysqli_query($con, "
    SELECT SUM(dp.sub_total) AS total_pendapatan
    FROM detail_pesanan dp
    JOIN produk p ON dp.id_produk = p.id_produk
    JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
    WHERE p.id_petani = '$id_petani' AND ps.status_pesanan = 'Selesai'
");
$pendapatan = mysqli_fetch_assoc($q_pendapatan)['total_pendapatan'] ?? 0;

// Hitung total dana yang sudah ditarik (Disetujui)
$q_ditarik = mysqli_query($con, "
    SELECT SUM(jumlah_dana) AS total_ditarik
    FROM pengajuan_dana_petani
    WHERE id_petani = '$id_petani' AND status = 'Disetujui'
");
$ditarik = mysqli_fetch_assoc($q_ditarik)['total_ditarik'] ?? 0;

// Hitung sisa saldo
$sisa_saldo = $pendapatan - $ditarik;
if ($sisa_saldo < 0) $sisa_saldo = 0; // antisipasi negatif jika ada kesalahan data


// Proses form jika disubmit
if (isset($_POST['submit'])) {
    $jumlah_dana = (int) $_POST['jumlah_dana'];
    $metode = mysqli_real_escape_string($con, $_POST['metode']);
    $catatan = mysqli_real_escape_string($con, $_POST['catatan']);
    $tanggal_pengajuan = date("Y-m-d");

    if ($jumlah_dana <= 0) {
        echo "<script>alert('Jumlah dana tidak valid!');</script>";
    } elseif ($jumlah_dana > $sisa_saldo) {
        echo "<script>alert('Jumlah dana melebihi sisa saldo!');</script>";
    } else {
        $query = mysqli_query($con, "INSERT INTO pengajuan_dana_petani 
            (id_petani, jumlah_dana, metode, catatan, status, tanggal_pengajuan) 
            VALUES 
            ('$id_petani', '$jumlah_dana', '$metode', '$catatan', 'Menunggu', '$tanggal_pengajuan')
        ");

        if ($query) {
            echo "<script>alert('Pengajuan berhasil diajukan!'); window.location='index.php';</script>";
        } else {
            echo "<script>alert('Terjadi kesalahan saat menyimpan pengajuan.');</script>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengajuan Penarikan Dana</title>
    <link rel="stylesheet" href="assets/css/style.css"> <!-- Sesuaikan jika perlu -->
</head>
<body>
   
<div class="panel">
<div class="panel-body">
        <div class="card-header">Form Pengajuan Penarikan Dana</div>
    <div class="card-body">
    <form method="POST">
        <label>Nama Petani:</label><br>
        <input type="text" value="<?= htmlspecialchars($nama_petani); ?>" disabled><br><br>

        <label>Sisa Saldo Anda (Rp):</label><br>
        <input type="text" value="<?= number_format($sisa_saldo, 0, ',', '.') ?>" disabled><br><br>

        <label>Tanggal Pengajuan:</label><br>
        <input type="text" value="<?= tgl_indo(date('Y-m-d')) ?>" disabled><br><br>

        <label>Jumlah Dana yang Diajukan (Rp):</label><br>
        <input type="number" class="form-control" name="jumlah_dana" required><br><br>

        <label>Metode Penarikan:</label><br>
        <select name="metode" required>
            <option value="">-- Pilih Metode --</option>
            <option value="Transfer">Transfer</option>
            <option value="Cash di Balai">Cash di Balai</option>
        </select><br><br>

        <label>Catatan / Keterangan:</label><br>
        <textarea name="catatan" rows="4" placeholder="Contoh: Dana untuk beli bibit..." required></textarea><br><br>

        <button type="submit" name="submit" class="btn btn-primary btn-sm">Ajukan Penarikan</button>
    </form>
</div>
</body>
</html>
