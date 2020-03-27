<?php
$id = isset($_GET['id']);
$sql = mysqli_query($conn,"select judul from berita where id_berita='$id'");
$j   = mysqli_fetch_array($sql);

if (ISSET($_GET['id'])){
		echo "$j[judul]";
}
else{
		echo "bukulokomedia.com - penerbit lokomedia yogyakarta";
}
?>
