<?php
// Atur judul halaman sebelum memanggil header
$page_title = "Checkout";
include 'header.php';

// PENGAMAN HALAMAN
if (!isset($_SESSION['pembeli_id'])) {
    $_SESSION['pesan_notifikasi'] = "Anda harus login untuk melanjutkan ke proses checkout.";
    echo "<script>window.location.href='login.php';</script>";
    exit();
}
if (empty($_SESSION['keranjang'])) {
    echo "<script>alert('Keranjang belanja Anda kosong, tidak ada yang bisa di-checkout.'); window.location.href='index.php';</script>";
    exit();
}

$id_pembeli = $_SESSION['pembeli_id'];
$pembeli_info = $con->query("SELECT * FROM pembeli WHERE id_pembeli = '$id_pembeli'")->fetch_assoc();

$biaya_admin = 1500;

// BARU: Ambil semua metode pengiriman yang aktif dari database
$query_metode = $con->query("SELECT * FROM metode_pengiriman WHERE status = 'Aktif' ORDER BY biaya ASC");

// LOGIKA UTAMA: MEMPROSES PESANAN
if (isset($_POST['buat_pesanan'])) {
    // Ambil data dari form
    $alamat_pengiriman = mysqli_real_escape_string($con, $_POST['alamat_pengiriman']);
    $id_metode_pengiriman = (int)$_POST['id_metode_pengiriman']; // Ambil ID metode yang dipilih

    // BARU: Validasi dan ambil biaya pengiriman langsung dari DB, bukan dari form (lebih aman)
    $stmt = $con->prepare("SELECT biaya FROM metode_pengiriman WHERE id_metode = ? AND status = 'Aktif'");
    $stmt->bind_param("i", $id_metode_pengiriman);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        die("Metode pengiriman tidak valid."); // Hentikan jika metode tidak ada
    }
    $ongkir_data = $result->fetch_assoc();
    $ongkir = $ongkir_data['biaya'];
    $stmt->close();

    // Hitung ulang total belanja dari session
    $total_belanja = 0;
    $ids_produk = array_keys($_SESSION['keranjang']);
    $ids_string = implode(',', $ids_produk);
    $query_items = $con->query("SELECT * FROM produk WHERE id_produk IN ($ids_string)");

    $item_list_string_for_wa = "";
    while ($item = $query_items->fetch_assoc()) {
        $jumlah = $_SESSION['keranjang'][$item['id_produk']];
        $total_belanja += $item['harga'] * $jumlah;
        $item_list_string_for_wa .= "- " . $item['nama_produk'] . " (x" . $jumlah . ")\n";
    }

    $total_bayar = $total_belanja + $ongkir + $biaya_admin;
    $kode_invoice = "INV-" . date("Ymd-His") . "-" . $id_pembeli;
    $status_pesanan = "Menunggu Pembayaran";

    // Mulai Transaksi Database
    $con->begin_transaction();
    try {
        // 1. Simpan data ke tabel 'pesanan' (dengan id_metode_pengiriman)
        $query_pesanan = "INSERT INTO pesanan (kode_invoice, id_pembeli, alamat_pengiriman, total_bayar, ongkir, biaya_admin, status_pesanan, id_metode_pengiriman)
                          VALUES ('$kode_invoice', '$id_pembeli', '$alamat_pengiriman', '$total_bayar', '$ongkir', '$biaya_admin', '$status_pesanan', '$id_metode_pengiriman')";
        $con->query($query_pesanan);
        $id_pesanan_baru = $con->insert_id;

        // 2. Simpan detail dan kurangi stok
        $query_items->data_seek(0);
        while ($item = $query_items->fetch_assoc()) {
            $id_produk = $item['id_produk'];
            $jumlah = $_SESSION['keranjang'][$id_produk];
            $harga_saat_pesan = $item['harga'];
            $sub_total = $harga_saat_pesan * $jumlah;
            $con->query("INSERT INTO detail_pesanan (id_pesanan, id_produk, jumlah, harga_saat_pesan, sub_total) VALUES ('$id_pesanan_baru', '$id_produk', '$jumlah', '$harga_saat_pesan', '$sub_total')");
            $con->query("UPDATE produk SET stok = stok - $jumlah WHERE id_produk = '$id_produk'");
        }
        $con->commit();

        // Notifikasi WhatsApp (jika ada, tidak perlu diubah)
        // ... (kode notifikasi WA Anda) ...
                // -----------------------------------------------------
        // BAGIAN NOTIFIKASI WHATSAPP SETELAH TRANSAKSI SUKSES
        // -----------------------------------------------------
        $token = "7HY2322NXBXt4LKDVpkU"; // GANTI DENGAN TOKEN FONNTE ANDA
        $target_wa = '62' . substr($pembeli_info['telp'], 1);

        $pesan = "Halo, *" . htmlspecialchars($pembeli_info['nama_pembeli']) . "* 👋\n\n" .
            "Terima kasih telah berbelanja di *E-Tani Lokpaikat*!\n\n" .
            "Pesanan Anda telah berhasil kami terima dengan detail sebagai berikut:\n" .
            "✅ *Nomor Invoice:* " . $kode_invoice . "\n" .
            "🛍️ *Item yang Dipesan:*\n" . $item_list_string_for_wa . "\n" .
            "💰 *Total Pembayaran:* Rp " . number_format($total_bayar) . "\n\n" .
            "Metode pembayaran Transfer Bank, mohon segera lakukan pembayaran dan konfirmasi agar pesanan Anda dapat kami proses, dan jangan lupa untuk upload bukti transfer / bukti pembayarannya.\n\n" .
            "Terima kasih.";

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => array('target' => $target_wa, 'message' => $pesan, 'countryCode' => '62'),
            CURLOPT_HTTPHEADER => array("Authorization: $token"),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        // -----------------------------------------------------
        // AKHIR BAGIAN NOTIFIKASI WHATSAPP
        // -----------------------------------------------------

        unset($_SESSION['keranjang']);
        echo "<script>alert('Pesanan berhasil dibuat!'); window.location.href='order_sukses.php?invoice=$kode_invoice';</script>";
        exit();
    } catch (mysqli_sql_exception $exception) {
        $con->rollback();
        echo "<script>alert('Terjadi kesalahan saat membuat pesanan. Stok mungkin tidak mencukupi atau ada masalah server.'); window.location.href='keranjang.php';</script>";
        exit();
    }
}
?>

<div class="breadcrumbs_area">
    <div class="container"><div class="row"><div class="col-12"><div class="breadcrumb_content"><h3>Checkout</h3><ul><li><a href="index.php">Home</a></li><li>Checkout</li></ul></div></div></div></div>
</div>

<div class="checkout_page_bg">
    <div class="container">
        <div class="checkout_form">
            <form action="checkout.php" method="POST" id="checkout-form">
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <h3>Data Pengiriman</h3>
                        <div class="row">
                            <div class="col-12 mb-20"><label>Nama Penerima <span>*</span></label><input type="text" value="<?= htmlspecialchars($pembeli_info['nama_pembeli']); ?>" required></div>
                            <div class="col-12 mb-20"><label>Alamat Lengkap Pengiriman <span>*</span></label><textarea name="alamat_pengiriman" placeholder="Tuliskan alamat lengkap..." required style="height: 120px; width: 100%; border: 1px solid #ddd; padding: 10px;"></textarea></div>
                            <div class="col-12 mb-20"><label>Telepon Penerima (WhatsApp) <span>*</span></label><input type="text" value="<?= htmlspecialchars($pembeli_info['telp']); ?>" required></div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <h3>Ringkasan Pesanan Anda</h3>
                        <div class="order_table table-responsive">
                            <table>
                                <thead>
                                    <tr><th>Produk</th><th>Total</th></tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $subtotal_checkout = 0;
                                    $ids_produk_checkout = array_keys($_SESSION['keranjang']);
                                    if (!empty($ids_produk_checkout)) {
                                        $ids_string_checkout = implode(',', $ids_produk_checkout);
                                        $query_items_checkout = $con->query("SELECT * FROM produk WHERE id_produk IN ($ids_string_checkout)");
                                        while ($item_checkout = $query_items_checkout->fetch_assoc()) {
                                            $jumlah_checkout = $_SESSION['keranjang'][$item_checkout['id_produk']];
                                            $item_total = $item_checkout['harga'] * $jumlah_checkout;
                                            $subtotal_checkout += $item_total;
                                    ?>
                                            <tr><td> <?= $item_checkout['nama_produk'] ?> <strong> × <?= $jumlah_checkout ?></strong></td><td> Rp <?= number_format($item_checkout['harga']) ?></td></tr>
                                    <?php } } ?>
                                </tbody>
                                <tfoot>
                                    <tr><th>Subtotal</th><td>Rp <?= number_format($subtotal_checkout) ?></td></tr>
                                    <tr><th>Pengiriman</th><td id="ongkir-display">Pilih metode pengiriman</td></tr>
                                    <tr><th>Biaya Admin</th><td>Rp <?= number_format($biaya_admin) ?></td></tr>
                                    <tr class="order_total"><th>Total Pesanan</th><td><strong id="total-display">Rp <?= number_format($subtotal_checkout + $biaya_admin) ?></strong></td></tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="payment_method">
                            <div class="panel-default" style="margin-bottom: 20px;">
                                <h4 style="font-size: 16px; font-weight: 600; border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 15px;">Pilih Metode Pengiriman</h4>
                                <?php while ($metode = $query_metode->fetch_assoc()): ?>
                                <div style="margin-bottom: 15px;">
                                    <input id="shipping_<?= $metode['id_metode']; ?>" name="id_metode_pengiriman" type="radio" value="<?= $metode['id_metode']; ?>" data-biaya="<?= $metode['biaya']; ?>" required>
                                    <label for="shipping_<?= $metode['id_metode']; ?>" style="display: inline; margin-left: 5px;">
                                        <strong><?= $metode['nama_metode']; ?> - Rp <?= number_format($metode['biaya']); ?></strong>
                                    </label>
                                    <p style="font-size: 13px; color: #666; margin-left: 25px; margin-top: 5px;"><?= $metode['deskripsi']; ?></p>
                                </div>
                                <?php endwhile; ?>
                            </div>
                            <div class="order_button">
                                <button type="submit" name="buat_pesanan">Buat Pesanan</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const shippingOptions = document.querySelectorAll('input[name="id_metode_pengiriman"]');
    const ongkirDisplay = document.getElementById('ongkir-display');
    const totalDisplay = document.getElementById('total-display');

    const subtotal = <?= $subtotal_checkout; ?>;
    const biayaAdmin = <?= $biaya_admin; ?>;

    // Fungsi untuk memformat angka menjadi format Rupiah
    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(angka).replace('Rp', 'Rp ');
    }

    shippingOptions.forEach(function(radio) {
        radio.addEventListener('change', function() {
            if (this.checked) {
                const biayaOngkir = parseFloat(this.getAttribute('data-biaya'));
                const totalBayar = subtotal + biayaOngkir + biayaAdmin;

                ongkirDisplay.textContent = formatRupiah(biayaOngkir);
                totalDisplay.innerHTML = '<strong>' + formatRupiah(totalBayar) + '</strong>';
            }
        });
    });

    // Jika hanya ada satu opsi pengiriman, otomatis pilih
    if (shippingOptions.length === 1) {
        shippingOptions[0].checked = true;
        // Memicu event 'change' secara manual
        shippingOptions[0].dispatchEvent(new Event('change'));
    }
});
</script>


<?php
include 'footer.php';
?>