<?php
include "inc/koneksi.php";
require_once("inc/tanggal.php");
session_start();

// Cek apakah user adalah petani
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'Petani') {
    echo "<script>alert('Akses ditolak! Halaman khusus petani.'); window.location='index.php';</script>";
    exit;
}

$id_petani = $_SESSION['user_id'];

?>

<div class="panel">
    <div class="panel-header d-flex justify-content-between align-items-center">
        <h4>Riwayat Penarikan Dana</h4>
        <a href="?page=penarikan_dana&aksi=petani_pengajuan" class="btn btn-primary">+ Pengajuan Baru</a>
    </div>

    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Jumlah Dana</th>
                        <th>Metode</th>
                        <th>Status</th>
                        <th>Catatan Petani</th>
                        <th>Tanggal Penarikan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($con, "
    SELECT r.*, p.nama_petani, a.catatan 
    FROM riwayat_penarikan_dana r
    JOIN petani p ON r.id_petani = p.id_petani
    JOIN pengajuan_dana_petani a ON r.id_pengajuan = a.id_pengajuan
    WHERE r.id_petani = '$id_petani'
    ORDER BY r.tanggal_penarikan DESC
");
                    ?>

                    <?php
                    if (mysqli_num_rows($query) == 0) {
                    ?>
                        <tr>
                            <td colspan="6" class="text-center">Belum ada riwayat penarikan dana.</td>
                        </tr>
                    <?php
                    } else {
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query)) {
                    ?>
                            <tr>
                                <td><?= $no ?></td>
                                <td>Rp <?= number_format($row['jumlah_dana'], 0, ',', '.') ?></td>
                                <td><?= $row['metode_penarikan'] ?></td>
                                <td><?= $row['status_penarikan'] ?></td>
                                <td><?= htmlspecialchars($row['catatan']) ?></td>
                                <td><?= date('d-m-Y', strtotime($row['tanggal_penarikan'])) ?></td>
                            </tr>
                    <?php
                            $no++;
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>