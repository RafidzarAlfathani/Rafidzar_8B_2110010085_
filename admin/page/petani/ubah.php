<?php
$id_petani = $_GET['id_petani'];
$sql = $con->query("SELECT * FROM petani WHERE id_petani='$id_petani'");
$row = mysqli_fetch_assoc($sql);
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Data Petani</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">Ubah Data Petani <b>"<?= $row['nama_petani']; ?>"</b></div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Nama Petani</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="nama_petani" value="<?= $row['nama_petani']; ?>" required>
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
                                <label class="col-sm-3 col-form-label">Alamat</label>
                                <div class="col-sm-9">
                                    <textarea name="alamat_petani" class="form-control" required><?= $row['alamat_petani']; ?></textarea>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Status Akun</label>
                                <div class="col-sm-9">
                                    <select name="status_akun" class="form-control">
                                        <option value="Menunggu Verifikasi" <?= ($row['status_akun'] == 'Menunggu Verifikasi') ? 'selected' : ''; ?>>Menunggu Verifikasi</option>
                                        <option value="Aktif" <?= ($row['status_akun'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                                        <option value="Non-Aktif" <?= ($row['status_akun'] == 'Non-Aktif') ? 'selected' : ''; ?>>Non-Aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Foto</label>
                                <div class="col-sm-9">
                                    <img src="images/petani/<?= $row['foto_petani']; ?>" width="100"> <br>
                                    <input type="file" class="form-control" name="foto_petani">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-9">
                                    <button type="submit" name="ubah" class="btn btn-success btn-sm">Ubah</button>
                                    <a href="?page=petani" class="btn btn-danger btn-sm">Kembali</a>
                                </div>
                            </div>
                        </form>
                        <?php
                            if (isset($_POST['ubah'])) {
                                $nama_petani = mysqli_real_escape_string($con, $_POST['nama_petani']);
                                $email = mysqli_real_escape_string($con, $_POST['email']);
                                $telp = mysqli_real_escape_string($con, $_POST['telp']);
                                $alamat_petani = mysqli_real_escape_string($con, $_POST['alamat_petani']);
                                $status_akun = mysqli_real_escape_string($con, $_POST['status_akun']);
                                $foto_petani = $_FILES['foto_petani']['name'];

                                // Logika untuk update foto
                                if ($foto_petani != '') {
                                    if ($row['foto_petani'] != '' && $row['foto_petani'] != 'default.png') {
                                        unlink("images/petani/" . $row['foto_petani']);
                                    }
                                    $foto_tmp = $_FILES['foto_petani']['tmp_name'];
                                    $foto_dir = "images/petani/" . $foto_petani;
                                    move_uploaded_file($foto_tmp, $foto_dir);
                                } else {
                                    $foto_petani = $row['foto_petani'];
                                }

                                // Logika untuk update password (jika diisi)
                                $password = $_POST['password'];
                                if (!empty($password)) {
                                    $password_update = ", password='" . mysqli_real_escape_string($con, $password) . "'";
                                } else {
                                    $password_update = "";
                                }

                                $query = "UPDATE petani SET
                                            nama_petani = '$nama_petani',
                                            email = '$email',
                                            telp = '$telp',
                                            alamat_petani = '$alamat_petani',
                                            status_akun = '$status_akun',
                                            foto_petani = '$foto_petani'
                                            $password_update
                                          WHERE id_petani='$id_petani'";

                                if ($con->query($query) === TRUE) {
                                    echo "<script>
                                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data berhasil diperbarui.' })
                                            .then((result) => { if (result.isConfirmed) { window.location.href = '?page=petani'; } });
                                          </script>";
                                } else {
                                    echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat memperbarui data.' }); </script>";
                                }
                            }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>