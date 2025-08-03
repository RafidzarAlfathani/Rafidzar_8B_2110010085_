<?php
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// Memulai session untuk mengelola status login
session_start();

// Atur judul halaman spesifik sebelum memanggil header
$page_title = "Toko Hasil Tani - E-Tani Lokpaikat";

// Panggil header (yang di dalamnya sudah ada koneksi.php)
include 'header.php';

// --- LOGIKA UTAMA HALAMAN INDEX ---

$halaman_aktif = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$limit = 9;
$offset = ($halaman_aktif - 1) * $limit;

// --- MEMBANGUN QUERY BERDASARKAN FILTER ---

// 1. Kondisi Dasar
$where_clause = "WHERE produk.status_produk = 'Tersedia'";

// 2. Inisialisasi parameter untuk URL
$kategori_aktif = '';
$kategori_param = '';
$search_param = '';

// 3. Logika untuk Filter Pencarian (SEARCH)
if (isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search_query = mysqli_real_escape_string($con, trim($_GET['q']));
    $where_clause .= " AND produk.nama_produk LIKE '%$search_query%'";
    $search_param = '&q=' . urlencode($search_query); // Simpan parameter untuk link
}

// 4. Logika untuk Filter Kategori
if (isset($_GET['kategori']) && !empty($_GET['kategori'])) {
    $id_kategori_filter = (int)$_GET['kategori'];
    $where_clause .= " AND produk.id_kategori = $id_kategori_filter";
    $kategori_param = '&kategori=' . $id_kategori_filter; // Simpan parameter untuk link

    $q_kategori = $con->query("SELECT nama_kategori FROM kategori_produk WHERE id_kategori = $id_kategori_filter");
    if ($q_kategori && $q_kategori->num_rows > 0) {
        $data_kategori = $q_kategori->fetch_assoc();
        $kategori_aktif = $data_kategori['nama_kategori'];
    }
}

// 5. Logika untuk Pengurutan (SORTING)
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : 'newness';
$sort_param = '&sort=' . $sort_option; // Simpan parameter untuk link
$order_by_clause = "ORDER BY ";
switch ($sort_option) {
    case 'price_asc':
        $order_by_clause .= "produk.harga ASC";
        break;
    case 'price_desc':
        $order_by_clause .= "produk.harga DESC";
        break;
    case 'name_asc':
        $order_by_clause .= "produk.nama_produk ASC";
        break;
    case 'name_desc':
        $order_by_clause .= "produk.nama_produk DESC";
        break;
    default:
        $order_by_clause .= "produk.tgl_upload DESC";
        break;
}

// --- EKSEKUSI QUERY ---

// Query untuk menghitung TOTAL PRODUK (dengan filter yang sama)
$query_total = "SELECT COUNT(*) AS total FROM produk $where_clause";
$result_total = $con->query($query_total);
$total_produk = $result_total->fetch_assoc()['total'];
$jumlah_halaman = ceil($total_produk / $limit);

// Query UTAMA untuk mengambil data produk yang akan ditampilkan
$query_produk = "SELECT produk.*, kategori_produk.nama_kategori FROM produk JOIN kategori_produk ON produk.id_kategori = kategori_produk.id_kategori $where_clause $order_by_clause LIMIT $limit OFFSET $offset";
$ambil_produk = $con->query($query_produk);

if ($ambil_produk === false) {
    die("Error Query: " . $con->error);
}
?>

<div class="breadcrumbs_area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb_content">
                    <h3>Toko</h3>
                    <ul>
                        <li><a href="index.php">home</a></li>
                        <li>
                            <?php
                            if (!empty($kategori_aktif)) {
                                echo $kategori_aktif;
                            } elseif (isset($_GET['q'])) {
                                echo 'Hasil Pencarian: "' . htmlspecialchars($_GET['q']) . '"';
                            } else {
                                echo "Semua Produk";
                            }
                            ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="shop_area shop_reverse mt-70 mb-70">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-12">
                <aside class="sidebar_widget">
                    <div class="widget_inner">
                        <div class="widget_list widget_categories">
                            <h3>Kategori Produk</h3>
                            <ul>
                                <li><a href="index.php">Semua Kategori</a></li>
                                <?php
                                $ambil_kategori_sidebar = $con->query("SELECT * FROM kategori_produk ORDER BY nama_kategori ASC");
                                while ($kat = $ambil_kategori_sidebar->fetch_assoc()) {
                                ?>
                                    <li><a href="index.php?kategori=<?= $kat['id_kategori']; ?>"><?= $kat['nama_kategori']; ?></a></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </aside>
            </div>
            <div class="col-lg-9 col-md-12">
                <div class="shop_toolbar_wrapper">
                    <div class="shop_toolbar_btn">
                        <button data-role="grid_3" type="button" class="active btn-grid-3" data-toggle="tooltip" title="3"></button>
                        <button data-role="grid_list" type="button" class="btn-list" data-toggle="tooltip" title="List"></button>
                    </div>



                    <div class="page_amount">
                        <p>Menampilkan <?= $ambil_produk->num_rows ?> dari <?= $total_produk ?> hasil</p>
                    </div>
                </div>
                <div class="row shop_wrapper">
                    <?php
                    if ($ambil_produk->num_rows > 0) {
                        while ($produk = $ambil_produk->fetch_assoc()) {
                    ?>
                            <div class="col-lg-4 col-md-4 col-sm-6 col-12 ">
                                <div class="single_product">
                                    <div class="product_thumb">
                                        <a class="primary_img" href="detail_produk.php?id=<?= $produk['id_produk']; ?>">
                                            <img class="gambar-produk-seragam" src="admin/images/produk/<?= $produk['foto_produk']; ?>" alt="Foto Produk <?= $produk['nama_produk']; ?>">
                                        </a>
                                        <div class="action_links">
                                            <ul>
                                                <li class="add_to_cart"><a href="keranjang.php?aksi=tambah&id=<?= $produk['id_produk']; ?>" data-tippy="Tambah ke Keranjang"><span class="lnr lnr-cart"></span></a></li>
                                                <li class="quick_button"><a href="detail_produk.php?id=<?= $produk['id_produk']; ?>" data-tippy="Lihat Detail"><span class="lnr lnr-magnifier"></span></a></li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="product_content grid_content">
                                        <h4 class="product_name"><a href="detail_produk.php?id=<?= $produk['id_produk']; ?>"><?= htmlspecialchars($produk['nama_produk']); ?></a></h4>
                                        <p><a href="index.php?kategori=<?= $produk['id_kategori']; ?>"><?= $produk['nama_kategori']; ?></a></p>
                                        <div class="price_box">
                                            <span class="current_price">Rp <?= number_format($produk['harga']); ?></span>
                                        </div>
                                    </div>

                                    <div class="product_content list_content">
                                        <h4 class="product_name"><a href="detail_produk.php?id=<?= $produk['id_produk']; ?>"><?= htmlspecialchars($produk['nama_produk']); ?></a></h4>
                                        <p><a href="index.php?kategori=<?= $produk['id_kategori']; ?>"><?= $produk['nama_kategori']; ?></a></p>
                                        <div class="price_box">
                                            <span class="current_price">Rp <?= number_format($produk['harga']); ?></span>
                                        </div>
                                        <div class="product_desc">
                                            <p><?= substr(strip_tags($produk['deskripsi']), 0, 200); ?>...</p>
                                        </div>
                                        <div class="action_links list_action_right">
                                            <ul>
                                                <li class="add_to_cart"><a href="keranjang.php?aksi=tambah&id=<?= $produk['id_produk']; ?>" title="Add to cart">Tambah ke Keranjang</a></li>
                                                <li class="quick_button"><a href="detail_produk.php?id=<?= $produk['id_produk']; ?>" data-tippy="Lihat Detail"><span class="lnr lnr-magnifier"></span></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                    <?php
                        }
                    } else {
                        echo "<div class='col-12'><div class='alert alert-warning text-center'>Produk tidak ditemukan.</div></div>";
                    }
                    ?>
                </div>

                <div class="shop_toolbar t_bottom">
                    <div class="pagination">
                        <ul>
                            <?php for ($i = 1; $i <= $jumlah_halaman; $i++) : ?>
                                <li class="<?= ($i == $halaman_aktif) ? 'current' : '' ?>">
                                    <a href="index.php?halaman=<?= $i; ?><?= $kategori_param ?><?= $sort_param ?><?= $search_param ?>"><?= $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="tutorial">
    <p>🔍 Butuh bantuan? Lihat panduan lengkap di halaman
        <a href="tutorial.php" class="tutorial-link">Petunjuk Pengguna</a>.
    </p>
</div>
<?php
include 'footer.php';

?>