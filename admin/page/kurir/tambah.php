<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Manajemen Kurir</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">Tambah Data Kurir</div>
                    <div class="card-body">
                        <form method="post">
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Nama Kurir</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="nama_kurir" required>
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
                                <label class="col-sm-3 col-form-label">Status</label>
                                <div class="col-sm-9">
                                    <select name="status" class="form-control">
                                        <option value="Tersedia">Tersedia</option>
                                        <option value="Bertugas">Bertugas</option>
                                        <option value="Tidak Aktif">Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-9">
                                    <button type="submit" name="tambah" class="btn btn-primary btn-sm">Simpan</button>
                                    <a href="?page=kurir" class="btn btn-danger btn-sm">Kembali</a>
                                </div>
                            </div>
                        </form>
                        <?php
                        if (isset($_POST['tambah'])) {
                            $nama_kurir = mysqli_real_escape_string($con, $_POST['nama_kurir']);
                            $username = mysqli_real_escape_string($con, $_POST['username']);
                            $password = mysqli_real_escape_string($con, $_POST['password']);
                            $telp = mysqli_real_escape_string($con, $_POST['telp']);
                            $status = mysqli_real_escape_string($con, $_POST['status']);
                            
                            // Cek username duplikat
                            $cek = $con->query("SELECT * FROM kurir WHERE username = '$username'");
                            if($cek->num_rows > 0) {
                                echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Username sudah digunakan, silakan pilih username lain.' }); </script>";
                            } else {
                                $query = "INSERT INTO kurir (nama_kurir, username, password, telp, status)
                                      VALUES ('$nama_kurir', '$username', '$password', '$telp', '$status')";
                                if ($con->query($query) === TRUE) {
                                    echo "<script>
                                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data kurir berhasil disimpan.' })
                                            .then((result) => { if (result.isConfirmed) { window.location.href = '?page=kurir'; } });
                                          </script>";
                                } else {
                                    echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat menyimpan data.' }); </script>";
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>