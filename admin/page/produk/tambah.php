<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Manajemen Produk</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">Tambah Data Produk</div>
                    <div class="card-body">
                        <form method="post" enctype="multipart/form-data">

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Petani Penjual</label>
                                <div class="col-sm-9">
                                    <?php if ($_SESSION['user_level'] == 'Admin' || $_SESSION['user_level'] == 'Pimpinan'): ?>
                                        <select name="id_petani" class="form-control" required>
                                            <option value="">-- Pilih Petani --</option>
                                            <?php
                                            $petani = $con->query("SELECT id_petani, nama_petani FROM petani WHERE status_akun = 'Aktif' ORDER BY nama_petani ASC");
                                            while ($p = $petani->fetch_assoc()) {
                                                echo "<option value='$p[id_petani]'>$p[nama_petani]</option>";
                                            }
                                            ?>
                                        </select>
                                    <?php else: ?>
                                        <input type="hidden" name="id_petani" value="<?= $_SESSION['user_id']; ?>">
                                        <input type="text" class="form-control" value="<?= $_SESSION['user_nama']; ?>" readonly>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Kategori Produk</label>
                                <div class="col-sm-9">
                                    <select name="id_kategori" class="form-control" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        <?php
                                        $kategori = $con->query("SELECT * FROM kategori_produk ORDER BY nama_kategori ASC");
                                        while ($k = $kategori->fetch_assoc()) {
                                            echo "<option value='$k[id_kategori]'>$k[nama_kategori]</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Nama Produk</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="nama_produk" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Deskripsi</label>
                                <div class="col-sm-9">
                                    <textarea name="deskripsi" class="form-control" rows="3" required></textarea>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Harga</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" name="harga" placeholder="Contoh: 5000" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Satuan</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="satuan" placeholder="Contoh: kg, ikat, buah" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Stok</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" name="stok" placeholder="Contoh: 100" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label" for="minimum_pembelian">Minimum Pembelian</label>
                                <div class="col-sm-9">
                                    <input type="number" name="minimum_pembelian" id="minimum_pembelian" class="form-control" min="1" required>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Status Produk</label>
                                <div class="col-sm-9">
                                    <?php if ($_SESSION['user_level'] == 'Admin' || $_SESSION['user_level'] == 'Pimpinan'): ?>
                                        <select name="status_produk" class="form-control">
                                            <option value="Menunggu Verifikasi">Menunggu Verifikasi</option>
                                            <option value="Tersedia">Tersedia</option>
                                            <option value="Habis">Habis</option>
                                        </select>
                                    <?php else: ?>
                                        <input type="text" class="form-control" name="status_produk" value="Tersedia" readonly>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Foto Produk</label>
                                <div class="col-sm-9">
                                    <input type="file" class="form-control" name="foto_produk" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-9">
                                    <button type="submit" name="tambah" class="btn btn-primary btn-sm">Simpan</button>
                                    <a href="?page=produk" class="btn btn-danger btn-sm">Kembali</a>
                                </div>
                            </div>

                        </form>
                        <?php
                        // Logika PHP untuk insert tidak perlu diubah, karena form sudah mengirimkan data yang benar
                        if (isset($_POST['tambah'])) {
                            // ... (kode PHP Anda untuk INSERT) ...
                            $id_petani = $_POST['id_petani'];
                            $id_kategori = $_POST['id_kategori'];
                            $nama_produk = mysqli_real_escape_string($con, $_POST['nama_produk']);
                            $deskripsi = mysqli_real_escape_string($con, $_POST['deskripsi']);
                            $harga = $_POST['harga'];
                            $satuan = mysqli_real_escape_string($con, $_POST['satuan']);
                            $stok = $_POST['stok'];
                            $minimum_pembelian = $_POST['minimum_pembelian'];
                            $status_produk = $_POST['status_produk'];
                            $foto_produk = $_FILES['foto_produk']['name'];

                            $foto_tmp = $_FILES['foto_produk']['tmp_name'];
                            $foto_dir = "images/produk/" . $foto_produk;
                            move_uploaded_file($foto_tmp, $foto_dir);

                            $query = "INSERT INTO produk (id_petani, id_kategori, nama_produk, deskripsi, harga, satuan, stok, status_produk, foto_produk, minimum_pembelian)
                                       VALUES ('$id_petani', '$id_kategori', '$nama_produk', '$deskripsi', '$harga', '$satuan', '$stok', '$status_produk', '$foto_produk', '$minimum_pembelian')";

                            if ($con->query($query) === TRUE) {
                                echo "<script>
                                         Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Produk berhasil ditambahkan.' })
                                         .then((result) => { if (result.isConfirmed) { window.location.href = '?page=produk'; } });
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