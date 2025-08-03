<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Data Pembeli</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">
                        Daftar Pembeli Terdaftar
                        <a href="?page=pembeli&aksi=tambah" class="btn btn-primary btn-sm float-end">Tambah Pembeli</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-dashed table-bordered table-hover digi-dataTable table-striped" id="componentDataTable">
                            <thead>
                                <tr>
                                    <th width="5" class="text-center">No.</th>
                                    <th>Nama Pembeli</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Tgl. Daftar</th>
                                    <th width="10" class="text-center">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $nomor = 1;
                                $ambil = $con->query("SELECT * FROM pembeli ORDER BY id_pembeli DESC");
                                while ($row = $ambil->fetch_assoc()) { ?>
                                    <tr>
                                        <td class="text-center"><?= $nomor++; ?></td>
                                        <td>
                                            <img src="images/pembeli/<?= $row['foto_pembeli']; ?>" width="40" class="me-2 rounded-circle">
                                            <?= $row['nama_pembeli']; ?>
                                        </td>
                                        <td><?= $row['email']; ?></td>
                                        <td><?= $row['telp']; ?></td>
                                        <td><?= date("d M Y, H:i", strtotime($row['tgl_daftar'])); ?></td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-warning dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-regular fa-cogs"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="?page=pembeli&aksi=ubah&id_pembeli=<?= $row['id_pembeli'] ?>">Ubah</a></li>
                                                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="confirmDelete(<?= $row['id_pembeli']; ?>)">Hapus</a></li>
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
function confirmDelete(id_pembeli) {
    Swal.fire({
        title: 'ANDA YAKIN?',
        // Peringatan penting karena adanya ON DELETE CASCADE di database
        html: "<b>PERHATIAN:</b> Menghapus data pembeli akan <b>MENGHAPUS SEMUA RIWAYAT PESANAN</b> yang pernah dilakukannya!<br><br>Tindakan ini tidak bisa dibatalkan.",
        icon: 'error',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Ya, saya mengerti dan ingin hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "?page=pembeli&aksi=hapus&id_pembeli=" + id_pembeli;
        }
    });
}
</script>