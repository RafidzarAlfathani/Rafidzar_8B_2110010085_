<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Data Petani</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">Tambah Data Petani</div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Nama Petani</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="nama_petani" required>
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
                                <label class="col-sm-3 col-form-label">Alamat</label>
                                <div class="col-sm-9">
                                    <textarea name="alamat_petani" class="form-control" required></textarea>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Status Akun</label>
                                <div class="col-sm-9">
                                    <select name="status_akun" class="form-control">
                                        <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                                        <option value="Aktif">Aktif</option>
                                        <option value="Non-Aktif">Non-Aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Foto</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" name="foto_petani">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-9">
                                    <button type="submit" name="tambah" class="btn btn-primary btn-sm">Simpan</button>
                                    <a href="?page=petani" class="btn btn-danger btn-sm">Kembali</a>
                                </div>
                            </div>
                        </form>
                        <?php
                        if (isset($_POST['tambah'])) {
                            $nama_petani = mysqli_real_escape_string($con, $_POST['nama_petani']);
                            $email = mysqli_real_escape_string($con, $_POST['email']);
                            $password = mysqli_real_escape_string($con, $_POST['password']); // Di produksi, gunakan password_hash()
                            $telp = mysqli_real_escape_string($con, $_POST['telp']);
                            $alamat_petani = mysqli_real_escape_string($con, $_POST['alamat_petani']);
                            $status_akun = mysqli_real_escape_string($con, $_POST['status_akun']);
                            $foto_petani = $_FILES['foto_petani']['name'];

                            if ($foto_petani != '') {
                                $foto_tmp = $_FILES['foto_petani']['tmp_name'];
                                $foto_dir = "images/petani/" . $foto_petani;
                                move_uploaded_file($foto_tmp, $foto_dir);
                            } else {
                                $foto_petani = "default.png";
                            }

                            $query = "INSERT INTO petani (nama_petani, email, password, telp, alamat_petani, status_akun, foto_petani)
                                      VALUES ('$nama_petani', '$email', '$password', '$telp', '$alamat_petani', '$status_akun', '$foto_petani')";
                            if ($con->query($query) === TRUE) {
                                echo "<script>
                                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data berhasil disimpan.' })
                                        .then((result) => { if (result.isConfirmed) { window.location.href = '?page=petani'; } });
                                      </script>";
                            } else {
                                echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat menyimpan data.' }); </script>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>