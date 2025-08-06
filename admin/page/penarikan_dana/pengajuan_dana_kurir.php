<?php
include "inc/koneksi.php";
session_start();

// Cek apakah user login sebagai Kurir
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'Kurir') {
    echo "<script>alert('Akses ditolak! Halaman khusus kurir.'); window.location='index.php';</script>";
    exit;
}

$id_kurir = $_SESSION['user_id']; // ID kurir dari session login
$nama_kurir = $_SESSION['user_nama'] ?? 'Kurir'; // fallback jika tidak ada nama

// Hitung total pendapatan kurir
$query_pendapatan = mysqli_query($con, "
    SELECT SUM(ongkir) AS total_pendapatan 
    FROM pesanan 
    WHERE id_kurir = '$id_kurir' AND status_pesanan = 'Selesai'
");
$total_pendapatan = ($row = mysqli_fetch_assoc($query_pendapatan)) ? $row['total_pendapatan'] : 0;

// Hitung total penarikan disetujui
$query_tarik = mysqli_query($con, "
    SELECT SUM(jumlah_dana) AS total_tarik 
    FROM pengajuan_dana_kurir 
    WHERE id_kurir = '$id_kurir' AND status = 'Disetujui'
");
$total_tarik = ($row = mysqli_fetch_assoc($query_tarik)) ? $row['total_tarik'] : 0;

// Hitung sisa saldo
$sisa_saldo = (int)$total_pendapatan - (int)$total_tarik;
if ($sisa_saldo < 0) $sisa_saldo = 0;

$pesan = '';

// Proses pengajuan
if (isset($_POST['ajukan'])) {
    $jumlah_dana = (int)$_POST['jumlah_dana'];
    $metode = htmlspecialchars($_POST['metode']);
    $catatan = htmlspecialchars($_POST['catatan']);

    if ($jumlah_dana > 0 && $jumlah_dana <= $sisa_saldo) {
        $query = mysqli_query($con, "INSERT INTO pengajuan_dana_kurir 
            (id_kurir, jumlah_dana, metode, catatan, status, tanggal_pengajuan) 
            VALUES 
            ('$id_kurir', '$jumlah_dana', '$metode', '$catatan', 'Menunggu', NOW())");

        if ($query) {
            $pesan = "<div class='alert alert-success'>Pengajuan dana berhasil dikirim.</div>";
            // Perbarui saldo terbaru
            $sisa_saldo -= $jumlah_dana;
        } else {
            $pesan = "<div class='alert alert-danger'>Terjadi kesalahan saat mengajukan dana.</div>";
        }
    } else {
        $pesan = "<div class='alert alert-warning'>Jumlah dana tidak valid atau melebihi sisa saldo.</div>";
    }
}
?>

<div class="container mt-4">
    <h3>Pengajuan Penarikan Dana Kurir</h3>
    <?= $pesan; ?>

    <div class="mb-3">
        <strong>Nama Kurir:</strong> <?= htmlspecialchars($nama_kurir); ?><br>
        <strong>Sisa Saldo Anda (Rp):</strong> Rp <?= number_format($sisa_saldo, 0, ',', '.'); ?>
    </div>

    <form method="POST">
        <div class="mb-3">
            <label for="jumlah_dana" class="form-label">Jumlah Dana (Rp)</label>
            <input type="number" name="jumlah_dana" id="jumlah_dana" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="metode" class="form-label">Metode Penarikan</label>
            <select name="metode" id="metode" class="form-control" required>
                <option value="">-- Pilih Metode --</option>
                <option value="Transfer">Transfer</option>
                <option value="Cash di Balai">Cash di Balai</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="catatan" class="form-label">Catatan (Opsional)</label>
            <textarea name="catatan" id="catatan" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" name="ajukan" class="btn btn-primary">Ajukan Dana</button>
        <a href="penarikan_dana_kurir.php" class="btn btn-secondary">Riwayat Penarikan</a>
    </form>
</div>