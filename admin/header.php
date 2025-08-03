<?php
// Ambil level dan ID pengguna dari sesi untuk mempermudah
$user_level = $_SESSION['user_level'];
$user_id = $_SESSION['user_id']; // ID pengguna yang sedang login

// 1. Tentukan path foto profil secara dinamis
$foto_path = 'images/user.png'; // Gambar default jika tidak ada
if (isset($user_data['foto']) && !empty($user_data['foto'])) {
    $nama_file_foto = $user_data['foto'];

    // Tentukan folder berdasarkan level
    if ($user_level == 'Admin' || $user_level == 'Pimpinan') {
        $foto_path = "images/admin/" . $nama_file_foto;
    } elseif ($user_level == 'Petani') {
        $foto_path = "images/petani/" . $nama_file_foto;
    }
    // Kurir akan otomatis menggunakan foto default 'user.png'
}


// 2. 🔑 PERBAIKAN: Tentukan link profil menggunakan $_SESSION['user_id'] secara langsung
$profile_link = '#'; // Link default
if ($user_level == 'Admin' || $user_level == 'Pimpinan') {
    // Link untuk Admin/Pimpinan
    $profile_link = "?page=admin&aksi=ubah&id_admin=" . $user_id;
} elseif ($user_level == 'Petani') {
    // Link untuk Petani
    $profile_link = "?page=petani&aksi=ubah&id_petani=" . $user_id;
} elseif ($user_level == 'Kurir') {
    // Link untuk Kurir
    $profile_link = "?page=kurir&aksi=ubah&id_kurir=" . $user_id;
}
?>

    <div class="header">
        <div class="row g-0 align-items-center">
            <div class="col-xxl-6 col-xl-5 col-4 d-flex align-items-center gap-20">
                <div class="main-logo d-lg-block d-none">
                    <div class="logo-big">
                        <a href="index.php">
                            <strong><?=$meta['singkat'] ?></strong>
                        </a>
                    </div>
                    <div class="logo-small">
                        <a href="index.php">
                            <img src="images/<?=$meta['logo'] ?>" alt="Logo">
                        </a>
                    </div>
                </div>
                <div class="nav-close-btn">
                    <button id="navClose"><i class="fa-light fa-bars-sort"></i></button>
                </div>
                <a href="#" class="btn btn-sm btn-primary site-view-btn">
                    <span>
                        <script type='text/javascript'>
                            <!--
                            var months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                            var myDays = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jum&#39;at', 'Sabtu'];
                            var date = new Date();
                            var day = date.getDate();
                            var month = date.getMonth();
                            var thisDay = date.getDay(),
                                thisDay = myDays[thisDay];
                            var yy = date.getYear();
                            var year = (yy < 1000) ? yy + 1900 : yy;
                            document.write(thisDay + ', ' + day + ' ' + months[month] + ' ' + year);
                            //-->
                        </script> | 
                        <span id="clock1"></span>
                        <script type="text/javascript"> 
                            function showTime() {
                               var a_p = "";
                                var today = new Date();
                                var curr_hour = today.getHours();
                                var curr_minute = today.getMinutes();
                                var curr_second = today.getSeconds();
                                if (curr_hour < 12) {
                                    a_p = "AM";
                                } else {
                                    a_p = "PM";
                                }
                                if (curr_hour == 0) {
                                    curr_hour = 12;
                                }
                                if (curr_hour > 12) {
                                    curr_hour = curr_hour - 12;
                                }
                                curr_hour = checkTime(curr_hour);
                                curr_minute = checkTime(curr_minute);
                                curr_second = checkTime(curr_second);
                               document.getElementById('clock1').innerHTML=curr_hour + ":" + curr_minute + ":" + curr_second + " " + a_p;
                            }
                    
                            function checkTime(i) {
                                if (i < 10) {
                                    i = "0" + i;
                                }
                                return i;
                            }
                            setInterval(showTime, 500); 
                        </script>
                    </span>
                </a>
            </div>
            <div class="col-4 d-lg-none">
                <div class="mobile-logo">
                    <a href="index.php">
                        <strong><?=$meta['singkat'] ?></strong>
                    </a>
                </div>
            </div>
            <div class="col-xxl-6 col-xl-7 col-lg-8 col-4">
                <div class="header-right-btns d-flex justify-content-end align-items-center">
                    <div class="header-collapse-group">
                        <div class="header-right-btns d-flex justify-content-end align-items-center p-0"> 
                            <div class="header-right-btns d-flex justify-content-end align-items-center p-0"> 
                                <button class="header-btn fullscreen-btn" id="btnFullscreen"><i class="fa-light fa-expand"></i></button>
                            </div>
                        </div>
                    </div>
                    <button class="header-btn header-collapse-group-btn d-lg-none"><i class="fa-light fa-ellipsis-vertical"></i></button>
                    <button class="header-btn theme-settings-btn d-lg-none"><i class="fa-light fa-gear"></i></button>
                    <div class="header-btn-box profile-btn-box">
                        <button class="profile-btn" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo $foto_path ?>" alt="image">
                        </button>
                        <ul class="dropdown-menu profile-dropdown-menu">
                            <li>
                                <div class="dropdown-txt text-center">
                                    <p class="mb-0"><?= $user_data['nama'] ?></p>
                                    <span class="d-block"><?= $user_data['level'] ?></span> 
                                </div>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= $profile_link ?>"><span class="dropdown-icon"><i class="fa-regular fa-circle-user"></i></span> Profile</a>
                            </li> 
                            <hr class="dropdown-divider">
                            
                            <?php if ($user_level == 'Admin' || $user_level == 'Pimpinan'): ?>
                            <li><a class="dropdown-item" href="?page=pengaturan&id_meta=<?=$meta['id_meta'] ?>"><span class="dropdown-icon"><i class="fa-regular fa-gear"></i></span> Settings</a></li>
                            <?php endif; ?>
                            
                            <li><a class="dropdown-item" href="#" onclick="logoutConfirmation()"><span class="dropdown-icon"><i class="fa-regular fa-arrow-right-from-bracket"></i></span> Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function logoutConfirmation() {
            Swal.fire({
                title: 'Apakah Anda yakin ingin logout?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, logout!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'logout.php';  
                }
            });
        }
    </script>