<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Data Pesanan Masuk</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">
                        Daftar Semua Pesanan
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dashed table-bordered table-hover digi-dataTable table-striped" id="componentDataTable">
                                <thead>
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th>Invoice</th>
                                        <th>Pembeli</th>
                                        <th>Tanggal Pesan</th>
                                        <th>Total Produk</th>
                                        <th>Ongkir</th>
                                        <th>Biaya Admin</th>
                                        <th>Total Bayar</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">#</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $nomor = 1;
                                    $user_level = $_SESSION['user_level'];
                                    $user_id = $_SESSION['user_id'];

                                    $query = "";
                                    if ($user_level == 'Admin' || $user_level == 'Pimpinan') {
                                        $query = "SELECT pesanan.*, pembeli.nama_pembeli
                                                  FROM pesanan
                                                  JOIN pembeli ON pesanan.id_pembeli = pembeli.id_pembeli";
                                    } elseif ($user_level == 'Petani') {
                                        $query = "SELECT DISTINCT pesanan.*, pembeli.nama_pembeli
                                                  FROM pesanan
                                                  JOIN pembeli ON pesanan.id_pembeli = pembeli.id_pembeli
                                                  JOIN detail_pesanan ON pesanan.id_pesanan = detail_pesanan.id_pesanan
                                                  JOIN produk ON detail_pesanan.id_produk = produk.id_produk
                                                  WHERE produk.id_petani = '$user_id'";
                                    } elseif ($user_level == 'Kurir') {
                                        $query = "SELECT pesanan.*, pembeli.nama_pembeli
                                                  FROM pesanan
                                                  JOIN pembeli ON pesanan.id_pembeli = pembeli.id_pembeli
                                                  WHERE pesanan.id_kurir = '$user_id'";
                                    }

                                    $query .= " ORDER BY pesanan.tgl_pesan DESC";
                                    $ambil = $con->query($query);
                                    
                                    while ($row = $ambil->fetch_assoc()) {
                                        $status = $row['status_pesanan'];
                                        $badge_color = 'bg-secondary';
                                        if ($status == 'Menunggu Pembayaran' || $status == 'Menunggu Verifikasi') {
                                            $badge_color = 'bg-warning';
                                        } elseif ($status == 'Diproses' || $status == 'Dikirim') {
                                            $badge_color = 'bg-info';
                                        } elseif ($status == 'Selesai') {
                                            $badge_color = 'bg-success';
                                        } elseif ($status == 'Dibatalkan') {
                                            $badge_color = 'bg-danger';
                                        }

                                        $total_bayar = $row['total_bayar'];
                                        $ongkir = $row['ongkir'];
                                        $biaya_admin = $row['biaya_admin'];
                                        $total_produk = $total_bayar - $ongkir - $biaya_admin;
                                    ?>
                                        <tr>
                                            <td class="text-center"><?= $nomor++; ?></td>
                                            <td><?= $row['kode_invoice']; ?></td>
                                            <td><?= $row['nama_pembeli']; ?></td>
                                            <td><?= date("d M Y, H:i", strtotime($row['tgl_pesan'])); ?></td>
                                            <td>Rp <?= number_format($total_produk); ?></td>
                                            <td>Rp <?= number_format($ongkir); ?></td>
                                            <td>Rp <?= number_format($biaya_admin); ?></td>
                                            <td><strong>Rp <?= number_format($total_bayar); ?></strong></td>
                                            <td class="text-center">
                                                <span class="badge <?= $badge_color; ?>"><?= $status; ?></span>
                                            </td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-primary dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                        Aksi
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a class="dropdown-item" href="?page=pesanan&aksi=detail&id_pesanan=<?= $row['id_pesanan'] ?>">Lihat Detail</a></li>
                                                        <?php if ($user_level == 'Admin' || $user_level == 'Pimpinan'): ?>
                                                            <li><a class="dropdown-item" href="javascript:void(0)" onclick="confirmDelete(<?= $row['id_pesanan']; ?>)">Hapus</a></li>
                                                        <?php endif; ?>
                                                    </ul>
                                                </div>
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
function confirmDelete(id_pesanan) {
    Swal.fire({
        title: 'ANDA YAKIN?',
        html: "Menghapus pesanan adalah tindakan permanen dan akan menghapus semua data terkait (detail item, tracking, dll).<br><br><b>Tindakan ini tidak bisa dibatalkan!</b>",
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, saya mengerti dan ingin hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "?page=pesanan&aksi=hapus&id_pesanan=" + id_pesanan;
        }
    });
}
</script>
