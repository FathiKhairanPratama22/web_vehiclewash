<?php
include '../config/koneksi.php';

$tanggal = $_GET['tanggal'] ?? date('Y-m-d');
$jam_tersedia = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00'];
foreach ($jam_tersedia as $jam) {
    $query = "SELECT COUNT(*) AS total FROM booking WHERE jam_booking='$jam' AND tanggal_booking='$tanggal'";
    $result = mysqli_query($conn, $query); 
    $data = mysqli_fetch_assoc($result);

    $tersedia = 5 - (int)$data['total'];
    $disabled = $tersedia <= 0 ? 'disabled' : '';

    echo "<option value='$jam' $disabled>$jam (tersisa $tersedia slot)</option>";
}
?>
