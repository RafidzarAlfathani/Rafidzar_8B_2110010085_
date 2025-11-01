<?php
// Setel judol halaman spesifik sebelum memanggil header
$page_title = 'Tutorial Pengguna - E-Tani Lokpaikat'; 

// Panggil header 
include 'header.php';
?>

<div class="breadcrumbs_area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb_content">
                    <h3>Tutorial Pengguna</h3> 
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-content-area pt-60 pb-60">
    <div class="container">
        <div class="row">
            <div class="col-lg-10 offset-lg-1">
                <div class="tutorial-wrapper"><br><br>
                    <h2 class="tutorial-title">Panduan Penggunaan Website E-Tani</h2><br>
                    
                    <div class="accordion" id="tutorialAccordion">

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button  bg-primary text-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    1. Bagaimana Cara Mendaftar Akun?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#tutorialAccordion">
                                <div class="accordion-body">
                                    <ol>
                                        <li>Klik tombol <strong>Daftar</strong> yang berada di pojok kanan atas halaman.</li>
                                        <li>Isi formolir pendaftaran dengan data yang valid (nama, email, dan password).</li>
                                        <li>Klik tombol <strong>Daftar</strong> untuk menyelesaikan proses registrasi.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button  bg-primary text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    2. Bagaimana Cara Masuk ke Akun?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#tutorialAccordion">
                                <div class="accordion-body">
                                     <ol>
                                        <li>Pilih menu <strong>Login</strong> di pojok kanan atas.</li>
                                        <li>Masukkan email dan password yang telah Anda daftarkan sebelumnya.</li>
                                        <li>Klik tombol <strong>Login</strong> untuk masuk ke dasbor akun Anda.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button  bg-primary text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    3. Bagaimana Cara Mencari dan Melihat Produk?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#tutorialAccordion">
                                <div class="accordion-body">
                                    <ol>
                                        <li>Gunakan <strong>kolom pencarian</strong> di bagian atas halaman untuk menemukan produk spesifik.</li>
                                        <li>Anda juga dapat menjelajahi produk berdasarkan kategori yang tersedia.</li>
                                        <li>Klik pada gambar atau nama produk untuk melihat detail lengkapnya.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button  bg-primary text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    4. Bagaimana Cara Menambahkan Produk ke Keranjang?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#tutorialAccordion">
                                <div class="accordion-body">
                                    <ol>
                                        <li>Pada halaman detail produk, tentukan jumlah yang ingin Anda beli.</li>
                                        <li>Klik tombol <strong>Tambah ke Keranjang</strong> untuk memasukkan produk ke keranjang belanja.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFive">
                                <button class="accordion-button  bg-primary text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                    5. Bagaimana Cara Melakukan Checkout Pesanan?
                                </button>
                            </h2>
                            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#tutorialAccordion">
                                <div class="accordion-body">
                                    <ol>
                                        <li>Masuk ke halaman <strong>Keranjang Belanja</strong> Anda.</li>
                                        <li>Periksa kembali daftar pesanan, lalu klik <strong>Lanjut Checkout</strong>.</li>
                                        <li>Isi alamat pengiriman dan nomor telepon (WhatsApp) yang aktif dengan benar.</li>
                                        <li>Klik <strong>Buat Pesanan</strong> untuk mengonfirmasi pesanan Anda.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingSix">
                                <button class="accordion-button  bg-primary text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                    6. Bagaimana Cara Mengunggah Bukti Pembayaran?
                                </button>
                            </h2>
                            <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#tutorialAccordion">
                                <div class="accordion-body">
                                    <ol>
                                        <li>Setelah membuat pesanan, masuk ke menu <strong>Akun Saya > Pesanan</strong>.</li>
                                        <li>Lakukan pembayaran melalui transfer bank ke nomor rekening yang tertera di detail pesanan.</li>
                                        <li>Setelah transfer berhasil, unggah foto atau screenshot bukti pembayaran pada halaman tersebut.</li>
                                        <li>Klik <strong>Kirim</strong> untuk konfirmasi. Pesanan Anda akan segera diproses oleh admin.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingSeven">
                                <button class="accordion-button  bg-primary text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                    7. Apakah Saya Bisa Melacak Pengiriman?
                                </button>
                            </h2>
                            <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#tutorialAccordion">
                                <div class="accordion-body">
                                    <ol>
                                        <li>Ya, setelah pesanan Anda dikirim, tombol <strong>Lacak</strong> akan muncol di detail pesanan Anda.</li>
                                        <li>Klik tombol tersebut untuk melihat posisi kurir di peta secara *real-time*.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEight">
                                <button class="accordion-button  bg-primary text-white collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                                    8. Bagaimana Cara Mencetak Invoice Pesanan?
                                </button>
                            </h2>
                            <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight" data-bs-parent="#tutorialAccordion">
                                <div class="accordion-body">
                                    <ol>
                                        <li>Pada halaman detail pesanan, klik tombol <strong>Detail</strong>.</li>
                                        <li>Anda akan menemukan opsi untuk <strong>Cetak Bukti</strong> atau <strong>Unduh PDF</strong> sebagai arsip Anda.</li>
                                    </ol>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Panggil footer yang berisi file-file JavaScript
include 'footer.php'; 
?>  