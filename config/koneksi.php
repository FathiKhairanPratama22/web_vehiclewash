<?php
$host = "mysql";
$user = "root"; 
$pass = "password"; 
$db   = "myapp"; 

$conn = mysqli_connect($host, $user, $pass, $db);


if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
