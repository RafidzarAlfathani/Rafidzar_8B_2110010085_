<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
// Logika keranjang harus berada di paling atas, sebelum header dipanggil
$page_title = "Keranjang Belanja";
include 'header.php'; // Ini akan otomatis memulai session dan koneksi db

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = array();
}

$biaya_admin = 1500;


// Cek jika ada aksi yang dilakukan (tambah, hapus, update)
if (isset($_GET['aksi'])) {

    // AKSI: Menambah item ke keranjang
    if ($_GET['aksi'] == 'tambah') {
        // Cek apakah pembeli sudah login atau belum
        if (!isset($_SESSION['pembeli_id'])) {
            $_SESSION['pesan_notifikasi'] = "Anda harus login terlebih dahulu untuk menambahkan produk ke keranjang.";
            echo "<script>window.location.href='login.php';</script>";
            exit();
        }

        $id_produk = (int)$_GET['id'];
        $jumlah = isset($_POST['jumlah']) ? (int)$_POST['jumlah'] : 1;

        $query_produk = $con->query("SELECT * FROM produk WHERE id_produk = '$id_produk' AND status_produk = 'Tersedia'");
        if ($query_produk->num_rows > 0) {
            $produk = $query_produk->fetch_assoc();
            if (isset($_SESSION['keranjang'][$id_produk])) {
                $_SESSION['keranjang'][$id_produk] += $jumlah;
            } else {
                $_SESSION['keranjang'][$id_produk] = $jumlah;
            }
        }
        echo "<script>window.location.href='keranjang.php';</script>";
        exit();
    }

    // AKSI: Menghapus item dari keranjang
    elseif ($_GET['aksi'] == 'hapus') {
        $id_produk = (int)$_GET['id'];
        if (isset($_SESSION['keranjang'][$id_produk])) {
            unset($_SESSION['keranjang'][$id_produk]);
        }
        echo "<script>window.location.href='keranjang.php';</script>";
        exit();
    }

    // AKSI: Mengosongkan seluruh keranjang
    elseif ($_GET['aksi'] == 'kosongkan') {
        $_SESSION['keranjang'] = array();
        echo "<script>window.location.href='keranjang.php';</script>";
        exit();
    }
}

// AKSI: Mengupdate jumlah item dari form di halaman keranjang
if (isset($_POST['update_keranjang'])) {
    foreach ($_POST['jumlah'] as $id_produk => $jumlah) {
        $id_produk = (int)$id_produk;
        $jumlah = (int)$jumlah;
        if ($jumlah > 0) {
            $_SESSION['keranjang'][$id_produk] = $jumlah;
        } else {
            unset($_SESSION['keranjang'][$id_produk]);
        }
    }
    echo "<script>window.location.href='keranjang.php';</script>";
    exit();
}
?>

<div class="breadcrumbs_area">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="breadcrumb_content">
                    <h3>Keranjang Belanja</h3>
                    <ul>
                        <li><a href="index.php">home</a></li>
                        <li>Keranjang Belanja</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="shopping_cart_area mt-60 mb-60">
    <div class="container">
        <form action="keranjang.php" method="POST">
            <div class="row">
                <div class="col-12">
                    <?php if (empty($_SESSION['keranjang'])): ?>
                        <div class="alert alert-info text-center" role="alert">
                            Keranjang belanja Anda masih kosong. Yuk, <a href="index.php" class="alert-link">mulai belanja</a>!
                        </div>
                    <?php else: ?>
                        <div class="table_desc">
                            <div class="cart_page table-responsive">
                                <table>
                                    <thead>
                                        <tr>
                                            <th class="product_remove">Hapus</th>
                                            <th class="product_thumb">Gambar</th>
                                            <th class="product_name">Produk</th>
                                            <th class="product-price">Harga</th>
                                            <th class="product_quantity">Jumlah</th>
                                            <th class="product_total">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $grand_total = 0;
                                        $ids_produk = array_keys($_SESSION['keranjang']);
                                        if (!empty($ids_produk)) {
                                            $ids_string = implode(',', $ids_produk);
                                            $query_items = $con->query("SELECT * FROM produk WHERE id_produk IN ($ids_string)");

                                            while ($item = $query_items->fetch_assoc()) {
                                                $id_produk = $item['id_produk'];
                                                $jumlah = $_SESSION['keranjang'][$id_produk];
                                                $sub_total = $item['harga'] * $jumlah;
                                                $grand_total += $sub_total;
                                        ?>
                                                <tr>
                                                    <td class="product_remove"><a href="keranjang.php?aksi=hapus&id=<?= $id_produk; ?>"><i class="fa fa-trash-o"></i></a></td>
                                                    <td class="product_thumb"><a href="detail_produk.php?id=<?= $id_produk; ?>"><img src="admin/images/produk/<?= $item['foto_produk']; ?>" alt=""></a></td>
                                                    <td class="product_name"><a href="detail_produk.php?id=<?= $id_produk; ?>"><?= $item['nama_produk']; ?></a></td>
                                                    <td class="product-price">Rp <?= number_format($item['harga']); ?></td>
                                                    <td class="product_quantity">
                                                        <input min="<?= $item['minimum_pembelian']; ?>" max="<?= $item['stok']; ?>" value="<?= $jumlah; ?>" type="number" name="jumlah[<?= $id_produk; ?>]">
                                                    </td>
                                                    <td class="product_total">Rp <?= number_format($sub_total); ?></td>
                                                </tr>
                                        <?php
                                            }
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="cart_submit">
                                <button type="submit" name="update_keranjang">Update Keranjang</button>
                                <a href="keranjang.php?aksi=kosongkan" class="btn btn-outline-danger">Kosongkan Keranjang</a>
                            </div>
                        </div>
                </div>
            </div>
            <div class="coupon_area">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="coupon_code right">
                            <h3>Total Keranjang</h3>
                            <div class="coupon_inner">
                                <div class="cart_subtotal">
                                    <p>Subtotal</p>
                                    <p class="cart_amount">Rp <?= number_format($grand_total); ?></p>
                                </div>
                                <div class="cart_subtotal ">
                                    <p>Pengiriman</p>
                                    <p class="cart_amount"><span>Akan dihitung saat checkout</span></p>
                                </div>

                                <div class="cart_subtotal">
                                    <p>Biaya Admin</p>
                                    <p class="cart_amount">Rp <?= number_format($biaya_admin); ?></p>
                                </div>
                                <div class="cart_subtotal">
                                    <p>Total</p>
                                    <p class="cart_amount"><strong>Rp <?= number_format($grand_total + $biaya_admin); ?></strong></p>
                                </div>

                                <div class="checkout_btn">
                                    <a href="checkout.php">Lanjutkan ke Checkout</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; // <-- INI ADALAH PENUTUP YANG HILANG 
        ?>
        </form>
    </div>
</div>

<?php
include 'footer.php';
?>