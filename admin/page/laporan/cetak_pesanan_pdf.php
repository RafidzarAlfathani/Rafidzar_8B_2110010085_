<?php
// 1. Inisialisasi mPDF
$nama_dokumen = 'Laporan Data Pesanan';
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
$status_filter = isset($_POST['status_pesanan']) ? $_POST['status_pesanan'] : '';
$petani_filter = isset($_POST['id_petani']) ? (int)$_POST['id_petani'] : '';
$kurir_filter = isset($_POST['id_kurir']) ? (int)$_POST['id_kurir'] : '';

// Replikasi query SQL
$sql_data = "SELECT DISTINCT ps.*, pm.nama_pembeli, kr.nama_kurir FROM pesanan ps JOIN pembeli pm ON ps.id_pembeli = pm.id_pembeli LEFT JOIN kurir kr ON ps.id_kurir = kr.id_kurir";
if ($petani_filter) { $sql_data .= " JOIN detail_pesanan dp ON ps.id_pesanan = dp.id_pesanan JOIN produk pr ON dp.id_produk = pr.id_produk"; }
$where_clauses = [];
if ($tgl_mulai_filter && $tgl_selesai_filter) { $where_clauses[] = "ps.tgl_pesan BETWEEN '$tgl_mulai_filter 00:00:00' AND '$tgl_selesai_filter 23:59:59'";}
if ($status_filter) { $where_clauses[] = "ps.status_pesanan = '$status_filter'"; }
if ($kurir_filter) { $where_clauses[] = "ps.id_kurir = $kurir_filter"; }
if ($petani_filter) { $where_clauses[] = "pr.id_petani = $petani_filter"; }
if (!empty($where_clauses)) { $sql_data .= " WHERE " . implode(' AND ', $where_clauses); }
$sql_data .= " ORDER BY ps.tgl_pesan DESC";
$ambil_data = $con->query($sql_data);

// Membuat info filter untuk ditampilkan di PDF
$info_filter = [];
if ($tgl_mulai_filter && $tgl_selesai_filter) { $info_filter[] = "Periode: " . tgl_indo($tgl_mulai_filter) . " s/d " . tgl_indo($tgl_selesai_filter); }
if ($status_filter) { $info_filter[] = "Status: " . htmlspecialchars($status_filter); }
?>

<html>
<head>
    <title>Cetak Laporan Data Pesanan</title>
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
            <td style="width: 90px;"><img src="../../images/<?= $meta['logo']; ?>" class="logo"></td>
            <td class="text-center">
                <div class="kop-instansi"><?= htmlspecialchars($meta['instansi']); ?></div>
                <div class="kop-alamat"><?= htmlspecialchars($meta['alamat']); ?><br>Email: <?= htmlspecialchars($meta['email']); ?> | Telp: <?= htmlspecialchars($meta['telp']); ?></div>
            </td>
            <td style="width: 90px;">&nbsp;</td>
        </tr>
    </table>

    <div class="report-title">Laporan Data Pesanan</div>
    <?php if(!empty($info_filter)): ?>
        <div class="filter-info">Filter Aktif: <?= implode(' | ', $info_filter); ?></div>
    <?php endif; ?>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>No.</th>
                <th class="text-left">Invoice</th>
                <th class="text-left">Pembeli</th>
                <th>Tgl. Pesan</th>
                <th class="text-right">Total Bayar</th>
                <th class="text-left">Kurir</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($ambil_data->num_rows > 0): $nomor = 1; while ($pesanan = $ambil_data->fetch_assoc()): ?>
            <tr>
                <td class="text-center"><?= $nomor++; ?></td>
                <td><?= $pesanan['kode_invoice']; ?></td>
                <td><?= htmlspecialchars($pesanan['nama_pembeli']); ?></td>
                <td class="text-center"><?= date("d M Y, H:i", strtotime($pesanan['tgl_pesan'])); ?></td>
                <td class="text-right">Rp <?= number_format($pesanan['total_bayar']); ?></td>
                <td><?= htmlspecialchars($pesanan['nama_kurir'] ?? '-'); ?></td>
                <td class="text-center"><?= htmlspecialchars($pesanan['status_pesanan']); ?></td>
            </tr>
            <?php endwhile; else: ?>
            <tr><td colspan="7" class="text-center">Data tidak ditemukan.</td></tr>
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