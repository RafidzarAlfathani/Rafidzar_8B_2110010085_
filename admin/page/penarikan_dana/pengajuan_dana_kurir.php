<?php
include "inc/koneksi.php";
require_once("inc/tanggal.php");
session_start();

if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'Kurir') {
    echo "<script>alert('Akses ditolak! Halaman khusus kurir.'); window.location='index.php';</script>";
    exit;
}

$id_kurir = $_SESSION['user_id'];
$nama_kurir = $_SESSION['user_nama'] ?? 'Kurir';

$query_pendapatan = mysqli_query($con, "
    SELECT SUM(ongkir) AS total_pendapatan 
    FROM pesanan 
    WHERE id_kurir = '$id_kurir' AND status_pesanan = 'Selesai'
");
$total_pendapatan = ($row = mysqli_fetch_assoc($query_pendapatan)) ? $row['total_pendapatan'] : 0;

$query_tarik = mysqli_query($con, "
    SELECT SUM(jumlah_dana) AS total_tarik 
    FROM pengajuan_dana_kurir 
    WHERE id_kurir = '$id_kurir' AND status = 'Disetujui'
");
$total_tarik = ($row = mysqli_fetch_assoc($query_tarik)) ? $row['total_tarik'] : 0;

$sisa_saldo = (int)$total_pendapatan - (int)$total_tarik;
if ($sisa_saldo < 0) $sisa_saldo = 0;

$pesan = '';

// Proses pengajuan
if (isset($_POST['ajukan'])) {
    $jumlah_dana = (int)$_POST['jumlah_dana'];
    $metode = mysqli_real_escape_string($con, $_POST['metode']);
    $catatan = mysqli_real_escape_string($con, $_POST['catatan']);
    $tanggal_pengajuan = date("Y-m-d");

    if ($jumlah_dana <= 0) {
        $pesan = "<div class='alert alert-warning'>Jumlah dana tidak valid!</div>";
    } elseif ($jumlah_dana > $sisa_saldo) {
        $pesan = "<div class='alert alert-warning'>Jumlah dana melebihi sisa saldo!</div>";
    } else {
        $query = mysqli_query($con, "INSERT INTO pengajuan_dana_kurir 
            (id_kurir, jumlah_dana, metode, catatan, status, tanggal_pengajuan) 
            VALUES 
            ('$id_kurir', '$jumlah_dana', '$metode', '$catatan', 'Menunggu', '$tanggal_pengajuan')");

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
    <title>Pengajuan Penarikan Dana Kurir</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<div class="panel">
    <div class="panel-body">
        <div class="card-header">Form Pengajuan Penarikan Dana Kurir</div>
        <div class="card-body">
            <?= $pesan ?>
            <form method="POST">
                <label>Nama Kurir:</label><br>
                <input type="text" value="<?= htmlspecialchars($nama_kurir); ?>" disabled><br><br>

                <label>Sisa Saldo Anda (Rp):</label><br>
                <input type="text" value="<?= number_format($sisa_saldo, 0, ',', '.') ?>" disabled><br><br>

                <label>Tanggal Pengajuan:</label><br>
                <input type="text" value="<?= tgl_indo(date('Y-m-d')) ?>" disabled><br><br>

                <label>Jumlah Dana yang Diajukan (Rp):</label><br>
                <input type="number" class="form-control" name="jumlah_dana" required><br><br>

                <label>Metode Penarikan:</label><br>
                <select name="metode" required>
                    <option value="">-- Pilih Metode --</option>
                    <!-- <option value="Transfer">Transfer</option> -->
                    <option value="Cash di Balai">Cash di Balai</option>
                </select><br><br>

                <label>Catatan / Keterangan:</label><br>
                <textarea name="catatan" rows="4" placeholder="Contoh: Dana untuk kebutuhan bahan bakar..." required></textarea><br><br>
                <button type="submit" name="ajukan" class="btn btn-primary btn-sm">Ajukan Penarikan</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
