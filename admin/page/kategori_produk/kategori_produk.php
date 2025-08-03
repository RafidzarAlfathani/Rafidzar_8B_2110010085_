<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Data Kategori Produk</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">
                        Daftar Kategori Produk
                        <a href="?page=kategori_produk&aksi=tambah" class="btn btn-primary btn-sm float-end">Tambah Kategori</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-dashed table-bordered table-hover digi-dataTable table-striped" id="componentDataTable">
                            <thead>
                                <tr>
                                    <th width="5" class="text-center">No.</th>
                                    <th>Nama Kategori</th>
                                    <th width="10" class="text-center">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $nomor = 1;
                                $ambil = $con->query("SELECT * FROM kategori_produk ORDER BY nama_kategori ASC");
                                while ($row = $ambil->fetch_assoc()) { ?>
                                    <tr>
                                        <td class="text-center"><?= $nomor++; ?></td>
                                        <td><?= $row['nama_kategori']; ?></td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-warning dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-regular fa-cogs"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="?page=kategori_produk&aksi=ubah&id_kategori=<?= $row['id_kategori'] ?>">Ubah</a></li>
                                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="confirmDelete(<?= $row['id_kategori']; ?>)">Hapus</a></li>
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
function confirmDelete(id_kategori) {
    Swal.fire({
        title: 'ANDA YAKIN?',
        // Peringatan penting karena adanya ON DELETE CASCADE di database
        html: "<b>PERHATIAN:</b> Menghapus kategori ini akan <b>MENGHAPUS SEMUA PRODUK</b> yang berada di dalamnya!<br><br>Tindakan ini tidak bisa dibatalkan.",
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, saya mengerti dan ingin hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "?page=kategori_produk&aksi=hapus&id_kategori=" + id_kategori;
        }
    });
}
</script>