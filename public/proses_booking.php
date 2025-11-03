<?php
include '../config/koneksi.php';

$nama = $_POST['nama'];
$no_hp = $_POST['no_hp'];
$jenis = $_POST['jenis_kendaraan'];
$id_layanan = $_POST['layanan']; 
$tanggal = $_POST['tanggal_booking'];
$jam = $_POST['jam_booking'];

$q_layanan = mysqli_query($conn, "SELECT nama_layanan FROM layanan WHERE id_layanan = '$id_layanan'");
if(mysqli_num_rows($q_layanan) > 0) {
    $data_layanan = mysqli_fetch_assoc($q_layanan);
    $layanan_nama = $data_layanan['nama_layanan']; 
} else {
    
    $layanan_nama = 'Layanan Tidak Dikenali'; 
}

$cek = mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE jam_booking='$jam' AND tanggal_booking='$tanggal'");
$data = mysqli_fetch_assoc($cek);

if ($data['total'] >= 5) {
    header("Location: index.php?status=penuh");
    exit();
}

$query = "INSERT INTO booking (nama, no_hp, jenis_kendaraan, layanan, tanggal_booking, jam_booking, status)
          VALUES ('$nama', '$no_hp', '$jenis', '$layanan_nama', '$tanggal', '$jam', 'Belum Dicuci')";

if (mysqli_query($conn, $query)) {
    header("Location: index.php?status=sukses");
    exit();
} else {
    header("Location: index.php?status=gagal");
    exit();
}
?>