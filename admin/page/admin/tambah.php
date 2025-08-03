<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Admin</h5>
            </div>
            <div class="panel-body">    
                <div class="card mb-20">
                    <div class="card-header">Tambah Data Admin</div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Nama</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="nama" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Username</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="username" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Password</label>
                                <div class="col-sm-9">
                                    <div class="input-group">
                                        <input type="password" class="form-control" name="password" id="password" required>
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
                                        <option value="Admin">Admin</option>
                                        <option value="Pimpinan">Pimpinan</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Status</label>
                                <div class="col-sm-9">
                                    <select name="status" class="form-control">
                                        <option value="Aktif">Aktif</option>
                                        <option value="Non-Aktif">Non-Aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Foto</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" name="foto">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-9">
                                	<button type="submit" name="tambah" class="btn btn-primary btn-sm">Simpan</button>
                                	<a href="?page=admin" class="btn btn-danger btn-sm">Kembali</a>
                                </div>
                            </div> 
                        </form>
                        <?php  
                         
                        if (isset($_POST['tambah'])) {
                            $nama = mysqli_real_escape_string($con, $_POST['nama']);
                            $username = mysqli_real_escape_string($con, $_POST['username']);
                            $password = mysqli_real_escape_string($con, $_POST['password']);
                            $level = mysqli_real_escape_string($con, $_POST['level']);
                            $status = mysqli_real_escape_string($con, $_POST['status']);
                            $foto = $_FILES['foto']['name'];
                         
                            if ($foto != '') {
                                $foto_tmp = $_FILES['foto']['tmp_name'];
                                $foto_dir = "images/admin/" . $foto;
                                move_uploaded_file($foto_tmp, $foto_dir);
                            }
                         
                            $query = "INSERT INTO admin (nama, username, password, level, status, foto) 
                                      VALUES ('$nama', '$username', '$password', '$level', '$status', '$foto')";
                            if ($con->query($query) === TRUE) {
                                echo "<script>
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: 'Data berhasil disimpan.',
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
                                            text: 'Terjadi kesalahan saat menyimpan data.',
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

