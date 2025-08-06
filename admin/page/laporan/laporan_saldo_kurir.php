<?php
include "inc/koneksi.php";

// Ambil filter jika ada
$id_kurir_filter = isset($_POST['id_kurir']) && !empty($_POST['id_kurir']) ? (int)$_POST['id_kurir'] : '';

// Query utama data saldo kurir
$sql_data = "SELECT 
                k.id_kurir,
                k.nama_kurir,
                COALESCE((
                    SELECT SUM(p.ongkir) 
                    FROM pesanan p 
                    WHERE p.id_kurir = k.id_kurir AND p.status_pesanan = 'Selesai'
                ), 0) AS total_pendapatan,
                COALESCE((
                    SELECT SUM(pk.jumlah_dana) 
                    FROM pengajuan_dana_kurir pk 
                    WHERE pk.id_kurir = k.id_kurir AND pk.status = 'Disetujui'
                ), 0) AS total_tarik
            FROM kurir k";

if ($id_kurir_filter) {
    $sql_data .= " WHERE k.id_kurir = $id_kurir_filter";
}

$sql_data .= " ORDER BY k.nama_kurir ASC";
$ambil_data = $con->query($sql_data);
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Laporan Saldo Kurir</h5>
            </div>
            <div class="panel-body">

                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filter Kurir</h5>

                    </div>

                    <div class="card-body">
                        <form action="?page=laporan&aksi=laporan_saldo_kurir" method="POST">
                            <div class="row">
                                <div class="col-md-10">
                                    <label class="form-label">Pilih Kurir</label>
                                    <select name="id_kurir" class="form-control">
                                        <option value="">-- Tampilkan Semua Kurir --</option>
                                        <?php
                                        $ambil_kurir = $con->query("SELECT id_kurir, nama_kurir FROM kurir ORDER BY nama_kurir ASC");
                                        while ($kurir = $ambil_kurir->fetch_assoc()) {
                                            $selected = ($kurir['id_kurir'] == $id_kurir_filter) ? 'selected' : '';
                                            echo "<option value='{$kurir['id_kurir']}' $selected>" . htmlspecialchars($kurir['nama_kurir']) . "</option>";
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
                        <h5 class="card-title mb-0"><i class="fas fa-wallet me-2"></i>Data Saldo Kurir</h5>
                        <form action="page/laporan/cetak_saldo_kurir_pdf.php" method="POST" target="_blank" class="m-0">
                            <button type="submit" class="btn btn-success"><i class="fas fa-print me-1"></i> Cetak PDF</button>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th>Nama Kurir</th>
                                        <th class="text-end">Total Pendapatan</th>
                                        <th class="text-end">Total Penarikan</th>
                                        <th class="text-end">Sisa Saldo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($ambil_data->num_rows > 0): $no = 1;
                                        while ($row = $ambil_data->fetch_assoc()):
                                            $sisa_saldo = $row['total_pendapatan'] - $row['total_tarik'];
                                            if ($sisa_saldo < 0) $sisa_saldo = 0;
                                    ?>
                                            <tr>
                                                <td class="text-center"><?= $no++; ?></td>
                                                <td><?= htmlspecialchars($row['nama_kurir']); ?></td>
                                                <td class="text-end">Rp <?= number_format($row['total_pendapatan'], 0, ',', '.'); ?></td>
                                                <td class="text-end">Rp <?= number_format($row['total_tarik'], 0, ',', '.'); ?></td>
                                                <td class="text-end text-success fw-bold">Rp <?= number_format($sisa_saldo, 0, ',', '.'); ?></td>
                                            </tr>
                                        <?php endwhile;
                                    else: ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Data tidak ditemukan.</td>
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