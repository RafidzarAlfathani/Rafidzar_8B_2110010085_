<?php
// 1. Inisialisasi mPDF
$nama_dokumen = 'Laporan Kinerja Petani';
require_once __DIR__ . '/../../../vendor/autoload.php';
use Mpdf\Mpdf;
$mpdf = new Mpdf(['format' => 'A4']);
ob_start();

// 2. Include Koneksi & Data
include "../../inc/koneksi.php";
include "../../inc/tanggal.php"; 

// 3. Ambil Filter & Data dari Database
$status_filter = isset($_POST['status_akun']) ? $_POST['status_akun'] : '';
$tgl_mulai_filter = isset($_POST['tanggal_mulai']) && !empty($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : '';
$tgl_selesai_filter = isset($_POST['tanggal_selesai']) && !empty($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : '';

// Replikasi query SQL persis seperti di halaman tampilan
$sql_data = "SELECT pt.*, (SELECT COUNT(*) FROM produk pr WHERE pr.id_petani = pt.id_petani) AS jumlah_produk,
                (SELECT COUNT(DISTINCT dp.id_pesanan) FROM detail_pesanan dp JOIN produk pr ON dp.id_produk = pr.id_produk JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan WHERE pr.id_petani = pt.id_petani ".($tgl_mulai_filter && $tgl_selesai_filter ? "AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'" : "").") AS jumlah_pesanan,
                (SELECT SUM(dp.sub_total) FROM detail_pesanan dp JOIN produk pr ON dp.id_produk = pr.id_produk JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan WHERE pr.id_petani = pt.id_petani AND ps.status_pesanan = 'Selesai' ".($tgl_mulai_filter && $tgl_selesai_filter ? "AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'" : "").") AS jumlah_pendapatan
             FROM petani pt";
if ($status_filter) { $sql_data .= " WHERE pt.status_akun = '$status_filter'"; }
$sql_data .= " ORDER BY pt.nama_petani ASC";
$ambil_data = $con->query($sql_data);

$info_filter = "Status Akun: " . ($status_filter ? htmlspecialchars($status_filter) : "Semua");
if ($tgl_mulai_filter && $tgl_selesai_filter) {
    $info_filter .= " | Periode Pesanan: " . tgl_indo($tgl_mulai_filter) . " s/d " . tgl_indo($tgl_selesai_filter);
}
?>

<html>
<head>
    <title>Cetak Laporan Kinerja Petani</title>
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

    <div class="report-title">Laporan Kinerja Petani</div>
    <div class="filter-info"><?= $info_filter; ?></div>
    
    <table class="data-table">
        <thead>
            <tr>
                <th>No.</th>
                <th class="text-left">Nama Petani</th>
                <th class="text-left">Kontak</th>
                <th>Status Akun</th>
                <th>Jml. Produk</th>
                <th>Jml. Pesanan</th>
                <th class="text-right">Total Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($ambil_data->num_rows > 0): $nomor = 1; while ($petani = $ambil_data->fetch_assoc()): ?>
            <tr>
                <td class="text-center"><?= $nomor++; ?></td>
                <td><?= htmlspecialchars($petani['nama_petani']); ?></td>
                <td>
                    <small>
                        Email: <?= htmlspecialchars($petani['email']); ?><br>
                        Telp: <?= htmlspecialchars($petani['telp']); ?>
                    </small>
                </td>
                <td class="text-center"><?= htmlspecialchars($petani['status_akun']); ?></td>
                <td class="text-center"><?= number_format($petani['jumlah_produk']); ?></td>
                <td class="text-center"><?= number_format($petani['jumlah_pesanan']); ?></td>
                <td class="text-right">Rp <?= number_format($petani['jumlah_pendapatan'] ?? 0); ?></td>
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