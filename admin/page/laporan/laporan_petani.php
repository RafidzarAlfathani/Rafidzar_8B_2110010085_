<?php
// --- LOGIKA PHP UNTUK MENGAMBIL DATA ---

// Ambil filter dari form jika ada
$status_filter = isset($_POST['status_akun']) ? $_POST['status_akun'] : '';
$tgl_mulai_filter = isset($_POST['tanggal_mulai']) && !empty($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : '';
$tgl_selesai_filter = isset($_POST['tanggal_selesai']) && !empty($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : '';

// Bangun query SQL dengan subquery untuk agregasi data
// Ini adalah cara yang efisien untuk menghitung statistik per petani
$sql_data = "SELECT
                pt.*,
                (SELECT COUNT(*) FROM produk pr WHERE pr.id_petani = pt.id_petani) AS jumlah_produk,

                (SELECT COUNT(DISTINCT dp.id_pesanan)
                 FROM detail_pesanan dp
                 JOIN produk pr ON dp.id_produk = pr.id_produk
                 JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
                 WHERE pr.id_petani = pt.id_petani " .
    ($tgl_mulai_filter && $tgl_selesai_filter ? "AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'" : "") .
    ") AS jumlah_pesanan,

                (SELECT SUM(dp.sub_total)
                 FROM detail_pesanan dp
                 JOIN produk pr ON dp.id_produk = pr.id_produk
                 JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
                 WHERE pr.id_petani = pt.id_petani AND ps.status_pesanan = 'Selesai' " .
    ($tgl_mulai_filter && $tgl_selesai_filter ? "AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'" : "") .
    ") AS jumlah_pendapatan

             FROM petani pt";

if ($status_filter) {
    $sql_data .= " WHERE pt.status_akun = '$status_filter'";
}

$sql_data .= " ORDER BY pt.nama_petani ASC";
$ambil_data = $con->query($sql_data);
?>
<?php
// Hitung total statistik keseluruhan
// Statistik utama
$sql_statistik = "SELECT
    COUNT(*) AS total_petani,
    (SELECT COUNT(*) FROM produk pr 
        JOIN petani p ON pr.id_petani = p.id_petani" .
    ($status_filter ? " WHERE p.status_akun = '$status_filter'" : "") .
    ") AS total_produk,
    (SELECT SUM(dp.sub_total) 
        FROM detail_pesanan dp 
        JOIN produk pr ON dp.id_produk = pr.id_produk
        JOIN petani p ON pr.id_petani = p.id_petani
        JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
        WHERE ps.status_pesanan = 'Selesai'" .
    ($status_filter ? " AND p.status_akun = '$status_filter'" : "") .
    ($tgl_mulai_filter && $tgl_selesai_filter ? " AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'" : "") .
    ") AS total_pendapatan 
FROM petani pt" .
    ($status_filter ? " WHERE pt.status_akun = '$status_filter'" : "");

$hasil_statistik = $con->query($sql_statistik);
$data_statistik = $hasil_statistik->fetch_assoc();
$total_petani = $data_statistik['total_petani'] ?? 0;
$total_produk = $data_statistik['total_produk'] ?? 0;
$total_pendapatan = $data_statistik['total_pendapatan'] ?? 0;

// Statistik tambahan: total petani aktif
$sql_aktif = "SELECT COUNT(*) AS total_aktif FROM petani WHERE status_akun = 'Aktif'";
$hasil_aktif = $con->query($sql_aktif);
$data_aktif = $hasil_aktif->fetch_assoc();
$total_petani_aktif = $data_aktif['total_aktif'] ?? 0;
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Laporan Kinerja Petani</h5>
            </div>
            <div class="panel-body">

                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Total Petani</h5>
                                <p class="card-text fs-4"><?= number_format($total_petani); ?> orang</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Petani Aktif</h5>
                                <p class="card-text fs-4"><?= number_format($total_petani_aktif); ?> orang</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5 class="card-title">Jumlah Produk</h5>
                                <p class="card-text fs-4"><?= number_format($total_produk); ?> produk</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5 class="card-title">Total Pendapatan</h5>
                                <p class="card-text fs-4">Rp <?= number_format($total_pendapatan); ?></p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan</h5>
                    </div>
                    <div class="card-body">
                        <form action="?page=laporan&aksi=laporan_petani" method="POST">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Berdasarkan Status Akun</label>
                                    <select name="status_akun" class="form-control">
                                        <option value="">-- Tampilkan Semua Status --</option>
                                        <option value="Aktif" <?= ($status_filter == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                                        <option value="Tidak Aktif" <?= ($status_filter == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Dari Tanggal (Pesanan)</label>
                                    <input type="date" name="tanggal_mulai" class="form-control" value="<?= $tgl_mulai_filter; ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Sampai Tanggal (Pesanan)</label>
                                    <input type="date" name="tanggal_selesai" class="form-control" value="<?= $tgl_selesai_filter; ?>">
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Tampilkan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fas fa-table me-2"></i>Hasil Laporan Kinerja Petani</h5>
                        <form action="page/laporan/cetak_petani_pdf.php" method="POST" target="_blank" class="m-0">
                            <input type="hidden" name="status_akun" value="<?= htmlspecialchars($status_filter); ?>">
                            <input type="hidden" name="tanggal_mulai" value="<?= htmlspecialchars($tgl_mulai_filter); ?>">
                            <input type="hidden" name="tanggal_selesai" value="<?= htmlspecialchars($tgl_selesai_filter); ?>">
                            <button type="submit" class="btn btn-success"><i class="fas fa-print me-1"></i> Cetak Laporan PDF</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th>Nama Petani</th>
                                        <th>Kontak</th>
                                        <th class="text-center">Status Akun</th>
                                        <th class="text-center">Jumlah Produk</th>
                                        <th class="text-center">Jumlah Pesanan</th>
                                        <th class="text-end">Total Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($ambil_data->num_rows > 0): $nomor = 1;
                                        while ($petani = $ambil_data->fetch_assoc()): ?>
                                            <tr>
                                                <td class="text-center"><?= $nomor++; ?></td>
                                                <td><?= htmlspecialchars($petani['nama_petani']); ?></td>
                                                <td>
                                                    <small>
                                                        Email: <?= htmlspecialchars($petani['email']); ?><br>
                                                        Telp: <?= htmlspecialchars($petani['telp']); ?>
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge <?= ($petani['status_akun'] == 'Aktif') ? 'bg-success' : 'bg-danger'; ?>">
                                                        <?= $petani['status_akun']; ?>
                                                    </span>
                                                </td>
                                                <td class="text-center"><?= number_format($petani['jumlah_produk']); ?></td>
                                                <td class="text-center"><?= number_format($petani['jumlah_pesanan']); ?></td>
                                                <td class="text-end">Rp <?= number_format($petani['jumlah_pendapatan'] ?? 0); ?></td>
                                            </tr>
                                        <?php endwhile;
                                    else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Data tidak ditemukan.</td>
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