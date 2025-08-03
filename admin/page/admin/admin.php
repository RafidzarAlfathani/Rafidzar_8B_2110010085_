<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Admin</h5>
            </div>
            <div class="panel-body">  
                <div class="card mb-20">
                    <div class="card-header">
                        Data Admin
                        <a href="?page=admin&aksi=tambah" class="btn btn-primary btn-sm float-end">Tambah</a>
                    </div>
                    <div class="card-body">
                        <table class="table table-dashed table-bordered table-hover digi-dataTable table-striped" id="componentDataTable">
                            <thead>
                                <tr>
                                    <th width="5" class="text-center">No.</th>
                                    <th>Nama</th>
                                    <th>Username</th>
                                    <th class="text-center">Level</th>
                                    <th class="text-center">Status</th> 
                                    <th width="10" class="text-center">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $nomor = 1; 
                                $ambil = $con->query("SELECT * FROM admin ORDER BY id_admin ASC"); 
                                while ($row = $ambil->fetch_assoc()) { ?>
                                    <tr>
                                        <td class="text-center"><?= $nomor; ?></td>
                                        <td><?= $row['nama']; ?></td>
                                        <td><?= $row['username']; ?></td>
                                        <td class="text-center"><?= $row['level']; ?></td>
                                        <td class="text-center"><?= $row['status']; ?></td> 
                                        <td class="text-center">
                                            <div class="dropdown">
										        <button class="btn btn-warning dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
										            <i class="fa-regular fa-cogs"></i>
										        </button>
										        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										            <li><a class="dropdown-item" href="?page=admin&aksi=ubah&id_admin=<?= $row['id_admin'] ?>">Ubah</a></li>
										            <li><a class="dropdown-item" href="javascript:void(0)" onclick="confirmDelete(<?= $row['id_admin']; ?>)">Hapus</a></li>
										        </ul>
										    </div>
                                        </td>
                                    </tr>
                                <?php 
                                $nomor++; 
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</div> 

<script>
function confirmDelete(id_admin) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda tidak bisa mengembalikan data yang telah dihapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "?page=admin&aksi=hapus&id_admin=" + id_admin;
        }
    });
}
</script> 

