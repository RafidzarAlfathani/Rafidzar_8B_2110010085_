<?php
    $id_kurir = $_GET['id_kurir'];

    // Hapus data dari database.
    $query = "DELETE FROM kurir WHERE id_kurir='$id_kurir'";
    if ($con->query($query) === TRUE) {
        echo "<script>
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data kurir berhasil dihapus.'})
                .then((result) => { if (result.isConfirmed) { window.location.href = '?page=kurir'; } });
              </script>";
    } else {
        echo "<script>
                Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat menghapus data.' });
              </script>";
    }
?>