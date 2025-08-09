<?php
include "inc/koneksi.php";
require_once("inc/tanggal.php");
session_start();

// Cek apakah user adalah kurir
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] !== 'Kurir') {
    echo "<script>alert('Akses ditolak! Halaman khusus kurir.'); window.location='index.php';</script>";
    exit;
}

$id_kurir = $_SESSION['user_id'];

?>

<div class="panel">
    <div class="panel-header d-flex justify-content-between align-items-center">
        <h4>Riwayat Penarikan Dana Kurir</h4>
        <a href="?page=penarikan_dana&aksi=kurir_pengajuan" class="btn btn-primary">+ Pengajuan Baru</a>
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
                        <th>Catatan</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Tanggal Verifikasi</th>
                        <th>Diverifikasi Oleh</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $query = mysqli_query($con, "
SELECT p.*, a.username AS nama_verifikator
FROM pengajuan_dana_kurir p
LEFT JOIN admin a 
    ON p.diverifikasi_oleh = a.id_admin
WHERE p.id_kurir = '$id_kurir'
ORDER BY p.tanggal_pengajuan DESC

                    ");

                    if (mysqli_num_rows($query) == 0) {
                        echo '<tr><td colspan="8" class="text-center">Belum ada pengajuan dana.</td></tr>';
                    } else {
                        $no = 1;
                        while ($row = mysqli_fetch_assoc($query)) {
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td>Rp <?= number_format($row['jumlah_dana'], 0, ',', '.'); ?></td>
                            <td><?= htmlspecialchars($row['metode']); ?></td>
                            <td>
                                <?php
                                if ($row['status'] == 'Menunggu') {
                                    echo '<span class="badge bg-warning text-dark">Menunggu</span>';
                                } elseif ($row['status'] == 'Disetujui') {
                                    echo '<span class="badge bg-success">Disetujui</span>';
                                } elseif ($row['status'] == 'Ditolak') {
                                    echo '<span class="badge bg-danger">Ditolak</span>';
                                } else {
                                    echo htmlspecialchars($row['status']);
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($row['catatan'] ?? '-') ?></td>
                            <td><?= date('d-m-Y', strtotime($row['tanggal_pengajuan'])) ?></td>
                            <td><?= $row['tanggal_verifikasi'] ? date('d-m-Y H:i', strtotime($row['tanggal_verifikasi'])) : '-'; ?></td>
                            <td><?= $row['nama_verifikator'] ?: '-' ?></td>

                        </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
