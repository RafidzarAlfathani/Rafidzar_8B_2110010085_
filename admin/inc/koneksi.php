<?php
date_default_timezone_set('Asia/Makassar'); 
	$con = mysqli_connect("localhost","root","","db_ecom_tani");

	if (!$con){
		echo "Koneksi Ke Database Gagal";
	} 

?> 
 
<?php $ambil_meta=$con->query("SELECT * FROM meta"); ?>
<?php $meta = $ambil_meta->fetch_assoc(); ?> 

<?php  