<?php
$server = "localhost";
$username = "root";
$password = "";
$database = "db_lokomedia";

// Koneksi dan memilih database di server
// mysql_connect($server,$username,$password) or die("Koneksi gagal");
// mysql_select_db($database) or die("Database tidak bisa dibuka");

$conn = mysqli_connect($server, $username, $password, $database);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>
