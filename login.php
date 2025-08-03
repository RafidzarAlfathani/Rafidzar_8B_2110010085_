<?php

error_reporting(E_ALL ^ (E_NOTICE | E_WARNING)); 
// Logika PHP ditaruh di paling atas
session_start();
include 'admin/inc/koneksi.php';

// Jika pengguna sudah login, langsung lempar ke halaman utama
if (isset($_SESSION['pembeli_id'])) {
    header("Location: index.php");
    exit();
}

$error_msg = ""; // Variabel untuk menampung pesan error

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    $query = "SELECT * FROM pembeli WHERE email = '$email'";
    $result = $con->query($query);

    if ($result->num_rows == 1) {
        $pembeli = $result->fetch_assoc();
        // Memeriksa password teks biasa (TIDAK AMAN)
        if ($password == $pembeli['password']) {
            $_SESSION['pembeli_id'] = $pembeli['id_pembeli'];
            $_SESSION['pembeli_nama'] = $pembeli['nama_pembeli'];
            header("Location: index.php");
            exit();
        } else {
            $error_msg = "Email atau password salah.";
        }
    } else {
        $error_msg = "Email atau password salah.";
    }
}

// Atur judul halaman spesifik
$page_title = "Login Pelanggan";

// Panggil header
include 'header.php';
?>

    <div class="breadcrumbs_area">
        <div class="container">   
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <h3>Login</h3>
                        <ul>
                            <li><a href="index.php">home</a></li>
                            <li>Login</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>         
    </div>
    <div class="customer_login mt-60 mb-60">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 col-md-8 offset-lg-3 offset-md-2">
                    <div class="account_form">
                        <h2>Login</h2>
                        <form method="POST" action="login.php">
                            <?php if (!empty($error_msg)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= $error_msg; ?>
                                </div>
                            <?php endif; ?>

                            <?php if(isset($_SESSION['pesan_notifikasi'])): ?>
                                <div class="alert alert-warning" role="alert">
                                    <?= $_SESSION['pesan_notifikasi']; ?>
                                </div>
                                <?php unset($_SESSION['pesan_notifikasi']); // Hapus pesan setelah ditampilkan ?>
                            <?php endif; ?>
                            
                            <?php if(isset($_SESSION['pesan_sukses'])): ?>
                                <div class="alert alert-success" role="alert">
                                    <?= $_SESSION['pesan_sukses']; ?>
                                </div>
                                <?php unset($_SESSION['pesan_sukses']); ?>
                            <?php endif; ?>

                            <p>   
                                <label>Email <span>*</span></label>
                                <input type="email" name="email" required>
                             </p>
                             <p>   
                                <label>Password <span>*</span></label>
                                <input type="password" name="password" required>
                             </p>
                            <div class="login_submit">
                               <a href="#">Lupa Password?</a>
                                <button type="submit" name="login">login</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>    
    </div>
    <?php
// Panggil footer
include 'footer.php';
?>