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
                                    <?php
                                    // 🔑 Kunci: Hanya tampilkan kolom 'Petani' jika yang login bukan Petani
                                    if ($_SESSION['user_level'] != 'Petani') {
                                        echo "<th>Petani</th>";
                                    }
                                    ?>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $nomor = 1;

                                // 🔑 Kunci: Membangun query secara dinamis berdasarkan level user
                                $query = "SELECT
                                    produk.*,
                                    petani.nama_petani,
                                    kategori_produk.nama_kategori
                                    FROM produk
                                    JOIN petani ON produk.id_petani = petani.id_petani
                                    JOIN kategori_produk ON produk.id_kategori = kategori_produk.id_kategori";
                                
                                // 👨‍🌾 Jika yang login adalah Petani, filter berdasarkan ID-nya
                                if ($_SESSION['user_level'] == 'Petani') {
                                    $id_petani_login = $_SESSION['user_id'];
                                    $query .= " WHERE produk.id_petani = '$id_petani_login'";
                                }
                                
                                $query .= " ORDER BY produk.id_produk DESC";

                                $ambil = $con->query($query);
                                while ($row = $ambil->fetch_assoc()) {
                                ?>
                                    <tr>
                                        <td class="text-center"><?= $nomor++; ?></td>
                                        <td>
                                            <img src="images/produk/<?= $row['foto_produk']; ?>" width="50" class="me-2">
                                            <?= $row['nama_produk']; ?>
                                        </td>
                                        <?php
                                        // 🔑 Kunci: Hanya tampilkan data 'Petani' jika yang login bukan Petani
                                        if ($_SESSION['user_level'] != 'Petani') {
                                            echo "<td>" . $row['nama_petani'] . "</td>";
                                        }
                                        ?>
                                        <td><?= $row['nama_kategori']; ?></td>
                                        <td>Rp <?= number_format($row['harga']); ?></td>
                                        <td><?= $row['stok']; ?> <?= $row['satuan']; ?></td>
                                        <td class="text-center"><?= $row['status_produk']; ?></td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-warning dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-regular fa-cogs"></i>
                                                </button>
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
        text: "Menghapus produk juga akan menghapus riwayat harga terkait!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "?page=produk&aksi=hapus&id_produk=" + id_produk;
        }
    });
}
</script>