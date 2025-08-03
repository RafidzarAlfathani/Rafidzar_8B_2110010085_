<?php
// Selalu mulai session di baris paling awal untuk semua halaman
session_start();

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING)); 
// Panggil file koneksi database
include 'admin/inc/koneksi.php';
?>
<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title><?= isset($page_title) ? htmlspecialchars($page_title) : 'E-Tani Lokpaikat'; ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.ico">
    
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/owl.carousel.min.css">
    <link rel="stylesheet" href="assets/css/slick.css">
    <link rel="stylesheet" href="assets/css/magnific-popup.css">
    <link rel="stylesheet" href="assets/css/font.awesome.css">
    <link rel="stylesheet" href="assets/css/ionicons.min.css">
    <link rel="stylesheet" href="assets/css/linearicons.css">
    <link rel="stylesheet" href="assets/css/animate.css">
    <link rel="stylesheet" href="assets/css/jquery-ui.min.css">
    <link rel="stylesheet" href="assets/css/slinky.menu.css">
    <link rel="stylesheet" href="assets/css/plugins.css">
    <link rel="stylesheet" href="assets/css/style.css">
    
    <script src="assets/js/vendor/modernizr-3.7.1.min.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <style>
        .gambar-produk-seragam {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border: 1px solid #eee;
            border-radius: 8px;
        }
    </style>
</head>
<body>
   <header>
        <div class="main_header">
            <div class="header_middle">
                <div class="container">
                    <div class="row align-items-center">
                        <div class="col-lg-2 col-md-3 col-sm-3 col-3">
                            <div class="logo">
                                <a href="index.php"><h3>E-Tani</h3></a>
                            </div>
                        </div>
                        <div class="col-lg-10 col-md-6 col-sm-7 col-8">
                            <div class="header_right_info">
                                <div class="search_container mobail_s_none">
                                   <form action="index.php" method="get">
                                        <div class="search_box">
                                            <input name="q" placeholder="Cari produk..." type="text" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                                             <button type="submit"><span class="lnr lnr-magnifier"></span></button>
                                        </div>
                                    </form>
                                </div>
                                <div class="header_account_area">
                                    <div class="header_account_list register">
                                        <?php if (isset($_SESSION['pembeli_id'])) : ?>
                                            <ul>
                                                <li style="font-size: 10px;">
                                                    <a href="akun.php">
                                                        <i class="fa fa-user"></i> 
                                                        Selamat, <?= htmlspecialchars($_SESSION['pembeli_nama']); ?>
                                                    </a>
                                                </li>
                                                <li><span>/</span></li>
                                                <li><a href="logout.php">Logout</a></li>
                                            </ul>
                                        <?php else : ?>
                                            <ul>
                                                <li><a href="register.php">Daftar</a></li>
                                                <li><span>/</span></li>
                                                <li><a href="login.php">Login</a></li>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                    <div class="header_account_list  mini_cart_wrapper">
                                        <a href="keranjang.php"><span class="lnr lnr-cart"></span><span class="item_count">0</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> 
    </header>