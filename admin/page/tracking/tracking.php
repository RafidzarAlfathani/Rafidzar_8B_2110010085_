<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Monitoring Tracking Pengiriman</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">
                        Daftar Pesanan dengan Riwayat Lacak
                        <?php
                        // 🔑 Kunci: Tombol "Tambah Titik Lacak" hanya untuk Admin dan Kurir
                        if ($_SESSION['user_level'] == 'Admin' || $_SESSION['user_level'] == 'Pimpinan' || $_SESSION['user_level'] == 'Kurir') {
                            echo '<a href="?page=tracking&aksi=tambah" class="btn btn-primary btn-sm float-end">Tambah Titik Lacak</a>';
                        }
                        ?>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th>Invoice</th>
                                        <th>Pembeli</th>
                                        <th>Status Pesanan</th>
                                        <th>Riwayat Lacak (Tracking History)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $nomor = 1;
                                    $user_id = $_SESSION['user_id'];
                                    $user_level = $_SESSION['user_level'];

                                    // 🔑 Kunci: Query utama untuk mengambil pesanan yang dilacak disesuaikan dengan peran
                                    $base_query = "SELECT DISTINCT ps.id_pesanan, ps.kode_invoice, pm.nama_pembeli, ps.status_pesanan
                                                   FROM pesanan ps
                                                   JOIN pembeli pm ON ps.id_pembeli = pm.id_pembeli
                                                   JOIN tracking_pengiriman tp ON ps.id_pesanan = tp.id_pesanan";
                                    
                                    if ($user_level == 'Petani') {
                                        // 👨‍🌾 Petani melihat tracking jika pesanan berisi produknya
                                        $base_query .= " JOIN detail_pesanan dp ON ps.id_pesanan = dp.id_pesanan
                                                         JOIN produk pr ON dp.id_produk = pr.id_produk
                                                         WHERE pr.id_petani = '$user_id'";
                                    } elseif ($user_level == 'Kurir') {
                                        // 🚚 Kurir melihat tracking untuk pesanan yang ditugaskan padanya
                                        $base_query .= " WHERE ps.id_kurir = '$user_id'";
                                    }
                                    
                                    $query_pesanan = $base_query . " ORDER BY ps.tgl_pesan DESC";
                                    $ambil_pesanan = $con->query($query_pesanan);

                                    while ($pesanan = $ambil_pesanan->fetch_assoc()) {
                                    ?>
                                        <tr>
                                            <td class="text-center align-top"><?= $nomor++; ?></td>
                                            <td class="align-top"><?= $pesanan['kode_invoice']; ?></td>
                                            <td class="align-top"><?= $pesanan['nama_pembeli']; ?></td>
                                            <td class="align-top text-center"><span class="badge bg-info"><?= $pesanan['status_pesanan']; ?></span></td>
                                            <td>
                                                <?php
                                                $id_pesanan_saat_ini = $pesanan['id_pesanan'];
                                                $query_tracking = "SELECT * FROM tracking_pengiriman WHERE id_pesanan = '$id_pesanan_saat_ini' ORDER BY waktu_update ASC";
                                                $ambil_tracking = $con->query($query_tracking);
                                                ?>
                                                <table class="table table-sm table-striped table-bordered mb-0">
                                                    <thead class="table-dark">
                                                        <tr>
                                                            <th class="text-white text-center">Waktu</th>
                                                            <th class="text-white">Keterangan</th>
                                                            <th class="text-white">Lokasi (Lat, Long)</th>
                                                            <?php if ($user_level != 'Petani'): // Petani tidak bisa hapus ?>
                                                            <th class="text-center text-white">#</th>
                                                            <?php endif; ?>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php while ($track = $ambil_tracking->fetch_assoc()) { ?>
                                                            <tr>
                                                                <td class="text-center"><?= date("d M Y, H:i", strtotime($track['waktu_update'])); ?></td>
                                                                <td><?= htmlspecialchars($track['keterangan']); ?></td>
                                                                <td><?= $track['latitude']; ?>, <?= $track['longitude']; ?></td>
                                                                <?php if ($user_level != 'Petani'): // Petani tidak bisa hapus ?>
                                                                <td class="text-center">
                                                                    <a href="javascript:void(0)" onclick="confirmDelete(<?= $track['id_tracking']; ?>)" class="btn btn-danger btn-sm p-1">
                                                                        <i class="fa-regular fa-trash-can"></i>
                                                                    </a>
                                                                </td>
                                                                <?php endif; ?>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id_tracking) {
    Swal.fire({
        title: 'Anda yakin ingin menghapus titik lacak ini?',
        text: "Tindakan ini tidak bisa dibatalkan.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "?page=tracking&aksi=hapus&id_tracking=" + id_tracking;
        }
    });
}
</script>