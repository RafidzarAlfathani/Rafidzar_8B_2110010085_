<?php
include "../inc/koneksi.php";
session_start();

// Cek hak akses: hanya Admin atau Pimpinan
if (!isset($_SESSION['user_level']) || !in_array($_SESSION['user_level'], ['Admin', 'Pimpinan'])) {
    echo "<script>alert('Akses ditolak!'); window.location='../index.php';</script>";
    exit;
}

// Ambil parameter ID dan tipe (petani / kurir)
$id_pengajuan = isset($_GET['id']) ? intval($_GET['id']) : 0;
$tipe = isset($_GET['tipe']) ? strtolower($_GET['tipe']) : '';

if (!$id_pengajuan || !in_array($tipe, ['petani', 'kurir'])) {
    echo "<script>alert('Parameter tidak valid.'); window.location='../index.php';</script>";
    exit;
}

// Tentukan nama tabel dan field ID berdasarkan tipe
if ($tipe === 'petani') {
    $tabel = 'pengajuan_dana_petani';
    $id_field = 'id_pengajuan';
} elseif ($tipe === 'kurir') {
    $tabel = 'pengajuan_dana_kurir';
    $id_field = 'id_pengajuan';
} else {
    echo "<script>alert('Tipe tidak dikenali.'); window.location='../index.php';</script>";
    exit;
}

// Data verifikasi
$tanggal_verifikasi = date("Y-m-d H:i:s");
$id_admin = $_SESSION['user_id']; // Simpan ID admin/pimpinan yang login

// Update status menjadi "Ditolak"
$query = mysqli_query($con, "UPDATE $tabel 
    SET status = 'Ditolak', 
        tanggal_verifikasi = '$tanggal_verifikasi', 
        diverifikasi_oleh = '$id_admin'
    WHERE $id_field = '$id_pengajuan'
");

if ($query) {
    echo "<script>
        alert('Pengajuan berhasil ditolak.');
        window.location.href='index.php?page=penarikan_dana&aksi=verifikasi';
    </script>";
} else {
    echo "<script>
        alert('Terjadi kesalahan saat memproses penolakan.');
        window.location.href='index.php?page=penarikan_dana&aksi=verifikasi';
    </script>";
}
?>
