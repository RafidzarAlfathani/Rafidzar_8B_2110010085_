<?php
    $id_pembeli = $_GET['id_pembeli'];

    // Ambil nama file foto sebelum menghapus data dari DB
    $ambil_foto = $con->query("SELECT foto_pembeli FROM pembeli WHERE id_pembeli='$id_pembeli'");
    $data_foto = $ambil_foto->fetch_assoc();
    $foto_pembeli = $data_foto['foto_pembeli'];

    // Hapus data dari database.
    // Karena ON DELETE CASCADE, semua pesanan terkait akan ikut terhapus.
    $query = "DELETE FROM pembeli WHERE id_pembeli='$id_pembeli'";
    if ($con->query($query) === TRUE) {
        // Jika query berhasil, hapus file fotonya dari folder
        if ($foto_pembeli != '' && $foto_pembeli != 'default.png') {
             if (file_exists("images/pembeli/" . $foto_pembeli)) {
                unlink("images/pembeli/" . $foto_pembeli);
             }
        }
        echo "<script>
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data pembeli dan semua riwayat pesanannya telah dihapus.'})
                .then((result) => { if (result.isConfirmed) { window.location.href = '?page=pembeli'; } });
              </script>";
    } else {
        echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat menghapus data.' }); </script>";
    }
?>