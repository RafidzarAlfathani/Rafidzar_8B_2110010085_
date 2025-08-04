<?php
$id_pesanan = $_GET['id_pesanan'];
$user_id = $_SESSION['user_id'];
$user_level = $_SESSION['user_level'];

// Query untuk mengambil data pesanan dasar
$sql_pesanan = $con->query("SELECT ps.*, pm.nama_pembeli, pm.email AS email_pembeli, pm.telp AS telp_pembeli, kr.nama_kurir
    FROM pesanan ps
    JOIN pembeli pm ON ps.id_pembeli = pm.id_pembeli
    LEFT JOIN kurir kr ON ps.id_kurir = kr.id_kurir
    WHERE ps.id_pesanan = '$id_pesanan'");
$pesanan = $sql_pesanan->fetch_assoc();

// Query ambil data alamat petani berdasarkan produk dalam pesanan
$query = "
SELECT DISTINCT p.nama_produk, pt.nama_petani, pt.alamat_petani
FROM detail_pesanan dp
JOIN produk p ON dp.id_produk = p.id_produk
JOIN petani pt ON p.id_petani = pt.id_petani
WHERE dp.id_pesanan = '$id_pesanan'
";

$result = $con->query($query);

// ... (Kode keamanan verifikasi hak akses Anda tidak perlu diubah) ...
if (!$pesanan) {
    echo "<script>Swal.fire('Error!', 'Pesanan tidak ditemukan.', 'error').then(() => window.location.href = '?page=pesanan');</script>";
    exit;
}
$has_access = false;
if ($user_level == 'Admin' || $user_level == 'Pimpinan') {
    $has_access = true;
} elseif ($user_level == 'Kurir' && $pesanan['id_kurir'] == $user_id) {
    $has_access = true;
} elseif ($user_level == 'Petani') {
    $cek_produk_petani = $con->query("SELECT COUNT(*) as jumlah FROM detail_pesanan dp JOIN produk pr ON dp.id_produk = pr.id_produk WHERE dp.id_pesanan = '$id_pesanan' AND pr.id_petani = '$user_id'");
    $jumlah_produk = $cek_produk_petani->fetch_assoc()['jumlah'];
    if ($jumlah_produk > 0) {
        $has_access = true;
    }
}
if (!$has_access) {
    echo "<script>Swal.fire('Akses Ditolak!', 'Anda tidak memiliki izin untuk melihat detail pesanan ini.', 'error').then(() => window.location.href = '?page=pesanan');</script>";
    exit;
}
?>

<div class="row">
    <div class="col-md-7">
        <div class="panel">
            <div class="panel-header">
                <h5>Detail Pesanan: <?= $pesanan['kode_invoice']; ?></h5>
            </div>
            <div class="panel-body">
                <div class="card mb-3">
                    <div class="card-header">Produk yang Dipesan</div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Produk</th>
                                    <th>Harga</th>
                                    <th>Jumlah</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_belanja_keseluruhan = 0;
                                $total_belanja_petani = 0;
                                $query_detail = "SELECT dp.*, pr.nama_produk, pr.foto_produk, pr.id_petani
                                                 FROM detail_pesanan dp
                                                 JOIN produk pr ON dp.id_produk = pr.id_produk
                                                 WHERE dp.id_pesanan = '$id_pesanan'";

                                $ambil_detail = $con->query($query_detail);
                                while ($item = $ambil_detail->fetch_assoc()) {
                                    $total_belanja_keseluruhan += $item['sub_total'];

                                    if ($user_level != 'Petani' || ($user_level == 'Petani' && $item['id_petani'] == $user_id)) {
                                        if ($user_level == 'Petani') {
                                            $total_belanja_petani += $item['sub_total'];
                                        }
                                ?>
                                        <tr>
                                            <td>
                                                <img src="images/produk/<?= $item['foto_produk']; ?>" width="40" class="me-2">
                                                <?= $item['nama_produk']; ?>
                                            </td>
                                            <td>Rp <?= number_format($item['harga_saat_pesan']); ?></td>
                                            <td><?= $item['jumlah']; ?></td>
                                            <td>Rp <?= number_format($item['sub_total']); ?></td>
                                        </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Detail Pengiriman & Pembeli</div>
                    <div class="card-body">
                        <h5>Alamat Tujuan:</h5>
                        <p><?= $pesanan['alamat_pengiriman']; ?></p>
                        <h5>Alamat Pengambilan Produk:</h5>
                        <p> <?php
                            $result = $con->query("
                                        SELECT DISTINCT pt.nama_petani, pt.alamat_petani 
                                        FROM detail_pesanan dp
                                        JOIN produk p ON dp.id_produk = p.id_produk
                                        JOIN petani pt ON p.id_petani = pt.id_petani
                                        WHERE dp.id_pesanan = '$id_pesanan'
                                    ");
                            while ($row = $result->fetch_assoc()) {
                                echo "<p><strong>{$row['nama_petani']}</strong><br>{$row['alamat_petani']}</p>";
                            }
                            ?>
                        </p>
                        <hr>
                        <h5>Info Pembeli:</h5>
                        <p>
                            <strong>Nama:</strong> <?= $pesanan['nama_pembeli']; ?><br>
                            <?php if ($user_level != 'Petani'): ?>
                                <strong>Email:</strong> <?= $pesanan['email_pembeli']; ?><br>
                                <strong>Telepon:</strong> <?= $pesanan['telp_pembeli']; ?>
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-5">
        <div class="panel">
            <div class="panel-header">
                <h5>Ringkasan & Status</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-3">
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <?php if ($user_level == 'Petani'): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center"><strong>Total Pendapatan Anda</strong> <span class="badge bg-success"><strong>Rp <?= number_format($total_belanja_petani); ?></strong></span></li>
                            <?php else: ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">Total Belanja <span class="badge bg-secondary">Rp <?= number_format($total_belanja_keseluruhan); ?></span></li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">Ongkos Kirim <span class="badge bg-secondary">Rp <?= number_format($pesanan['ongkir']); ?></span></li>
                                <li class="list-group-item d-flex justify-content-between align-items-center"><strong>Total Bayar</strong> <span class="badge bg-primary"><strong>Rp <?= number_format($pesanan['total_bayar']); ?></strong></span></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <?php if (($user_level == 'Admin' || $user_level == 'Pimpinan') && $pesanan['metode_pembayaran'] == 'Transfer Bank' && !empty($pesanan['bukti_bayar'])) : ?>
                    <div class="card mb-3">
                        <div class="card-header">Bukti Pembayaran</div>
                        <div class="card-body text-center">
                            <a href="images/bukti_bayar/<?= $pesanan['bukti_bayar']; ?>" target="_blank">
                                <img src="images/bukti_bayar/<?= $pesanan['bukti_bayar']; ?>" class="img-fluid" style="max-height: 300px;">
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">Update Status & Pengiriman</div>
                    <div class="card-body">
                        <?php
                        // 🔑 Kunci: Cek apakah pesanan sudah selesai atau dibatalkan
                        if ($pesanan['status_pesanan'] == 'Selesai' || $pesanan['status_pesanan'] == 'Dibatalkan') :
                        ?>
                            <div class="alert alert-success text-center" role="alert">
                                <h4 class="alert-heading">Pesanan Telah Selesai!</h4>
                                <p>Pesanan ini sudah ditandai sebagai <strong><?= $pesanan['status_pesanan']; ?></strong> dan tidak dapat diubah lagi.</p>
                            </div>
                            <hr>
                            <p>Status Akhir:</p>
                            <h4><span class="badge bg-success"><?= $pesanan['status_pesanan']; ?></span></h4>
                            <hr>
                            <p>Kurir Bertugas:</p>
                            <h5><?= $pesanan['nama_kurir'] ?? '<span class="text-muted">Tidak Ada</span>'; ?></h5>

                        <?php else: ?>
                            <form method="post">
                                <?php if ($user_level == 'Admin' || $user_level == 'Pimpinan'): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Ubah Status Pesanan</label>
                                        <select name="status_pesanan" class="form-control" required>
                                            <option value="Menunggu Pembayaran" <?= $pesanan['status_pesanan'] == 'Menunggu Pembayaran' ? 'selected' : '' ?>>Menunggu Pembayaran</option>
                                            <option value="Menunggu Verifikasi" <?= $pesanan['status_pesanan'] == 'Menunggu Verifikasi' ? 'selected' : '' ?>>Menunggu Verifikasi</option>
                                            <option value="Diproses" <?= $pesanan['status_pesanan'] == 'Diproses' ? 'selected' : '' ?>>Diproses</option>
                                            <option value="Dikirim" <?= $pesanan['status_pesanan'] == 'Dikirim' ? 'selected' : '' ?>>Dikirim</option>
                                            <option value="Selesai" <?= $pesanan['status_pesanan'] == 'Selesai' ? 'selected' : '' ?>>Selesai</option>
                                            <option value="Dibatalkan" <?= $pesanan['status_pesanan'] == 'Dibatalkan' ? 'selected' : '' ?>>Dibatalkan</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Tugaskan Kurir</label>
                                        <select name="id_kurir" class="form-control">
                                            <option value="">-- Tidak Ada Kurir / Lepas Tugas --</option>
                                            <?php
                                            $kurir = $con->query("SELECT * FROM kurir WHERE status != 'Tidak Aktif' ORDER BY nama_kurir ASC");
                                            while ($kr = $kurir->fetch_assoc()) {
                                                $selected = ($kr['id_kurir'] == $pesanan['id_kurir']) ? 'selected' : '';
                                                echo "<option value='$kr[id_kurir]' $selected>$kr[nama_kurir]</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <button type="submit" name="update_pesanan" class="btn btn-success w-100">Update Pesanan</button>

                                <?php elseif ($user_level == 'Kurir' && $pesanan['status_pesanan'] == 'Dikirim'): ?>
                                    <div class="mb-3">
                                        <label class="form-label">Ubah Status Pesanan</label>
                                        <select name="status_pesanan" class="form-control" required>
                                            <option value="Dikirim" selected>Dikirim</option>
                                            <option value="Selesai">Selesai</option>
                                        </select>
                                    </div>
                                    <button type="submit" name="update_pesanan" class="btn btn-success w-100">Update Status</button>

                                <?php else: ?>
                                    <p>Status Saat Ini:</p>
                                    <h4><span class="badge bg-primary"><?= $pesanan['status_pesanan']; ?></span></h4>
                                    <hr>
                                    <p>Kurir Bertugas:</p>
                                    <h5><?= $pesanan['nama_kurir'] ?? '<span class="text-muted">Belum Ditugaskan</span>'; ?></h5>
                                <?php endif; ?>
                            </form>
                        <?php endif; // Akhir dari pengecekan status Selesai/Dibatalkan 
                        ?>

                        <?php
                        // 🛡️ Kunci Keamanan: Proses update hanya jika pesanan belum selesai
                        if (isset($_POST['update_pesanan']) && $pesanan['status_pesanan'] != 'Selesai' && $pesanan['status_pesanan'] != 'Dibatalkan') {
                            $status_baru = $_POST['status_pesanan'];
                            $id_kurir_sql_part = "";
                            if ($user_level == 'Admin' || $user_level == 'Pimpinan') {
                                $id_kurir_baru = !empty($_POST['id_kurir']) ? "'" . $_POST['id_kurir'] . "'" : "NULL";
                                $id_kurir_sql_part = ", id_kurir = $id_kurir_baru";
                            }

                            $query = "UPDATE pesanan SET status_pesanan = '$status_baru' $id_kurir_sql_part WHERE id_pesanan = '$id_pesanan'";

                            // Kirim pesan WhatsApp ke kurir jika ditugaskan
                            if (!empty($_POST['id_kurir'])) {
                                $id_kurir_terpilih = $_POST['id_kurir'];
                                $kurir_result = $con->query("SELECT nama_kurir, telp FROM kurir WHERE id_kurir = '$id_kurir_terpilih'");

                                if ($kurir_result && $kurir_result->num_rows > 0) {
                                    $kurir_data = $kurir_result->fetch_assoc();
                                    $nama_kurir = $kurir_data['nama_kurir'];
                                    $no_wa_kurir = '62' . substr($kurir_data['telp'], 1); // ubah 08... → 628...

                                    // Ambil alamat petani untuk teks
                                    $alamat_pengambilan = "";
                                    $ambil_alamat = $con->query("
            SELECT DISTINCT pt.nama_petani, pt.alamat_petani 
            FROM detail_pesanan dp
            JOIN produk p ON dp.id_produk = p.id_produk
            JOIN petani pt ON p.id_petani = pt.id_petani
            WHERE dp.id_pesanan = '$id_pesanan'
        ");
                                    while ($row = $ambil_alamat->fetch_assoc()) {
                                        $alamat_pengambilan .= "📍 " . $row['nama_petani'] . " - " . $row['alamat_petani'] . "\n";
                                    }

                                    // Token Fonnte
                                    $token = "7HY2322NXBXt4LKDVpkU"; // Ganti dengan token milik Anda

                                    // Format pesan
                                    $pesan = "Halo *$nama_kurir*, Anda telah ditugaskan untuk mengantar pesanan berikut:\n\n" .
                                        "📦 *Kode Invoice:* $pesanan[kode_invoice]\n" .
                                        "🚚 *Status:* $status_baru\n\n" .
                                        "🧑‍🌾 *Alamat Pengambilan Produk:*\n$alamat_pengambilan\n" .
                                        "📍 *Alamat Tujuan:* $pesanan[alamat_pengiriman]\n\n" .
                                        "Silakan segera proses pengiriman. Terima kasih.";

                                    // Kirim via API Fonnte
                                    $curl = curl_init();
                                    curl_setopt_array($curl, array(
                                        CURLOPT_URL => 'https://api.fonnte.com/send',
                                        CURLOPT_RETURNTRANSFER => true,
                                        CURLOPT_ENCODING => '',
                                        CURLOPT_MAXREDIRS => 10,
                                        CURLOPT_TIMEOUT => 0,
                                        CURLOPT_FOLLOWLOCATION => true,
                                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                        CURLOPT_CUSTOMREQUEST => 'POST',
                                        CURLOPT_POSTFIELDS => array(
                                            'target' => $no_wa_kurir,
                                            'message' => $pesan,
                                            'countryCode' => '62'
                                        ),
                                        CURLOPT_HTTPHEADER => array(
                                            "Authorization: $token"
                                        ),
                                    ));
                                    $response = curl_exec($curl);
                                    curl_close($curl);
                                    // Optional: log atau tampilkan respons untuk debug
                                    // echo $response;
                                }
                            }


                            if ($con->query($query) === TRUE) {
                                echo "<script>
                                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Status pesanan berhasil diperbarui.' })
                                        .then((result) => { if (result.isConfirmed) { document.location.href = '?page=pesanan&aksi=detail&id_pesanan=$id_pesanan'; } });
                                      </script>";
                            } else {
                                echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan.' }); </script>";
                            }
                        }
                        ?>
                    </div>
                </div>
                <a href="?page=pesanan" class="btn btn-danger btn-sm mt-3">Kembali ke Daftar Pesanan</a>
                <br>
                <?php if ($pesanan['status_pesanan'] == 'Selesai' && empty($pesanan['bukti_sampai'])): ?>
                    <a href="?page=pesanan&aksi=tambah_sampai&id=<?= $data['id_pesanan']; ?>" class="btn btn-primary">📷 Upload Bukti Sampai</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>