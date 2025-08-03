<?php
$id_pembeli = $_GET['id_pembeli'];
$sql = $con->query("SELECT * FROM pembeli WHERE id_pembeli='$id_pembeli'");
$row = mysqli_fetch_assoc($sql);
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Manajemen Pembeli</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">Ubah Data Pembeli <b>"<?= $row['nama_pembeli']; ?>"</b></div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Nama Pembeli</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="nama_pembeli" value="<?= $row['nama_pembeli']; ?>" required>
                                </div>
                            </div>
                             <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="email" value="<?= $row['email']; ?>" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" name="password" placeholder="Kosongkan jika tidak ingin mengubah password">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Telepon</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="telp" value="<?= $row['telp']; ?>" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Foto Profil</label>
                                <div class="col-sm-9">
                                    <img src="images/pembeli/<?= $row['foto_pembeli']; ?>" width="100"> <br>
                                    <input type="file" class="form-control" name="foto_pembeli">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-9">
                                    <button type="submit" name="ubah" class="btn btn-success btn-sm">Ubah</button>
                                    <a href="?page=pembeli" class="btn btn-danger btn-sm">Kembali</a>
                                </div>
                            </div>
                        </form>
                        <?php
                            if (isset($_POST['ubah'])) {
                                $nama_pembeli = mysqli_real_escape_string($con, $_POST['nama_pembeli']);
                                $email = mysqli_real_escape_string($con, $_POST['email']);
                                $telp = mysqli_real_escape_string($con, $_POST['telp']);
                                $foto_pembeli = $_FILES['foto_pembeli']['name'];

                                // Logika untuk update foto
                                if ($foto_pembeli != '') {
                                    if ($row['foto_pembeli'] != '' && $row['foto_pembeli'] != 'default.png') {
                                        unlink("images/pembeli/" . $row['foto_pembeli']);
                                    }
                                    $foto_tmp = $_FILES['foto_pembeli']['tmp_name'];
                                    $foto_dir = "images/pembeli/" . $foto_pembeli;
                                    move_uploaded_file($foto_tmp, $foto_dir);
                                } else {
                                    $foto_pembeli = $row['foto_pembeli'];
                                }

                                // Logika untuk update password (jika diisi)
                                $password = $_POST['password'];
                                if (!empty($password)) {
                                    $password_update = ", password='" . mysqli_real_escape_string($con, $password) . "'";
                                } else {
                                    $password_update = "";
                                }

                                $query = "UPDATE pembeli SET
                                            nama_pembeli = '$nama_pembeli',
                                            email = '$email',
                                            telp = '$telp',
                                            foto_pembeli = '$foto_pembeli'
                                            $password_update
                                          WHERE id_pembeli='$id_pembeli'";

                                if ($con->query($query) === TRUE) {
                                    echo "<script>
                                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data pembeli berhasil diperbarui.' })
                                            .then((result) => { if (result.isConfirmed) { window.location.href = '?page=pembeli'; } });
                                          </script>";
                                } else {
                                    echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan atau email sudah terdaftar.' }); </script>";
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>