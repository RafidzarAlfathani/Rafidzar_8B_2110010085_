<?php
// 1. Inisialisasi mPDF
$nama_dokumen = 'Laporan Data Pembeli';
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

// Replikasi query SQL persis seperti di halaman tampilan
$sql_data = "SELECT pm.*,
                (SELECT COUNT(id_pesanan) FROM pesanan ps WHERE ps.id_pembeli = pm.id_pembeli ".($tgl_mulai_filter && $tgl_selesai_filter ? "AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'" : "").") AS total_transaksi,
                (SELECT SUM(total_bayar) FROM pesanan ps WHERE ps.id_pembeli = pm.id_pembeli AND ps.status_pesanan NOT IN ('Dibatalkan', 'Menunggu Pembayaran') ".($tgl_mulai_filter && $tgl_selesai_filter ? "AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'" : "").") AS total_pembayaran
             FROM pembeli pm ORDER BY pm.nama_pembeli ASC";
$ambil_data = $con->query($sql_data);

$info_periode = ($tgl_mulai_filter && $tgl_selesai_filter) ? "Periode Transaksi: " . tgl_indo($tgl_mulai_filter) . " s/d " . tgl_indo($tgl_selesai_filter) : "Periode Transaksi: Semua Waktu";
?>

<html>
<head>
    <title>Cetak Laporan Data Pembeli</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header-table { width: 100%; border-bottom: 3px double #000; padding-bottom: 10px; }
        .header-table td { vertical-align: middle; }
        .logo { width: 60px; }
        .kop-instansi { font-size: 18px; font-weight: bold; text-transform: uppercase; line-height: 1.1; }
        .kop-alamat { font-size: 11px; line-height: 1.4; }
        .report-title { font-size: 16px; font-weight: bold; text-align: center; margin-top: 20px; text-transform: uppercase; }
        .filter-info { font-size: 12px; text-align: center; margin-bottom: 20px; }
        .data-table { width: 100%; border-collapse: collapse; font-size: 10px; }
        .data-table th, .data-table td { border: 1px solid #333; padding: 8px; }
        .data-table th { background-color: #f2f2f2; font-weight: bold; text-align: center; }
        .ttd-section { margin-top: 50px; text-align: right; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 90px;">
                <?php 
                $path_logo = '../../images/' . $meta['logo'];
                if(file_exists($path_logo) && !empty($meta['logo'])): ?>
                    <img src="<?= $path_logo; ?>" class="logo">
                <?php endif; ?>
            </td>
            <td class="text-center">
                <div class="kop-instansi"><?= htmlspecialchars($meta['instansi']); ?></div>
                <div class="kop-alamat">
                    <?= htmlspecialchars($meta['alamat']); ?><br>
                    Email: <?= htmlspecialchars($meta['email']); ?> | Telp: <?= htmlspecialchars($meta['telp']); ?>
                </div>
            </td>
            <td style="width: 90px;">&nbsp;</td>
        </tr>
    </table>

    <div class="report-title">Laporan Data Pembeli</div>
    <div class="filter-info"><?= $info_periode; ?></div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>No.</th>
                <th class="text-left">Nama Pembeli</th>
                <th class="text-left">Kontak</th> 
                <th>Total Transaksi</th>
                <th class="text-right">Total Pembayaran</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($ambil_data->num_rows > 0): $nomor = 1; while ($pembeli = $ambil_data->fetch_assoc()): ?>
            <tr>
                <td class="text-center"><?= $nomor++; ?></td>
                <td><?= htmlspecialchars($pembeli['nama_pembeli']); ?></td>
                <td>
                    <small>
                        Email: <?= htmlspecialchars($pembeli['email']); ?><br>
                        Telp: <?= htmlspecialchars($pembeli['telp']); ?>
                    </small>
                </td> 
                <td class="text-center"><?= number_format($pembeli['total_transaksi'] ?? 0); ?></td>
                <td class="text-right">Rp <?= number_format($pembeli['total_pembayaran'] ?? 0); ?></td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="6" class="text-center">Data tidak ditemukan.</td></tr>
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