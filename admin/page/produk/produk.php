<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Data Produk</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">
                        Daftar Produk Hasil Tani
                        <a href="?page=produk&aksi=tambah" class="btn btn-primary btn-sm float-end">Tambah Produk</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-dashed table-bordered table-hover digi-dataTable table-striped" id="componentDataTable">
                            <thead>
                                <tr>
                                    <th class="text-center">No.</th>
                                    <th>Produk</th>
                                    <?php if ($_SESSION['user_level'] != 'Petani') echo "<th>Petani</th>"; ?>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Tgl Panen</th> <th class="text-center">Status</th>
                                    <th class="text-center">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $nomor = 1;
                                $query = "SELECT p.*, pt.nama_petani, kp.nama_kategori FROM produk p
                                          JOIN petani pt ON p.id_petani = pt.id_petani
                                          JOIN kategori_produk kp ON p.id_kategori = kp.id_kategori";
                                
                                if ($_SESSION['user_level'] == 'Petani') {
                                    $id_petani_login = $_SESSION['user_id'];
                                    $query .= " WHERE p.id_petani = '$id_petani_login'";
                                }
                                $query .= " ORDER BY p.id_produk DESC";

                                $ambil = $con->query($query);
                                while ($row = $ambil->fetch_assoc()) {
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $nomor++; ?></td>
                                        <td>
                                            <img src="images/produk/<?= $row['foto_produk']; ?>" width="50" class="me-2">
                                            <?= $row['nama_produk']; ?>
                                        </td>
                                        <?php if ($_SESSION['user_level'] != 'Petani') echo "<td>" . $row['nama_petani'] . "</td>"; ?>
                                        <td><?= $row['nama_kategori']; ?></td>
                                        <td>Rp <?= number_format($row['harga']); ?></td>
                                        <td><?= $row['stok']; ?> <?= $row['satuan']; ?></td>
                                        
                                        <td>
                                            <?php
                                            if (!empty($row['tanggal_panen'])) {
                                                echo date('d M Y', strtotime($row['tanggal_panen']));
                                            } else {
                                                echo "-";
                                            }
                                            ?>
                                        </td>
                                        
                                        <td class="text-center"><?= $row['status_produk']; ?></td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-warning dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa fa-cogs"></i> </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="?page=produk&aksi=ubah&id_produk=<?= $row['id_produk'] ?>">Ubah</a></li>
                                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="confirmDelete(<?= $row['id_produk']; ?>)">Hapus</a></li>
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

<script>
function confirmDelete(id_produk) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data produk yang dihapus tidak dapat dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "?page=produk&aksi=hapus&id_produk=" + id_produk;
        }
    });
}
</script>