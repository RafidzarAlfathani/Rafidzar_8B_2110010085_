<?php
    $id_kategori = $_GET['id_kategori'];

    // Hapus data dari database.
    // Karena ON DELETE CASCADE, semua produk terkait akan ikut terhapus.
    $query = "DELETE FROM kategori_produk WHERE id_kategori='$id_kategori'";
    if ($con->query($query) === TRUE) {
        echo "<script>
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Kategori dan semua produk terkait berhasil dihapus.'})
                .then((result) => { if (result.isConfirmed) { window.location.href = '?page=kategori_produk'; } });
              </script>";
    } else {
        echo "<script>
                Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat menghapus data.' });
              </script>";
    }
?>