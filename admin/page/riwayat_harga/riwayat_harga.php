<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Riwayat Perubahan Harga</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">
                        Riwayat Harga per Produk
                        <a href="?page=produk" class="btn btn-primary btn-sm float-end">Ubah Harga Produk</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center">No.</th>
                                        <th>Nama Produk</th>
                                        <?php if ($_SESSION['user_level'] != 'Petani'): ?>
                                            <th>Petani</th>
                                        <?php endif; ?>
                                        <th>Tanggal Input Produk</th>
                                        <th>Riwayat Perubahan Harga</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $nomor = 1;

                                    // 🔑 Kunci: Query utama untuk mengambil produk disesuaikan dengan level user
                                    $query_produk = "SELECT produk.*, petani.nama_petani FROM produk JOIN petani ON produk.id_petani = petani.id_petani";
                                    
                                    // 👨‍🌾 Jika yang login adalah Petani, filter produknya
                                    if ($_SESSION['user_level'] == 'Petani') {
                                        $id_petani_login = $_SESSION['user_id'];
                                        $query_produk .= " WHERE produk.id_petani = '$id_petani_login'";
                                    }
                                    
                                    $query_produk .= " ORDER BY produk.nama_produk ASC";
                                    $ambil_produk = $con->query($query_produk);

                                    while ($produk = $ambil_produk->fetch_assoc()) {
                                    ?>
                                        <tr>
                                            <td class="text-center align-top"><?= $nomor++; ?></td>
                                            <td class="align-top"><?= htmlspecialchars($produk['nama_produk']); ?></td>
                                            
                                            <?php if ($_SESSION['user_level'] != 'Petani'): ?>
                                                <td class="align-top"><?= htmlspecialchars($produk['nama_petani']); ?></td>
                                            <?php endif; ?>

                                            <td class="align-top"><?= date("d F Y", strtotime($produk['tgl_upload'])); ?></td>
                                            <td>
                                                <?php
                                                // Logika untuk mengambil riwayat harga per produk (tidak perlu diubah)
                                                $id_produk_saat_ini = $produk['id_produk'];
                                                $ambil_riwayat = $con->query("SELECT * FROM riwayat_harga WHERE id_produk = '$id_produk_saat_ini' ORDER BY tgl_perubahan DESC");

                                                if ($ambil_riwayat->num_rows > 0) {
                                                ?>
                                                    <table class="table table-sm table-striped table-bordered mb-0">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th class="text-white">Harga Lama</th>
                                                                <th class="text-white">Harga Baru</th>
                                                                <th class="text-white">Tanggal Update</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            while ($riwayat = $ambil_riwayat->fetch_assoc()) {
                                                            ?>
                                                                <tr>
                                                                    <td>Rp <?= number_format($riwayat['harga_lama']); ?></td>
                                                                    <td>Rp <?= number_format($riwayat['harga_baru']); ?></td>
                                                                    <td><?= date("d F Y", strtotime($riwayat['tgl_perubahan'])); ?></td>
                                                                </tr>
                                                            <?php } ?>
                                                        </tbody>
                                                    </table>
                                                <?php
                                                } else {
                                                    echo "<span class='text-muted fst-italic'>- Belum ada riwayat perubahan harga -</span>";
                                                }
                                                ?>
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