<?php
$page_title = "Pesanan Berhasil";
include 'header.php';

// Ambil kode invoice dari URL untuk ditampilkan
$kode_invoice = isset($_GET['invoice']) ? htmlspecialchars($_GET['invoice']) : 'Tidak Ditemukan';
?>

    <div class="breadcrumbs_area">
        <div class="container">   
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <h3>Pesanan Berhasil</h3>
                    </div>
                </div>
            </div>
        </div>         
    </div>

    <div class="checkout_page_bg">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-success text-center" style="padding: 40px;">
                        <h4 class="alert-heading">Terima Kasih!</h4>
                        <p>Pesanan Anda telah berhasil dibuat.</p>
                        <hr>
                        <p>Nomor Invoice Anda adalah: <strong><?= $kode_invoice; ?></strong></p>
                        <p class="mb-0">Silakan lakukan pembayaran jika Anda memilih metode Transfer Bank. Anda dapat melihat detail pesanan Anda di halaman Akun Saya.</p>
                        <a href="index.php" class="btn btn-primary mt-3">Kembali ke Toko</a>
                        <a href="akun.php" class="btn btn-secondary mt-3">Lihat Riwayat Pesanan</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php
include 'footer.php';
?>