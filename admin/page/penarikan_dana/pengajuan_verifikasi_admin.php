<?php
include "inc/koneksi.php";
session_start();

if (!isset($_SESSION['user_level']) || !in_array($_SESSION['user_level'], ['Admin', 'Pimpinan'])) {
    echo "<script>alert('Akses ditolak!'); window.location='index.php';</script>";
    exit;
}

// Gabungkan pengajuan petani dan kurir
$data = [];

// Ambil pengajuan dari petani
$q1 = mysqli_query($con, "SELECT pd.*, p.nama_petani AS nama, 'Petani' AS role FROM pengajuan_dana_petani pd JOIN petani p ON pd.id_petani = p.id_petani WHERE pd.status = 'Menunggu'");
while ($d = mysqli_fetch_assoc($q1)) {
    $data[] = $d;
}

// Ambil pengajuan dari kurir
$q2 = mysqli_query($con, "SELECT pk.*, k.nama_kurir AS nama, 'Kurir' AS role FROM pengajuan_dana_kurir pk JOIN kurir k ON pk.id_kurir = k.id_kurir WHERE pk.status = 'Menunggu'");
while ($d = mysqli_fetch_assoc($q2)) {
    $data[] = $d;
}

// Urutkan berdasarkan tanggal pengajuan terbaru
usort($data, fn($a, $b) => strtotime($b['tanggal_pengajuan']) - strtotime($a['tanggal_pengajuan']));
?>

<div class="container mt-4">
    <h3 class="mb-4">Verifikasi Pengajuan Penarikan Dana</h3>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Peran</th>
                    <th>Jumlah Dana</th>
                    <th>Metode</th>
                    <th>Catatan</th>
                    <th>Tanggal Pengajuan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($data) > 0): $no = 1;
                    foreach ($data as $row): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nama']); ?></td>
                            <td><?= $row['role']; ?></td>
                            <td>Rp <?= number_format($row['jumlah_dana'], 0, ',', '.'); ?></td>
                            <td><?= htmlspecialchars($row['metode'] ?? '-'); ?></td>
                            <td><?= htmlspecialchars($row['catatan']); ?></td>
                            <td><?= date('d-m-Y', strtotime($row['tanggal_pengajuan'])); ?></td>
                            <td>
                                <a href="?page=penarikan_dana&aksi=proses&id=<?= $row['id_pengajuan']; ?>&tipe=<?= strtolower($row['role']); ?>" class="btn btn-success btn-sm">Setujui</a>
                                <a href="?page=penarikan_dana&aksi=tolak&id=<?= $row['id_pengajuan']; ?>&tipe=<?= strtolower($row['role']); ?>" class="btn btn-danger btn-sm">Tolak</a>
                            </td>
                        </tr>
                    <?php endforeach;
                else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Tidak ada pengajuan dana yang menunggu verifikasi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>