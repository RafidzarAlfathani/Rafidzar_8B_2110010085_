<?php
$id_kategori = $_GET['id_kategori'];
$sql = $con->query("SELECT * FROM kategori_produk WHERE id_kategori='$id_kategori'");
$row = mysqli_fetch_assoc($sql);
?>

<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Manajemen Kategori</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">Ubah Kategori Produk</div>
                    <div class="card-body">
                        <form method="post">
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Nama Kategori</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" name="nama_kategori" value="<?= $row['nama_kategori']; ?>" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-9">
                                    <button type="submit" name="ubah" class="btn btn-success btn-sm">Ubah</button>
                                    <a href="?page=kategori_produk" class="btn btn-danger btn-sm">Kembali</a>
                                </div>
                            </div>
                        </form>
                        <?php
                        if (isset($_POST['ubah'])) {
                            $nama_kategori = mysqli_real_escape_string($con, $_POST['nama_kategori']);

                            // Cek duplikasi dengan nama lain
                            $cek = $con->query("SELECT * FROM kategori_produk WHERE nama_kategori = '$nama_kategori' AND id_kategori != '$id_kategori'");
                            if($cek->num_rows > 0) {
                                echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Nama kategori sudah ada, silakan gunakan nama lain.' }); </script>";
                            } else {
                                $query = "UPDATE kategori_produk SET nama_kategori = '$nama_kategori' WHERE id_kategori = '$id_kategori'";
                                if ($con->query($query) === TRUE) {
                                    echo "<script>
                                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Kategori berhasil diperbarui.' })
                                            .then((result) => { if (result.isConfirmed) { window.location.href = '?page=kategori_produk'; } });
                                          </script>";
                                } else {
                                    echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat memperbarui data.' }); </script>";
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