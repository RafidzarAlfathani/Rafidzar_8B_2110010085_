<?php
include "../inc/koneksi.php";
session_start();

// Pastikan hanya Admin atau Pimpinan yang bisa akses
if (!isset($_SESSION['user_level']) || !in_array($_SESSION['user_level'], ['Admin', 'Pimpinan'])) {
    echo "<script>alert('Akses ditolak!'); window.location='../index.php';</script>";
    exit;
}

$id_pengajuan = isset($_GET['id']) ? intval($_GET['id']) : 0;
$tipe         = isset($_GET['tipe']) ? strtolower($_GET['tipe']) : '';

if (!$id_pengajuan || !in_array($tipe, ['petani', 'kurir'])) {
    echo "<script>alert('Parameter tidak valid.'); window.location='../index.php';</script>";
    exit;
}

// Tentukan nama tabel dan field ID berdasarkan tipe
if ($tipe === 'petani') {
    $tabel    = 'pengajuan_dana_petani';
    $id_field = 'id_pengajuan';
} elseif ($tipe === 'kurir') {
    $tabel    = 'pengajuan_dana_kurir';
    $id_field = 'id_pengajuan';
}

// Data verifikasi
$tanggal_verifikasi = date("Y-m-d H:i:s");

// Simpan ID admin sebagai verifikator
$verifikator_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
if ($verifikator_id <= 0) {
    echo "<script>alert('Data verifikator tidak valid.'); window.location='../index.php';</script>";
    exit;
}

// Lakukan update status ke "Disetujui"
$query = mysqli_query($con, "
    UPDATE $tabel 
    SET status = 'Disetujui', 
        tanggal_verifikasi = '$tanggal_verifikasi', 
        diverifikasi_oleh = '$verifikator_id'
    WHERE $id_field = '$id_pengajuan'
");

if ($query) {
    echo "<script>alert('Pengajuan berhasil disetujui.'); window.location.href='index.php?page=penarikan_dana&aksi=verifikasi';</script>";
} else {
    echo "<script>alert('Terjadi kesalahan saat memproses pengajuan.'); window.location.href='index.php?page=penarikan_dana&aksi=verifikasi';</script>";
}
?>
