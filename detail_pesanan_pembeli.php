<?php
$page_title = "Detail Pesanan";
include 'header.php';

// PENGAMAN HALAMAN
if (!isset($_SESSION['pembeli_id'])) {
    $_SESSION['pesan_notifikasi'] = "Anda harus login untuk melihat detail pesanan.";
    echo "<script>window.location.href='login.php';</script>";
    exit();
}
if (!isset($_GET['invoice']) || empty($_GET['invoice'])) {
    echo "<script>window.location.href='akun.php';</script>";
    exit();
}

$id_pembeli = $_SESSION['pembeli_id'];
$kode_invoice = mysqli_real_escape_string($con, $_GET['invoice']);

// Query utama dengan cek keamanan: pastikan invoice ini milik pembeli yang sedang login
$sql = $con->query("SELECT * FROM pesanan WHERE id_pembeli = '$id_pembeli' AND kode_invoice = '$kode_invoice'");

if ($sql->num_rows == 0) {
    echo "<script>alert('Pesanan tidak ditemukan atau bukan milik Anda.'); window.location.href='akun.php';</script>";
    exit();
}

$pesanan = $sql->fetch_assoc();
$id_pesanan = $pesanan['id_pesanan'];
?>

<div class="breadcrumbs_area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb_content">
                    <h3>Detail Pesanan</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="akun.php">Akun Saya</a></li>
                        <li>Detail Pesanan</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="checkout_page_bg">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <h4 class="mb-3">Detail untuk Invoice: <?= $pesanan['kode_invoice']; ?></h4>

                <?php if ($pesanan['status_pesanan'] == 'Menunggu Pembayaran'): ?>
                    <div class="alert alert-warning">
                        <strong>Harap Selesaikan Pembayaran Anda.</strong><br>
                        Silakan transfer sejumlah <strong>Rp <?= number_format($pesanan['total_bayar']); ?></strong> ke rekening berikut:
                        <ul>
                            <li>Bank XYZ: 123-456-7890 a/n E-Tani Sejahtera</li>
                            <li>Bank ABC: 098-765-4321 a/n E-Tani Sejahtera</li>
                        </ul>
                        <hr>
                        <p class="mb-0">Setelah melakukan pembayaran, silakan klik tombol di bawah ini untuk mengunggah bukti transfer Anda.</p>
                        <div class="text-center mt-3">
                            <a href="konfirmasi_pembayaran.php?invoice=<?= $pesanan['kode_invoice']; ?>" class="btn btn-success">
                                <i class="fa fa-upload"></i> Konfirmasi Pembayaran
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 col-md-6">
                <div class="card mb-3">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Ringkasan Pesanan</h5>
                        <?php
                        // Tombol cetak hanya muncul jika statusnya relevan
                        $status = $pesanan['status_pesanan'];
                        if ($status == 'Menunggu Verifikasi' || $status == 'Diproses' || $status == 'Dikirim' || $status == 'Selesai') :
                        ?>
                            <a href="cetak_invoice.php?invoice=<?= $pesanan['kode_invoice']; ?>" target="_blank" class="btn btn-info btn-sm">
                                <i class="fa fa-print"></i> Cetak Invoice
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <p><strong>Status:</strong> <?= $pesanan['status_pesanan']; ?></p>
                        <p><strong>Tanggal:</strong> <?= date("d F Y, H:i", strtotime($pesanan['tgl_pesan'])); ?></p>
                        <p><strong>Total Bayar:</strong> Rp <?= number_format($pesanan['total_bayar']); ?></p>
                        <p><strong>Metode Pembayaran:</strong> <?= $pesanan['metode_pembayaran']; ?></p>
                        <hr>
                        <h5>Alamat Pengiriman</h5>
                        <p><?= nl2br(htmlspecialchars($pesanan['alamat_pengiriman'])); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Item yang Dipesan</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table class="table">
                            <tbody>
                                <?php
                                $ambil_detail = $con->query("SELECT dp.*, pr.nama_produk
                                    FROM detail_pesanan dp
                                    JOIN produk pr ON dp.id_produk = pr.id_produk
                                    WHERE dp.id_pesanan = '$id_pesanan'");
                                while ($item = $ambil_detail->fetch_assoc()) {
                                ?>
                                    <tr>
                                        <td><?= $item['nama_produk']; ?></td>
                                        <td><?= $item['jumlah']; ?> x Rp <?= number_format($item['harga_saat_pesan']); ?></td>
                                        <td class="text-end">Rp <?= number_format($item['sub_total']); ?></td>
                                    </tr>
                                <?php } ?>
                                <tr class="fw-bold">
                                    <td colspan="2">Subtotal</td>
                                    <td class="text-end">Rp <?= number_format($pesanan['total_bayar'] - $pesanan['ongkir']); ?></td>
                                </tr>
                                <tr class="fw-bold">
                                    <td colspan="2">Ongkos Kirim</td>
                                    <td class="text-end">Rp <?= number_format($pesanan['ongkir']); ?></td>
                                </tr>
                                <tr class="fw-bold table-active">
                                    <td colspan="2">Grand Total</td>
                                    <td class="text-end">Rp <?= number_format($pesanan['total_bayar']); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6">
                <?php if (!empty($pesanan['bukti_sampai'])): ?>
                    <div class=" card mb-3">
                        <div class="card-header">
                            <label><strong>Bukti Sampai:</strong></label><br>
                        </div>
                        <img src="../../images/bukti_sampai/<?= htmlspecialchars($pesanan['bukti_sampai']); ?>" alt="Bukti Sampai" class="img-fluid img-thumbnail" style="max-width: 300px;">
                    </div>
                <?php else: ?>
                    <div class="mt-3 text-muted">
                        <em>Bukti sampai belum diunggah.</em>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <a href="akun.php" class="btn btn-secondary mt-3"><i class="fa fa-arrow-left"></i> Kembali ke Riwayat Pesanan</a>
    </div>
</div>

<?php
include 'footer.php';
?>