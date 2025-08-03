<?php
// --- LOGIKA PHP UNTUK MENGAMBIL DATA ---

// Ambil filter dari form jika ada
$id_petani_filter = isset($_POST['id_petani']) && !empty($_POST['id_petani']) ? (int)$_POST['id_petani'] : '';
$id_kategori_filter = isset($_POST['id_kategori']) && !empty($_POST['id_kategori']) ? (int)$_POST['id_kategori'] : '';

// Query utama untuk mengambil data produk
// Termasuk subquery untuk menghitung total item terjual untuk setiap produk
$sql_data = "SELECT 
                pr.*, 
                pt.nama_petani, 
                kp.nama_kategori,
                (SELECT SUM(dp.jumlah) FROM detail_pesanan dp WHERE dp.id_produk = pr.id_produk) AS total_dipesan
             FROM produk pr
             JOIN petani pt ON pr.id_petani = pt.id_petani
             JOIN kategori_produk kp ON pr.id_kategori = kp.id_kategori";

// Buat array untuk menampung kondisi WHERE
$where_clauses = [];
if ($id_petani_filter) {
    $where_clauses[] = "pr.id_petani = $id_petani_filter";
}
if ($id_kategori_filter) {
    $where_clauses[] = "pr.id_kategori = $id_kategori_filter";
}

// Gabungkan kondisi WHERE jika ada filter yang aktif
if (!empty($where_clauses)) {
    $sql_data .= " WHERE " . implode(' AND ', $where_clauses);
}

$sql_data .= " ORDER BY pr.nama_produk ASC";
$ambil_data = $con->query($sql_data);
?>
<?php
// Statistik: Total Produk, Total Stok, Total Dipesan
$sql_statistik = "SELECT 
                    COUNT(*) AS total_produk, 
                    SUM(pr.stok) AS total_stok,
                    SUM((SELECT SUM(dp.jumlah) FROM detail_pesanan dp WHERE dp.id_produk = pr.id_produk)) AS total_dipesan
                 FROM produk pr";

// Tambahkan filter jika ada
if (!empty($where_clauses)) {
    $sql_statistik .= " WHERE " . implode(' AND ', $where_clauses);
}

$hasil_statistik = $con->query($sql_statistik);
$data_statistik = $hasil_statistik->fetch_assoc();
$total_produk = $data_statistik['total_produk'] ?? 0;
$total_stok = $data_statistik['total_stok'] ?? 0;
$total_dipesan = $data_statistik['total_dipesan'] ?? 0;
?>


<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Laporan Data Produk</h5>
            </div>
            <div class="panel-body">

                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5 class="card-title">Total Produk </h5>
                                <p class="card-text fs-4"><?= number_format($total_produk); ?> item</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5 class="card-title">Jumlah Stok Tersedia</h5>
                                <p class="card-text fs-4"><?= number_format($total_stok); ?> produk</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-white bg-info">
                            <div class="card-body">
                                <h5 class="card-title">total dipesan</h5>
                                <p class="card-text fs-4"><?= number_format($total_dipesan); ?> kategori</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan Produk</h5>
                    </div>
                    <div class="card-body">
                        <form action="?page=laporan&aksi=laporan_produk" method="POST">
                            <div class="row">
                                <div class="col-md-5">
                                    <label class="form-label">Berdasarkan Petani</label>
                                    <select name="id_petani" class="form-control">
                                        <option value="">-- Tampilkan Semua Petani --</option>
                                        <?php
                                        $ambil_petani = $con->query("SELECT id_petani, nama_petani FROM petani ORDER BY nama_petani ASC");
                                        while ($petani = $ambil_petani->fetch_assoc()) {
                                            $selected = ($petani['id_petani'] == $id_petani_filter) ? 'selected' : '';
                                            echo "<option value='{$petani['id_petani']}' $selected>" . htmlspecialchars($petani['nama_petani']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label">Berdasarkan Kategori</label>
                                    <select name="id_kategori" class="form-control">
                                        <option value="">-- Tampilkan Semua Kategori --</option>
                                        <?php
                                        $ambil_kategori = $con->query("SELECT id_kategori, nama_kategori FROM kategori_produk ORDER BY nama_kategori ASC");
                                        while ($kategori = $ambil_kategori->fetch_assoc()) {
                                            $selected = ($kategori['id_kategori'] == $id_kategori_filter) ? 'selected' : '';
                                            echo "<option value='{$kategori['id_kategori']}' $selected>" . htmlspecialchars($kategori['nama_kategori']) . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Tampilkan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fas fa-table me-2"></i>Hasil Laporan Produk</h5>
                        <form action="page/laporan/cetak_produk_pdf.php" method="POST" target="_blank" class="m-0">
                            <input type="hidden" name="id_petani" value="<?= htmlspecialchars($id_petani_filter); ?>">
                            <input type="hidden" name="id_kategori" value="<?= htmlspecialchars($id_kategori_filter); ?>">
                            <button type="submit" class="btn btn-success"><i class="fas fa-print me-1"></i> Cetak Laporan PDF</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th>Nama Produk</th>
                                        <th>Petani</th>
                                        <th>Kategori</th>
                                        <th class="text-end">Harga</th>
                                        <th class="text-center">Stok</th>
                                        <th class="text-center">Total Dipesan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($ambil_data->num_rows > 0): $nomor = 1;
                                        while ($produk = $ambil_data->fetch_assoc()): ?>
                                            <tr>
                                                <td class="text-center"><?= $nomor++; ?></td>
                                                <td><?= htmlspecialchars($produk['nama_produk']); ?></td>
                                                <td><?= htmlspecialchars($produk['nama_petani']); ?></td>
                                                <td><?= htmlspecialchars($produk['nama_kategori']); ?></td>
                                                <td class="text-end">Rp <?= number_format($produk['harga']); ?></td>
                                                <td class="text-center"><?= number_format($produk['stok']); ?> <?= htmlspecialchars($produk['satuan']); ?></td>
                                                <td class="text-center"><?= number_format($produk['total_dipesan'] ?? 0); ?> item</td>
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