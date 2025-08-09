<?php
session_start();
include 'admin/inc/koneksi.php';

// ===================================================================
// PENGAMAN HALAMAN & PENGAMBILAN DATA
// ===================================================================

// Cek login
if (!isset($_SESSION['pembeli_id'])) {
    die("Akses ditolak. Anda harus login untuk melihat invoice.");
}
// Cek parameter invoice
if (!isset($_GET['invoice']) || empty($_GET['invoice'])) {
    die("Invoice tidak ditemukan.");
}

$id_pembeli = $_SESSION['pembeli_id'];
$kode_invoice = mysqli_real_escape_string($con, $_GET['invoice']);

// Query keamanan: pastikan invoice ini milik pembeli yang sedang login
$sql = $con->query("SELECT ps.*, pm.nama_pembeli, pm.email AS email_pembeli, pm.telp AS telp_pembeli
                    FROM pesanan ps
                    JOIN pembeli pm ON ps.id_pembeli = pm.id_pembeli
                    WHERE ps.id_pembeli = '$id_pembeli' AND ps.kode_invoice = '$kode_invoice'");

if ($sql->num_rows == 0) {
    die("Akses ditolak. Invoice ini bukan milik Anda.");
}

$pesanan = $sql->fetch_assoc();
$id_pesanan = $pesanan['id_pesanan'];

// Ambil detail item pesanan
$query_items = $con->query("SELECT dp.*, pr.nama_produk, pr.satuan
                           FROM detail_pesanan dp
                           JOIN produk pr ON dp.id_produk = pr.id_produk
                           WHERE dp.id_pesanan = '$id_pesanan'");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= $pesanan['kode_invoice']; ?></title>
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
        }
        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }
        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }
        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }
        .invoice-box table tr.item td{
            border-bottom: 1px solid #eee;
        }
        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }
        .text-end {
            text-align: right;
        }
        @media print {
            .no-print {
                display: none;
            }
            .invoice-box {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="4">
                    <table>
                        <tr>
                            <td class="title">
                                <h2>E-Tani Lokpaikat</h2>
                            </td>
                            <td class="text-end">
                                <strong>Invoice #: <?= $pesanan['kode_invoice']; ?></strong><br>
                                Dibuat: <?= date("d F Y", strtotime($pesanan['tgl_pesan'])); ?><br>
                                Status: <?= $pesanan['status_pesanan']; ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="4">
                    <table>
                        <tr>
                            <td>
                                <strong>Dikirim Kepada:</strong><br>
                                <?= htmlspecialchars($pesanan['nama_pembeli']); ?><br>
                                <?= nl2br(htmlspecialchars($pesanan['alamat_pengiriman'])); ?><br>
                                <?= htmlspecialchars($pesanan['telp_pembeli']); ?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="heading">
                <td>Produk</td>
                <td class="text-end">Harga Satuan</td>
                <td class="text-end">Jumlah</td>
                <td class="text-end">Subtotal</td>
            </tr>
            <?php while ($item = $query_items->fetch_assoc()): ?>
            <tr class="item">
                <td><?= htmlspecialchars($item['nama_produk']); ?></td>
                <td class="text-end">Rp <?= number_format($item['harga_saat_pesan']); ?></td>
                <td class="text-end"><?= $item['jumlah']; ?> <?= $item['satuan']; ?></td>
                <td class="text-end">Rp <?= number_format($item['sub_total']); ?></td>
            </tr>
            <?php endwhile; ?>
            <tr class="total">
                <td colspan="3" class="text-end">Subtotal</td>
                <td class="text-end">Rp <?= number_format($pesanan['total_bayar'] - $pesanan['ongkir'] - $pesanan['biaya_admin']); ?></td>
            </tr>
            <tr class="total">
                <td colspan="3" class="text-end">Ongkos Kirim</td>
                <td class="text-end">Rp <?= number_format($pesanan['ongkir']); ?></td>
            </tr>
            <tr class="total">
                <td colspan="3" class="text-end">Biaya Admin</td>
                <td class="text-end">Rp <?= number_format($pesanan['biaya_admin']); ?></td>
            </tr>
            <tr class="total">
                <td colspan="3" class="text-end"><strong>Grand Total</strong></td>
                <td class="text-end"><strong>Rp <?= number_format($pesanan['total_bayar']); ?></strong></td>
            </tr>
        </table>
        <hr>
        <div class="text-center no-print">
            <button onclick="window.print();" class="btn btn-primary">Cetak Halaman Ini</button> 
        </div>
    </div>
    <script>
        // Otomatis membuka dialog print saat halaman selesai dimuat
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
