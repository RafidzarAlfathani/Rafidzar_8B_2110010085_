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
                <div class="profile-sidebar">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="profile-sidebar-title">Pengaturan</h5>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-icon btn-outline-primary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa-solid fa-ellipsis"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-sm dropdown-menu-sm-end">
                                <li><a class="dropdown-item" href="?page=pengaturan&aksi=ubah&id_meta=<?= $data['id_meta'] ?>"><i class="fa-regular fa-pen-to-square"></i> Ubah</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="top">
                        <div class="image-wrap">
                            <div class="part-img rounded-circle overflow-hidden">
                                <img src="images/<?= $data['logo'] ?>" alt="admin">
                            </div> 
                        </div>
                        <div class="part-txt">
                            <h4 class="admin-name"><?= $data['instansi'] ?></h4>
                            <span class="admin-role"><?= $data['alamat'] ?></span>
                            <div class="admin-social"> 
                                <a href="https://www.youtube.com/@sman1banjarbaru64" target="_blank"><i class="fa-brands fa-youtube"></i></a>
                                <a href="https://www.instagram.com/sman_1_banjarbaru/" target="_blank"><i class="fa-brands fa-instagram"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="bottom">
                        <h6 class="profile-sidebar-subtitle">Communication Info</h6>
                        <ul>
                            <li><span>Instansi:</span> <?= $data['instansi'] ?></li>
                            <li><span>Telepon:</span> <?= $data['telp'] ?></li>
                            <li><span>Email:</span> <?= $data['email'] ?></li>
                            <li><span>Alamat:</span> <?= $data['alamat'] ?></li>
                            <li><span>Kepala Sekolah:</span> <?= $data['pimpinan'] ?></li>
                        </ul> 
                    </div>
                </div> 
            </div>
        </div>
    </div>
    <div class="col-md-3"></div>
</div>

