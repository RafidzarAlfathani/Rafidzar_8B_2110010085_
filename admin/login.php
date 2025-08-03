<?php 
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING));
session_start();
include 'inc/koneksi.php';
?>

<!DOCTYPE html>
<html lang="en">

<!-- Mirrored from digiboard-html.codebasket.xyz/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 21 Oct 2024 20:07:16 GMT -->
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$meta['instansi'] ?></title>
    
    <link rel="shortcut icon" href="images/<?=$meta['logo'] ?>">
    <link rel="stylesheet" href="assets/vendor/css/all.min.css">
    <link rel="stylesheet" href="assets/vendor/css/OverlayScrollbars.min.css">
    <link rel="stylesheet" href="assets/vendor/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" id="primaryColor" href="assets/css/blue-color.css">
    <link rel="stylesheet" id="rtlStyle" href="#">  
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="light-theme">
    <!-- preloader start -->
    <div class="preloader d-none">
        <div class="loader">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
    <!-- preloader end -->

    <!-- theme color hidden button -->
    <button class="header-btn theme-color-btn d-none"><i class="fa-light fa-sun-bright"></i></button>
    <!-- theme color hidden button -->

    <!-- main content start -->
    <div class="main-content login-panel">
        <div class="login-body">
            <div class="top d-flex justify-content-between align-items-center">
                <div class="logo" align="justify">  
                    <strong>Formulir Login</strong> 
                </div> 
            </div>
            <div class="bottom">
                <h4 class="panel-title" style="text-transform: uppercase;">
                    <b><?=$meta['instansi'] ?></b> <br>
                    <p style="font-size: 11px;">
                        <img src="images/<?= $meta['logo'] ?>" width="100" alt="admin"> <br> 
                        <b><?=$meta['alamat'] ?></b>
                    </p>
                </h4>
                <form method="post" onsubmit="return validateForm();">
                    <div class="input-group mb-25">
                        <span class="input-group-text"><i class="fa-regular fa-user"></i></span>
                        <input type="text" class="form-control" placeholder="Username" name="username" required autofocus>
                    </div>
                    <div class="input-group mb-20">
                        <span class="input-group-text"><i class="fa-regular fa-lock"></i></span>
                        <input type="password" class="form-control rounded-end" placeholder="Password" name="password" required>
                        <a role="button" class="password-show"><i class="fa-duotone fa-eye"></i></a>
                    </div>
                    <div class="d-flex justify-content-between mb-25">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="" id="loginCheckbox">
                            <label class="form-check-label text-white" for="loginCheckbox">
                                Remember Me
                            </label>
                        </div> 
                    </div>
                    <input type="submit" name="login" value="Sign In" class="btn btn-primary w-100"> 
                </form> 

                <?php
                if (isset($_POST['login'])) {
                    $username = mysqli_real_escape_string($con, $_POST['username']);
                    $password = mysqli_real_escape_string($con, $_POST['password']);
                    $login_sukses = false;

                    // 1. Cek sebagai Admin
                    $sql_admin = mysqli_query($con, "SELECT * FROM admin WHERE username='$username' AND status='Aktif'");
                    if (mysqli_num_rows($sql_admin) == 1) {
                        $data = mysqli_fetch_assoc($sql_admin);
                        if ($password == $data['password']) { // Ganti dengan password_verify jika password di-hash
                            $_SESSION['user_id'] = $data['id_admin'];
                            $_SESSION['user_nama'] = $data['nama'];
                            $_SESSION['user_level'] = $data['level']; // "Admin" atau "Pimpinan"
                            $_SESSION['user_foto'] = $data['foto'];
                            $login_sukses = true;
                        }
                    }

                    // 2. Jika bukan admin, cek sebagai Petani (login dengan email)
                    if (!$login_sukses) {
                        $sql_petani = mysqli_query($con, "SELECT * FROM petani WHERE email='$username' AND status_akun='Aktif'");
                        if (mysqli_num_rows($sql_petani) == 1) {
                            $data = mysqli_fetch_assoc($sql_petani);
                            if ($password == $data['password']) { // Ganti dengan password_verify
                                $_SESSION['user_id'] = $data['id_petani'];
                                $_SESSION['user_nama'] = $data['nama_petani'];
                                $_SESSION['user_level'] = 'Petani'; // Set level secara manual
                                $_SESSION['user_foto'] = $data['foto_petani'];
                                $login_sukses = true;
                            }
                        }
                    }

                    // 3. Jika bukan petani, cek sebagai Kurir
                    if (!$login_sukses) {
                        $sql_kurir = mysqli_query($con, "SELECT * FROM kurir WHERE username='$username' ");
                        if (mysqli_num_rows($sql_kurir) == 1) {
                            $data = mysqli_fetch_assoc($sql_kurir);
                            if ($password == $data['password']) { // Ganti dengan password_verify
                                $_SESSION['user_id'] = $data['id_kurir'];
                                $_SESSION['user_nama'] = $data['nama_kurir'];
                                $_SESSION['user_level'] = 'Kurir'; // Set level secara manual
                                $_SESSION['user_foto'] = 'user.png'; // Asumsi kurir tidak punya foto di DB
                                $login_sukses = true;
                            }
                        }
                    }

                    // Logika setelah pengecekan
                    if ($login_sukses) {
                        echo "<script>
                            Swal.fire({
                                title: 'Login Berhasil!',
                                text: 'Selamat datang, " . $_SESSION['user_nama'] . ".',
                                icon: 'success',
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                window.location = 'index.php';
                            });
                        </script>";
                    } else {
                        // Jika tidak ada user yang cocok sama sekali
                        echo "<script>
                            Swal.fire({
                                title: 'Login Gagal!',
                                text: 'Username atau Password salah!',
                                icon: 'error',
                                confirmButtonText: 'Coba Lagi'
                            }).then(() => {
                                window.location = 'login.php';
                            });
                        </script>";
                    }
                }
                ?>
            </div>
        </div>

        <!-- footer start -->
        <?php include "footer.php" ?>
        <!-- footer end -->
    </div>
    <!-- main content end -->
    
    <script src="assets/vendor/js/jquery-3.6.0.min.js"></script>
    <script src="assets/vendor/js/jquery.overlayScrollbars.min.js"></script>
    <script src="assets/vendor/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script> 
    <!-- for demo purpose -->
    <script>
        var rtlReady = $('html').attr('dir', 'ltr');
        if (rtlReady !== undefined) {
            localStorage.setItem('layoutDirection', 'ltr');
        }
    </script>
    <!-- for demo purpose -->
</body>

<!-- Mirrored from digiboard-html.codebasket.xyz/login.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 21 Oct 2024 20:07:16 GMT -->
</html>