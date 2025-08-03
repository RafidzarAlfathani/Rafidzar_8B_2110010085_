<?php
    $id_pesanan = $_GET['id_pesanan'];

    // Ambil nama file bukti bayar sebelum menghapus data dari DB
    $ambil_bukti = $con->query("SELECT bukti_bayar FROM pesanan WHERE id_pesanan='$id_pesanan'");
    $data_bukti = $ambil_bukti->fetch_assoc();
    $bukti_bayar = $data_bukti['bukti_bayar'];

    // Hapus data dari database.
    // Relasi ON DELETE CASCADE akan otomatis menghapus detail_pesanan & tracking_pengiriman
    $query = "DELETE FROM pesanan WHERE id_pesanan='$id_pesanan'";
    if ($con->query($query) === TRUE) {
        // Jika query berhasil, hapus file fotonya dari folder
        if (!empty($bukti_bayar)) {
             if (file_exists("images/bukti_bayar/" . $bukti_bayar)) {
                unlink("images/bukti_bayar/" . $bukti_bayar);
             }
        }
        echo "<script>
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Data pesanan berhasil dihapus.'})
                .then((result) => { if (result.isConfirmed) { window.location.href = '?page=pesanan'; } });
              </script>";
    } else {
        echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat menghapus data.' }); </script>";
    }
?>