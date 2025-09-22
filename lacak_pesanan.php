<?php
$page_title = "Lacak Pesanan";
include 'header.php';

// ===================================================================
// PENGAMAN HALAMAN & PENGAMBILAN DATA
// ===================================================================

// Cek login
if (!isset($_SESSION['pembeli_id'])) {
    $_SESSION['pesan_notifikasi'] = "Anda harus login untuk melacak pesanan.";
    echo "<script>window.location.href='login.php';</script>";
    exit();
}
// Cek parameter invoice
if (!isset($_GET['invoice']) || empty($_GET['invoice'])) {
    echo "<script>window.location.href='akun.php';</script>";
    exit();
}

$id_pembeli = $_SESSION['pembeli_id'];
$kode_invoice = mysqli_real_escape_string($con, $_GET['invoice']);

// Query keamanan: pastikan invoice ini milik pembeli yang sedang login
$sql_pesanan = $con->query("SELECT * FROM pesanan WHERE id_pembeli = '$id_pembeli' AND kode_invoice = '$kode_invoice'");

if ($sql_pesanan->num_rows == 0) {
    echo "<script>alert('Pesanan tidak ditemukan atau bukan milik Anda.'); window.location.href='akun.php';</script>";
    exit();
}

$pesanan = $sql_pesanan->fetch_assoc();
$id_pesanan = $pesanan['id_pesanan'];

// Ambil semua data tracking untuk pesanan ini, urutkan dari yang paling awal
$query_tracking = $con->query("SELECT * FROM tracking_pengiriman WHERE id_pesanan = '$id_pesanan' ORDER BY waktu_update ASC");

// Simpan data tracking ke dalam sebuah array PHP
$tracking_points = [];
if ($query_tracking->num_rows > 0) {
    while($row = $query_tracking->fetch_assoc()){
        $tracking_points[] = $row;
    }
}
$estimasi_terbaru = "";
if (!empty($tracking_points)) {
    $last_point = end($tracking_points);
    $estimasi_terbaru = $last_point['estimasi_tiba'];
}

?>

    <div class="breadcrumbs_area">
        <div class="container">   
            <div class="row">
                <div class="col-12">
                    <div class="breadcrumb_content">
                        <h3>Lacak Pengiriman</h3>
                        <ul>
                            <li><a href="index.php">Home</a></li>
                            <li><a href="akun.php">Akun Saya</a></li>
                            <li>Lacak Pesanan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>         
    </div>

    <div class="checkout_page_bg">
        <div class="container">
            <h4 class="mb-3">Status Pengiriman untuk Invoice: <?= $pesanan['kode_invoice']; ?></h4>
            <?php if (!empty($estimasi_terbaru)): ?>
    <div class="alert alert-success">
        <strong>Estimasi Tiba:</strong> <?= htmlspecialchars($estimasi_terbaru); ?>
    </div>
<?php endif; ?>

            <?php if (empty($tracking_points)): ?>
                <div class="alert alert-info text-center">Belum ada informasi pelacakan untuk pesanan ini.</div>
            <?php else: ?>
                <div class="row">
                    <div class="col-lg-7 col-md-12">
                        <div id="map" style="height: 500px; width: 100%; border: 1px solid #ddd; border-radius: 8px;"></div>
                    </div>
                    
                    <div class="col-lg-5 col-md-12">
                        <h5>Riwayat Perjalanan</h5>
                        <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                            <table class="table table-striped">
                                <tbody>
                                    <?php
                                    // Balik urutan array untuk menampilkan yang terbaru di atas
                                    foreach (array_reverse($tracking_points) as $index => $point):
                                    ?>
                                    <tr>
                                        <td style="width: 150px;">
                                            <?= date("d M Y", strtotime($point['waktu_update'])); ?><br>
                                            <small><?= date("H:i", strtotime($point['waktu_update'])); ?> WIB</small>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($point['keterangan']); ?></strong>
                                            <?php if ($index == 0): // Tandai sebagai posisi terakhir ?>
                                                <span class="badge bg-success ms-2">Posisi Terkini</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <a href="akun.php" class="btn btn-secondary mt-4"><i class="fa fa-arrow-left"></i> Kembali ke Riwayat Pesanan</a>
        </div>
    </div>

<script>
// Menunggu seluruh halaman HTML selesai dimuat
document.addEventListener('DOMContentLoaded', function() {
    // Ambil data tracking dari PHP dan ubah menjadi objek JavaScript
    var trackingPoints = <?= json_encode($tracking_points); ?>;

    // Jangan jalankan skrip peta jika tidak ada titik lacak
    if (trackingPoints.length === 0) {
        return;
    }

    // Tentukan titik awal dan akhir
    var startPoint = trackingPoints[0];
    var endPoint = trackingPoints[trackingPoints.length - 1];

    // Inisialisasi Peta, berpusat pada titik terakhir
    var map = L.map('map').setView([endPoint.latitude, endPoint.longitude], 15);

    // Tambahkan Tile Layer dari OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Buat array berisi koordinat [lat, lng] untuk menggambar garis
    var latlngs = trackingPoints.map(function(point) {
        return [point.latitude, point.longitude];
    });

    // Gambar garis rute di peta
    var polyline = L.polyline(latlngs, {color: 'blue'}).addTo(map);

    // Tambahkan marker untuk setiap titik
    trackingPoints.forEach(function(point, index) {
        var markerIcon;
        // Buat ikon khusus untuk titik awal (hijau) dan akhir (merah)
        if (index === 0) { // Titik Awal
            markerIcon = L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });
        } else if (index === trackingPoints.length - 1) { // Titik Akhir
            markerIcon = L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });
        } else { // Titik di tengah
             markerIcon = L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });
        }
        
        L.marker([point.latitude, point.longitude], {icon: markerIcon})
         .addTo(map)
         .bindPopup('<b>' + point.keterangan + '</b><br>' + new Date(point.waktu_update).toLocaleString('id-ID'));
    });

    // Atur zoom dan posisi peta agar semua rute terlihat
    map.fitBounds(polyline.getBounds().pad(0.1));
});
</script>

<?php
include 'footer.php';
?>