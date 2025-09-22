<?php
$user_id = $_SESSION['user_id'];
$user_level = $_SESSION['user_level'];

// 🛡️ Kunci Keamanan: Petani tidak boleh mengakses halaman ini sama sekali.
if ($user_level == 'Petani') {
    echo "<script>Swal.fire('Akses Ditolak!', 'Anda tidak memiliki izin untuk mengakses halaman ini.', 'error').then(() => window.location.href = '?page=tracking');</script>";
    exit;
}
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Tambah Titik Lacak Baru</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">Formulir Penambahan Titik Lacak</div>
                    <div class="card-body">
                        <form method="post">
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Pilih Pesanan</label>
                                <div class="col-sm-9">
                                    <select name="id_pesanan" class="form-control" required>
                                        <option value="">-- Pilih Pesanan yang Sedang Dikirim --</option>
                                        <?php
                                        // 🔑 Kunci: Query dinamis untuk daftar pesanan
                                        $query_pesanan = "SELECT ps.id_pesanan, ps.kode_invoice, pm.nama_pembeli
                                                        FROM pesanan ps JOIN pembeli pm ON ps.id_pembeli = pm.id_pembeli
                                                        WHERE ps.status_pesanan = 'Dikirim'";

                                        // 🚚 Jika yang login adalah Kurir, filter berdasarkan ID kurir
                                        if ($user_level == 'Kurir') {
                                            $query_pesanan .= " AND ps.id_kurir = '$user_id'";
                                        }

                                        $query_pesanan .= " ORDER BY ps.tgl_pesan DESC";
                                        $pesanan_dikirim = $con->query($query_pesanan);

                                        while ($p = $pesanan_dikirim->fetch_assoc()) {
                                            echo "<option value='$p[id_pesanan]'>$p[kode_invoice] - a/n $p[nama_pembeli]</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Keterangan</label>
                                <div class="col-sm-9">
                                    <textarea name="keterangan" class="form-control" rows="3" placeholder="Contoh: Paket telah dijemput kurir dari lokasi petani." required></textarea>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Pilih Lokasi di Peta</label>
                                <div class="col-sm-9">
                                    <div id="map" style="height: 400px; width: 100%;"></div>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Estimasi Waktu Tiba</label>
                                <div class="col-sm-9">
                                    <select name="estimasi_tiba" class="form-control" required>
                                        <option value="">-- Pilih Estimasi Waktu --</option>
                                        <option value="00:05:00">5 Menit</option>
                                        <option value="00:15:00">15 Menit</option>
                                        <option value="00:25:00">25 Menit</option>
                                        <option value="00:40:00">40 Menit</option>
                                        <option value="01:00:00">1 Jam</option>
                                        <option value="01:30:00">1 Jam 30 Menit</option>
                                    </select>
                                    <small class="form-text text-muted">Pilih estimasi waktu tiba sejak titik lacak ini dicatat.</small>
                                </div>
                            </div>


                            <div class="row mb-3">
                                <label class="col-sm-3 col-form-label">Latitude</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="latitude" id="latitude" placeholder="Akan terisi otomatis dari peta" required readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Longitude</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="longitude" id="longitude" placeholder="Akan terisi otomatis dari peta" required readonly>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-9">
                                    <button type="submit" name="tambah" class="btn btn-primary btn-sm">Simpan Titik Lacak</button>
                                    <a href="?page=tracking" class="btn btn-danger btn-sm">Kembali</a>
                                </div>
                            </div>
                        </form>
                        <?php
                        // Logika PHP untuk insert tidak perlu diubah
                        if (isset($_POST['tambah'])) {
                            $id_pesanan = $_POST['id_pesanan'];
                            $keterangan = mysqli_real_escape_string($con, $_POST['keterangan']);
                            $latitude = mysqli_real_escape_string($con, $_POST['latitude']);
                            $longitude = mysqli_real_escape_string($con, $_POST['longitude']);
                            $estimasi_tiba = $_POST['estimasi_tiba']; // format HH:MM:SS

                            $query = "INSERT INTO tracking_pengiriman (id_pesanan, keterangan, latitude, longitude, estimasi_tiba)
              VALUES ('$id_pesanan', '$keterangan', '$latitude', '$longitude', '$estimasi_tiba')";


                            if ($con->query($query) === TRUE) {
                                echo "<script>
                                        Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Titik lacak baru berhasil ditambahkan.' })
                                        .then((result) => { if (result.isConfirmed) { window.location.href = '?page=tracking'; } });
                                      </script>";
                            } else {
                                echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan.' }); </script>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Kode Javascript untuk Peta tidak perlu diubah
    var initialLat = -2.9818;
    var initialLng = 115.2662;
    var map = L.map('map').setView([initialLat, initialLng], 14);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    var marker = L.marker([initialLat, initialLng], {
        draggable: true
    }).addTo(map);
    var latInput = document.getElementById('latitude');
    var lngInput = document.getElementById('longitude');
    latInput.value = initialLat;
    lngInput.value = initialLng;
    map.on('click', function(e) {
        var lat = e.latlng.lat.toFixed(6);
        var lng = e.latlng.lng.toFixed(6);
        marker.setLatLng(e.latlng);
        latInput.value = lat;
        lngInput.value = lng;
    });
    marker.on('dragend', function(e) {
        var lat = e.target.getLatLng().lat.toFixed(6);
        var lng = e.target.getLatLng().lng.toFixed(6);
        latInput.value = lat;
        lngInput.value = lng;
    });
</script>