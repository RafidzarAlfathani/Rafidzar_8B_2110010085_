<?php
// Setiap halaman yang membutuhkan session dan koneksi harus memanggil ini di awal
session_start();

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING)); 
include 'admin/inc/koneksi.php';

// --- LOGIKA KHUSUS HALAMAN DETAIL PRODUK ---

// 1. Validasi dan ambil ID produk dari URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    // Jika tidak ada ID, kembali ke halaman utama
    header("Location: index.php");
    exit();
}
$id_produk = (int)$_GET['id'];

// 2. Query untuk mengambil data produk spesifik, digabung dengan data petani dan kategori
$query_produk = $con->query("SELECT
                                p.*,
                                k.nama_kategori,
                                pt.nama_petani
                            FROM produk p
                            JOIN kategori_produk k ON p.id_kategori = k.id_kategori
                            JOIN petani pt ON p.id_petani = pt.id_petani
                            WHERE p.id_produk = '$id_produk' AND p.status_produk = 'Tersedia'");

// Jika produk dengan ID tersebut tidak ditemukan atau tidak tersedia
if ($query_produk->num_rows == 0) {
    echo "<script>alert('Produk tidak ditemukan atau tidak tersedia.'); window.location.href='index.php';</script>";
    exit();
}
$produk = $query_produk->fetch_assoc();


// 3. Atur judul halaman secara dinamis berdasarkan nama produk
$page_title = htmlspecialchars($produk['nama_produk']);


// 4. Panggil file header.php
include 'header.php';


// 5. Query untuk mengambil produk terkait (dari kategori yang sama)
$id_kategori_terkait = $produk['id_kategori'];
$query_terkait = $con->query("SELECT * FROM produk
                              WHERE id_kategori = '$id_kategori_terkait'
                              AND id_produk != '$id_produk'
                              AND status_produk = 'Tersedia'
                              LIMIT 4");
?>

    <div class="breadcrumbs_area">
        <div class="container">   
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <h3>Detail Produk</h3>
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            <li><a href="index.php?kategori=<?= $produk['id_kategori']; ?>"><?= $produk['nama_kategori']; ?></a></li>
                            <li><?= htmlspecialchars($produk['nama_produk']); ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>         
    </div>
    <div class="product_details mt-60 mb-60">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-6">
                    <div class="product-details-tab">
                        <div id="img-1" class="zoomWrapper single-zoom">
                            <a href="#">
                                <img id="zoom1" src="admin/images/produk/<?= $produk['foto_produk']; ?>" data-zoom-image="admin/images/produk/<?= $produk['foto_produk']; ?>" alt="Foto Produk">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6">
                    <div class="product_d_right">
                       <form action="keranjang.php?aksi=tambah&id=<?= $produk['id_produk']; ?>" method="POST">
                           
                            <h1><?= htmlspecialchars($produk['nama_produk']); ?></h1>
                            
                            <div class="price_box">
                                <span class="current_price">Rp <?= number_format($produk['harga']); ?> / <?= $produk['satuan']; ?></span>
                            </div>
                            <div class="product_desc">
                                <p><?= substr(strip_tags($produk['deskripsi']), 0, 250); ?>...</p>
                            </div>
                            <div class="product_variant_info">
                                <ul>
                                    <li><b>Status Stok:</b> 
                                        <?php if ($produk['stok'] > 0): ?>
                                            <span class="badge bg-success">Tersedia (Stok: <?= $produk['stok'] ?>)</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Habis</span>
                                        <?php endif; ?>
                                    </li>
                                    <li><b>Kategori:</b> <a href="index.php?kategori=<?= $produk['id_kategori']; ?>"><?= $produk['nama_kategori']; ?></a></li>
                                    <li><b>Petani:</b> <?= $produk['nama_petani']; ?></li>
                                </ul>
                            </div>
                            
                            <?php if ($produk['stok'] > 0): ?>
                            <div class="product_variant quantity">
                                <label>Jumlah</label>
                                <input min="1" max="<?= $produk['stok']; ?>" value="1" type="number" name="jumlah">
                                <button class="button" type="submit">Tambah ke Keranjang</button>  
                            </div>
                            <?php else: ?>
                            <div class="product_variant quantity">
                                <button class="button" type="button" disabled>Stok Habis</button>  
                            </div>
                            <?php endif; ?>

                        </form>
                    </div>
                </div>
            </div>
        </div>    
    </div>
    <div class="product_d_info mb-60">
        <div class="container">   
            <div class="row">
                <div class="col-12">
                    <div class="product_d_inner">   
                        <div class="product_info_button">    
                            <ul class="nav" role="tablist">
                                <li >
                                    <a class="active" data-bs-toggle="tab" href="#info" role="tab" aria-controls="info" aria-selected="false">Deskripsi Lengkap</a>
                                </li>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <div class="tab-pane fade show active" id="info" role="tabpanel" >
                                <div class="product_info_content">
                                    <p><?= nl2br(htmlspecialchars($produk['deskripsi'])); ?></p>
                                </div>    
                            </div>
                        </div>
                    </div>   
                </div>
            </div>
        </div>
    </div>
    <?php if ($query_terkait->num_rows > 0) : ?>
    <section class="related_product_area mb-50">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="section_title">
                        <h2>Produk Terkait</h2>
                    </div>
                    <div class="product_carousel product_column4 owl-carousel">
                        <?php while($terkait = $query_terkait->fetch_assoc()): ?>
                        <div class="single_product">
                            <div class="product_thumb">
                                <a class="primary_img" href="detail_produk.php?id=<?= $terkait['id_produk']; ?>">
                                    <img class="gambar-produk-seragam" src="admin/images/produk/<?= $terkait['foto_produk']; ?>" alt="">
                                </a>
                                <div class="action_links">
                                    <ul>
                                        <li class="add_to_cart"><a href="keranjang.php?aksi=tambah&id=<?= $terkait['id_produk']; ?>" data-tippy="Tambah ke Keranjang"> <span class="lnr lnr-cart"></span></a></li>
                                        <li class="quick_button"><a href="detail_produk.php?id=<?= $terkait['id_produk']; ?>" data-tippy="Lihat Detail"> <span class="lnr lnr-magnifier"></span></a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="product_content">
                                <h4 class="product_name"><a href="detail_produk.php?id=<?= $terkait['id_produk']; ?>"><?= htmlspecialchars($terkait['nama_produk']); ?></a></h4>
                                <div class="price_box">
                                    <span class="current_price">Rp <?= number_format($terkait['harga']); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endif; ?>
    <?php
// Panggil file footer.php
include 'footer.php';
?>