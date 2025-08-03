<?php
// Atur judul halaman dan panggil header
$page_title = "Akun Saya";
include 'header.php';

// PENGAMAN HALAMAN: Pastikan hanya pembeli yang sudah login yang bisa mengakses halaman ini
if (!isset($_SESSION['pembeli_id'])) {
    $_SESSION['pesan_notifikasi'] = "Anda harus login untuk mengakses halaman Akun Saya.";
    echo "<script>window.location.href='login.php';</script>";
    exit();
}

// Ambil ID pembeli dari session
$id_pembeli = $_SESSION['pembeli_id'];

// Query untuk mengambil semua pesanan milik pembeli yang sedang login
$query_pesanan = $con->query("SELECT * FROM pesanan WHERE id_pembeli = '$id_pembeli' ORDER BY tgl_pesan DESC");

?>

    <div class="breadcrumbs_area">
        <div class="container">   
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <h3>Akun Saya</h3>
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            <li>Riwayat Pesanan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>         
    </div>

    <section class="main_content_area">
        <div class="container">   
            <div class="account_dashboard_area">
                <div class="row">
                    <div class="col-sm-12 col-md-3 col-lg-3">
                        <div class="dashboard_tab_button">
                            <ul role="tablist" class="nav flex-column dashboard-list">
                                <li><a href="#orders" data-bs-toggle="tab" class="nav-link active">Riwayat Pesanan</a></li>
                                <li><a href="logout.php" class="nav-link">Logout</a></li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-9 col-lg-9">
                        <div class="tab-content dashboard_content">
                            <div class="tab-pane fade show active" id="orders" >
                                <h3>Riwayat Pesanan Anda</h3>
                                <div class="table-responsive">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Invoice</th>
                                                <th>Tanggal</th>
                                                <th>Status</th>
                                                <th>Total</th>
                                                <th>Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($query_pesanan->num_rows > 0): ?>
                                                <?php while ($pesanan = $query_pesanan->fetch_assoc()): 
                                                    // Logika untuk warna badge status
                                                    $status = $pesanan['status_pesanan'];
                                                    if ($status == 'Menunggu Pembayaran' || $status == 'Menunggu Verifikasi') {
                                                        $badge_color = 'bg-warning';
                                                    } elseif ($status == 'Diproses' || $status == 'Dikirim') {
                                                        $badge_color = 'bg-info';
                                                    } elseif ($status == 'Selesai') {
                                                        $badge_color = 'bg-success';
                                                    } else { // Dibatalkan
                                                        $badge_color = 'bg-danger';
                                                    }
                                                ?>
                                                <tr>
                                                    <td><?= $pesanan['kode_invoice']; ?></td>
                                                    <td><?= date("d M Y", strtotime($pesanan['tgl_pesan'])); ?></td>
                                                    <td><span class="badge <?= $badge_color; ?>"><?= $status; ?></span></td>
                                                    <td>Rp <?= number_format($pesanan['total_bayar']); ?></td>
                                                    <td class="d-flex flex-column gap-2">
                                                        <a href="detail_pesanan_pembeli.php?invoice=<?= $pesanan['kode_invoice']; ?>" class="btn btn-primary btn-sm">Lihat Detail</a>
                                                        <?php
                                                        // Tombol Lacak hanya muncul jika statusnya sudah dikirim atau selesai
                                                        if ($pesanan['status_pesanan'] == 'Dikirim' || $pesanan['status_pesanan'] == 'Selesai') :
                                                        ?>
                                                            <a href="lacak_pesanan.php?invoice=<?= $pesanan['kode_invoice']; ?>" class="btn btn-info btn-sm">
                                                                <i class="fa fa-map-marker"></i> Lacak Paket
                                                            </a>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endwhile; ?>
                                            <?php else: ?>
                                                <tr>
                                                    <td colspan="5" class="text-center">Anda belum memiliki riwayat pesanan.</td>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>  
        </div>        	
    </section>
    
<?php
include 'footer.php';
?>