<?php
    $id_produk = $_GET['id_produk'];

    // Ambil nama file foto sebelum menghapus data dari DB
    $ambil_foto = $con->query("SELECT foto_produk FROM produk WHERE id_produk='$id_produk'");
    $data_foto = $ambil_foto->fetch_assoc();
    $foto_produk = $data_foto['foto_produk'];

    // Hapus data dari database
    $query = "DELETE FROM produk WHERE id_produk='$id_produk'";
    if ($con->query($query) === TRUE) {
        // Jika query berhasil, hapus file fotonya dari folder
        if ($foto_produk != '') {
             if (file_exists("images/produk/" . $foto_produk)) {
                unlink("images/produk/" . $foto_produk);
             }
        }
        echo "<script>
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Produk berhasil dihapus.'})
                .then((result) => { if (result.isConfirmed) { window.location.href = '?page=produk'; } });
              </script>";
    } else {
        echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat menghapus data.' }); </script>";
    }
?>