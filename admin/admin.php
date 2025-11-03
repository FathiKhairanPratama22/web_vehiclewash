<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

include '../config/koneksi.php';


$tanggal_filter = isset($_GET['tanggal_booking']) ? $_GET['tanggal_booking'] : '';
if ($tanggal_filter != '') {
    $query = "SELECT * FROM booking WHERE tanggal_booking='$tanggal_filter' ORDER BY jam_booking ASC";
} else {
    $query = "SELECT * FROM booking ORDER BY tanggal_booking DESC, jam_booking ASC";
}
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Booking Cuci Kendaraan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <div class="container">
        
        
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>📋 Daftar Booking Cuci Kendaraan</h2>
            <a href="logout.php" class="btn btn-danger btn-sm">Logout</a>
        </div>
        

        
        <form method="GET" class="mb-3">
            <div class="row g-2">
                <div class="col-auto">
                    <input type="date" name="tanggal" value="<?= $tanggal_filter ?>" class="form-control">
                </div>
                <div class="col-auto">
                    <button class="btn btn-primary">Tampilkan</button>
                </div>
                <div class="col-auto">
                    <a href="admin.php" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>

        
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>No HP</th>
                    <th>Jenis Kendaraan</th>
                    <th>Layanan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                if (mysqli_num_rows($result) > 0) {
                    while ($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>
                                <td>{$no}</td>
                                <td>{$row['nama']}</td>
                                <td>{$row['no_hp']}</td>
                                <td>{$row['jenis_kendaraan']}</td>
                                <td>{$row['layanan']}</td>
                                <td>{$row['tanggal_booking']}</td>
                                <td>{$row['jam_booking']}</td>
                              </tr>";
                        $no++;
                    }
                } else {
                    echo "<tr><td colspan='7' class='text-center'>Tidak ada data</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>