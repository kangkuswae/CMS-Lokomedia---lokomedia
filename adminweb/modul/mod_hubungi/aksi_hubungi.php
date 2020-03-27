<?php
include "../../../config/koneksi.php";

$module=$_GET['module'];
$act=isset($_GET['act']) ? $_GET['act']:'';

// Hapus hubungi
if ($module=='hubungi' AND $act=='hapus'){
  mysqli_query($conn,"DELETE FROM hubungi WHERE id_hubungi='$_GET[id]'");
  header('location:../../media.php?module='.$module);
}
?>
