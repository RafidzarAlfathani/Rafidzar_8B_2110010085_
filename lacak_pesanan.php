<?php
$page_title = "Lacak Pesanan";
include 'header.php';

// ===================================================================
// PENGAMAN HALAMAN & PENGAMBILAN DATA
// ===================================================================

// Cek login & parameter invoice
if (!isset($_SESSION['pembeli_id'])) {
    $_SESSION['pesan_notifikasi'] = "Anda harus login untuk melacak pesanan.";
    echo "<script>window.location.href='login.php';</script>";
    exit();
}
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

// Ambil semua data tracking, urutkan dari yang PALING BARU untuk kemudahan tampilan timeline
$query_tracking = $con->query("SELECT * FROM tracking_pengiriman WHERE id_pesanan = '$id_pesanan' ORDER BY waktu_update DESC");

$tracking_points = [];
if ($query_tracking->num_rows > 0) {
    while($row = $query_tracking->fetch_assoc()){
        $tracking_points[] = $row;
    }
}

// Ambil estimasi terbaru dari titik paling akhir (karena sudah diurutkan DESC, ini adalah item pertama)
$estimasi_terbaru = "";
if (!empty($tracking_points)) {
    $estimasi_terbaru = $tracking_points[0]['estimasi_tiba'];
}
?>

<style>
.timeline {
    position: relative;
    padding: 20px 0;
    list-style: none;
}
.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    left: 20px;
    height: 100%;
    width: 4px;
    background: #e9ecef;
}
.timeline-item {
    margin-bottom: 20px;
    position: relative;
    padding-left: 50px;
}
.timeline-item:last-child {
    margin-bottom: 0;
}
.timeline-icon {
    position: absolute;
    left: 7px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: #5a6e56; /* Warna hijau dari tema */
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    border: 3px solid #f7f6f2;
}
.timeline-content {
    background: #fff;
    padding: 15px;
    border-radius: 6px;
    border: 1px solid #e9ecef;
}
.timeline-content .keterangan {
    font-weight: 600;
    color: #333;
    margin-bottom: 5px;
}
.timeline-content .waktu {
    font-size: 0.85rem;
    color: #6c757d;
}
</style>


<div class="breadcrumbs_area">
    <div class="container">   
        <div class="row"><div class="col-12"><div class="breadcrumb_content">
            <h3>Lacak Pengiriman</h3>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="akun.php">Akun Saya</a></li>
                <li>Lacak Pesanan</li>
            </ul>
        </div></div></div>
    </div>         
</div>

<div class="checkout_page_bg" style="background-color: #f7f6f2;">
    <div class="container">
        <div class="card" style="border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.07); border-radius: 12px; padding: 30px;">
            <div class="card-body">
                <h4 class="mb-3">Status Pengiriman Invoice: <strong><?= $pesanan['kode_invoice']; ?></strong></h4>

                <?php if (!empty($estimasi_terbaru) && $estimasi_terbaru != '00:00:00'): ?>
                    <div class="alert alert-success" style="display: flex; align-items: center; gap: 15px;">
                        <i class="fa fa-clock-o fa-2x"></i>
                        <div>
                            <strong style="display: block; font-size: 1.1rem;">Estimasi Waktu Tiba</strong>
                            <span>Paket Anda diperkirakan akan tiba dalam <strong><?= htmlspecialchars(date('H \j\a\m i \m\e\n\i\t', strtotime($estimasi_terbaru))); ?></strong> dari posisi terakhir kurir.</span>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if (empty($tracking_points)): ?>
                    <div class="alert alert-info text-center mt-4">Belum ada informasi pelacakan untuk pesanan ini.</div>
                <?php else: ?>
                    <div class="row mt-4">
                        <div class="col-lg-7 col-md-12 mb-4">
                            <div id="map" style="height: 500px; width: 100%; border: 1px solid #ddd; border-radius: 8px;"></div>
                        </div>
                        
                        <div class="col-lg-5 col-md-12">
                            <h5>Riwayat Perjalanan</h5>
                            <div style="max-height: 500px; overflow-y: auto;">
                                <ul class="timeline">
                                    <?php foreach ($tracking_points as $index => $point): ?>
                                    <li class="timeline-item">
                                        <div class="timeline-icon">
                                            <i class="fa <?= ($index == 0) ? 'fa-map-marker' : 'fa-circle'; ?>"></i>
                                        </div>
                                        <div class="timeline-content">
                                            <p class="keterangan">
                                                <?= htmlspecialchars($point['keterangan']); ?>
                                                <?php if ($index == 0): // Tandai sebagai posisi terakhir ?>
                                                    <span class="badge bg-success ms-2">Posisi Terkini</span>
                                                <?php endif; ?>
                                            </p>
                                            <span class="waktu"><?= date("d M Y, H:i", strtotime($point['waktu_update'])); ?> WIB</span>
                                        </div>
                                    </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <a href="akun.php" class="btn btn-secondary mt-4"><i class="fa fa-arrow-left"></i> Kembali ke Riwayat Pesanan</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Balik urutan array agar titik awal berada di index 0 untuk menggambar rute
    var trackingPoints = <?= json_encode(array_reverse($tracking_points)); ?>;

    if (trackingPoints.length === 0) {
        return;
    }

    var startPoint = trackingPoints[0];
    var endPoint = trackingPoints[trackingPoints.length - 1];

    var map = L.map('map').setView([endPoint.latitude, endPoint.longitude], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    var latlngs = trackingPoints.map(point => [point.latitude, point.longitude]);

    var polyline = L.polyline(latlngs, {color: '#5a6e56', weight: 5}).addTo(map);

    trackingPoints.forEach(function(point, index) {
        var markerIcon;
        // Ikon untuk titik awal (keberangkatan)
        if (index === 0) {
            markerIcon = L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });
        // Ikon untuk titik akhir (posisi terkini)
        } else if (index === trackingPoints.length - 1) {
            markerIcon = L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });
        // Ikon untuk titik di tengah perjalanan
        } else {
             markerIcon = L.icon({ iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-grey.png', shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png', iconSize: [25, 41], iconAnchor: [12, 41], popupAnchor: [1, -34], shadowSize: [41, 41] });
        }
        
        L.marker([point.latitude, point.longitude], {icon: markerIcon})
         .addTo(map)
         .bindPopup('<b>' + point.keterangan + '</b><br>' + new Date(point.waktu_update).toLocaleString('id-ID'));
    });

    // Atur zoom dan posisi peta agar semua rute terlihat
    map.fitBounds(polyline.getBounds().pad(0.2));
});
</script>

<?php
include 'footer.php';
?>