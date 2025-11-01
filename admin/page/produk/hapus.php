<?php
    $id_produk = $_GET['id_produk'];

    // Ambil nama file foto sebelum menghapus data dari DB
    $ambil_data = $con->query("SELECT foto_produk, foto_panen FROM produk WHERE id_produk='$id_produk'");
    $data_foto = $ambil_data->fetch_assoc();
    $foto_produk = $data_foto['foto_produk'];
    $foto_panen = $data_foto['foto_panen'];

    // Hapus data dari database
    $query = "DELETE FROM produk WHERE id_produk='$id_produk'";
    if ($con->query($query) === TRUE) {
        // Hapus file foto produk utama
        if (!empty($foto_produk) && file_exists("images/produk/" . $foto_produk)) {
            unlink("images/produk/" . $foto_produk);
        }
        // Hapus file foto panen
        if (!empty($foto_panen) && file_exists("images/panen/" . $foto_panen)) {
            unlink("images/panen/" . $foto_panen);
        }

        echo "<script>
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Produk berhasil dihapus.'})
                .then((result) => { if (result.isConfirmed) { window.location.href = '?page=produk'; } });
              </script>";
    } else {
        echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat menghapus data.' }); </script>";
    }
?>