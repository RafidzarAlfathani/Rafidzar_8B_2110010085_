<?php 
// --- LOGIKA PHP UNTUK MENGAMBIL DATA ---
$sql_data = "SELECT 
                pt.id_petani,
                pt.nama_petani,
                pt.telp,
                COALESCE(SUM(dp.sub_total), 0) AS total_pendapatan
             FROM petani pt
             LEFT JOIN produk pr ON pt.id_petani = pr.id_petani
             LEFT JOIN detail_pesanan dp ON pr.id_produk = dp.id_produk
             LEFT JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
             WHERE ps.status_pesanan = 'Selesai'
             GROUP BY pt.id_petani
             ORDER BY total_pendapatan DESC";

$ambil_data = $con->query($sql_data);
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Laporan Sisa Saldo Petani</h5>
            </div>
            <div class="panel-body">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0"><i class="fas fa-wallet me-2"></i>Hasil Laporan Sisa Saldo Petani</h5>
                        <form action="page/laporan/cetak_saldo_petani_pdf.php" method="POST" target="_blank" class="m-0">
                            <button type="submit" class="btn btn-success"><i class="fas fa-print me-1"></i> Cetak PDF</button>
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
                                        <th class="text-end">Sisa Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($ambil_data->num_rows > 0): 
                                        $nomor = 1; 
                                        while ($petani = $ambil_data->fetch_assoc()):
                                            // Ambil total dana yang sudah ditarik oleh petani ini
                                            $id_petani = $petani['id_petani'];
                                            $query_tarik = $con->query("SELECT SUM(jumlah_dana) as total_tarik FROM pengajuan_dana_petani WHERE id_petani = '$id_petani' AND status = 'Disetujui'");
                                            $dana_ditarik = $query_tarik->fetch_assoc()['total_tarik'] ?? 0;

                                            $sisa_saldo = $petani['total_pendapatan'] - $dana_ditarik;
                                            if ($sisa_saldo < 0) $sisa_saldo = 0;
                                    ?>
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
                                        <td class="text-end"><strong>Rp <?= number_format($sisa_saldo, 0, ',', '.'); ?></strong></td>
                                    </tr>
                                    <?php endwhile; else: ?>
                                    <tr><td colspan="4" class="text-center">Data tidak ditemukan.</td></tr>
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
