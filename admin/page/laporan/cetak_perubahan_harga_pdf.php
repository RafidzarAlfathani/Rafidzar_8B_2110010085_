<?php
// 1. Inisialisasi mPDF
$nama_dokumen = 'Laporan Riwayat Perubahan Harga';
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
$produk_filter = isset($_POST['id_produk']) ? (int)$_POST['id_produk'] : '';
$petani_filter = isset($_POST['id_petani']) ? (int)$_POST['id_petani'] : '';

// Replikasi query
$sql_data = "SELECT rh.*, pr.nama_produk, pt.nama_petani FROM riwayat_harga rh JOIN produk pr ON rh.id_produk = pr.id_produk JOIN petani pt ON pr.id_petani = pt.id_petani";
$where_clauses = [];
if ($tgl_mulai_filter && $tgl_selesai_filter) { $where_clauses[] = "DATE(rh.tgl_perubahan) BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'"; }
if ($produk_filter) { $where_clauses[] = "rh.id_produk = $produk_filter"; }
if ($petani_filter) { $where_clauses[] = "pr.id_petani = $petani_filter"; }
if (!empty($where_clauses)) { $sql_data .= " WHERE " . implode(' AND ', $where_clauses); }
$sql_data .= " ORDER BY rh.tgl_perubahan DESC";
$ambil_data = $con->query($sql_data);

// Info filter untuk judul
$info_filter = [];
if ($tgl_mulai_filter && $tgl_selesai_filter) { $info_filter[] = "Periode: " . tgl_indo($tgl_mulai_filter) . " s/d " . tgl_indo($tgl_selesai_filter); }
?>

<html>
<head>
    <title>Cetak Laporan Perubahan Harga</title>
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

    <div class="report-title">Laporan Riwayat Perubahan Harga</div>
    <?php if(!empty($info_filter)): ?>
        <div class="filter-info"><?= implode(' | ', $info_filter); ?></div>
    <?php endif; ?>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>No.</th>
                <th>Tanggal Update</th>
                <th style="text-align: left;">Nama Produk</th>
                <th style="text-align: left;">Petani</th>
                <th style="text-align: right;">Harga Lama</th>
                <th style="text-align: right;">Harga Baru</th>
                <th style="text-align: right;">Perubahan</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($ambil_data->num_rows > 0): $nomor = 1; while ($riwayat = $ambil_data->fetch_assoc()): ?>
            <tr>
                <td style="text-align: center;"><?= $nomor++; ?></td>
                <td style="text-align: center;"><?= date("d M Y, H:i", strtotime($riwayat['tgl_perubahan'])); ?></td>
                <td><?= htmlspecialchars($riwayat['nama_produk']); ?></td>
                <td><?= htmlspecialchars($riwayat['nama_petani']); ?></td>
                <td style="text-align: right;">Rp <?= number_format($riwayat['harga_lama']); ?></td>
                <td style="text-align: right;">Rp <?= number_format($riwayat['harga_baru']); ?></td>
                <td style="text-align: right;">Rp <?= number_format($riwayat['harga_baru'] - $riwayat['harga_lama']); ?></td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="7" style="text-align: center;">Data tidak ditemukan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>

    <div class="ttd-section">
        Banjarbaru, <?= tgl_indo(date('Y-m-d')); ?><br>
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