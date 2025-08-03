<?php
require_once __DIR__ . '/../../../vendor/autoload.php';
use Mpdf\Mpdf;

$nama_dokumen = 'Laporan Data Produk';
$mpdf = new Mpdf([
    'mode' => 'utf-8',
    'format' => 'A4-L', // A4 Landscape
]);
ob_start();

// 2. Include Koneksi & Data
include "../../inc/koneksi.php";
include "../../inc/tanggal.php"; 

// 3. Ambil Filter & Data dari Database
$id_petani_filter = isset($_POST['id_petani']) && !empty($_POST['id_petani']) ? (int)$_POST['id_petani'] : '';
$id_kategori_filter = isset($_POST['id_kategori']) && !empty($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : '';

$sql_data = "SELECT pr.*, pt.nama_petani, kp.nama_kategori,
                (SELECT SUM(dp.jumlah) FROM detail_pesanan dp WHERE dp.id_produk = pr.id_produk) AS total_dipesan
             FROM produk pr
             JOIN petani pt ON pr.id_petani = pt.id_petani
             JOIN kategori_produk kp ON pr.id_kategori = kp.id_kategori";

$where_clauses = [];
$info_filter = "";
if ($id_petani_filter) { 
    $where_clauses[] = "pr.id_petani = $id_petani_filter"; 
    $nama_petani = $con->query("SELECT nama_petani FROM petani WHERE id_petani = $id_petani_filter")->fetch_assoc()['nama_petani'];
    $info_filter .= "Petani: <strong>" . htmlspecialchars($nama_petani) . "</strong>";
}
if ($id_kategori_filter) { 
    $where_clauses[] = "pr.id_kategori = $id_kategori_filter"; 
    $nama_kategori = $con->query("SELECT nama_kategori FROM kategori_produk WHERE id_kategori = $id_kategori_filter")->fetch_assoc()['nama_kategori'];
    $info_filter .= ($info_filter ? " & " : "") . "Kategori: <strong>" . htmlspecialchars($nama_kategori) . "</strong>";
}

if (!empty($where_clauses)) { $sql_data .= " WHERE " . implode(' AND ', $where_clauses); }
$sql_data .= " ORDER BY pr.nama_produk ASC";

$ambil_data = $con->query($sql_data);

?>

<html>
<head>
    <title>Cetak Laporan Produk</title>
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

    <div class="report-title">Laporan Data Produk</div>
    <?php if(!empty($info_filter)): ?>
        <div class="filter-info">Filter Aktif: <?= $info_filter; ?></div>
    <?php endif; ?>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>No.</th>
                <th class="text-left">Nama Produk</th>
                <th class="text-left">Petani</th>
                <th class="text-left">Kategori</th>
                <th class="text-right">Harga</th>
                <th>Stok</th>
                <th>Total Dipesan</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($ambil_data->num_rows > 0): $nomor = 1; while ($produk = $ambil_data->fetch_assoc()): ?>
            <tr>
                <td class="text-center"><?= $nomor++; ?></td>
                <td><?= htmlspecialchars($produk['nama_produk']); ?></td>
                <td><?= htmlspecialchars($produk['nama_petani']); ?></td>
                <td><?= htmlspecialchars($produk['nama_kategori']); ?></td>
                <td class="text-right">Rp <?= number_format($produk['harga']); ?></td>
                <td class="text-center"><?= number_format($produk['stok']); ?> <?= htmlspecialchars($produk['satuan']); ?></td>
                <td class="text-center"><?= number_format($produk['total_dipesan'] ?? 0); ?> item</td>
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
// Finalisasi dan Output PDF
$html = ob_get_contents();
ob_end_clean();
$mpdf->WriteHTML($html);
$mpdf->Output($nama_dokumen . ".pdf", 'I');
exit;
?>