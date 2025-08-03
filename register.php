<?php
// 1. Logika PHP ditaruh di paling atas sebelum HTML apapun
//    Header akan dipanggil setelahnya.
session_start();
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING)); 
include 'admin/inc/koneksi.php';

$error_msg = ""; // Variabel untuk menampung pesan error

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($con, $_POST['nama_pembeli']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $telp = mysqli_real_escape_string($con, $_POST['telp']);
    $password = mysqli_real_escape_string($con, $_POST['password']);
    $password_konfirmasi = $_POST['password_konfirmasi'];

    // Validasi
    if ($password !== $password_konfirmasi) {
        $error_msg = "Konfirmasi password tidak cocok!";
    } else {
        $cek_email = $con->query("SELECT email FROM pembeli WHERE email = '$email'");
        if ($cek_email->num_rows > 0) {
            $error_msg = "Email sudah terdaftar, silakan gunakan email lain atau login.";
        } else {
            // Simpan password sebagai teks biasa (TIDAK AMAN)
            $query = "INSERT INTO pembeli (nama_pembeli, email, password, telp) VALUES ('$nama', '$email', '$password', '$telp')";
            if ($con->query($query) === TRUE) {
                $_SESSION['pesan_sukses'] = "Pendaftaran berhasil! Silakan login.";
                header("Location: login.php");
                exit();
            } else {
                $error_msg = "Terjadi kesalahan pada server.";
            }
        }
    }
}

// 2. Atur judul halaman spesifik untuk file ini
$page_title = "Daftar Akun Baru";

// 3. Panggil header
include 'header.php';
?>

    <div class="breadcrumbs_area">
        <div class="container">   
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <h3>Daftar Akun</h3>
                        <ul>
                            <li><a href="index.php">home</a></li>
                            <li>Daftar</li>
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
                    <div class="account_form register">
                        <h2>Daftar Akun Baru</h2>
                        <form method="POST" action="register.php">
                            <?php if (!empty($error_msg)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= $error_msg; ?>
                                </div>
                            <?php endif; ?>
                            <p>   
                                <label>Nama Lengkap <span>*</span></label>
                                <input type="text" name="nama_pembeli" required>
                             </p>
                             <p>   
                                <label>Email <span>*</span></label>
                                <input type="email" name="email" required>
                             </p>
                             <p>   
                                <label>Telepon <span>*</span></label>
                                <input type="text" name="telp" required>
                             </p>
                            <p>   
                                <label>Password <span>*</span></label>
                                <input type="password" name="password" required>
                             </p>
                             <p>   
                                <label>Konfirmasi Password <span>*</span></label>
                                <input type="password" name="password_konfirmasi" required>
                             </p>
                            <div class="login_submit">
                                <button type="submit" name="register">Daftar</button>
                            </div>
                        </form>
                        <div class="text-center mt-3">
                            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
                        </div>
                    </div>
                </div>
                </div>
        </div>    
    </div>
    <?php
// 4. Panggil footer
include 'footer.php';
?>