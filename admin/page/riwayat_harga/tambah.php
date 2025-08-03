<div class="row">
    <div class="col-12">
        <div class="panel">
            <div class="panel-header">
                <h5>Ubah Harga Produk</h5>
            </div>
            <div class="panel-body">
                <div class="card mb-20">
                    <div class="card-header">Formulir Perubahan Harga</div>
                    <div class="card-body">
                        <form method="post">
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Pilih Produk</label>
                                <div class="col-sm-9">
                                    <select name="id_produk" class="form-control" required>
                                        <option value="">-- Pilih Produk yang Harganya Akan Diubah --</option>
                                        <?php
                                        $produk = $con->query("SELECT id_produk, nama_produk, harga FROM produk WHERE status_produk = 'Tersedia' ORDER BY nama_produk ASC");
                                        while($p = $produk->fetch_assoc()){
                                            echo "<option value='$p[id_produk]'>$p[nama_produk] (Harga saat ini: Rp ".number_format($p['harga']).")</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">Masukkan Harga Baru</label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control" name="harga_baru" placeholder="Contoh: 15000" required>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <label class="col-sm-3 col-form-label">&nbsp;</label>
                                <div class="col-sm-9">
                                    <button type="submit" name="simpan_harga" class="btn btn-primary btn-sm">Simpan Perubahan</button>
                                    <a href="?page=riwayat_harga" class="btn btn-danger btn-sm">Kembali</a>
                                </div>
                            </div>
                        </form>
                        <?php
                        if (isset($_POST['simpan_harga'])) {
                            $id_produk = $_POST['id_produk'];
                            $harga_baru = $_POST['harga_baru'];

                            // 1. Ambil harga lama dari produk yang dipilih
                            $ambil_harga_lama = $con->query("SELECT harga FROM produk WHERE id_produk = '$id_produk'");
                            $data_lama = $ambil_harga_lama->fetch_assoc();
                            $harga_lama = $data_lama['harga'];

                            // Cek jika harga baru sama dengan harga lama, tidak perlu proses
                            if ($harga_baru == $harga_lama) {
                                echo "<script> Swal.fire({ icon: 'info', title: 'Informasi', text: 'Harga baru sama dengan harga saat ini, tidak ada perubahan yang disimpan.' }); </script>";
                            } else {
                                // 2. Update harga di tabel produk
                                $update_produk = $con->query("UPDATE produk SET harga = '$harga_baru' WHERE id_produk = '$id_produk'");

                                if ($update_produk) {
                                    // 3. Jika update produk berhasil, catat ke tabel riwayat
                                    $con->query("INSERT INTO riwayat_harga (id_produk, harga_lama, harga_baru) VALUES ('$id_produk', '$harga_lama', '$harga_baru')");

                                    echo "<script>
                                            Swal.fire({ icon: 'success', title: 'Berhasil!', text: 'Harga produk berhasil diperbarui dan riwayatnya telah dicatat.' })
                                            .then((result) => { if (result.isConfirmed) { window.location.href = '?page=riwayat_harga'; } });
                                          </script>";
                                } else {
                                    echo "<script> Swal.fire({ icon: 'error', title: 'Gagal!', text: 'Terjadi kesalahan saat memperbarui harga produk.' }); </script>";
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