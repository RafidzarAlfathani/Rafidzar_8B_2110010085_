<?php
$page_title = "Konfirmasi Pembayaran";
include 'header.php';

// PENGAMAN HALAMAN
if (!isset($_SESSION['pembeli_id'])) {
    $_SESSION['pesan_notifikasi'] = "Anda harus login untuk melakukan konfirmasi.";
    echo "<script>window.location.href='login.php';</script>";
    exit();
}
if (!isset($_GET['invoice']) || empty($_GET['invoice'])) {
    echo "<script>window.location.href='akun.php';</script>";
    exit();
}

$id_pembeli = $_SESSION['pembeli_id'];
$kode_invoice = mysqli_real_escape_string($con, $_GET['invoice']);

// Query untuk memastikan invoice ini milik pembeli yang login & statusnya 'Menunggu Pembayaran'
$sql = $con->query("SELECT * FROM pesanan WHERE id_pembeli = '$id_pembeli' AND kode_invoice = '$kode_invoice' AND status_pesanan = 'Menunggu Pembayaran'");
if ($sql->num_rows == 0) {
    echo "<script>alert('Pesanan tidak ditemukan atau tidak dapat dikonfirmasi.'); window.location.href='akun.php';</script>";
    exit();
}
$pesanan = $sql->fetch_assoc();


// LOGIKA PROSES UPLOAD BUKTI BAYAR
if (isset($_POST['kirim_konfirmasi'])) {
    $error_upload = "";
    // Cek apakah file sudah dipilih
    if (isset($_FILES['bukti_bayar']) && $_FILES['bukti_bayar']['error'] == 0) {
        $file = $_FILES['bukti_bayar'];
        $nama_file = $file['name'];
        $lokasi_tmp = $file['tmp_name'];
        $ukuran_file = $file['size'];
        $tipe_file = $file['type'];
        $ekstensi_file = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        
        // Tentukan ekstensi yang diizinkan
        $ekstensi_diizinkan = ['jpg', 'jpeg', 'png'];
        
        // Validasi ekstensi
        if (in_array($ekstensi_file, $ekstensi_diizinkan)) {
            // Validasi ukuran file (misal: maks 2MB)
            if ($ukuran_file <= 2000000) {
                // Buat nama file baru yang unik
                $nama_file_baru = $kode_invoice . '_' . time() . '.' . $ekstensi_file;
                $tujuan_upload = "admin/images/bukti_bayar/" . $nama_file_baru;

                // Pindahkan file yang diupload
                if (move_uploaded_file($lokasi_tmp, $tujuan_upload)) {
                    // Jika upload berhasil, update database
                    $query_update = "UPDATE pesanan SET 
                                        bukti_bayar = '$nama_file_baru', 
                                        status_pesanan = 'Menunggu Verifikasi', 
                                        tgl_bayar = NOW() 
                                     WHERE kode_invoice = '$kode_invoice'";
                    
                    if ($con->query($query_update) === TRUE) {
                        echo "<script>alert('Terima kasih. Konfirmasi pembayaran Anda telah kami terima dan akan segera kami verifikasi.'); window.location.href='detail_pesanan_pembeli.php?invoice=$kode_invoice';</script>";
                        exit();
                    } else {
                        $error_upload = "Gagal memperbarui database.";
                    }
                } else {
                    $error_upload = "Gagal memindahkan file yang diupload.";
                }
            } else {
                $error_upload = "Ukuran file terlalu besar. Maksimal 2MB.";
            }
        } else {
            $error_upload = "Tipe file tidak diizinkan. Harap upload file JPG, JPEG, atau PNG.";
        }
    } else {
        $error_upload = "Anda belum memilih file untuk diupload.";
    }
}

?>

    <div class="breadcrumbs_area">
        <div class="container">   
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <h3>Konfirmasi Pembayaran</h3>
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            <li><a href="akun.php">Akun Saya</a></li>
                            <li>Konfirmasi</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>         
    </div>

    <div class="customer_login mt-60 mb-60">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-8 offset-lg-3 offset-md-2">
                    <div class="account_form">
                        <h2>Upload Bukti Pembayaran</h2>
                        <p>Anda akan melakukan konfirmasi untuk Invoice <strong>#<?= $pesanan['kode_invoice']; ?></strong></p>
                        <p>Total yang harus dibayar: <strong>Rp <?= number_format($pesanan['total_bayar']); ?></strong></p>
                        <hr>
                        
                        <form method="POST" enctype="multipart/form-data">
                            <?php if (!empty($error_upload)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= $error_upload; ?>
                                </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label for="bukti_bayar" class="form-label">Pilih File Bukti Transfer <span>*</span></label>
                                <input type="file" class="form-control" name="bukti_bayar" id="bukti_bayar" required>
                                <small class="form-text text-muted">Tipe file yang diizinkan: JPG, PNG, JPEG. Ukuran maksimal: 2MB.</small>
                            </div>
                            <div class="login_submit">
                                <button type="submit" name="kirim_konfirmasi">Kirim Konfirmasi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>    
    </div>

<?php
include 'footer.php';
?>