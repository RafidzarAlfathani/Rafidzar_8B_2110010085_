<?php
include "inc/koneksi.php";
require_once("inc/tanggal.php");
session_start();

// Cek apakah user login dan levelnya Petani
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'Petani') {
    echo "<script>alert('Akses ditolak! Halaman khusus petani.'); window.location='index.php';</script>";
    exit;
}

$id_petani = $_SESSION['user_id'];
$nama_petani = $_SESSION['user_nama'] ?? 'Petani';

// Hitung total pendapatan dari penjualan produk (pesanan selesai)
$q_pendapatan = mysqli_query($con, "
    SELECT SUM(dp.sub_total) AS total_pendapatan
    FROM detail_pesanan dp
    JOIN produk p ON dp.id_produk = p.id_produk
    JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
    WHERE p.id_petani = '$id_petani' AND ps.status_pesanan = 'Selesai'
");
$pendapatan = mysqli_fetch_assoc($q_pendapatan)['total_pendapatan'] ?? 0;

// Hitung total dana yang sudah ditarik
$q_ditarik = mysqli_query($con, "
    SELECT SUM(jumlah_dana) AS total_ditarik
    FROM pengajuan_dana_petani
    WHERE id_petani = '$id_petani' AND status = 'Disetujui'
");
$ditarik = mysqli_fetch_assoc($q_ditarik)['total_ditarik'] ?? 0;

// Hitung sisa saldo
$sisa_saldo = $pendapatan - $ditarik;
if ($sisa_saldo < 0) $sisa_saldo = 0;

// Proses form
if (isset($_POST['submit'])) {
    $jumlah_dana = (int) $_POST['jumlah_dana'];
    $metode = mysqli_real_escape_string($con, $_POST['metode']);
    $catatan = mysqli_real_escape_string($con, $_POST['catatan']);
    $tanggal_pengajuan = date("Y-m-d");

    if ($jumlah_dana <= 0) {
        echo "<script>alert('Jumlah dana tidak valid!');</script>";
    } elseif ($jumlah_dana > $sisa_saldo) {
        echo "<script>alert('Jumlah dana melebihi sisa saldo!');</script>";
    } else {
        $query = mysqli_query($con, "INSERT INTO pengajuan_dana_petani 
            (id_petani, jumlah_dana, metode, catatan, status, tanggal_pengajuan) 
            VALUES 
            ('$id_petani', '$jumlah_dana', '$metode', '$catatan', 'Menunggu', '$tanggal_pengajuan')
        ");

        if ($query) {
            echo "<script>
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Pengajuan berhasil diajukan.' })
                .then(() => { window.location='?page=penarikan_dana'; });
            </script>";
        } else {
            echo "<script>
                Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat menyimpan pengajuan.' });
            </script>";
        }
    }
}
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Pengajuan Penarikan Dana</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">Form Pengajuan</div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Nama Petani</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" value="<?= htmlspecialchars($nama_petani); ?>" readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Sisa Saldo (Rp)</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" value="<?= number_format($sisa_saldo, 0, ',', '.') ?>" readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Tanggal Pengajuan</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" value="<?= tgl_indo(date('Y-m-d')) ?>" readonly>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Jumlah Dana Ditarik(Rp)</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" name="jumlah_dana" required>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Metode Penarikan</label>
                                <div class="col-sm-9">
                                    <select name="metode" class="form-control" required>
                                        <option value="">-- Pilih Metode --</option>
                                        <option value="Cash di Balai">Cash di Balai</option>
                                        <!-- <option value="Transfer">Transfer</option> -->
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Catatan / Keterangan</label>
                                <div class="col-sm-9">
                                    <textarea name="catatan" class="form-control" rows="3" placeholder="Contoh: Dana untuk beli bibit..." required></textarea>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-9">
                                    <button type="submit" name="submit" class="btn btn-primary btn-sm">Ajukan Penarikan</button>
                                    <a href="index.php" class="btn btn-danger btn-sm">Kembali</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
