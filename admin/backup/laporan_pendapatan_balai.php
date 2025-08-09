<?php
include 'inc/koneksi.php';
session_start();

// Hanya admin dan pimpinan yang boleh mengakses
if (!isset($_SESSION['user_level']) || !in_array($_SESSION['user_level'], ['Admin', 'Pimpinan'])) {
    echo "<script>alert('Akses ditolak!'); window.location='index.php';</script>";
    exit();
}

// Ambil total keuntungan dari pesanan yang selesai dan biaya adminnya lebih dari 0
$query = "SELECT 
              SUM(biaya_admin) AS total_keuntungan, 
              COUNT(*) AS jumlah_pesanan 
          FROM pesanan 
          WHERE status_pesanan = 'Selesai' AND biaya_admin > 0";

$result = $con->query($query);
$data = $result->fetch_assoc();
$total_keuntungan = $data['total_keuntungan'];
$jumlah_pesanan = $data['jumlah_pesanan'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Laporan Keuntungan Balai</title>
    <link rel="stylesheet" href="admin/assets/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Laporan Keuntungan Pihak Balai</h2>

    <table class="table table-bordered">
        <tr>
            <th>Jumlah Pesanan Selesai</th>
            <td><?= $jumlah_pesanan ?></td>
        </tr>
        <tr>
            <th>Total Keuntungan dari Biaya Admin</th>
            <td><strong>Rp <?= number_format($total_keuntungan, 0, ',', '.') ?></strong></td>
        </tr>
    </table>

    <a href="index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
</div>
</body>
</html>
