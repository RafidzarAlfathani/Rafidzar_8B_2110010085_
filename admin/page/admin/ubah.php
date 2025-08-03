    
<?php
$id_admin = $_GET['id_admin'];
$sql = $con->query("SELECT * FROM admin WHERE id_admin='$id_admin'");
$row = mysqli_fetch_assoc($sql);
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Admin</h5>
            </div>
            <div class="panel-body"> 
                <div class="card mb-20">
                    <div class="card-header">Ubah Data Admin <b>"<?= $row['nama']; ?>"</b></div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <input type="hidden" name="foto_lama" value="<?= $row['foto']; ?>">
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Nama</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="nama" value="<?= $row['nama']; ?>" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Username</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="username" value="<?= $row['username']; ?>" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Password</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password" id="password" value="<?= $row['password']; ?>" required>
                                        <div class="input-group-append">
                                            <button class="btn btn-warning" type="button" onclick="togglePassword()" data-toggle="tooltip" data-placement="top" title="Lihat password">
                                                <i id="toggleIcon" class="fa-regular fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Level</label>
                                <div class="col-sm-9">
                                    <select name="level" class="form-control">
                                        <option value="Admin" <?= ($row['level'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                                        <option value="Pimpinan" <?= ($row['level'] == 'Pimpinan') ? 'selected' : ''; ?>>Pimpinan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Status</label>
                                <div class="col-sm-9">
                                    <select name="status" class="form-control">
                                        <option value="Aktif" <?= ($row['status'] == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                                        <option value="Non-Aktif" <?= ($row['status'] == 'Non-Aktif') ? 'selected' : ''; ?>>Non-Aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Foto</label>
                                <div class="col-sm-9">
                                    <img src="images/admin/<?= $row['foto']; ?>" width="100"> <br>
                                    <input type="file" class="form-control" name="foto">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-9">
                                	<button type="submit" name="ubah" class="btn btn-success btn-sm">Ubah</button>
                                	<a href="?page=admin" class="btn btn-danger btn-sm">Kembali</a>
                                </div>
                            </div> 
                        </form>
                        <?php 
                            if (isset($_POST['ubah']) && !empty($id_admin)) {
                            $nama = mysqli_real_escape_string($con, $_POST['nama']);
                            $username = mysqli_real_escape_string($con, $_POST['username']);
                            $level = mysqli_real_escape_string($con, $_POST['level']);
                            $status = mysqli_real_escape_string($con, $_POST['status']);
                            $foto = $_FILES['foto']['name'];
                         
                            if ($foto != '') { 
                                $result = $con->query("SELECT foto FROM admin WHERE id_admin='$id_admin'");
                                $data = $result->fetch_assoc();
                                if ($data['foto'] != '') {
                                    unlink("images/admin/" . $data['foto']);
                                }
                         
                                $foto_tmp = $_FILES['foto']['tmp_name'];
                                $foto_dir = "images/admin/" . $foto;
                                move_uploaded_file($foto_tmp, $foto_dir);
                            } else { 
                                $foto = $_POST['foto_lama'];
                            }
                         
                            $query = "UPDATE admin SET nama='$nama', username='$username', level='$level', status='$status', foto='$foto' WHERE id_admin='$id_admin'";
                            if ($con->query($query) === TRUE) {
                                echo "<script>
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: 'Data berhasil diperbarui.',
                                            confirmButtonText: 'OK'
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                window.location.href = '?page=admin';
                                            }
                                        });
                                      </script>";
                            } else {
                                echo "<script>
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal!',
                                            text: 'Terjadi kesalahan saat memperbarui data.',
                                            confirmButtonText: 'OK'
                                        });
                                      </script>";
                            }
                        }
                        ?>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</div>

<script>
    function togglePassword() {
        var passwordField = document.getElementById("password");
        var toggleIcon = document.getElementById("toggleIcon");
        if (passwordField.type === "password") {
            passwordField.type = "text";
            toggleIcon.classList.remove("fa-eye");
            toggleIcon.classList.add("fa-eye-slash");
        } else {
            passwordField.type = "password";
            toggleIcon.classList.remove("fa-eye-slash");
            toggleIcon.classList.add("fa-eye");
        }
    }
</script> 