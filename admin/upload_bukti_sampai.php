<?php
include "../inc/koneksi.php";
session_start();

// Cek apakah kurir sudah login
if (!isset($_SESSION['id_kurir'])) {
    header("Location: login_kurir.php");
    exit;
}

$id_pesanan = $_GET['id'] ?? '';
$id_kurir = $_SESSION['id_kurir'];

// Ambil data pesanan
$query = mysqli_query($koneksi, "SELECT * FROM tb_pesanan WHERE id_pesanan='$id_pesanan' AND id_kurir='$id_kurir'");
$data = mysqli_fetch_assoc($query);

// Validasi hak akses & status pesanan
if (!$data || $data['status'] != 'Selesai' || !empty($data['bukti_sampai'])) {
    echo "<script>alert('Tidak bisa mengakses halaman ini.'); window.location.href='?page=pesanan';</script>";
    exit;
}

// Jika form disubmit
if (isset($_POST['upload'])) {
    $file = $_FILES['bukti'];

    if ($file['error'] == 0) {
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $namaFile = 'bukti_' . time() . '.' . $ext;
        $targetDir = '../images/bukti_sampai/';
        $targetFile = $targetDir . $namaFile;

        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            $update = mysqli_query($koneksi, "UPDATE tb_pesanan SET bukti_sampai='$namaFile' WHERE id_pesanan='$id_pesanan'");
            if ($update) {
                echo "<script>alert('Bukti berhasil diunggah.'); window.location.href='?page=pesanan&aksi=detail&id_pesanan=$id_pesanan';</script>";
            } else {
                echo "<script>alert('Gagal memperbarui data.');</script>";
            }
        } else {
            echo "<script>alert('Gagal mengunggah file.');</script>";
        }
    } else {
        echo "<script>alert('Terjadi kesalahan saat memilih file.');</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Upload Bukti Sampai</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h4>Upload Bukti Produk Telah Sampai</h4>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="bukti" class="form-label">Pilih Foto (jpg, jpeg, png)</label>
            <input type="file" name="bukti" class="form-control" accept="image/*" required>
        </div>
        <button type="submit" name="upload" class="btn btn-primary">Upload</button>
        <a href="?page=pesanan&aksi=detail&id_pesanan=<?= $id_pesanan; ?>" class="btn btn-secondary">Kembali</a>
    </form>
</div>
</body>
</html>
