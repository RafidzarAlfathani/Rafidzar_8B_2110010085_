<?php
$id_kurir = $_GET['id_kurir'];
$sql = $con->query("SELECT * FROM kurir WHERE id_kurir='$id_kurir'");
$row = mysqli_fetch_assoc($sql);
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Manajemen Kurir</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">Ubah Data Kurir <b>"<?= $row['nama_kurir']; ?>"</b></div>
                    <div class="card-body">
                        <form method="post">
                             <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Nama Kurir</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="nama_kurir" value="<?= $row['nama_kurir']; ?>" required>
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
                                <label class="col-sm-3 col-form-label">Status</label>
                                <div class="col-sm-9">
                                    <select name="status" class="form-control">
                                        <option value="Tersedia" <?= ($row['status'] == 'Tersedia') ? 'selected' : ''; ?>>Tersedia</option>
                                        <option value="Bertugas" <?= ($row['status'] == 'Bertugas') ? 'selected' : ''; ?>>Bertugas</option>
                                        <option value="Tidak Aktif" <?= ($row['status'] == 'Tidak Aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-9">
                                    <button type="submit" name="ubah" class="btn btn-success btn-sm">Ubah</button>
                                    <a href="?page=kurir" class="btn btn-danger btn-sm">Kembali</a>
                                </div>
                            </div>
                        </form>
                        <?php
                            if (isset($_POST['ubah'])) {
                                $nama_kurir = mysqli_real_escape_string($con, $_POST['nama_kurir']);
                                $username = mysqli_real_escape_string($con, $_POST['username']);
                                $telp = mysqli_real_escape_string($con, $_POST['telp']);
                                $status = mysqli_real_escape_string($con, $_POST['status']);

                                // Cek username duplikat (kecuali username milik sendiri)
                                $cek = $con->query("SELECT * FROM kurir WHERE username = '$username' AND id_kurir != '$id_kurir'");
                                if ($cek->num_rows > 0) {
                                    echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Username sudah digunakan oleh kurir lain.' }); </script>";
                                } else {
                                    // Logika untuk update password (jika diisi)
                                    $password = $_POST['password'];
                                    if (!empty($password)) {
                                        $password_update = ", password='" . mysqli_real_escape_string($con, $password) . "'";
                                    } else {
                                        $password_update = "";
                                    }

                                    $query = "UPDATE kurir SET
                                                nama_kurir = '$nama_kurir',
                                                username = '$username',
                                                telp = '$telp',
                                                status = '$status'
                                                $password_update
                                              WHERE id_kurir='$id_kurir'";

                                    if ($con->query($query) === TRUE) {
                                        echo "<script>
                                                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data kurir berhasil diperbarui.' })
                                                .then((result) => { if (result.isConfirmed) { window.location.href = '?page=kurir'; } });
                                              </script>";
                                    } else {
                                        echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat memperbarui data.' }); </script>";
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