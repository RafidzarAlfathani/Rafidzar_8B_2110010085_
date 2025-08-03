<?php
// Mulai atau lanjutkan sesi yang ada.
session_start();

// Kosongkan semua variabel session.
$_SESSION = array();

// Hancurkan sesi.
session_destroy();

// Alihkan pengguna kembali ke halaman utama (index.php).
header("location: index.php");
exit;
?>