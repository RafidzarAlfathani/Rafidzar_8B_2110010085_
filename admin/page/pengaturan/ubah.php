<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>

<?php
$id_meta = $_GET['id_meta'];
$sql = $con->query("SELECT * FROM meta WHERE id_meta='$id_meta'");
$data = mysqli_fetch_assoc($sql);
?>

<div class="row g-4">
    <div class="col-md-3"></div>
    <div class="col-md-6">
        <div class="panel">
            <div class="panel-body"> 
                <div class="profile-sidebar" id="editForm">
                    <div class="bottom">
                        <h6 class="profile-sidebar-subtitle">Ubah Data</h6> 
                        <form method="post" enctype="multipart/form-data" action="">
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Instansi</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="instansi" value="<?= $data['instansi'] ?>" required>
                                </div>
                            </div> 
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Alamat</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="alamat" value="<?= $data['alamat'] ?>" required>
                                </div>
                            </div> 
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Telepon</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="telp" value="<?= $data['telp'] ?>" required>
                                </div>
                            </div> 
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Email</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="email" value="<?= $data['email'] ?>" required>
                                </div>
                            </div> 
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Kepala Balai</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="pimpinan" value="<?= $data['pimpinan'] ?>" required>
                                </div>
                            </div>  
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Logo</label>
                                <div class="col-sm-9">
                                    <?php if (!empty($data['logo'])): ?>
                                        <img src="images/<?= $data['logo'] ?>" alt="Logo" style="width: 100px; height: auto;">
                                    <?php endif; ?>
                                    <input type="file" class="form-control" name="logo">
                                </div>
                            </div>  
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-9">
                                    <button type="submit" name="update" class="btn btn-success btn-sm">Ubah</button>
                                    <a href="?page=pengaturan&id_meta=<?= $data['id_meta'] ?>" class="btn btn-danger btn-sm">Kembali</a>
                                </div>
                            </div>                                  
                        </form>

                        <?php 
                        if (isset($_POST['update'])) 
                        {
                            
                            $instansi     = $_POST['instansi']; 
                            $telp      = $_POST['telp']; 
                            $email     = $_POST['email']; 
                            $alamat    = $_POST['alamat']; 
                            $pimpinan  = $_POST['pimpinan'];   
                            $logo      = $_FILES['logo']['name'];
                            $lokasi         = $_FILES['logo']['tmp_name'];
                            if (!empty($lokasi)) 
                            {
                                move_uploaded_file($lokasi, "images/".$logo);
                                $con->query("UPDATE meta SET instansi='$instansi',
                                                             telp='$telp',
                                                             email='$email',
                                                             alamat='$alamat',
                                                             pimpinan='$pimpinan', 
                                                             logo='$logo' WHERE id_meta='$_GET[id_meta]'"); 
                            }
                            else
                            {
                                $con->query("UPDATE meta SET instansi='$instansi',
                                                             telp='$telp',
                                                             email='$email',
                                                             alamat='$alamat',
                                                             pimpinan='$pimpinan' WHERE id_meta='$_GET[id_meta]'"); 
                            } 
                            echo " 
                            <script>
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Data berhasil diubah!',
                                    timer: 1700,
                                    showConfirmButton: false
                                }).then(function() {
                                    window.location = '?page=pengaturan&id_meta=$id_meta';
                                });
                            </script>";
                        }
                        ?> 
                    </div>                             
                </div> 
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

