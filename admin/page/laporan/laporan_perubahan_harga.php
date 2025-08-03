<?php
// --- LOGIKA PHP UNTUK MENGAMBIL DATA ---

// Ambil filter dari form jika ada
$tgl_mulai_filter = isset($_POST['tanggal_mulai']) && !empty($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : '';
$tgl_selesai_filter = isset($_POST['tanggal_selesai']) && !empty($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : '';
$produk_filter = isset($_POST['id_produk']) ? (int)$_POST['id_produk'] : '';
$petani_filter = isset($_POST['id_petani']) ? (int)$_POST['id_petani'] : '';

// Query dasar untuk mengambil data riwayat harga
$sql_data = "SELECT 
                rh.*,
                pr.nama_produk,
                pt.nama_petani
             FROM riwayat_harga rh
             JOIN produk pr ON rh.id_produk = pr.id_produk
             JOIN petani pt ON pr.id_petani = pt.id_petani";

// Buat array untuk menampung kondisi WHERE
$where_clauses = [];
if ($tgl_mulai_filter && $tgl_selesai_filter) {
    $where_clauses[] = "DATE(rh.tgl_perubahan) BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'";
}
if ($produk_filter) {
    $where_clauses[] = "rh.id_produk = $produk_filter";
}
if ($petani_filter) {
    $where_clauses[] = "pr.id_petani = $petani_filter";
}

// Gabungkan kondisi WHERE jika ada filter yang aktif
if (!empty($where_clauses)) {
    $sql_data .= " WHERE " . implode(' AND ', $where_clauses);
}

$sql_data .= " ORDER BY rh.tgl_perubahan DESC";
$ambil_data = $con->query($sql_data);
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Laporan Riwayat Perubahan Harga</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-3">
                    <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan</h5></div>
                    <div class="card-body">
                        <form action="?page=laporan&aksi=laporan_perubahan_harga" method="POST">
                            <div class="row align-items-end">
                                <div class="col-md-3"><label>Dari Tanggal</label><input type="date" name="tanggal_mulai" class="form-control" value="<?= $tgl_mulai_filter; ?>"></div>
                                <div class="col-md-3"><label>Sampai Tanggal</label><input type="date" name="tanggal_selesai" class="form-control" value="<?= $tgl_selesai_filter; ?>"></div>
                                <div class="col-md-3">
                                    <label>Produk</label>
                                    <select name="id_produk" class="form-control">
                                        <option value="">Semua</option>
                                        <?php $q_produk = $con->query("SELECT id_produk, nama_produk FROM produk ORDER BY nama_produk"); while($p = $q_produk->fetch_assoc()): ?>
                                            <option value="<?= $p['id_produk'] ?>" <?= $produk_filter == $p['id_produk'] ? 'selected' : '' ?>><?= $p['nama_produk'] ?></option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label>Petani</label>
                                    <select name="id_petani" class="form-control">
                                        <option value="">Semua</option>
                                        <?php $q_petani = $con->query("SELECT id_petani, nama_petani FROM petani ORDER BY nama_petani"); while($pt = $q_petani->fetch_assoc()): ?>
                                            <option value="<?= $pt['id_petani'] ?>" <?= $petani_filter == $pt['id_petani'] ? 'selected' : '' ?>><?= $pt['nama_petani'] ?></option>
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
                        <h5 class="card-title mb-0"><i class="fas fa-table me-2"></i>Hasil Laporan Perubahan Harga</h5>
                        <form action="page/laporan/cetak_perubahan_harga_pdf.php" method="POST" target="_blank" class="m-0">
                            <input type="hidden" name="tanggal_mulai" value="<?= htmlspecialchars($tgl_mulai_filter); ?>">
                            <input type="hidden" name="tanggal_selesai" value="<?= htmlspecialchars($tgl_selesai_filter); ?>">
                            <input type="hidden" name="id_produk" value="<?= htmlspecialchars($produk_filter); ?>">
                            <input type="hidden" name="id_petani" value="<?= htmlspecialchars($petani_filter); ?>">
                            <button type="submit" class="btn btn-success"><i class="fas fa-print me-1"></i> Cetak Laporan PDF</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th>Tanggal Update</th>
                                        <th>Nama Produk</th>
                                        <th>Petani</th>
                                        <th class="text-end">Harga Lama</th>
                                        <th class="text-end">Harga Baru</th>
                                        <th class="text-end">Perubahan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($ambil_data->num_rows > 0): $nomor = 1; while ($riwayat = $ambil_data->fetch_assoc()): 
                                        $perubahan = $riwayat['harga_baru'] - $riwayat['harga_lama'];
                                        $class_perubahan = $perubahan > 0 ? 'text-success' : 'text-danger';
                                        $icon_perubahan = $perubahan > 0 ? 'fa-arrow-up' : 'fa-arrow-down';
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $nomor++; ?></td>
                                        <td><?= date("d M Y, H:i", strtotime($riwayat['tgl_perubahan'])); ?></td>
                                        <td><?= htmlspecialchars($riwayat['nama_produk']); ?></td>
                                        <td><?= htmlspecialchars($riwayat['nama_petani']); ?></td>
                                        <td class="text-end">Rp <?= number_format($riwayat['harga_lama']); ?></td>
                                        <td class="text-end">Rp <?= number_format($riwayat['harga_baru']); ?></td>
                                        <td class="text-end <?= $class_perubahan; ?>">
                                            <i class="fas <?= $icon_perubahan; ?>"></i>
                                            Rp <?= number_format(abs($perubahan)); ?>
                                        </td>
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