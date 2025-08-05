<?php
$id_pesanan = $_GET['id_pesanan'];

// Ambil nama file bukti bayar dan bukti sampai sebelum menghapus data dari DB
$ambil_bukti = $con->query("SELECT bukti_bayar, bukti_sampai FROM pesanan WHERE id_pesanan='$id_pesanan'");
$data_bukti = $ambil_bukti->fetch_assoc();
$bukti_bayar = $data_bukti['bukti_bayar'];
$bukti_sampai = $data_bukti['bukti_sampai'];

// Hapus data dari database.
// Relasi ON DELETE CASCADE akan otomatis menghapus detail_pesanan & tracking_pengiriman
$query = "DELETE FROM pesanan WHERE id_pesanan='$id_pesanan'";
if ($con->query($query) === TRUE) {
    // Hapus file bukti_bayar jika ada
    if (!empty($bukti_bayar)) {
        $path_bukti_bayar = "images/bukti_bayar/" . $bukti_bayar;
        if (file_exists($path_bukti_bayar)) {
            unlink($path_bukti_bayar);
        }
    }

    // Hapus file bukti_sampai jika ada
    if (!empty($bukti_sampai)) {
        $path_bukti_sampai = "../../images/bukti_sampai/". $bukti_sampai;
        if (file_exists($path_bukti_sampai)) {
            unlink($path_bukti_sampai);
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
