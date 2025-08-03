<?php
// --- LOGIKA PHP UNTUK MENGAMBIL DATA ---

// Ambil semua filter dari form jika ada
$tgl_mulai_filter = isset($_POST['tanggal_mulai']) && !empty($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : '';
$tgl_selesai_filter = isset($_POST['tanggal_selesai']) && !empty($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : '';
$status_filter = isset($_POST['status_pesanan']) ? $_POST['status_pesanan'] : '';
$petani_filter = isset($_POST['id_petani']) ? (int)$_POST['id_petani'] : '';
$kurir_filter = isset($_POST['id_kurir']) ? (int)$_POST['id_kurir'] : '';

// Query dasar
$sql_data = "SELECT DISTINCT
                ps.*, 
                pm.nama_pembeli,
                kr.nama_kurir
             FROM pesanan ps
             JOIN pembeli pm ON ps.id_pembeli = pm.id_pembeli
             LEFT JOIN kurir kr ON ps.id_kurir = kr.id_kurir";

// Jika ada filter by petani, kita perlu JOIN tambahan
if ($petani_filter) {
    $sql_data .= " JOIN detail_pesanan dp ON ps.id_pesanan = dp.id_pesanan
                   JOIN produk pr ON dp.id_produk = pr.id_produk";
}

// Buat array untuk menampung kondisi WHERE
$where_clauses = [];
if ($tgl_mulai_filter && $tgl_selesai_filter) {
    $where_clauses[] = "ps.tgl_pesan BETWEEN '$tgl_mulai_filter 00:00:00' AND '$tgl_selesai_filter 23:59:59'";
}
if ($status_filter) {
    $where_clauses[] = "ps.status_pesanan = '$status_filter'";
}
if ($kurir_filter) {
    $where_clauses[] = "ps.id_kurir = $kurir_filter";
}
if ($petani_filter) {
    $where_clauses[] = "pr.id_petani = $petani_filter";
}

// Gabungkan kondisi WHERE jika ada filter yang aktif
if (!empty($where_clauses)) {
    $sql_data .= " WHERE " . implode(' AND ', $where_clauses);
}

$sql_data .= " ORDER BY ps.tgl_pesan DESC";
$ambil_data = $con->query($sql_data);
?>

<?php
// Statistik jumlah pesanan per status
$query_status_pesanan = "SELECT status_pesanan, COUNT(*) AS jumlah 
                         FROM pesanan 
                         WHERE 1";

// Filter tanggal jika ada
if (!empty($tgl_mulai_filter) && !empty($tgl_selesai_filter)) {
    $query_status_pesanan .= " AND tgl_pesan BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'";
}

$query_status_pesanan .= " GROUP BY status_pesanan";
$hasil_status = mysqli_query($con, $query_status_pesanan);

// Simpan ke array
$status_statistik = [];
while ($row = mysqli_fetch_assoc($hasil_status)) {
    $status_statistik[$row['status_pesanan']] = $row['jumlah'];
}

// Statistik jumlah pesanan per tanggal
$query_per_tanggal = "SELECT tgl_pesan, COUNT(*) AS jumlah 
                      FROM pesanan 
                      WHERE 1";

// Filter tanggal jika digunakan
if (!empty($tgl_mulai_filter) && !empty($tgl_selesai_filter)) {
    $query_per_tanggal .= " AND tgl_pesan BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'";
}

$query_per_tanggal .= " GROUP BY tgl_pesan ORDER BY tgl_pesan ASC";

$hasil_per_tanggal = mysqli_query($con, $query_per_tanggal);

// Siapkan data untuk grafik
$tanggal_data = [];
$jumlah_data = [];

while ($row = mysqli_fetch_assoc($hasil_per_tanggal)) {
    $tanggal_data[] = $row['tgl_pesan'];
    $jumlah_data[] = $row['jumlah'];
}
?>


<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Laporan Data Pesanan</h5>
            </div>
            <div class="panel-body">
            <div class="row mb-4">

    <?php foreach ($status_statistik as $status => $jumlah): ?>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card bg-light border-primary h-100">
                <div class="card-body text-center ">
                    <h6 class="card-title"><?= htmlspecialchars($status); ?></h6>
                    <p class="fs-4 fw-bold"><?= number_format($jumlah); ?> pesanan</p>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>


                <div class="card mb-3">
                    <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan</h5></div>
                    <div class="card-body">
                        <form action="?page=laporan&aksi=laporan_pesanan" method="POST">
                            <div class="row align-items-end">
                                <div class="col-md-3"><label>Dari Tanggal</label><input type="date" name="tanggal_mulai" class="form-control" value="<?= $tgl_mulai_filter; ?>"></div>
                                <div class="col-md-3"><label>Sampai Tanggal</label><input type="date" name="tanggal_selesai" class="form-control" value="<?= $tgl_selesai_filter; ?>"></div>
                                <div class="col-md-2">
                                    <label>Status Pesanan</label>
                                    <select name="status_pesanan" class="form-control">
                                        <option value="">Semua</option>
                                        <option value="Menunggu Pembayaran" <?= $status_filter == 'Menunggu Pembayaran' ? 'selected' : '' ?>>Menunggu Pembayaran</option>
                                        <option value="Menunggu Verifikasi" <?= $status_filter == 'Menunggu Verifikasi' ? 'selected' : '' ?>>Menunggu Verifikasi</option>
                                        <option value="Diproses" <?= $status_filter == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                                        <option value="Dikirim" <?= $status_filter == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                        <option value="Selesai" <?= $status_filter == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                        <option value="Dibatalkan" <?= $status_filter == 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label>Petani</label>
                                    <select name="id_petani" class="form-control">
                                        <option value="">Semua</option>
                                        <?php $q_petani = $con->query("SELECT id_petani, nama_petani FROM petani ORDER BY nama_petani"); while($p = $q_petani->fetch_assoc()): ?>
                                            <option value="<?= $p['id_petani'] ?>" <?= $petani_filter == $p['id_petani'] ? 'selected' : '' ?>><?= $p['nama_petani'] ?></option>
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
                        <h5 class="card-title mb-0"><i class="fas fa-table me-2"></i>Hasil Laporan Pesanan</h5>
                        <form action="page/laporan/cetak_pesanan_pdf.php" method="POST" target="_blank" class="m-0">
                            <input type="hidden" name="tanggal_mulai" value="<?= htmlspecialchars($tgl_mulai_filter); ?>">
                            <input type="hidden" name="tanggal_selesai" value="<?= htmlspecialchars($tgl_selesai_filter); ?>">
                            <input type="hidden" name="status_pesanan" value="<?= htmlspecialchars($status_filter); ?>">
                            <input type="hidden" name="id_petani" value="<?= htmlspecialchars($petani_filter); ?>">
                            <input type="hidden" name="id_kurir" value="<?= htmlspecialchars($kurir_filter); ?>">
                            <button type="submit" class="btn btn-success"><i class="fas fa-print me-1"></i> Cetak Laporan PDF</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th>Invoice</th>
                                        <th>Pembeli</th>
                                        <th>Tgl. Pesan</th>
                                        <th class="text-end">Total Bayar</th>
                                        <th>Kurir</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($ambil_data->num_rows > 0): $nomor = 1; while ($pesanan = $ambil_data->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center"><?= $nomor++; ?></td>
                                        <td><a href="?page=pesanan&aksi=detail&id_pesanan=<?= $pesanan['id_pesanan']; ?>"><?= $pesanan['kode_invoice']; ?></a></td>
                                        <td><?= htmlspecialchars($pesanan['nama_pembeli']); ?></td>
                                        <td><?= date("d M Y, H:i", strtotime($pesanan['tgl_pesan'])); ?></td>
                                        <td class="text-end">Rp <?= number_format($pesanan['total_bayar']); ?></td>
                                        <td><?= htmlspecialchars($pesanan['nama_kurir'] ?? '-'); ?></td>
                                        <td class="text-center"><span class="badge bg-info"><?= $pesanan['status_pesanan']; ?></span></td>
                                    </tr>
                                    <?php endwhile; else: ?>
                                    <tr><td colspan="7" class="text-center">Data tidak ditemukan.</td></tr>
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