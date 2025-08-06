<?php
// 1. Inisialisasi mPDF
$nama_dokumen = 'Laporan Saldo Kurir';
require_once __DIR__ . '/../../../vendor/autoload.php';
use Mpdf\Mpdf;
$mpdf = new Mpdf(['format' => 'A4']);

ob_start();

// 2. Include koneksi & data instansi
include "../../inc/koneksi.php";
include "../../inc/tanggal.php"; 

// Ambil data saldo kurir
$sql = "SELECT 
            k.nama_kurir,
            COALESCE(SUM(CASE WHEN p.status_pesanan = 'Selesai' THEN p.ongkir ELSE 0 END), 0) AS total_pendapatan,
            COALESCE((
                SELECT SUM(jumlah_dana) 
                FROM pengajuan_dana_kurir pd 
                WHERE pd.id_kurir = k.id_kurir AND pd.status = 'Disetujui'
            ), 0) AS total_tarik
        FROM kurir k
        LEFT JOIN pesanan p ON k.id_kurir = p.id_kurir
        GROUP BY k.id_kurir
        ORDER BY k.nama_kurir ASC";

$data_kurir = $con->query($sql);
?>


<html>
<head>
    <title>Cetak Laporan Saldo Kurir</title>
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
                <div class="kop-alamat"><?= htmlspecialchars($meta['alamat']); ?><br>Email: <?= htmlspecialchars($meta['email']); ?> | Telp: <?= htmlspecialchars($meta['telp']); ?></div>
            </td>
            <td style="width: 90px;">&nbsp;</td>
        </tr>
    </table>

    <div class="report-title">Laporan Saldo Kurir</div><br>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>Nama Kurir</th>
                <th>Total Pendapatan</th>
                <th>Total Penarikan</th>
                <th>Sisa Saldo</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($data_kurir && $data_kurir->num_rows > 0): $no = 1;
                while ($row = $data_kurir->fetch_assoc()):
                    $sisa = (int)$row['total_pendapatan'] - (int)$row['total_tarik'];
                    if ($sisa < 0) $sisa = 0;
            ?>
                <tr>
                    <td class="text-center"><?= $no++; ?></td>
                    <td><?= htmlspecialchars($row['nama_kurir']); ?></td>
                    <td style="text-align: right;">Rp <?= number_format($row['total_pendapatan'], 0, ',', '.'); ?></td>
                    <td style="text-align: right;">Rp <?= number_format($row['total_tarik'], 0, ',', '.'); ?></td>
                    <td style="text-align: right;">Rp <?= number_format($sisa, 0, ',', '.'); ?></td>
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
        <br><br><br><br>
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