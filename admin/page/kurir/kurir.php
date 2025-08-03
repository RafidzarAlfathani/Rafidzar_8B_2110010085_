<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Data Kurir</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">
                        Daftar Kurir Pengiriman
                        <a href="?page=kurir&aksi=tambah" class="btn btn-primary btn-sm float-end">Tambah Kurir</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-dashed table-bordered table-hover digi-dataTable table-striped" id="componentDataTable">
                            <thead>
                                <tr>
                                    <th width="5" class="text-center">No.</th>
                                    <th>Nama Kurir</th>
                                    <th>Username</th>
                                    <th>Telepon</th>
                                    <th class="text-center">Status</th>
                                    <th width="10" class="text-center">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $nomor = 1;
                                $ambil = $con->query("SELECT * FROM kurir ORDER BY id_kurir DESC");
                                while ($row = $ambil->fetch_assoc()) { ?>
                                    <tr>
                                        <td class="text-center"><?= $nomor++; ?></td>
                                        <td><?= $row['nama_kurir']; ?></td>
                                        <td><?= $row['username']; ?></td>
                                        <td><?= $row['telp']; ?></td>
                                        <td class="text-center"><?= $row['status']; ?></td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-warning dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-regular fa-cogs"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="?page=kurir&aksi=ubah&id_kurir=<?= $row['id_kurir'] ?>">Ubah</a></li>
                                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="confirmDelete(<?= $row['id_kurir']; ?>)">Hapus</a></li>
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
function confirmDelete(id_kurir) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        html: "Menghapus kurir akan melepaskan keterkaitan kurir dari semua riwayat pesanan yang pernah ditangani.<br><br><b>Data pesanan tidak akan hilang.</b>",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "?page=kurir&aksi=hapus&id_kurir=" + id_kurir;
        }
    });
}
</script>