<?php
// --- LOGIKA PHP UNTUK MENGAMBIL DATA ---

// Ambil filter dari form jika ada
$tgl_mulai_filter = isset($_POST['tanggal_mulai']) && !empty($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : '';
$tgl_selesai_filter = isset($_POST['tanggal_selesai']) && !empty($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : '';
$kategori_filter = isset($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : '';
$limit_filter = isset($_POST['limit']) ? (int)$_POST['limit'] : 10; // Default 10 produk teratas

// Query utama untuk mengagregasi penjualan per produk
$sql_data = "SELECT 
                pr.nama_produk,
                pt.nama_petani,
                kp.nama_kategori,
                SUM(dp.jumlah) AS total_terjual
             FROM detail_pesanan dp
             JOIN produk pr ON dp.id_produk = pr.id_produk
             JOIN petani pt ON pr.id_petani = pt.id_petani
             JOIN kategori_produk kp ON pr.id_kategori = kp.id_kategori
             JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
             WHERE ps.status_pesanan != 'Dibatalkan'";

// Terapkan filter tanggal jika ada
if ($tgl_mulai_filter && $tgl_selesai_filter) {
    $sql_data .= " AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter 00:00:00' AND '$tgl_selesai_filter 23:59:59'";
}
// Terapkan filter kategori jika ada
if ($kategori_filter) {
    $sql_data .= " AND pr.id_kategori = $kategori_filter";
}

$sql_data .= " GROUP BY pr.id_produk
               ORDER BY total_terjual DESC
               LIMIT $limit_filter";

$ambil_data = $con->query($sql_data);
?>
<?php
// Statistik total produk terjual
$sql_total_item = "SELECT SUM(dp.jumlah) AS total_item 
                   FROM detail_pesanan dp 
                   JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan 
                   JOIN produk pr ON dp.id_produk = pr.id_produk
                   WHERE ps.status_pesanan != 'Dibatalkan'";

// Tambah filter jika ada
if ($tgl_mulai_filter && $tgl_selesai_filter) {
    $sql_total_item .= " AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter 00:00:00' AND '$tgl_selesai_filter 23:59:59'";
}
if ($kategori_filter) {
    $sql_total_item .= " AND pr.id_kategori = $kategori_filter";
}

$res_total_item = $con->query($sql_total_item);
$total_item = $res_total_item->fetch_assoc()['total_item'] ?? 0;

// Total produk berbeda
$res_produk_unik = $con->query("SELECT COUNT(DISTINCT dp.id_produk) AS produk_unik 
                                FROM detail_pesanan dp
                                JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
                                JOIN produk pr ON dp.id_produk = pr.id_produk
                                WHERE ps.status_pesanan != 'Dibatalkan'"
    . ($tgl_mulai_filter && $tgl_selesai_filter ? " AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter 00:00:00' AND '$tgl_selesai_filter 23:59:59'" : "")
    . ($kategori_filter ? " AND pr.id_kategori = $kategori_filter" : ""));
$total_produk = $res_produk_unik->fetch_assoc()['produk_unik'] ?? 0;

// Total kategori unik
$res_kategori = $con->query("SELECT COUNT(DISTINCT pr.id_kategori) AS kategori_aktif
                             FROM detail_pesanan dp
                             JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
                             JOIN produk pr ON dp.id_produk = pr.id_produk
                             WHERE ps.status_pesanan != 'Dibatalkan'"
    . ($tgl_mulai_filter && $tgl_selesai_filter ? " AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter 00:00:00' AND '$tgl_selesai_filter 23:59:59'" : "")
    . ($kategori_filter ? " AND pr.id_kategori = $kategori_filter" : ""));
$kategori_aktif = $res_kategori->fetch_assoc()['kategori_aktif'] ?? 0;
?>


<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Laporan Produk Terlaris</h5>
            </div>
            <div class="panel-body">
                
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Total Produk Terjual</h5>
                                <p class="card-text fs-4"><?= number_format($total_item); ?> item</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Jumlah Produk Berbeda</h5>
                                <p class="card-text fs-4"><?= number_format($total_produk); ?> produk</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5 class="card-title">Kategori Terlibat</h5>
                                <p class="card-text fs-4"><?= number_format($kategori_aktif); ?> kategori</p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan</h5>
                    </div>
                    <div class="card-body">
                        <form action="?page=laporan&aksi=laporan_produk_terlaris" method="POST">
                            <div class="row align-items-end">
                                <div class="col-md-4"><label>Dari Tanggal (Pesanan)</label><input type="date" name="tanggal_mulai" class="form-control" value="<?= $tgl_mulai_filter; ?>"></div>
                                <div class="col-md-3"><label>Sampai Tanggal (Pesanan)</label><input type="date" name="tanggal_selesai" class="form-control" value="<?= $tgl_selesai_filter; ?>"></div>
                                <div class="col-md-3">
                                    <label>Kategori</label>
                                    <select name="id_kategori" class="form-control">
                                        <option value="">Semua</option>
                                        <?php $q_kategori = $con->query("SELECT * FROM kategori_produk ORDER BY nama_kategori");
                                        while ($k = $q_kategori->fetch_assoc()): ?>
                                            <option value="<?= $k['id_kategori'] ?>" <?= $kategori_filter == $k['id_kategori'] ? 'selected' : '' ?>><?= $k['nama_kategori'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-2"><button type="submit" class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Tampilkan</button></div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fas fa-star me-2"></i>Hasil Laporan Produk Terlaris</h5>
                        <form action="page/laporan/cetak_produk_terlaris_pdf.php" method="POST" target="_blank" class="m-0">
                            <input type="hidden" name="tanggal_mulai" value="<?= htmlspecialchars($tgl_mulai_filter); ?>">
                            <input type="hidden" name="tanggal_selesai" value="<?= htmlspecialchars($tgl_selesai_filter); ?>">
                            <input type="hidden" name="id_kategori" value="<?= htmlspecialchars($kategori_filter); ?>">
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
                                        <th>Nama Produk</th>
                                        <th>Kategori</th>
                                        <th>Petani</th>
                                        <th class="text-center">Total Terjual</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($ambil_data->num_rows > 0): $nomor = 1;
                                        while ($produk = $ambil_data->fetch_assoc()): ?>
                                            <tr>
                                                <td class="text-center">
                                                    <?php if ($nomor <= 3): ?>
                                                        <span class="badge bg-warning text-dark fs-6">#<?= $nomor++; ?></span>
                                                    <?php else: ?>
                                                        #<?= $nomor++; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?= htmlspecialchars($produk['nama_produk']); ?></td>
                                                <td><?= htmlspecialchars($produk['nama_kategori']); ?></td>
                                                <td><?= htmlspecialchars($produk['nama_petani']); ?></td>
                                                <td class="text-center"><strong><?= number_format($produk['total_terjual'] ?? 0); ?></strong> item</td>
                                            </tr>
                                        <?php endwhile;
                                    else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Data penjualan tidak ditemukan.</td>
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