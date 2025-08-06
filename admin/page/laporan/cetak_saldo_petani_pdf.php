<?php
// 1. Inisialisasi mPDF
$nama_dokumen = 'Laporan Sisa Saldo Petani';
require_once __DIR__ . '/../../../vendor/autoload.php';
use Mpdf\Mpdf;
$mpdf = new Mpdf(['format' => 'A4']);
ob_start();

// 2. Include Koneksi & Data
include "../../inc/koneksi.php";
include "../../inc/tanggal.php"; 

// 4. Query Data Sisa Saldo Petani
$sql_data = "
SELECT 
    pt.nama_petani,
    pt.telp,
    (
        IFNULL((
            SELECT SUM(dp.sub_total)
            FROM produk pr 
            JOIN detail_pesanan dp ON pr.id_produk = dp.id_produk
            JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
            WHERE pr.id_petani = pt.id_petani
              AND ps.status_pesanan = 'Selesai'";

$sql_data .= "
        ), 0)
        -
        IFNULL((
            SELECT SUM(pdp.jumlah_dana)
            FROM pengajuan_dana_petani pdp
            WHERE pdp.id_petani = pt.id_petani
              AND pdp.status = 'Disetujui'
        ), 0)
    ) AS sisa_saldo
FROM petani pt
ORDER BY sisa_saldo DESC
";

$ambil_data = $con->query($sql_data);

?>

<html>
<head>
    <title>Cetak Laporan Sisa Saldo Petani</title>
    <style>
        body { font-family: Arial, sans-serif; }
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
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td style="width: 90px;"><img src="../../images/<?= $meta['logo']; ?>" class="logo"></td>
            <td style="text-align: center;">
                <div class="kop-instansi"><?= htmlspecialchars($meta['instansi']); ?></div>
                <div class="kop-alamat"><?= htmlspecialchars($meta['alamat']); ?><br>
                Email: <?= htmlspecialchars($meta['email']); ?> | Telp: <?= htmlspecialchars($meta['telp']); ?></div>
            </td>
            <td style="width: 90px;">&nbsp;</td>
        </tr>
    </table>

    <div class="report-title">Laporan Sisa Saldo Petani</div>
    <br>

    <table class="data-table">
        <thead>
            <tr>
                <th>No.</th>
                <th style="text-align: left;">Nama Petani</th>
                <th style="text-align: left;">Telepon</th>
                <th style="text-align: right;">Sisa Saldo</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($ambil_data->num_rows > 0): $no = 1; while ($petani = $ambil_data->fetch_assoc()): ?>
                <tr>
                    <td style="text-align: center;"><?= $no++; ?></td>
                    <td><?= htmlspecialchars($petani['nama_petani']); ?></td>
                    <td><?= htmlspecialchars($petani['telp']); ?></td>
                    <td style="text-align: right;">Rp <?= number_format($petani['sisa_saldo'], 0, ',', '.'); ?></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="4" style="text-align: center;">Data tidak ditemukan.</td></tr>
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
