<?php
    $id_tracking = $_GET['id_tracking'];

    $query = "DELETE FROM tracking_pengiriman WHERE id_tracking='$id_tracking'";
    if ($con->query($query) === TRUE) {
        echo "<script>
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Titik lacak berhasil dihapus.'})
                .then((result) => { if (result.isConfirmed) { window.location.href = '?page=tracking'; } });
              </script>";
    } else {
        echo "<script>
                Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat menghapus data.' });
              </script>";
    }
?>