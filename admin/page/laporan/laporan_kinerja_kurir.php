<?php
// --- LOGIKA PHP UNTUK MENGAMBIL DATA ---

// Ambil filter dari form jika ada
$status_filter = isset($_POST['status']) ? $_POST['status'] : '';
$tgl_mulai_filter = isset($_POST['tanggal_mulai']) && !empty($_POST['tanggal_mulai']) ? $_POST['tanggal_mulai'] : '';
$tgl_selesai_filter = isset($_POST['tanggal_selesai']) && !empty($_POST['tanggal_selesai']) ? $_POST['tanggal_selesai'] : '';

// Bangun query SQL dengan subquery untuk menghitung kinerja
$sql_data = "SELECT 
                kr.*,
                (SELECT COUNT(id_pesanan) 
                 FROM pesanan ps 
                 WHERE ps.id_kurir = kr.id_kurir " .
                 ($tgl_mulai_filter && $tgl_selesai_filter ? "AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'" : "") .
                ") AS total_tugas,

                (SELECT COUNT(id_pesanan) 
                 FROM pesanan ps 
                 WHERE ps.id_kurir = kr.id_kurir AND ps.status_pesanan = 'Selesai' " .
                 ($tgl_mulai_filter && $tgl_selesai_filter ? "AND ps.tgl_pesan BETWEEN '$tgl_mulai_filter' AND '$tgl_selesai_filter'" : "") .
                ") AS tugas_selesai
             FROM kurir kr";

if ($status_filter) {
    $sql_data .= " WHERE kr.status = '$status_filter'";
}

$sql_data .= " ORDER BY tugas_selesai DESC, kr.nama_kurir ASC";
$ambil_data = $con->query($sql_data);
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Laporan Kinerja Kurir</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-3">
                    <div class="card-header"><h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filter Laporan</h5></div>
                    <div class="card-body">
                        <form action="?page=laporan&aksi=laporan_kinerja_kurir" method="POST">
                            <div class="row align-items-end">
                                <div class="col-md-4">
                                    <label class="form-label">Berdasarkan Status Akun</label>
                                    <select name="status" class="form-control">
                                        <option value="">-- Tampilkan Semua --</option>
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
                        <h5 class="card-title mb-0"><i class="fas fa-table me-2"></i>Hasil Laporan Kinerja Kurir</h5>
                        <form action="page/laporan/cetak_kinerja_kurir_pdf.php" method="POST" target="_blank" class="m-0">
                            <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter); ?>">
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
                                        <th>Nama Kurir</th>
                                        <th>Telepon</th> 
                                        <th class="text-center">Total Tugas</th>
                                        <th class="text-center">Tugas Selesai</th>
                                        <th class="text-center">Tingkat Penyelesaian</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($ambil_data->num_rows > 0): $nomor = 1; while ($kurir = $ambil_data->fetch_assoc()): 
                                        $tingkat_penyelesaian = ($kurir['total_tugas'] > 0) ? ($kurir['tugas_selesai'] / $kurir['total_tugas']) * 100 : 0;
                                    ?>
                                    <tr>
                                        <td class="text-center"><?= $nomor++; ?></td>
                                        <td><?= htmlspecialchars($kurir['nama_kurir']); ?></td>
                                        <td><?= htmlspecialchars($kurir['telp']); ?></td> 
                                        <td class="text-center"><?= number_format($kurir['total_tugas']); ?></td>
                                        <td class="text-center"><?= number_format($kurir['tugas_selesai']); ?></td>
                                        <td class="text-center">
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar" role="progressbar" style="width: <?= $tingkat_penyelesaian; ?>%;" aria-valuenow="<?= $tingkat_penyelesaian; ?>" aria-valuemin="0" aria-valuemax="100"><?= number_format($tingkat_penyelesaian, 1); ?>%</div>
                                            </div>
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