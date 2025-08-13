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

    // --- START: Validasi Saldo untuk Petani di proses.php ---
    // Ambil detail pengajuan dan ID petani
    $q_pengajuan = mysqli_query($con, "SELECT id_petani, jumlah_dana FROM $tabel WHERE $id_field = '$id_pengajuan'");
    $detail_pengajuan = mysqli_fetch_assoc($q_pengajuan);

    if (!$detail_pengajuan) {
        echo "<script>alert('Pengajuan tidak ditemukan.'); window.location.href='index.php?page=penarikan_dana&aksi=verifikasi';</script>";
        exit;
    }

    $id_petani_pengajuan = $detail_pengajuan['id_petani'];
    $jumlah_dana_pengajuan = $detail_pengajuan['jumlah_dana'];

    // Hitung sisa saldo petani saat ini
    $q_pendapatan = mysqli_query($con, "
        SELECT SUM(dp.sub_total) AS total_pendapatan
        FROM detail_pesanan dp
        JOIN produk p ON dp.id_produk = p.id_produk
        JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
        WHERE p.id_petani = '$id_petani_pengajuan' AND ps.status_pesanan = 'Selesai'
    ");
    $pendapatan = mysqli_fetch_assoc($q_pendapatan)['total_pendapatan'] ?? 0;

    $q_ditarik = mysqli_query($con, "
        SELECT SUM(jumlah_dana) AS total_ditarik
        FROM pengajuan_dana_petani
        WHERE id_petani = '$id_petani_pengajuan' AND status = 'Disetujui'
    ");
    $ditarik = mysqli_fetch_assoc($q_ditarik)['total_ditarik'] ?? 0;

    $sisa_saldo_petani = $pendapatan - $ditarik;

    // Cek apakah jumlah pengajuan melebihi sisa saldo
    if ($jumlah_dana_pengajuan > $sisa_saldo_petani) {
        echo "<script>alert('Gagal menyetujui! Jumlah pengajuan (Rp " . number_format($jumlah_dana_pengajuan, 0, ',', '.') . ") melebihi sisa saldo petani (Rp " . number_format($sisa_saldo_petani, 0, ',', '.') . ").'); window.location.href='index.php?page=penarikan_dana&aksi=verifikasi';</script>";
        exit; // Hentikan proses jika saldo tidak cukup
    }
    // --- END: Validasi Saldo untuk Petani di proses.php ---

} elseif ($tipe === 'kurir') {
    $tabel    = 'pengajuan_dana_kurir';
    $id_field = 'id_pengajuan';
    // Tidak ada validasi saldo khusus untuk kurir di sini, karena fokus pada petani
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
    echo "<script>alert('Terjadi kesalahan saat memproses pengajuan: " . mysqli_error($con) . "'); window.location.href='index.php?page=penarikan_dana&aksi=verifikasi';</script>";
}
?>
