<?php
// --- LOGIKA PHP UNTUK MENGAMBIL DATA ---

// Ambil filter dari form jika ada
$tgl_mulai_filter = isset($_POST['tanggal_mulai']) && !empty($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : '';
$tgl_selesai_filter = isset($_POST['tanggal_selesai']) && !empty($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : '';
$limit_filter = isset($_POST['limit']) ? (int)$_POST['limit'] : 10; // Default 10 petani teratas

// Query utama untuk mengagregasi pendapatan per petani
$sql_data = "SELECT 
                pt.nama_petani,
                pt.telp,
                SUM(dp.sub_total) AS total_pendapatan
             FROM detail_pesanan dp
             JOIN produk pr ON dp.id_produk = pr.id_produk
             JOIN petani pt ON pr.id_petani = pt.id_petani
             JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
             WHERE ps.status_pesanan = 'Selesai'";

// Terapkan filter tanggal jika ada
if ($tgl_mulai_filter && $tgl_selesai_filter) {
    $sql_data .= " AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter 00:00:00' AND '$tgl_selesai_filter 23:59:59'";
}

$sql_data .= " GROUP BY pt.id_petani
               ORDER BY total_pendapatan DESC
               LIMIT $limit_filter";

$ambil_data = $con->query($sql_data);
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Laporan Petani dengan Pendapatan Tertinggi</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-3">
                    <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan</h5></div>
                    <div class="card-body">
                        <form action="?page=laporan&aksi=laporan_pendapatan_petani" method="POST">
                            <div class="row align-items-end">
                                <div class="col-md-4"><label>Dari Tanggal (Pesanan)</label><input type="date" name="tanggal_mulai" class="form-control" value="<?= $tgl_mulai_filter; ?>"></div>
                                <div class="col-md-4"><label>Sampai Tanggal (Pesanan)</label><input type="date" name="tanggal_selesai" class="form-control" value="<?= $tgl_selesai_filter; ?>"></div>
                                <div class="col-md-2">
                                    <label>Jumlah Petani</label>
                                    <select name="limit" class="form-control">
                                        <option value="5" <?= $limit_filter == 5 ? 'selected' : '' ?>>5 Teratas</option>
                                        <option value="10" <?= $limit_filter == 10 ? 'selected' : '' ?>>10 Teratas</option>
                                        <option value="20" <?= $limit_filter == 20 ? 'selected' : '' ?>>20 Teratas</option>
                                    </select>
                                </div>
                                <div class="col-md-2"><button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Tampilkan</button></div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fas fa-trophy me-2"></i>Hasil Laporan Pendapatan Petani</h5>
                        <form action="page/laporan/cetak_pendapatan_petani_pdf.php" method="POST" target="_blank" class="m-0">
                            <input type="hidden" name="tanggal_mulai" value="<?= htmlspecialchars($tgl_mulai_filter); ?>">
                            <input type="hidden" name="tanggal_selesai" value="<?= htmlspecialchars($tgl_selesai_filter); ?>">
                            <input type="hidden" name="limit" value="<?= htmlspecialchars($limit_filter); ?>">
                            <button type="submit" class="btn btn-success"><i class="fas fa-print me-1"></i> Cetak Laporan PDF</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">Peringkat</th>
                                        <th>Nama Petani</th>
                                        <th>Telepon</th>
                                        <th class="text-end">Total Pendapatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($ambil_data->num_rows > 0): $nomor = 1; while ($petani = $ambil_data->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center">
                                            <?php if ($nomor == 1): ?>
                                                <span class="badge bg-warning text-dark">#<?= $nomor++; ?> <i class="fas fa-crown"></i></span>
                                            <?php else: ?>
                                                #<?= $nomor++; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= htmlspecialchars($petani['nama_petani']); ?></td>
                                        <td><?= htmlspecialchars($petani['telp']); ?></td>
                                        <td class="text-end"><strong>Rp <?= number_format($petani['total_pendapatan'] ?? 0); ?></strong></td>
                                    </tr>
                                    <?php endwhile; else: ?>
                                    <tr><td colspan="4" class="text-center">Data pendapatan tidak ditemukan.</td></tr>
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