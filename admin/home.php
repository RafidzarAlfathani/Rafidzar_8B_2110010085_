<?php
// Ambil peran dan ID pengguna yang sedang login
$user_level = $_SESSION['user_level'];
$user_id = $_SESSION['user_id'];

// ===================================================================
// --- LOGIKA & TAMPILAN UNTUK ADMIN / PIMPINAN 👑 ---
// ===================================================================
if ($user_level == 'Admin' || $user_level == 'Pimpinan') :

    // --- Bagian Logika PHP ---
    
    // 1. Widget Ringkasan
    $total_produk = $con->query("SELECT COUNT(*) as total FROM produk")->fetch_assoc()['total'];
    $total_petani = $con->query("SELECT COUNT(*) as total FROM petani WHERE status_akun = 'Aktif'")->fetch_assoc()['total'];
    $total_pembeli = $con->query("SELECT COUNT(*) as total FROM pembeli")->fetch_assoc()['total'];
    $pesanan_aktif = $con->query("SELECT COUNT(*) as total FROM pesanan WHERE status_pesanan IN ('Menunggu Verifikasi', 'Diproses', 'Dikirim')")->fetch_assoc()['total'];
    
    $hari_ini = date('Y-m-d');
    $pendapatan_hari_ini = $con->query("SELECT SUM(total_bayar) as total FROM pesanan WHERE DATE(tgl_bayar) = '$hari_ini' AND status_pesanan NOT IN ('Dibatalkan', 'Menunggu Pembayaran')")->fetch_assoc()['total'] ?? 0;

    // 2. Logika untuk Grafik & Laporan Produk
    $bulan_terpilih = isset($_GET['bulan']) ? (int)$_GET['bulan'] : date('m');
    $tahun_terpilih = isset($_GET['tahun']) ? (int)$_GET['tahun'] : date('Y');

    $q_tren_produk = $con->query("SELECT p.nama_produk, SUM(dp.jumlah) AS total_terjual
                                 FROM detail_pesanan dp
                                 JOIN produk p ON dp.id_produk = p.id_produk
                                 JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
                                 WHERE MONTH(ps.tgl_pesan) = $bulan_terpilih
                                   AND YEAR(ps.tgl_pesan) = $tahun_terpilih
                                   AND ps.status_pesanan != 'Dibatalkan'
                                 GROUP BY dp.id_produk, p.nama_produk
                                 ORDER BY total_terjual DESC
                                 LIMIT 7"); // Ambil 7 produk terlaris untuk grafik

    $data_chart_labels = [];
    $data_chart_values = [];
    while($data = $q_tren_produk->fetch_assoc()){
        $data_chart_labels[] = $data['nama_produk'];
        $data_chart_values[] = $data['total_terjual'];
    }
    // Reset pointer query untuk digunakan lagi di daftar
    $q_tren_produk->data_seek(0); 

    // 3. Pesanan Terbaru
    $query_pesanan_terbaru = $con->query("SELECT ps.id_pesanan, ps.kode_invoice, ps.total_bayar, ps.status_pesanan, ps.tgl_pesan, pm.nama_pembeli
                                        FROM pesanan ps JOIN pembeli pm ON ps.id_pembeli = pm.id_pembeli
                                        ORDER BY ps.tgl_pesan DESC LIMIT 5");
?>
    <div class="row mb-25">
        <div class="col-lg col-md-6 col-sm-6"><div class="card card-stats"><div class="card-body"><div class="row"><div class="col-5"><div class="icon-big text-center"><i class="fa fa-cubes text-primary"></i></div></div><div class="col-7 d-flex align-items-center"><div class="numbers"><p class="card-category">Total Produk</p><h4 class="card-title"><?= number_format($total_produk); ?></h4></div></div></div></div></div></div>
        <div class="col-lg col-md-6 col-sm-6"><div class="card card-stats"><div class="card-body"><div class="row"><div class="col-5"><div class="icon-big text-center"><i class="fa fa-users text-success"></i></div></div><div class="col-7 d-flex align-items-center"><div class="numbers"><p class="card-category">Total Petani</p><h4 class="card-title"><?= number_format($total_petani); ?></h4></div></div></div></div></div></div>
        <div class="col-lg col-md-6 col-sm-6"><div class="card card-stats"><div class="card-body"><div class="row"><div class="col-5"><div class="icon-big text-center"><i class="fa fa-user-friends text-info"></i></div></div><div class="col-7 d-flex align-items-center"><div class="numbers"><p class="card-category">Total Pembeli</p><h4 class="card-title"><?= number_format($total_pembeli); ?></h4></div></div></div></div></div></div>
        <div class="col-lg col-md-6 col-sm-6"><div class="card card-stats"><div class="card-body"><div class="row"><div class="col-5"><div class="icon-big text-center"><i class="fa fa-shopping-cart text-danger"></i></div></div><div class="col-7 d-flex align-items-center"><div class="numbers"><p class="card-category">Pesanan Aktif</p><h4 class="card-title"><?= number_format($pesanan_aktif); ?></h4></div></div></div></div></div></div>
        <div class="col-lg col-md-6 col-sm-6"><div class="card card-stats"><div class="card-body"><div class="row"><div class="col-5"><div class="icon-big text-center"><i class="fa fa-money-bill-wave text-success"></i></div></div><div class="col-7 d-flex align-items-center"><div class="numbers"><p class="card-category">Pendapatan Hari Ini</p><h4 class="card-title">Rp <?= number_format($pendapatan_hari_ini); ?></h4></div></div></div></div></div></div>
    </div>

    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Grafik Perbandingan Produk Terlaris</h5>
                    <form action="" method="GET" class="form-inline float-end" style="margin-top: -35px;">
                        <select name="bulan" class="form-control-sm me-2"><?php for ($i=1; $i<=12; $i++): ?><option value="<?= $i; ?>" <?= ($i == $bulan_terpilih) ? 'selected' : ''; ?>><?= date('F', mktime(0, 0, 0, $i, 10)); ?></option><?php endfor; ?></select>
                        <select name="tahun" class="form-control-sm me-2"><?php $tahun_awal = $con->query("SELECT YEAR(MIN(tgl_pesan)) as tahun FROM pesanan")->fetch_assoc()['tahun'] ?? date('Y'); $tahun_sekarang = date('Y'); for ($i = $tahun_sekarang; $i >= $tahun_awal; $i--): ?><option value="<?= $i; ?>" <?= ($i == $tahun_terpilih) ? 'selected' : ''; ?>><?= $i; ?></option><?php endfor; ?></select>
                        <button type="submit" class="btn btn-primary btn-sm">Tampilkan</button>
                    </form>
                </div>
                <div class="card-body">
                    <div id="produkChart"></div> </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header"><h5 class="card-title">Laporan Produk Terlaris</h5></div>
                <div class="card-body">
                    <h6>Periode: <strong><?= date('F', mktime(0, 0, 0, $bulan_terpilih, 10)); ?> <?= $tahun_terpilih; ?></strong></h6>
                    <ol>
                        <?php if($q_tren_produk->num_rows > 0): while($tren = $q_tren_produk->fetch_assoc()): ?>
                            <li><strong><?= htmlspecialchars($tren['nama_produk']); ?></strong> (Terjual: <?= $tren['total_terjual']; ?> item)</li>
                        <?php endwhile; else: ?>
                            <p>Tidak ada data penjualan untuk periode ini.</p>
                        <?php endif; ?>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5 class="card-title">Pesanan Terbaru</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="text-primary"><tr><th class="text-center">No.</th><th>Invoice</th><th>Nama Pembeli</th><th class="text-end">Total Bayar</th><th class="text-center">Status</th><th>Aksi</th></tr></thead>
                            <tbody>
                                <?php if ($query_pesanan_terbaru->num_rows > 0): $nomor = 1; while($pesanan = $query_pesanan_terbaru->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center"><?= $nomor++; ?></td>
                                    <td><?= $pesanan['kode_invoice']; ?></td>
                                    <td><?= htmlspecialchars($pesanan['nama_pembeli']); ?></td>
                                    <td class="text-end">Rp <?= number_format($pesanan['total_bayar']); ?></td>
                                    <td class="text-center"><span class="badge bg-info"><?= $pesanan['status_pesanan']; ?></span></td>
                                    <td class="text-center"><a href="?page=pesanan&aksi=detail&id_pesanan=<?= $pesanan['id_pesanan']; ?>" class="btn btn-primary btn-sm">Detail</a></td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="6" class="text-center">Belum ada pesanan.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


<?php
// ===================================================================
// --- LOGIKA & TAMPILAN UNTUK PETANI 👨‍🌾 ---
// ===================================================================
elseif ($user_level == 'Petani') :

    // --- Logika PHP untuk Petani ---
    $id_petani_login = $user_id;
    $total_produk_saya = $con->query("SELECT COUNT(*) as total FROM produk WHERE id_petani = '$id_petani_login'")->fetch_assoc()['total'];
    
    // Total pendapatan dari produknya yang status pesanannya sudah selesai
    $pendapatan_total_saya = $con->query("SELECT SUM(dp.sub_total) as total FROM detail_pesanan dp
                                        JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
                                        JOIN produk pr ON dp.id_produk = pr.id_produk
                                        WHERE pr.id_petani = '$id_petani_login' AND ps.status_pesanan = 'Selesai'")->fetch_assoc()['total'] ?? 0;
    
    // Pesanan aktif yang mengandung produknya
    $pesanan_aktif_saya = $con->query("SELECT COUNT(DISTINCT ps.id_pesanan) as total FROM pesanan ps
                                    JOIN detail_pesanan dp ON ps.id_pesanan = dp.id_pesanan
                                    JOIN produk pr ON dp.id_produk = pr.id_produk
                                    WHERE pr.id_petani = '$id_petani_login' AND ps.status_pesanan IN ('Menunggu Verifikasi', 'Diproses', 'Dikirim')")->fetch_assoc()['total'];

    // Produk saya terlaris (sepanjang waktu)
    $produk_terlaris_saya = $con->query("SELECT pr.nama_produk, SUM(dp.jumlah) as total_terjual FROM detail_pesanan dp
                                        JOIN produk pr ON dp.id_produk = pr.id_produk
                                        JOIN pesanan ps ON dp.id_pesanan = ps.id_pesanan
                                        WHERE pr.id_petani = '$id_petani_login' AND ps.status_pesanan != 'Dibatalkan'
                                        GROUP BY pr.id_produk, pr.nama_produk
                                        ORDER BY total_terjual DESC LIMIT 5");

    // Hitung total dana yang sudah ditarik (status Disetujui)
    $dana_ditarik = $con->query("SELECT SUM(jumlah_dana) as total_ditarik FROM pengajuan_dana_petani 
                             WHERE id_petani = '$id_petani_login' AND status = 'Disetujui'")
                   ->fetch_assoc()['total_ditarik'] ?? 0;

    // Hitung sisa saldo
    $sisa_saldo = $pendapatan_total_saya - $dana_ditarik;
    if ($sisa_saldo < 0) $sisa_saldo = 0;

?>
    <div class="alert alert-success" role="alert">
        <h4 class="alert-heading">Selamat Datang, <?= htmlspecialchars($_SESSION['user_nama']); ?>!</h4>
        <p>Ini adalah ringkasan aktivitas penjualan produk Anda. Terus tingkatkan kualitas dan stok produk Anda untuk mendapatkan lebih banyak pesanan.</p>
    </div>

    <div class="row mb-25">
        <div class="col-lg-4 col-md-6 col-sm-6"><div class="card card-stats"><div class="card-body"><div class="row"><div class="col-5"><div class="icon-big text-center"><i class="fa fa-cubes text-primary"></i></div></div><div class="col-7 d-flex align-items-center"><div class="numbers"><p class="card-category">Jumlah Produk Saya</p><h4 class="card-title"><?= number_format($total_produk_saya); ?></h4></div></div></div></div></div></div>
        <div class="col-lg-4 col-md-6 col-sm-6"><div class="card card-stats"><div class="card-body"><div class="row"><div class="col-5"><div class="icon-big text-center"><i class="fa fa-shopping-cart text-danger"></i></div></div><div class="col-7 d-flex align-items-center"><div class="numbers"><p class="card-category">Pesanan Aktif</p><h4 class="card-title"><?= number_format($pesanan_aktif_saya); ?></h4></div></div></div></div></div></div>
        <!-- <div class="col-lg-4 col-md-6 col-sm-6"><div class="card card-stats"><div class="card-body"><div class="row"><div class="col-5"><div class="icon-big text-center"><i class="fa fa-wallet text-success"></i></div></div><div class="col-7 d-flex align-items-center"><div class="numbers"><p class="card-category">Total Pendapatan</p><h4 class="card-title">Rp <?= number_format($pendapatan_total_saya); ?></h4></div></div></div></div></div></div> -->
  
        <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="card card-stats">
                <div class="card-body">
                    <div class="row">
                        <div class="col-5">
                            <div class="icon-big text-center">
                                <i class="fa fa-coins text-warning"></i>
                            </div>
                        </div>
                        <div class="col-7 d-flex align-items-center">
                            <div class="numbers">
                                <p class="card-category">Sisa Saldo</p>
                                <h4 class="card-title">Rp <?= number_format($sisa_saldo, 0, ',', '.'); ?></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5 class="card-title">5 Produk Anda yang Paling Laris</h5></div>
                <div class="card-body">
                    <?php if($produk_terlaris_saya->num_rows > 0): ?>
                    <ol>
                        <?php while($produk = $produk_terlaris_saya->fetch_assoc()): ?>
                            <li><strong><?= htmlspecialchars($produk['nama_produk']); ?></strong> (Terjual: <?= $produk['total_terjual']; ?> item)</li>
                        <?php endwhile; ?>
                    </ol>
                    <?php else: ?>
                    <p>Belum ada produk Anda yang terjual.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>


<?php
// ===================================================================
// --- LOGIKA & TAMPILAN UNTUK KURIR 🚚 ---
// ===================================================================
elseif ($user_level == 'Kurir') :

    // --- Logika PHP untuk Kurir ---
    $id_kurir_login = $user_id;
    $perlu_dikirim = $con->query("SELECT COUNT(*) as total FROM pesanan WHERE id_kurir = '$id_kurir_login' AND status_pesanan = 'Diproses'")->fetch_assoc()['total'];
    $sedang_dikirim = $con->query("SELECT COUNT(*) as total FROM pesanan WHERE id_kurir = '$id_kurir_login' AND status_pesanan = 'Dikirim'")->fetch_assoc()['total'];
    
    // 🔑 PERBAIKAN: Query dan variabel untuk 'Selesai Hari Ini' dihapus untuk menghindari error.
    // Kita tetap hitung total pesanan selesai untuk data lain.
    $selesai_total = $con->query("SELECT COUNT(*) as total FROM pesanan WHERE id_kurir = '$id_kurir_login' AND status_pesanan = 'Selesai'")->fetch_assoc()['total'];

    // Logika untuk data grafik pie
    $q_status_kurir = $con->query("SELECT status_pesanan, COUNT(*) as jumlah
                                 FROM pesanan
                                 WHERE id_kurir = '$id_kurir_login'
                                 GROUP BY status_pesanan");
    
// ===================================================================
// --- LOGIKA PERHITUNGAN SISA SALDO UNTUK KURIR 🚚 ---
// ===================================================================

$id_kurir_login = $user_id;

// Ambil total pendapatan dari pesanan yang sudah selesai
$q_total_pendapatan = $con->query("
    SELECT SUM(ongkir) AS total 
    FROM pesanan 
    WHERE id_kurir = '$id_kurir_login' AND status_pesanan = 'Selesai'
");

$total_pendapatan = 0;
if ($q_total_pendapatan) {
    $data_pendapatan = $q_total_pendapatan->fetch_assoc();
    $total_pendapatan = (int) ($data_pendapatan['total'] ?? 0);
} else {
    echo "<script>console.error('Query pendapatan gagal: " . $con->error . "');</script>";
}

// Ambil total penarikan dana yang sudah disetujui
$q_total_tarik = $con->query("
    SELECT SUM(jumlah_dana) AS total 
    FROM pengajuan_dana_kurir 
    WHERE id_kurir = '$id_kurir_login' AND status = 'Disetujui'
");

$total_tarik = 0;
if ($q_total_tarik) {
    $data_tarik = $q_total_tarik->fetch_assoc();
    $total_tarik = (int) ($data_tarik['total'] ?? 0);
} else {
    echo "<script>console.error('Query penarikan gagal: " . $con->error . "');</script>";
}

// Hitung sisa saldo
$sisa_saldo = $total_pendapatan - $total_tarik;
if ($sisa_saldo < 0) $sisa_saldo = 0;

// Debug (opsional, hapus di produksi)
echo "<pre>";
echo "Total Pendapatan: Rp " . number_format($total_pendapatan, 0, ',', '.') . "\n";
echo "Total Penarikan Disetujui: Rp " . number_format($total_tarik, 0, ',', '.') . "\n";
echo "Sisa Saldo: Rp " . number_format($sisa_saldo, 0, ',', '.') . "\n";
echo "</pre>";

?>

    <div class="alert alert-info" role="alert">
        <h4 class="alert-heading">Halo, Kurir <?= htmlspecialchars($_SESSION['user_nama']); ?>!</h4>
        <p>Berikut adalah ringkasan tugas pengiriman Anda. Mohon segera proses pesanan yang perlu dikirim.</p>
    </div>

    <div class="row mb-25">
        <div class="col-lg-4 col-md-6 col-sm-6"><div class="card card-stats"><div class="card-body"><div class="row"><div class="col-5"><div class="icon-big text-center"><i class="fa fa-box-open text-warning"></i></div></div><div class="col-7 d-flex align-items-center"><div class="numbers"><p class="card-category">Perlu Dikirim</p><h4 class="card-title"><?= number_format($perlu_dikirim); ?></h4></div></div></div></div></div></div>
        <div class="col-lg-4 col-md-6 col-sm-6"><div class="card card-stats"><div class="card-body"><div class="row"><div class="col-5"><div class="icon-big text-center"><i class="fa fa-truck text-info"></i></div></div><div class="col-7 d-flex align-items-center"><div class="numbers"><p class="card-category">Sedang Dikirim</p><h4 class="card-title"><?= number_format($sedang_dikirim); ?></h4></div></div></div></div></div></div>
        <div class="col-lg-4 col-md-6 col-sm-6"><div class="card card-stats"><div class="card-body"><div class="row"><div class="col-5"><div class="icon-big text-center"><i class="fa fa-star text-primary"></i></div></div><div class="col-7 d-flex align-items-center"><div class="numbers"><p class="card-category">Total Pesanan Selesai</p><h4 class="card-title"><?= number_format($selesai_total); ?></h4></div></div></div></div></div></div>
        <div class="col-lg-4 col-md-6 col-sm-6">
        <div class="card card-stats">
            <div class="card-body">
                <div class="row">
                    <div class="col-5">
                        <div class="icon-big text-center">
                            <i class="fa fa-wallet text-success"></i>
                        </div>
                    </div>
                    <div class="col-7 d-flex align-items-center">
                        <div class="numbers">
                            <p class="card-category">Sisa Saldo Anda</p>
                            <h4 class="card-title">Rp <?= number_format($sisa_saldo, 0, ',', '.'); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5 class="card-title">Grafik Komposisi Status Pesanan Anda</h5></div>
                <div class="card-body">
                    <div id="chartKurirContainer" style="height: 400px;"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header"><h5 class="card-title">Tugas Pengiriman Anda</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead><tr><th class="text-center">No.</th><th>Invoice</th><th>Pembeli</th><th>Alamat Pengiriman</th><th class="text-center">Status</th><th>Aksi</th></tr></thead>
                            <tbody>
                                <?php $tugas_pengiriman = $con->query("SELECT ps.id_pesanan, ps.kode_invoice, ps.alamat_pengiriman, pm.nama_pembeli, ps.status_pesanan FROM pesanan ps JOIN pembeli pm ON ps.id_pembeli = pm.id_pembeli WHERE ps.id_kurir = '$id_kurir_login' AND ps.status_pesanan IN ('Diproses', 'Dikirim') ORDER BY FIELD(ps.status_pesanan, 'Diproses', 'Dikirim'), ps.tgl_pesan ASC"); ?>
                                <?php if($tugas_pengiriman->num_rows > 0): $nomor = 1; while($tugas = $tugas_pengiriman->fetch_assoc()): ?>
                                <tr>
                                    <td class="text-center"><?= $nomor++; ?></td>
                                    <td><?= $tugas['kode_invoice']; ?></td>
                                    <td><?= htmlspecialchars($tugas['nama_pembeli']); ?></td>
                                    <td><?= htmlspecialchars($tugas['alamat_pengiriman']); ?></td>
                                    <td class="text-center"><span class="badge <?= ($tugas['status_pesanan'] == 'Diproses') ? 'bg-warning' : 'bg-info'; ?>"><?= $tugas['status_pesanan']; ?></span></td>
                                    <td><a href="?page=pesanan&aksi=detail&id_pesanan=<?= $tugas['id_pesanan']; ?>" class="btn btn-sm btn-primary">Lihat & Update</a></td>
                                </tr>
                                <?php endwhile; else: ?>
                                <tr><td colspan="6" class="text-center">Tidak ada tugas pengiriman saat ini.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; // Akhir dari pengecekan peran ?>

<?php if ($user_level == 'Admin' || $user_level == 'Pimpinan') : ?>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var options = {
        series: [{
            name: 'Jumlah Terjual',
            data: <?= json_encode($data_chart_values); ?>
        }],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            }
        },
        plotOptions: {
            bar: {
                horizontal: false,
                columnWidth: '55%',
                endingShape: 'rounded'
            },
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: <?= json_encode($data_chart_labels); ?>,
        },
        yaxis: {
            title: {
                text: 'Jumlah Item Terjual'
            }
        },
        fill: {
            opacity: 1
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + " item"
                }
            }
        },
        colors: ['#007bff'] // Warna biru primer
    };

    var chart = new ApexCharts(document.querySelector("#produkChart"), options);
    chart.render();
});
</script>
<?php endif; ?>