<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Manajemen Kategori</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">Tambah Kategori Produk</div>
                    <div class="card-body">
                        <form method="post">
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Nama Kategori</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="nama_kategori" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-9">
                                    <button type="submit" name="tambah" class="btn btn-primary btn-sm">Simpan</button>
                                    <a href="?page=kategori_produk" class="btn btn-danger btn-sm">Kembali</a>
                                </div>
                            </div>
                        </form>
                        <?php
                        if (isset($_POST['tambah'])) {
                            $nama_kategori = mysqli_real_escape_string($con, $_POST['nama_kategori']);

                            // Cek apakah kategori sudah ada
                            $cek = $con->query("SELECT * FROM kategori_produk WHERE nama_kategori = '$nama_kategori'");
                            if ($cek->num_rows > 0) {
                                echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Nama kategori sudah ada, silakan gunakan nama lain.' }); </script>";
                            } else {
                                $query = "INSERT INTO kategori_produk (nama_kategori) VALUES ('$nama_kategori')";
                                if ($con->query($query) === TRUE) {
                                    echo "<script>
                                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Kategori berhasil ditambahkan.' })
                                            .then((result) => { if (result.isConfirmed) { window.location.href = '?page=kategori_produk'; } });
                                          </script>";
                                } else {
                                    echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat menyimpan data.' }); </script>";
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>