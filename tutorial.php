<?php
// Panggil header (yang di dalamnya sudah ada koneksi.php)
include 'header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <title>Tutorial Pengguna</title>
</head>
<body>
<div class="breadcrumbs_area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb_content">
                    <h3>Toko</h3>
                    <ul>
                        <li><a href="index.php">home</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<h2>Tutorial Penggunaan Website</h2>

<!-- accordion-tutorial Items -->
<button class="accordion-tutorial">1. Registrasi Akun</button>
<div class="panel-tutorial">
  <ul>
    <li>Klik tombol <strong>Daftar</strong> di kanan atas.</li>
    <li>Isi data lengkap (nama, email, password).</li>
    <li>Klik <strong>Daftar</strong> dan tunggu konfirmasi.</li>
  </ul>
</div>

<button class="accordion-tutorial">2. Login</button>
<div class="panel-tutorial">
  <ul>
    <li>Klik menu <strong>Login</strong>.</li>
    <li>Masukkan username dan password.</li>
    <li>Klik tombol <strong>Login</strong>.</li>
  </ul>
</div>

<button class="accordion-tutorial">3. Menjelajahi Produk</button>
<div class="panel-tutorial">
  <ul>
    <li>Gunakan kolom pencarian atau filter kategori.</li>
    <li>Klik produk untuk melihat detail.</li>
  </ul>
</div>

<button class="accordion-tutorial">4. Menambahkan Produk ke Keranjang</button>
<div class="panel-tutorial">
  <ul>
    <li>Pilih produk.</li>
    <li>Pilih jumlah produk.</li>
    <li>Klik <strong>Tambah ke Keranjang</strong>.</li>
  </ul>
</div>

<button class="accordion-tutorial">5. Checkout dan Konfirmasi Pesanan</button>
<div class="panel-tutorial">
  <ul>
    <li>Buka halaman keranjang.</li>
    <li>klik <strong>Lanjut Checkout</strong></li>
    <li>Masukkan alamat pengiriman & nomor Penerima(WA)</li>
    <li>Klik <strong>Buat Pesanan</strong>.</li>
  </ul>
</div>

<button class="accordion-tutorial">6. Upload Bukti Pembayaran</button>
<div class="panel-tutorial">
  <ul>
    <li>Buka <strong>Pesanan Saya</strong>.</li>
    <li>Lakukan Pembayaran Dengan metode Transfer.</li>
    <li><strong>Nomor Rekening</strong> dan <strong>Penerima </strong>Tertera di Halaman Detail Pesanan.</li>
    <li>Upload bukti transfer.</li>
    <li>Klik <strong>Kirim</strong>.</li>
  </ul>
</div>

<button class="accordion-tutorial">7. Melacak Pengiriman</button>
<div class="panel-tutorial">
  <ul>
    <li>Klik tombol <strong>Lacak</strong> di pesanan Anda.</li>
    <li>Lihat posisi kurir di peta.</li>
  </ul>
</div>

<button class="accordion-tutorial">8. Mencetak Bukti Pesanan</button>
<div class="panel-tutorial">
  <ul>
    <li>Klik tombol <strong>Detail</strong> pada pesanan.</li>
    <li>Klik <strong>Cetak Bukti</strong> atau <strong>Unduh PDF</strong>.</li>
  </ul>
</div>

<!-- JavaScript accordion-tutorial -->
<script>
  const acc = document.querySelectorAll(".accordion-tutorial");
  acc.forEach(button => {
    button.addEventListener("click", function () {
      this.classList.toggle("active");
      const panel = this.nextElementSibling;
      panel.style.display = (panel.style.display === "block") ? "none" : "block";
    });
  });
</script>

</body>
</html>
