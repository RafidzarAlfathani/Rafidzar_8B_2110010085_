<?php
// 1. Inisialisasi mPDF
$nama_dokumen = 'Laporan Produk Terlaris';
require_once __DIR__ . '/../../../vendor/autoload.php';
use Mpdf\Mpdf;
$mpdf = new Mpdf(['format' => 'A4']);

ob_start();

// 2. Include Koneksi & Data
include "../../inc/koneksi.php";
include "../../inc/tanggal.php"; 

// 3. Ambil Filter & Data dari Database
$tgl_mulai_filter = isset($_POST['tanggal_mulai']) && !empty($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : '';
$tgl_selesai_filter = isset($_POST['tanggal_selesai']) && !empty($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : '';
$kategori_filter = isset($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : '';
$limit_filter = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;

// Replikasi query
$sql_data = "SELECT pr.nama_produk, pt.nama_petani, kp.nama_kategori, SUM(dp.jumlah) AS total_terjual FROM detail_pesanan dp JOIN produk pr ON dp.id_produk = pr.id_produk JOIN petani pt ON pr.id_petani = pt.id_petani JOIN kategori_produk kp ON pr.id_kategori = kp.id_kategori JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan WHERE ps.status_pesanan != 'Dibatalkan'";
if ($tgl_mulai_filter && $tgl_selesai_filter) { $sql_data .= " AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter 00:00:00' AND '$tgl_selesai_filter 23:59:59'"; }
if ($kategori_filter) { $sql_data .= " AND pr.id_kategori = $kategori_filter"; }
$sql_data .= " GROUP BY pr.id_produk ORDER BY total_terjual DESC LIMIT $limit_filter";
$ambil_data = $con->query($sql_data);

$info_periode = ($tgl_mulai_filter && $tgl_selesai_filter) ? "Periode: " . tgl_indo($tgl_mulai_filter) . " s/d " . tgl_indo($tgl_selesai_filter) : "Periode: Semua Waktu";
?>

<html>
<head>
    <title>Cetak Laporan Produk Terlaris</title>
    <style>
        body { font-family: Arial, sans-serif; } .header-table { width: 100%; border-bottom: 3px double #000; padding-bottom: 10px; } .header-table td { vertical-align: middle; } .logo { width: 60px; } .kop-instansi { font-size: 18px; font-weight: bold; text-transform: uppercase; line-height: 1.1; } .kop-alamat { font-size: 11px; line-height: 1.4; } .report-title { font-size: 16px; font-weight: bold; text-align: center; margin-top: 20px; text-transform: uppercase; } .filter-info { font-size: 12px; text-align: center; margin-bottom: 20px; } .data-table { width: 100%; border-collapse: collapse; font-size: 10px; } .data-table th, .data-table td { border: 1px solid #333; padding: 8px; } .data-table th { background-color: #f2f2f2; font-weight: bold; text-align: center; } .ttd-section { margin-top: 50px; text-align: right; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 90px;"><img src="../../images/<?= $meta['logo']; ?>" class="logo"></td>
            <td style="text-align: center;">
                <div class="kop-instansi"><?= htmlspecialchars($meta['instansi']); ?></div>
                <div class="kop-alamat"><?= htmlspecialchars($meta['alamat']); ?><br>Email: <?= htmlspecialchars($meta['email']); ?> | Telp: <?= htmlspecialchars($meta['telp']); ?></div>
            </td>
            <td style="width: 90px;">&nbsp;</td>
        </tr>
    </table>

    <div class="report-title">Laporan Produk Terlaris</div>
    <div class="filter-info"><?= $info_periode; ?></div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>Peringkat</th>
                <th style="text-align: left;">Nama Produk</th>
                <th style="text-align: left;">Kategori</th>
                <th style="text-align: left;">Petani</th>
                <th>Total Terjual</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($ambil_data->num_rows > 0): $nomor = 1; while ($produk = $ambil_data->fetch_assoc()): ?>
            <tr>
                <td style="text-align: center;">#<?= $nomor++; ?></td>
                <td><?= htmlspecialchars($produk['nama_produk']); ?></td>
                <td><?= htmlspecialchars($produk['nama_kategori']); ?></td>
                <td><?= htmlspecialchars($produk['nama_petani']); ?></td>
                <td style="text-align: center;"><?= number_format($produk['total_terjual'] ?? 0); ?> item</td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="5" style="text-align: center;">Data tidak ditemukan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="ttd-section">
        Lokpaikat, <?= tgl_indo(date('Y-m-d')); ?><br>
        Mengetahui,<br>
        Pimpinan
        <br><br><br><br><br>
        <b><u><?= htmlspecialchars($meta['pimpinan']); ?></u></b>
    </div>
</body>
</html>

<?php
$html = ob_get_contents();
ob_end_clean();
$mpdf->WriteHTML($html);
$mpdf->Output($nama_dokumen . ".pdf", 'I');
exit;
?>