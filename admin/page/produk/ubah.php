<?php
$id_produk = $_GET['id_produk'];
$sql = $con->query("SELECT * FROM produk WHERE id_produk='$id_produk'");
$row = mysqli_fetch_assoc($sql);

// 🛡️ Kunci Keamanan: Cek kepemilikan data
if ($_SESSION['user_level'] == 'Petani' && $row['id_petani'] != $_SESSION['user_id']) {
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Akses Ditolak!',
                text: 'Anda tidak memiliki izin untuk mengubah produk ini.'
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = '?page=produk'; }
            });
          </script>";
    exit;
}
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Manajemen Produk</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">Ubah Data Produk <b>"<?= $row['nama_produk']; ?>"</b></div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Petani Penjual</label>
                                <div class="col-sm-9">
                                     <?php if ($_SESSION['user_level'] == 'Admin' || $_SESSION['user_level'] == 'Pimpinan'): ?>
                                        <select name="id_petani" class="form-control" required>
                                            <?php
                                            $petani = $con->query("SELECT id_petani, nama_petani FROM petani WHERE status_akun = 'Aktif' ORDER BY nama_petani ASC");
                                            while ($p = $petani->fetch_assoc()) {
                                                $selected = ($p['id_petani'] == $row['id_petani']) ? 'selected' : '';
                                                echo "<option value='$p[id_petani]' $selected>$p[nama_petani]</option>";
                                            }
                                            ?>
                                        </select>
                                    <?php else: ?>
                                        <input type="hidden" name="id_petani" value="<?= $row['id_petani']; ?>">
                                        <input type="text" class="form-control" value="<?= $_SESSION['user_nama']; ?>" readonly>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="row mb-2"><label class="col-sm-3 col-form-label">Kategori Produk</label><div class="col-sm-9"><select name="id_kategori" class="form-control" required><?php $kategori = $con->query("SELECT * FROM kategori_produk ORDER BY nama_kategori ASC"); while ($k = $kategori->fetch_assoc()) { $selected = ($k['id_kategori'] == $row['id_kategori']) ? 'selected' : ''; echo "<option value='$k[id_kategori]' $selected>$k[nama_kategori]</option>"; } ?></select></div></div>
                            <div class="row mb-2"><label class="col-sm-3 col-form-label">Nama Produk</label><div class="col-sm-9"><input type="text" class="form-control" name="nama_produk" value="<?= $row['nama_produk']; ?>" required></div></div>
                            <div class="row mb-2"><label class="col-sm-3 col-form-label">Deskripsi</label><div class="col-sm-9"><textarea name="deskripsi" class="form-control" rows="3" required><?= $row['deskripsi']; ?></textarea></div></div>
                            <div class="row mb-2"><label class="col-sm-3 col-form-label">Harga</label><div class="col-sm-9"><input type="number" class="form-control" name="harga" value="<?= $row['harga']; ?>" required></div></div>
                            <div class="row mb-2"><label class="col-sm-3 col-form-label">Satuan</label><div class="col-sm-9"><input type="text" class="form-control" name="satuan" value="<?= $row['satuan']; ?>" required></div></div>
                            <div class="row mb-2"><label class="col-sm-3 col-form-label">Stok</label><div class="col-sm-9"><input type="number" class="form-control" name="stok" value="<?= $row['stok']; ?>" required></div></div>
                            <div class="row mb-2"><label class="col-sm-3 col-form-label" for="minimum_pembelian">Minimum Pembelian</label><div class="col-sm-9"><input type="number" name="minimum_pembelian" id="minimum_pembelian" class="form-control" min="1" value="<?= $row['minimum_pembelian']; ?>" required></div></div>


                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Tanggal Panen</label>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" name="tanggal_panen" value="<?= $row['tanggal_panen']; ?>">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Foto Saat Panen</label>
                                <div class="col-sm-9">
                                    <?php if (!empty($row['foto_panen'])): ?>
                                        <img src="images/panen/<?= $row['foto_panen']; ?>" width="100" class="mb-2"> <br>
                                    <?php endif; ?>
                                    <small>Ganti foto jika perlu.</small>
                                    <input type="file" class="form-control" name="foto_panen">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Foto Produk Utama</label>
                                <div class="col-sm-9">
                                    <img src="images/produk/<?= $row['foto_produk']; ?>" width="100" class="mb-2"> <br>
                                    <small>Ganti foto jika perlu.</small>
                                    <input type="file" class="form-control" name="foto_produk">
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-9">
                                    <button type="submit" name="ubah" class="btn btn-success btn-sm">Ubah</button>
                                    <a href="?page=produk" class="btn btn-danger btn-sm">Kembali</a>
                                </div>
                            </div>
                        </form>
                        <?php
                        if (isset($_POST['ubah'])) {
                            $id_petani = $_POST['id_petani'];
                            $id_kategori = $_POST['id_kategori'];
                            $nama_produk = mysqli_real_escape_string($con, $_POST['nama_produk']);
                            $deskripsi = mysqli_real_escape_string($con, $_POST['deskripsi']);
                            $harga_baru = $_POST['harga'];
                            $harga_lama = $row['harga'];
                            $satuan = mysqli_real_escape_string($con, $_POST['satuan']);
                            $stok = $_POST['stok'];
                            $minimum_pembelian = $_POST['minimum_pembelian'];
                            
                            // Ambil data baru
                            $tanggal_panen = $_POST['tanggal_panen'];
                            $tanggal_panen_sql = !empty($tanggal_panen) ? "'$tanggal_panen'" : "NULL";
                            
                            // Proses Foto Produk Utama
                            $foto_produk = $_FILES['foto_produk']['name'];
                            if (!empty($foto_produk)) {
                                if (!empty($row['foto_produk'])) unlink("images/produk/" . $row['foto_produk']);
                                move_uploaded_file($_FILES['foto_produk']['tmp_name'], "images/produk/" . $foto_produk);
                            } else {
                                $foto_produk = $row['foto_produk'];
                            }

                            // Proses Foto Panen
                            $foto_panen = $_FILES['foto_panen']['name'];
                            if (!empty($foto_panen)) {
                                if (!empty($row['foto_panen'])) unlink("images/panen/" . $row['foto_panen']);
                                move_uploaded_file($_FILES['foto_panen']['tmp_name'], "images/panen/" . $foto_panen);
                            } else {
                                $foto_panen = $row['foto_panen'];
                            }
                            $foto_panen_sql = !empty($foto_panen) ? "'$foto_panen'" : "NULL";

                            if ($harga_baru != $harga_lama) {
                                $con->query("INSERT INTO riwayat_harga (id_produk, harga_lama, harga_baru) VALUES ('$id_produk', '$harga_lama', '$harga_baru')");
                            }

                            $query = "UPDATE produk SET
                                         id_petani = '$id_petani',
                                         id_kategori = '$id_kategori',
                                         nama_produk = '$nama_produk',
                                         deskripsi = '$deskripsi',
                                         harga = '$harga_baru',
                                         satuan = '$satuan',
                                         stok = '$stok',
                                         minimum_pembelian = '$minimum_pembelian',
                                         tanggal_panen = $tanggal_panen_sql,
                                         foto_panen = $foto_panen_sql,
                                         foto_produk = '$foto_produk'
                                       WHERE id_produk='$id_produk'";

                            if ($con->query($query) === TRUE) {
                                echo "<script>
                                             Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Produk berhasil diperbarui.' })
                                             .then((result) => { if (result.isConfirmed) { window.location.href = '?page=produk'; } });
                                           </script>";
                            } else {
                                echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan: " . $con->error . "' }); </script>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>