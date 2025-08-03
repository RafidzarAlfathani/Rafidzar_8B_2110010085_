<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Manajemen Pembeli</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">Tambah Data Pembeli</div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Nama Pembeli</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="nama_pembeli" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Password</label>
                                <div class="col-sm-9">
                                    <input type="password" class="form-control" name="password" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Telepon</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="telp" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Foto Profil</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" name="foto_pembeli">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-9">
                                    <button type="submit" name="tambah" class="btn btn-primary btn-sm">Simpan</button>
                                    <a href="?page=pembeli" class="btn btn-danger btn-sm">Kembali</a>
                                </div>
                            </div>
                        </form>
                        <?php
                        if (isset($_POST['tambah'])) {
                            $nama_pembeli = mysqli_real_escape_string($con, $_POST['nama_pembeli']);
                            $email = mysqli_real_escape_string($con, $_POST['email']);
                            $password = mysqli_real_escape_string($con, $_POST['password']); // Di produksi, gunakan password_hash()
                            $telp = mysqli_real_escape_string($con, $_POST['telp']);
                            $foto_pembeli = $_FILES['foto_pembeli']['name'];

                            if ($foto_pembeli != '') {
                                $foto_tmp = $_FILES['foto_pembeli']['tmp_name'];
                                $foto_dir = "images/pembeli/" . $foto_pembeli;
                                move_uploaded_file($foto_tmp, $foto_dir);
                            } else {
                                $foto_pembeli = "default.png";
                            }

                            $query = "INSERT INTO pembeli (nama_pembeli, email, password, telp, foto_pembeli)
                                      VALUES ('$nama_pembeli', '$email', '$password', '$telp', '$foto_pembeli')";
                            if ($con->query($query) === TRUE) {
                                echo "<script>
                                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data pembeli berhasil disimpan.' })
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