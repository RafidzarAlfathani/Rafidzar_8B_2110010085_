<?php
// --- LOGIKA PHP UNTUK MENGAMBIL DATA ---

// Ambil filter dari form jika ada
$tgl_mulai_filter = isset($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : '';
$tgl_selesai_filter = isset($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : '';
$kategori_filter = isset($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : '';
$limit_filter = 10; // Tetapkan 10 produk

// --- Query untuk Produk Terlaris (DESC) ---
$sql_terlaris = "SELECT pr.nama_produk, pt.nama_petani, kp.nama_kategori, SUM(dp.jumlah) AS total_terjual FROM detail_pesanan dp JOIN produk pr ON dp.id_produk = pr.id_produk JOIN petani pt ON pr.id_petani = pt.id_petani JOIN kategori_produk kp ON pr.id_kategori = kp.id_kategori JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan WHERE ps.status_pesanan != 'Dibatalkan'";
if ($tgl_mulai_filter && $tgl_selesai_filter) { $sql_terlaris .= " AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter 00:00:00' AND '$tgl_selesai_filter 23:59:59'"; }
if ($kategori_filter) { $sql_terlaris .= " AND pr.id_kategori = $kategori_filter"; }
$sql_terlaris .= " GROUP BY pr.id_produk ORDER BY total_terjual DESC LIMIT $limit_filter";
$data_terlaris = $con->query($sql_terlaris);

// --- Query untuk Produk Kurang Laris (ASC) ---
$sql_kurang_laris = "SELECT pr.nama_produk, pt.nama_petani, kp.nama_kategori, COALESCE(SUM(dp.jumlah), 0) AS total_terjual FROM produk pr LEFT JOIN detail_pesanan dp ON pr.id_produk = dp.id_produk LEFT JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan AND ps.status_pesanan != 'Dibatalkan' JOIN petani pt ON pr.id_petani = pt.id_petani JOIN kategori_produk kp ON pr.id_kategori = kp.id_kategori";
$where_clauses = [];
if ($tgl_mulai_filter && $tgl_selesai_filter) { $where_clauses[] = "ps.tgl_pesan BETWEEN '$tgl_mulai_filter 00:00:00' AND '$tgl_selesai_filter 23:59:59'"; }
if ($kategori_filter) { $where_clauses[] = "pr.id_kategori = $kategori_filter"; }
if (!empty($where_clauses)) { $sql_kurang_laris .= " WHERE " . implode(' AND ', $where_clauses); }
$sql_kurang_laris .= " GROUP BY pr.id_produk ORDER BY total_terjual ASC LIMIT $limit_filter";
$data_kurang_laris = $con->query($sql_kurang_laris);

// --- Statistik (SAMA SEPERTI SEBELUMNYA) ---
$sql_total_item = "SELECT SUM(dp.jumlah) AS total_item FROM detail_pesanan dp JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan JOIN produk pr ON dp.id_produk = pr.id_produk WHERE ps.status_pesanan != 'Dibatalkan'";
if ($tgl_mulai_filter && $tgl_selesai_filter) { $sql_total_item .= " AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter 00:00:00' AND '$tgl_selesai_filter 23:59:59'"; }
if ($kategori_filter) { $sql_total_item .= " AND pr.id_kategori = $kategori_filter"; }
$total_item = $con->query($sql_total_item)->fetch_assoc()['total_item'] ?? 0;
// ... (statistik lainnya bisa ditambahkan jika perlu, untuk saat ini kita sederhanakan) ...
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Laporan Penjualan Produk</h5>
            </div>
            <div class="panel-body">
                
                <div class="card mb-3">
                    <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan</h5></div>
                    <div class="card-body">
                        <form action="?page=laporan&aksi=laporan_produk_terlaris" method="POST">
                            <div class="row align-items-end">
                                <div class="col-md-4"><label>Dari Tanggal</label><input type="date" name="tanggal_mulai" class="form-control" value="<?= $tgl_mulai_filter; ?>"></div>
                                <div class="col-md-3"><label>Sampai Tanggal</label><input type="date" name="tanggal_selesai" class="form-control" value="<?= $tgl_selesai_filter; ?>"></div>
                                <div class="col-md-3">
                                    <label>Kategori</label>
                                    <select name="id_kategori" class="form-control">
                                        <option value="">Semua Kategori</option>
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

                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="terlaris-tab" data-bs-toggle="tab" data-bs-target="#terlaris" type="button" role="tab" aria-controls="terlaris" aria-selected="true">
                            <i class="fas fa-chart-line me-1"></i> Produk Terlaris
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="kurang-laris-tab" data-bs-toggle="tab" data-bs-target="#kurang-laris" type="button" role="tab" aria-controls="kurang-laris" aria-selected="false">
                            <i class="fas fa-chart-area me-1"></i> Produk Kurang Laris
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane fade show active" id="terlaris" role="tabpanel" aria-labelledby="terlaris-tab">
                        <div class="card border-top-0">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">Top 10 Produk Terlaris</h5>
                                <form action="page/laporan/cetak_produk_terlaris_pdf.php" method="POST" target="_blank" class="m-0">
                                    <input type="hidden" name="tanggal_mulai" value="<?= htmlspecialchars($tgl_mulai_filter); ?>">
                                    <input type="hidden" name="tanggal_selesai" value="<?= htmlspecialchars($tgl_selesai_filter); ?>">
                                    <input type="hidden" name="id_kategori" value="<?= htmlspecialchars($kategori_filter); ?>">
                                    <button type="submit" class="btn btn-success"><i class="fas fa-print me-1"></i> Cetak PDF</button>
                                </form>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead><tr><th>Peringkat</th><th>Nama Produk</th><th>Kategori</th><th>Petani</th><th>Total Terjual</th></tr></thead>
                                    <tbody>
                                        <?php if ($data_terlaris->num_rows > 0): $nomor = 1; while ($produk = $data_terlaris->fetch_assoc()): ?>
                                            <tr>
                                                <td class="text-center"><?php if ($nomor <= 3) echo "<span class='badge bg-warning text-dark fs-6'>#$nomor</span>"; else echo "#$nomor"; $nomor++; ?></td>
                                                <td><?= htmlspecialchars($produk['nama_produk']); ?></td>
                                                <td><?= htmlspecialchars($produk['nama_kategori']); ?></td>
                                                <td><?= htmlspecialchars($produk['nama_petani']); ?></td>
                                                <td class="text-center"><strong><?= number_format($produk['total_terjual']); ?></strong> item</td>
                                            </tr>
                                        <?php endwhile; else: ?>
                                            <tr><td colspan="5" class="text-center">Data tidak ditemukan.</td></tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="kurang-laris" role="tabpanel" aria-labelledby="kurang-laris-tab">
                        <div class="card border-top-0">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">10 Produk Kurang Laris (Termasuk yang Belum Terjual)</h5>
                                <form action="page/laporan/cetak_produk_kurang_laris_pdf.php" method="POST" target="_blank" class="m-0">
                                    <input type="hidden" name="tanggal_mulai" value="<?= htmlspecialchars($tgl_mulai_filter); ?>">
                                    <input type="hidden" name="tanggal_selesai" value="<?= htmlspecialchars($tgl_selesai_filter); ?>">
                                    <input type="hidden" name="id_kategori" value="<?= htmlspecialchars($kategori_filter); ?>">
                                    <button type="submit" class="btn btn-danger"><i class="fas fa-print me-1"></i> Cetak PDF</button>
                                </form>
                            </div>
                             <div class="card-body">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead><tr><th>Peringkat</th><th>Nama Produk</th><th>Kategori</th><th>Petani</th><th>Total Terjual</th></tr></thead>
                                    <tbody>
                                        <?php if ($data_kurang_laris->num_rows > 0): $nomor = 1; while ($produk = $data_kurang_laris->fetch_assoc()): ?>
                                            <tr>
                                                <td class="text-center">#<?= $nomor++; ?></td>
                                                <td><?= htmlspecialchars($produk['nama_produk']); ?></td>
                                                <td><?= htmlspecialchars($produk['nama_kategori']); ?></td>
                                                <td><?= htmlspecialchars($produk['nama_petani']); ?></td>
                                                <td class="text-center"><strong><?= number_format($produk['total_terjual']); ?></strong> item</td>
                                            </tr>
                                        <?php endwhile; else: ?>
                                            <tr><td colspan="5" class="text-center">Data tidak ditemukan.</td></tr>
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
</div>