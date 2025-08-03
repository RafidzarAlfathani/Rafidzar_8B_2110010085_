<?php
// --- LOGIKA PHP UNTUK MENGAMBIL DATA ---

// Ambil semua filter dari form jika ada
$tgl_mulai_filter = isset($_POST['tanggal_mulai']) && !empty($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : '';
$tgl_selesai_filter = isset($_POST['tanggal_selesai']) && !empty($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : '';
$status_filter = isset($_POST['status_pesanan']) ? $_POST['status_pesanan'] : '';
$kurir_filter = isset($_POST['id_kurir']) ? (int)$_POST['id_kurir'] : '';

// Query dasar untuk mengambil semua data tracking
$sql_data = "SELECT 
                tp.*,
                ps.kode_invoice,
                ps.status_pesanan,
                pm.nama_pembeli,
                kr.nama_kurir
             FROM tracking_pengiriman tp
             JOIN pesanan ps ON tp.id_pesanan = ps.id_pesanan
             JOIN pembeli pm ON ps.id_pembeli = pm.id_pembeli
             LEFT JOIN kurir kr ON ps.id_kurir = kr.id_kurir";

// Buat array untuk menampung kondisi WHERE
$where_clauses = [];
if ($tgl_mulai_filter && $tgl_selesai_filter) {
    $where_clauses[] = "DATE(tp.waktu_update) BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'";
}
if ($status_filter) {
    $where_clauses[] = "ps.status_pesanan = '$status_filter'";
}
if ($kurir_filter) {
    $where_clauses[] = "ps.id_kurir = $kurir_filter";
}

// Gabungkan kondisi WHERE jika ada filter yang aktif
if (!empty($where_clauses)) {
    $sql_data .= " WHERE " . implode(' AND ', $where_clauses);
}

$sql_data .= " ORDER BY tp.waktu_update DESC";
$ambil_data = $con->query($sql_data);

?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Laporan Riwayat Tracking</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-3">
                    <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan</h5></div>
                    <div class="card-body">
                        <form action="?page=laporan&aksi=laporan_tracking" method="POST">
                            <div class="row align-items-end">
                                <div class="col-md-3"><label>Dari Tanggal Update</label><input type="date" name="tanggal_mulai" class="form-control" value="<?= $tgl_mulai_filter; ?>"></div>
                                <div class="col-md-3"><label>Sampai Tanggal Update</label><input type="date" name="tanggal_selesai" class="form-control" value="<?= $tgl_selesai_filter; ?>"></div>
                                <div class="col-md-3">
                                    <label>Status Pesanan</label>
                                    <select name="status_pesanan" class="form-control">
                                        <option value="">Semua</option>
                                        <option value="Diproses" <?= $status_filter == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                                        <option value="Dikirim" <?= $status_filter == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                        <option value="Selesai" <?= $status_filter == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Kurir</label>
                                    <select name="id_kurir" class="form-control">
                                        <option value="">Semua</option>
                                        <?php $q_kurir = $con->query("SELECT id_kurir, nama_kurir FROM kurir ORDER BY nama_kurir"); while($k = $q_kurir->fetch_assoc()): ?>
                                            <option value="<?= $k['id_kurir'] ?>" <?= $kurir_filter == $k['id_kurir'] ? 'selected' : '' ?>><?= $k['nama_kurir'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-12 mt-3">
                                    <button type="submit" class="btn btn-primary"><i class="fas fa-search me-1"></i> Tampilkan</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fas fa-table me-2"></i>Hasil Laporan Riwayat Tracking</h5>
                        <form action="page/laporan/cetak_tracking_pdf.php" method="POST" target="_blank" class="m-0">
                            <input type="hidden" name="tanggal_mulai" value="<?= htmlspecialchars($tgl_mulai_filter); ?>">
                            <input type="hidden" name="tanggal_selesai" value="<?= htmlspecialchars($tgl_selesai_filter); ?>">
                            <input type="hidden" name="status_pesanan" value="<?= htmlspecialchars($status_filter); ?>">
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
                                        <th>Waktu Update</th>
                                        <th>Invoice</th>
                                        <th>Keterangan</th>
                                        <th>Kurir</th>
                                        <th class="text-center">Status Pesanan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($ambil_data->num_rows > 0): $nomor = 1; while ($track = $ambil_data->fetch_assoc()): ?>
                                    <tr>
                                        <td class="text-center"><?= $nomor++; ?></td>
                                        <td><?= date("d M Y, H:i", strtotime($track['waktu_update'])); ?></td>
                                        <td><a href="?page=pesanan&aksi=detail&id_pesanan=<?= $track['id_pesanan']; ?>"><?= $track['kode_invoice']; ?></a></td>
                                        <td><?= htmlspecialchars($track['keterangan']); ?></td>
                                        <td><?= htmlspecialchars($track['nama_kurir'] ?? '-'); ?></td>
                                        <td class="text-center"><span class="badge bg-info"><?= $track['status_pesanan']; ?></span></td>
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