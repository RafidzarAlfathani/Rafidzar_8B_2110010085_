<?php
session_start();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">     
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <script> 
        Swal.fire({
            title: 'Logout Berhasil!',
            text: 'Anda telah logout.',
            icon: 'success',
            showConfirmButton: false,
            timer: 2000  
        }).then(() => {
            window.location = 'login.php';  
        });
    </script>
</body>
</html>
