<?php
// 🔑 Kunci: Pengecekan hak akses diubah untuk mengizinkan Petani
$user_level = $_SESSION['user_level'];
$user_id = $_SESSION['user_id'];

// Hanya Kurir yang benar-benar tidak boleh masuk
if ($user_level == 'Kurir') {
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Akses Ditolak!',
                text: 'Anda tidak memiliki izin untuk mengakses halaman ini.'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php';
                }
            });
          </script>";
    exit; 
}
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Data Petani</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">
                        Daftar Petani Terdaftar
                        <?php
                        // 🔑 Kunci: Tombol "Tambah" hanya untuk Admin
                        if ($user_level == 'Admin' || $user_level == 'Pimpinan') {
                            echo '<a href="?page=petani&aksi=tambah" class="btn btn-primary btn-sm float-end">Tambah</a>';
                        }
                        ?>
                    </div>
                    <div class="card-body">
                        <table class="table table-dashed table-bordered table-hover digi-dataTable table-striped" id="componentDataTable">
                            <thead>
                                <tr>
                                    <th width="5" class="text-center">No.</th>
                                    <th>Nama Petani</th>
                                    <th>Email</th>
                                    <th>Telepon</th>
                                    <th>Status Akun</th>
                                    <th width="10" class="text-center">#</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $nomor = 1;

                                // 🔑 Kunci: Query dinamis berdasarkan peran
                                $query = "SELECT * FROM petani";
                                if ($user_level == 'Petani') {
                                    // Jika Petani, hanya tampilkan datanya sendiri
                                    $query .= " WHERE id_petani = '$user_id'";
                                }
                                $query .= " ORDER BY id_petani DESC";
                                
                                $ambil = $con->query($query);
                                while ($row = $ambil->fetch_assoc()) { ?>
                                    <tr>
                                        <td class="text-center"><?= $nomor; ?></td>
                                        <td><?= $row['nama_petani']; ?></td>
                                        <td><?= $row['email']; ?></td>
                                        <td><?= $row['telp']; ?></td>
                                        <td class="text-center"><?= $row['status_akun']; ?></td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-warning dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-regular fa-cogs"></i>
                                                </button>
                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                    <li><a class="dropdown-item" href="?page=petani&aksi=ubah&id_petani=<?= $row['id_petani'] ?>">Ubah</a></li>
                                                    
                                                    <?php
                                                    // 🔑 Kunci: Opsi "Hapus" hanya untuk Admin
                                                    if ($user_level == 'Admin' || $user_level == 'Pimpinan') {
                                                        echo '<li><a class="dropdown-item" href="javascript:void(0)" onclick="confirmDelete('.$row['id_petani'].')">Hapus</a></li>';
                                                    }
                                                    ?>
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
function confirmDelete(id_petani) {
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
            window.location.href = "?page=petani&aksi=hapus&id_petani=" + id_petani;
        }
    });
}
</script>