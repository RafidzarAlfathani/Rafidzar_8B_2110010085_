<?php
// Validasi sesi dan koneksi
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'Kurir') {
    echo "<script>alert('Akses ditolak! Hanya kurir yang dapat mengakses halaman ini.'); window.location.href='?page=pesanan';</script>";
    exit();
}

if (!isset($_GET['id_pesanan']) || empty($_GET['id_pesanan'])) {
    echo "<script>alert('ID pesanan tidak valid.'); window.location.href='?page=pesanan';</script>";
    exit();
}

include '../../inc/koneksi.php';

$id_pesanan = mysqli_real_escape_string($con, $_GET['id_pesanan']);
$id_kurir = $_SESSION['user_id']; // diasumsikan ini adalah ID kurir yang login

// Pastikan pesanan memang milik kurir yang login
$cek = $con->query("SELECT * FROM pesanan WHERE id_pesanan = '$id_pesanan' AND id_kurir = '$id_kurir'");
if ($cek->num_rows == 0) {
    echo "<script>alert('Akses ditolak! Anda bukan kurir untuk pesanan ini.'); window.location.href='?page=pesanan';</script>";
    exit();
}

$pesanan = $cek->fetch_assoc();
$error_upload = "";

// Proses upload
if (isset($_POST['upload'])) {
    if (isset($_FILES['bukti_sampai']) && $_FILES['bukti_sampai']['error'] == 0) {
        $file = $_FILES['bukti_sampai'];
        $nama_file = $file['name'];
        $tmp_file = $file['tmp_name'];
        $ukuran = $file['size'];
        $ekstensi = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));

        $ekstensi_diizinkan = ['jpg', 'jpeg', 'png'];
        if (!in_array($ekstensi, $ekstensi_diizinkan)) {
            $error_upload = "Tipe file tidak valid. Harap upload JPG, JPEG, atau PNG.";
        } elseif ($ukuran > 2000000) {
            $error_upload = "Ukuran file terlalu besar. Maksimal 2MB.";
        } else {
            $nama_baru = 'bukti_sampai_' . $id_pesanan . '_' . time() . '.' . $ekstensi;
            $folder = 'images/bukti_sampai/';
            if (!file_exists($folder)) {
                mkdir($folder, 0777, true);
            }
            $tujuan = $folder . $nama_baru;

            if (move_uploaded_file($tmp_file, $tujuan)) {
                $update = $con->query("UPDATE pesanan SET bukti_sampai = '$nama_baru', status_pesanan = 'Selesai' WHERE id_pesanan = '$id_pesanan'");
                if ($update) {
                    echo "<script>alert('Bukti berhasil diupload!'); window.location.href='?page=pesanan&aksi=detail&id_pesanan=$id_pesanan';</script>";
                    exit();
                } else {
                    $error_upload = "Gagal memperbarui database.";
                }
            } else {
                $error_upload = "Gagal memindahkan file.";
            }
        }
    } else {
        $error_upload = "File belum dipilih atau terjadi kesalahan saat upload.";
    }
}
?>

<div class="card">
    <div class="card-header">
        <h4>Upload Bukti Barang Telah Sampai</h4>
    </div>
    <div class="card-body">
        <form method="POST" enctype="multipart/form-data">
            <?php if (!empty($error_upload)): ?>
                <div class="alert alert-danger"><?= $error_upload; ?></div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="bukti_sampai" class="form-label">Upload Foto Bukti</label>
                <input type="file" class="form-control" name="bukti_sampai" id="bukti_sampai" required accept="image/*">
                <small class="form-text text-muted">Format: JPG, PNG. Maksimal 2MB.</small>
            </div>
            <button type="submit" name="upload" class="btn btn-primary">Upload</button>
            <a href="?page=pesanan&aksi=detail&id_pesanan=<?= $id_pesanan; ?>" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</div>
