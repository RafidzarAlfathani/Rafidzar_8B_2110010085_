<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
session_start();
include("inc/koneksi.php");
include("inc/tanggal.php");

// Cek apakah ada sesi user yang aktif (apapun levelnya)
if (isset($_SESSION['user_level'])) {
    $user_id = $_SESSION['user_id'];
    $user_level = $_SESSION['user_level'];
    $user_foto = $_SESSION['user_foto'];
    $user_data = [];

    // Ambil data user berdasarkan levelnya
    if ($user_level == 'Admin' || $user_level == 'Pimpinan') {
        $sql = mysqli_query($con, "SELECT * FROM admin WHERE id_admin='$user_id'");
        $user_data = mysqli_fetch_assoc($sql);
    } elseif ($user_level == 'Petani') {
        $sql = mysqli_query($con, "SELECT id_petani as id_admin, nama_petani as nama, foto_petani as foto, 'Petani' as level FROM petani WHERE id_petani='$user_id'");
        $user_data = mysqli_fetch_assoc($sql);
    } elseif ($user_level == 'Kurir') {
        $sql = mysqli_query($con, "SELECT id_kurir as id_admin, nama_kurir as nama, 'user.png' as foto, 'Kurir' as level FROM kurir WHERE id_kurir='$user_id'");
        $user_data = mysqli_fetch_assoc($sql);
    }
    // 'admin' menjadi variabel global untuk template header, dll.
    $admin = $user_data;
?>

    <!DOCTYPE html>
    <html lang="en" data-menu="vertical" data-nav-size="nav-default">

    <!-- Mirrored from digiboard-html.codebasket.xyz/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 21 Oct 2024 20:07:12 GMT -->

    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $meta['instansi'] ?></title>

        <link rel="shortcut icon" href="images/<?= $meta['logo'] ?>">
        <link rel="stylesheet" href="assets/vendor/css/all.min.css">
        <link rel="stylesheet" href="assets/vendor/css/OverlayScrollbars.min.css">
        <link rel="stylesheet" href="assets/vendor/css/jquery.dataTables.min.css">
        <link rel="stylesheet" href="assets/vendor/css/daterangepicker.css">
        <link rel="stylesheet" href="assets/vendor/css/bootstrap.min.css">
        <link rel="stylesheet" href="assets/css/style.css">
        <link rel="stylesheet" id="primaryColor" href="assets/css/blue-color.css">
        <link rel="stylesheet" id="rtlStyle" href="#">

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    </head>



    <body class="body-padding body-p-top light-theme">
        <?php
        if ($_SESSION['Admin']) {
            $user = $_SESSION['Admin'];
        }
        if ($_SESSION['Pimpinan']) {
            $user = $_SESSION['Pimpinan'];
        }
        $sql = mysqli_query($con, "SELECT * FROM admin WHERE id_admin='$user'");
        $admin = mysqli_fetch_assoc($sql);
        ?>
        <!-- preloader start -->
        <div class="preloader d-none">
            <div class="loader">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
        <!-- preloader end -->

        <!-- header start -->
        <?php include "header.php" ?>
        <!-- header end -->

        <!-- right sidebar start -->
        <?php include "sidebar.php" ?>
        <!-- right sidebar end -->

        <!-- main sidebar start -->
        <div class="main-sidebar">
            <div class="main-menu">
                <ul class="sidebar-menu scrollable">
                    <li class="sidebar-item open">
                        <a role="button" class="sidebar-link-group-title has-sub">Main Menu</a>
                        <ul class="sidebar-link-group">

                            <li class="sidebar-dropdown-item">
                                <a href="index.php" class="sidebar-link">
                                    <span class="nav-icon"><i class="fa-light fa-regular fa-house"></i></span>
                                    <span class="sidebar-txt">Beranda</span>
                                </a>
                            </li>

                            <?php if ($user_level == 'Admin' || $user_level == 'Pimpinan'): ?>
                                <li class="sidebar-dropdown-item">
                                    <a role="button" class="sidebar-link has-sub" data-dropdown="dtMaster">
                                        <span class="nav-icon"><i class="fa-light fa-archive"></i></span>
                                        <span class="sidebar-txt">Data Master</span>
                                    </a>
                                    <ul class="sidebar-dropdown-menu" id="dtMaster">
                                        <li class="sidebar-dropdown-item"><a href="?page=admin" class="sidebar-link"><i class="fa-light fa-regular fa-angle-right"></i> Admin</a></li>
                                        <li class="sidebar-dropdown-item"><a href="?page=pengaturan&id_meta=<?= $meta['id_meta'] ?>" class="sidebar-link"><i class="fa-light fa-regular fa-angle-right"></i> Pengaturan</a></li>
                                        <li class="sidebar-dropdown-item"><a href="?page=kategori_produk" class="sidebar-link"><i class="fa-light fa-regular fa-angle-right"></i> Kategori Produk</a></li>
                                    </ul>
                                </li>
                                <li class="sidebar-dropdown-item">
                                    <a href="?page=petani" class="sidebar-link"><span class="nav-icon"><i class="fa-light fa-users"></i></span> <span class="sidebar-txt">Petani</span></a>
                                </li>
                                <li class="sidebar-dropdown-item">
                                    <a href="?page=kurir" class="sidebar-link"><span class="nav-icon"><i class="fa-light fa-users"></i></span> <span class="sidebar-txt">Kurir</span></a>
                                </li>
                            <?php endif; ?>

                            <?php if (in_array($user_level, ['Admin', 'Pimpinan', 'Petani'])): ?>
                                <li class="sidebar-dropdown-item">
                                    <a href="?page=pembeli" class="sidebar-link"><span class="nav-icon"><i class="fa-light fa-users"></i></span> <span class="sidebar-txt">Pembeli</span></a>
                                </li>
                                <li class="sidebar-dropdown-item">
                                    <a href="?page=produk" class="sidebar-link"><span class="nav-icon"><i class="fa-light fa-box"></i></span> <span class="sidebar-txt">Produk</span></a>
                                </li>
                                <li class="sidebar-dropdown-item">
                                    <a href="?page=riwayat_harga" class="sidebar-link"><span class="nav-icon"><i class="fa-light fa-list"></i></span> <span class="sidebar-txt">Perubahan Harga Produk</span></a>
                                </li>
                            <?php endif; ?>

                            <?php if (in_array($user_level, ['Admin', 'Pimpinan', 'Petani', 'Kurir'])): ?>
                                <li class="sidebar-dropdown-item">
                                    <a href="?page=pesanan" class="sidebar-link"><span class="nav-icon"><i class="fa-light fa-list"></i></span> <span class="sidebar-txt">Pesanan</span></a>
                                </li>
                                <li class="sidebar-dropdown-item">
                                    <a href="?page=tracking" class="sidebar-link"><span class="nav-icon"><i class="fa-light fa-list"></i></span> <span class="sidebar-txt">Tracking Pengiriman</span></a>
                                </li>
                            <?php endif; ?>
                            <?php if (in_array($user_level, ['Petani'])): ?>
                                <li class="sidebar-dropdown-item">
                                    <a href="?page=penarikan_dana&aksi=petani_penarikan" class="sidebar-link">
                                        <span class="nav-icon"><i class="fa-light fa-money-bill"></i></span>
                                        <span class="sidebar-txt">Pengajuan Penarikan Dana</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (in_array($user_level, ['Kurir'])): ?>
                                <li class="sidebar-dropdown-item">
                                    <a href="?page=penarikan_dana&aksi=kurir_penarikan" class="sidebar-link">
                                        <span class="nav-icon"><i class="fa-light fa-money-bill"></i></span>
                                        <span class="sidebar-txt">Pengajuan Penarikan Dana</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (in_array($user_level, ['Admin', 'Pimpinan'])): ?>
                                <li class="sidebar-dropdown-item">
                                    <a href="?page=penarikan_dana&aksi=verifikasi" class="sidebar-link">
                                        <span class="nav-icon"><i class="fa-light fa-sack-dollar"></i></span>
                                        <span class="sidebar-txt">Verifikasi Pengajuan Dana</span>
                                    </a>
                                </li>
                            <?php endif; ?>


                            <?php if (in_array($user_level, ['Admin', 'Pimpinan'])): ?>
                                <li class="sidebar-dropdown-item">
                                    <a role="button" class="sidebar-link has-sub" data-dropdown="laporanMenu">
                                        <span class="nav-icon"><i class="fa-light fa-file-invoice"></i></span>
                                        <span class="sidebar-txt">Data Laporan</span>
                                    </a>
                                    <ul class="sidebar-dropdown-menu" id="laporanMenu">
                                        <li class="sidebar-dropdown-item"><a href="?page=laporan&aksi=laporan_produk" class="sidebar-link"><i class="fa-light fa-regular fa-angle-right"></i> Laporan Produk</a></li>
                                        <li class="sidebar-dropdown-item"><a href="?page=laporan&aksi=laporan_petani" class="sidebar-link"><i class="fa-light fa-regular fa-angle-right"></i> Laporan Petani</a></li>
                                        <li class="sidebar-dropdown-item"><a href="?page=laporan&aksi=laporan_pembeli" class="sidebar-link"><i class="fa-light fa-regular fa-angle-right"></i> Laporan Pembeli</a></li>
                                        <li class="sidebar-dropdown-item"><a href="?page=laporan&aksi=laporan_pesanan" class="sidebar-link"><i class="fa-light fa-regular fa-angle-right"></i> Laporan Pesanan</a></li>
                                        <li class="sidebar-dropdown-item"><a href="?page=laporan&aksi=laporan_produk_terlaris" class="sidebar-link"><i class="fa-light fa-regular fa-angle-right"></i> Produk Terlaris</a></li>
                                        <li class="sidebar-dropdown-item"><a href="?page=laporan&aksi=laporan_pendapatan_petani" class="sidebar-link"><i class="fa-light fa-regular fa-angle-right"></i> Pendapatan Petani</a></li>
                                        <!-- <li class="sidebar-dropdown-item"><a href="?page=laporan&aksi=laporan_saldo_petani" class="sidebar-link"><i class="fa-light fa-regular fa-angle-right"></i> saldo Petani</a></li> -->
                                        <!-- <li class="sidebar-dropdown-item"><a href="?page=laporan&aksi=laporan_saldo_kurir" class="sidebar-link"><i class="fa-light fa-regular fa-angle-right"></i> saldo Kurir</a></li> -->
                                        <li class="sidebar-dropdown-item"><a href="?page=laporan&aksi=laporan_kinerja_kurir" class="sidebar-link"><i class="fa-light fa-regular fa-angle-right"></i> Kinerja Kurir</a></li>
                                        <li class="sidebar-dropdown-item"><a href="?page=laporan&aksi=laporan_perubahan_harga" class="sidebar-link"><i class="fa-light fa-regular fa-angle-right"></i> Perubahan Harga</a></li>
                                        <li class="sidebar-dropdown-item"><a href="?page=laporan&aksi=laporan_tracking" class="sidebar-link"><i class="fa-light fa-regular fa-angle-right"></i> Tracking Pesanan</a></li>
                                    </ul>
                                </li>
                            <?php endif; ?>

                        </ul>
                    </li>
                </ul>
            </div>
        </div>
        <!-- main sidebar end -->

        <!-- main content start -->
        <div class="main-content">

            <?php

            $page = $_GET['page'];
            $aksi = $_GET['aksi'];

            if ($page == "pengaturan") {
                if ($aksi == "") {
                    include "page/pengaturan/pengaturan.php";
                }
                if ($aksi == "ubah") {
                    include "page/pengaturan/ubah.php";
                }
            }

            if ($page == "petani") {
                if ($aksi == "") {
                    include "page/petani/petani.php";
                }
                if ($aksi == "tambah") {
                    include "page/petani/tambah.php";
                }
                if ($aksi == "ubah") {
                    include "page/petani/ubah.php";
                }
                if ($aksi == "hapus") {
                    include "page/petani/hapus.php";
                }
            }

            if ($page == "admin") {
                if ($aksi == "") {
                    include "page/admin/admin.php";
                }
                if ($aksi == "tambah") {
                    include "page/admin/tambah.php";
                }
                if ($aksi == "ubah") {
                    include "page/admin/ubah.php";
                }
                if ($aksi == "hapus") {
                    include "page/admin/hapus.php";
                }
            }

            if ($page == "produk") {
                if ($aksi == "") {
                    include "page/produk/produk.php";
                }
                if ($aksi == "tambah") {
                    include "page/produk/tambah.php";
                }
                if ($aksi == "ubah") {
                    include "page/produk/ubah.php";
                }
                if ($aksi == "hapus") {
                    include "page/produk/hapus.php";
                }
            }

            if ($page == "kategori_produk") {
                if ($aksi == "") {
                    include "page/kategori_produk/kategori_produk.php";
                }
                if ($aksi == "tambah") {
                    include "page/kategori_produk/tambah.php";
                }
                if ($aksi == "ubah") {
                    include "page/kategori_produk/ubah.php";
                }
                if ($aksi == "hapus") {
                    include "page/kategori_produk/hapus.php";
                }
            }

            if ($page == "pembeli") {
                if ($aksi == "") {
                    include "page/pembeli/pembeli.php";
                }
                if ($aksi == "tambah") {
                    include "page/pembeli/tambah.php";
                }
                if ($aksi == "ubah") {
                    include "page/pembeli/ubah.php";
                }
                if ($aksi == "hapus") {
                    include "page/pembeli/hapus.php";
                }
            }

            if ($page == "kurir") {
                if ($aksi == "") {
                    include "page/kurir/kurir.php";
                }
                if ($aksi == "tambah") {
                    include "page/kurir/tambah.php";
                }
                if ($aksi == "ubah") {
                    include "page/kurir/ubah.php";
                }
                if ($aksi == "hapus") {
                    include "page/kurir/hapus.php";
                }
            }

            if ($page == "riwayat_harga") {
                if ($aksi == "") {
                    include "page/riwayat_harga/riwayat_harga.php";
                }
                if ($aksi == "tambah") {
                    include "page/riwayat_harga/tambah.php";
                }
                if ($aksi == "ubah") {
                    include "page/riwayat_harga/ubah.php";
                }
                if ($aksi == "hapus") {
                    include "page/riwayat_harga/hapus.php";
                }
            }

            if ($page == "pesanan") {
                if ($aksi == "") {
                    include "page/pesanan/pesanan.php";
                }
                if ($aksi == "tambah") {
                    include "page/pesanan/tambah.php";
                }
                if ($aksi == "ubah") {
                    include "page/pesanan/ubah.php";
                }
                if ($aksi == "hapus") {
                    include "page/pesanan/hapus.php";
                }
                if ($aksi == "detail") {
                    include "page/pesanan/detail.php";
                }
                if ($aksi == "upload_bukti_sampai") {
                    include "page/pesanan/upload_bukti_sampai.php";
                }
            }

            if ($page == "tracking") {
                if ($aksi == "") {
                    include "page/tracking/tracking.php";
                }
                if ($aksi == "tambah") {
                    include "page/tracking/tambah.php";
                }
                if ($aksi == "ubah") {
                    include "page/tracking/ubah.php";
                }
                if ($aksi == "hapus") {
                    include "page/tracking/hapus.php";
                }
            }

            if ($page == "penarikan_dana") {
                if ($aksi == "petani_penarikan") {
                    include "page/penarikan_dana/penarikan_dana.php";
                }
                if ($aksi == "kurir_penarikan") {
                    include "page/penarikan_dana/penarikan_dana_kurir.php";
                }
                if ($aksi == "kurir_pengajuan") {
                    include "page/penarikan_dana/pengajuan_dana_kurir.php";
                }
                if ($aksi == "petani_pengajuan") {
                    include "page/penarikan_dana/pengajuan_dana_petani.php";
                }
                if ($aksi == "verifikasi") {
                    include "page/penarikan_dana/pengajuan_verifikasi_admin.php";
                }
                if ($aksi == "proses") {
                    include "page/penarikan_dana/proses.php";
                }
                if ($aksi == "tolak") {
                    include "page/penarikan_dana/tolak.php";
                }
            }


            if ($page == "laporan") {
                // Laporan Utama
                if ($aksi == "laporan_produk") {
                    include "page/laporan/laporan_produk.php";
                }
                if ($aksi == "laporan_petani") {
                    include "page/laporan/laporan_petani.php";
                }
                if ($aksi == "laporan_pembeli") {
                    include "page/laporan/laporan_pembeli.php";
                }
                if ($aksi == "laporan_pesanan") {
                    include "page/laporan/laporan_pesanan.php";
                }

                // Laporan Analisa
                if ($aksi == "laporan_produk_terlaris") {
                    include "page/laporan/laporan_produk_terlaris.php";
                }
                if ($aksi == "laporan_pendapatan_petani") {
                    include "page/laporan/laporan_pendapatan_petani.php";
                }
                if ($aksi == "laporan_kinerja_kurir") {
                    include "page/laporan/laporan_kinerja_kurir.php";
                }
                if ($aksi == "laporan_saldo_petani") {
                    include "page/laporan/laporan_saldo_petani.php";
                }
                if ($aksi == "laporan_saldo_kurir") {
                    include "page/laporan/laporan_saldo_kurir.php";
                }

                // Laporan Riwayat
                if ($aksi == "laporan_perubahan_harga") {
                    include "page/laporan/laporan_perubahan_harga.php";
                }
                if ($aksi == "laporan_tracking") {
                    include "page/laporan/laporan_tracking.php";
                }
            }

            if ($page == "") {
                include "home.php";
            }
            ?>

            <!-- footer start -->
            <?php include "footer.php" ?>
            <!-- footer end -->
        </div>
        <!-- main content end -->

        <script src="assets/vendor/js/jquery-3.6.0.min.js"></script>
        <script src="assets/vendor/js/jquery.overlayScrollbars.min.js"></script>
        <script src="assets/vendor/js/apexcharts.js"></script>
        <script src="assets/vendor/js/jquery.dataTables.min.js"></script>
        <script src="assets/vendor/js/moment.min.js"></script>
        <script src="assets/vendor/js/daterangepicker.js"></script>
        <script src="assets/vendor/js/bootstrap.bundle.min.js"></script>
        <script src="assets/js/dashboard.js"></script>
        <script src="assets/js/main.js"></script>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <!-- for demo purpose -->
        <script>
            var rtlReady = $('html').attr('dir', 'ltr');
            if (rtlReady !== undefined) {
                localStorage.setItem('layoutDirection', 'ltr');
            }
        </script>
        <!-- for demo purpose -->
    </body>

    <!-- Mirrored from digiboard-html.codebasket.xyz/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 21 Oct 2024 20:07:12 GMT -->

    </html>

<?php
} else {
    echo '<script language="javascript">alert("Anda belum login, Klik OK untuk Login"); document.location="login.php";</script>';
}
?>