<?php
// --- LOGIKA PHP UNTUK MENGAMBIL DATA ---

// Ambil filter dari form jika ada
$tgl_mulai_filter = isset($_POST['tanggal_mulai']) && !empty($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : '';
$tgl_selesai_filter = isset($_POST['tanggal_selesai']) && !empty($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : '';

// Bangun query SQL dengan subquery untuk agregasi data
$sql_data = "SELECT 
                pm.*,
                (SELECT COUNT(id_pesanan) 
                 FROM pesanan ps 
                 WHERE ps.id_pembeli = pm.id_pembeli " .
                 ($tgl_mulai_filter && $tgl_selesai_filter ? "AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'" : "") .
                ") AS total_transaksi,

                (SELECT SUM(total_bayar) 
                 FROM pesanan ps 
                 WHERE ps.id_pembeli = pm.id_pembeli 
                   AND ps.status_pesanan NOT IN ('Dibatalkan', 'Menunggu Pembayaran') " .
                 ($tgl_mulai_filter && $tgl_selesai_filter ? "AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'" : "") .
                ") AS total_pembayaran
             FROM pembeli pm
             ORDER BY pm.nama_pembeli ASC";

$ambil_data = $con->query($sql_data);
?>
<?php
// --- LOGIKA STATISTIK UTAMA ---
// Query total pembeli
$sql_total_pembeli = "SELECT COUNT(*) AS total_pembeli FROM pembeli";
$hasil_total_pembeli = $con->query($sql_total_pembeli)->fetch_assoc()['total_pembeli'] ?? 0;

// Query total transaksi
$sql_total_transaksi = "SELECT COUNT(*) AS total_transaksi FROM pesanan WHERE 1";
$sql_total_pembayaran = "SELECT SUM(total_bayar) AS total_pembayaran FROM pesanan WHERE status_pesanan NOT IN ('Dibatalkan', 'Menunggu Pembayaran')";

if ($tgl_mulai_filter && $tgl_selesai_filter) {
    $sql_total_transaksi .= " AND tgl_pesan BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'";
    $sql_total_pembayaran .= " AND tgl_pesan BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'";
}

$hasil_total_transaksi = $con->query($sql_total_transaksi)->fetch_assoc()['total_transaksi'] ?? 0;
$hasil_total_pembayaran = $con->query($sql_total_pembayaran)->fetch_assoc()['total_pembayaran'] ?? 0;

// Query 5 pembeli dengan jumlah transaksi terbanyak
$sql_top_pembeli = "SELECT p.id_pembeli, pb.nama_pembeli, COUNT(p.id_pesanan) AS jumlah_transaksi
                    FROM pesanan p
                    JOIN pembeli pb ON p.id_pembeli = pb.id_pembeli
                    WHERE p.status_pesanan NOT IN ('Dibatalkan', 'Menunggu Pembayaran')";

// Tambahkan filter tanggal jika digunakan
if (!empty($tgl_mulai_filter) && !empty($tgl_selesai_filter)) {
    $sql_top_pembeli .= " AND p.tgl_pesan BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'";
}

$sql_top_pembeli .= " GROUP BY p.id_pembeli
                      ORDER BY jumlah_transaksi DESC
                      LIMIT 5";

$query_top_pembeli = mysqli_query($konekt, $sql_top_pembeli);

?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Laporan Data Pembeli</h5>
            </div>
            <div class="panel-body">

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h6 class="card-title">Total Pembeli</h6>
                <h4 class="mb-0"><?= number_format($hasil_total_pembeli); ?> Orang</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h6 class="card-title">Total Transaksi</h6>
                <h4 class="mb-0"><?= number_format($hasil_total_transaksi); ?> Transaksi</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h6 class="card-title">Total Pembayaran</h6>
                <h4 class="mb-0">Rp <?= number_format($hasil_total_pembayaran); ?></h4>
            </div>
        </div>
    </div>
</div>
                <div class="card mb-3">
                    <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan</h5></div>
                    <div class="card-body">
                        <form action="?page=laporan&aksi=laporan_pembeli" method="POST">
                            <div class="row align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label">Dari Tanggal (Pesanan)</label>
                                    <input type="date" name="tanggal_mulai" class="form-control" value="<?= $tgl_mulai_filter; ?>">
                                </div>
                                <div class="col-md-5">
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
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Top 5 Pembeli dengan Transaksi Terbanyak</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Nama Pembeli</th>
                        <th>Jumlah Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    while ($top = mysqli_fetch_assoc($query_top_pembeli)) {
                        echo "<tr>
                                <td>{$no}</td>
                                <td>{$top['nama_pembeli']}</td>
                                <td>{$top['jumlah_transaksi']}</td>
                              </tr>";
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fas fa-table me-2"></i>Hasil Laporan Data Pembeli</h5>
                        <form action="page/laporan/cetak_pembeli_pdf.php" method="POST" target="_blank" class="m-0">
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
                                        <th>Nama Pembeli</th>
                                        <th>Kontak</th> 
                                        <th class="text-center">Total Transaksi</th>
                                        <th class="text-end">Total Pembayaran</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($ambil_data->num_rows > 0): $nomor = 1; while ($pembeli = $ambil_data->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center"><?= $nomor++; ?></td>
                                        <td><?= htmlspecialchars($pembeli['nama_pembeli']); ?></td>
                                        <td>
                                            <small>
                                                Email: <?= htmlspecialchars($pembeli['email']); ?><br>
                                                Telp: <?= htmlspecialchars($pembeli['telp']); ?>
                                            </small>
                                        </td>  
                                        <td class="text-center"><?= number_format($pembeli['total_transaksi'] ?? 0); ?></td>
                                        <td class="text-end">Rp <?= number_format($pembeli['total_pembayaran'] ?? 0); ?></td>
                                    </tr>
                                    <?php endwhile; else: ?>
                                    <tr><td colspan="6" class="text-center">Data tidak ditemukan.</td></tr>
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