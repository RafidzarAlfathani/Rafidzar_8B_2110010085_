<?php
include "inc/koneksi.php";
session_start();

if (!isset($_SESSION['user_level']) || !in_array($_SESSION['user_level'], ['Admin', 'Pimpinan'])) {
    echo "<script>alert('Akses ditolak!'); window.location='index.php';</script>";
    exit;
}

$data = [];

// Ambil pengajuan dari petani dengan perhitungan saldo
$q1 = mysqli_query($con, "
    SELECT
        pd.*,
        p.nama_petani AS nama,
        'Petani' AS role,
        (SELECT SUM(dp.sub_total)
         FROM detail_pesanan dp
         JOIN produk pr ON dp.id_produk = pr.id_produk
         JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
         WHERE pr.id_petani = pd.id_petani AND ps.status_pesanan = 'Selesai' AND ps.bukti_sampai IS NOT NULL) AS total_pendapatan_petani,
        (SELECT SUM(jumlah_dana)
         FROM pengajuan_dana_petani
         WHERE id_petani = pd.id_petani AND status = 'Disetujui') AS total_ditarik_petani
    FROM pengajuan_dana_petani pd
    JOIN petani p ON pd.id_petani = p.id_petani
    WHERE pd.status = 'Menunggu'
");

while ($d = mysqli_fetch_assoc($q1)) {
    $d['sisa_saldo'] = ($d['total_pendapatan_petani'] ?? 0) - ($d['total_ditarik_petani'] ?? 0);
    if ($d['sisa_saldo'] < 0) $d['sisa_saldo'] = 0;
    $data[] = $d;
}

// Ambil pengajuan dari kurir dengan perhitungan saldo
$q2 = mysqli_query($con, "
    SELECT
        pk.*,
        k.nama_kurir AS nama,
        'Kurir' AS role,
        (SELECT SUM(ongkir)
         FROM pesanan
         WHERE id_kurir = pk.id_kurir AND status_pesanan = 'Selesai') AS total_pendapatan_kurir,
        (SELECT SUM(jumlah_dana)
         FROM pengajuan_dana_kurir
         WHERE id_kurir = pk.id_kurir AND status = 'Disetujui') AS total_ditarik_kurir
    FROM pengajuan_dana_kurir pk
    JOIN kurir k ON pk.id_kurir = k.id_kurir
    WHERE pk.status = 'Menunggu'
");

while ($d = mysqli_fetch_assoc($q2)) {
    $d['sisa_saldo'] = ($d['total_pendapatan_kurir'] ?? 0) - ($d['total_ditarik_kurir'] ?? 0);
    if ($d['sisa_saldo'] < 0) $d['sisa_saldo'] = 0;
    $data[] = $d;
}

// Urutkan berdasarkan tanggal pengajuan terbaru
usort($data, fn($a, $b) => strtotime($b['tanggal_pengajuan']) - strtotime($a['tanggal_pengajuan']));
?>
<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Verifikasi Pengajuan Penarikan Dana</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">
                        Daftar Pengajuan Dana
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dashed table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th class="text-center">No</th>
                                        <th>Nama</th>
                                        <th>Peran</th>
                                        <th>Jumlah Dana</th>
                                        <th>Sisa Saldo</th>
                                        <th>Metode</th>
                                        <th>Catatan</th>
                                        <th>Tanggal Pengajuan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (count($data) > 0): $no = 1; ?>
                                        <?php foreach ($data as $row): ?>
                                            <tr>
                                                <td class="text-center"><?= $no++; ?></td>
                                                <td><?= htmlspecialchars($row['nama']); ?></td>
                                                <td><?= $row['role']; ?></td>
                                                <td>Rp <?= number_format($row['jumlah_dana'], 0, ',', '.'); ?></td>
                                                <td>
                                                    Rp <?= number_format($row['sisa_saldo'], 0, ',', '.'); ?>
                                                    <?php if ($row['jumlah_dana'] > $row['sisa_saldo']): ?>
                                                        <span class="badge bg-danger">Saldo Tidak Cukup!</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($row['metode'] ?? '-'); ?></td>
                                                <td><?= htmlspecialchars($row['catatan']); ?></td>
                                                <td><?= date('d-m-Y', strtotime($row['tanggal_pengajuan'])); ?></td>
                                                <td class="text-center">
                                                    <?php $disable_approve = ($row['jumlah_dana'] > $row['sisa_saldo']); ?>
                                                    <a href="?page=penarikan_dana&aksi=proses&id=<?= $row['id_pengajuan']; ?>&tipe=<?= strtolower($row['role']); ?>"
                                                       class="btn btn-success btn-sm <?= $disable_approve ? 'disabled' : ''; ?>"
                                                       onclick="return <?= $disable_approve ? 'false' : 'confirmApprove()'; ?>">Setujui</a>
                                                    <a href="?page=penarikan_dana&aksi=tolak&id=<?= $row['id_pengajuan']; ?>&tipe=<?= strtolower($row['role']); ?>"
                                                       class="btn btn-danger btn-sm"
                                                       onclick="return confirmReject();">Tolak</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center">Tidak ada pengajuan dana yang menunggu verifikasi.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmApprove() {
    return confirm('Yakin ingin menyetujui pengajuan ini?');
}

function confirmReject() {
    return confirm('Yakin ingin menolak pengajuan ini?');
}
</script>
