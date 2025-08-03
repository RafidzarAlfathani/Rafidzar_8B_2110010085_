<?php
    $id_petani = $_GET['id_petani'];

    // Ambil nama file foto sebelum menghapus data dari DB
    $ambil_foto = $con->query("SELECT foto_petani FROM petani WHERE id_petani='$id_petani'");
    $data_foto = $ambil_foto->fetch_assoc();
    $foto_petani = $data_foto['foto_petani'];

    // Hapus data dari database
    $query = "DELETE FROM petani WHERE id_petani='$id_petani'";
    if ($con->query($query) === TRUE) {
        // Jika query berhasil, hapus file fotonya dari folder
        if ($foto_petani != '' && $foto_petani != 'default.png') {
             if (file_exists("images/petani/" . $foto_petani)) {
                unlink("images/petani/" . $foto_petani);
             }
        }
        echo "<script>
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data berhasil dihapus.'})
                .then((result) => { if (result.isConfirmed) { window.location.href = '?page=petani'; } });
              </script>";
    } else {
        echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat menghapus data.' }); </script>";
    }
?>