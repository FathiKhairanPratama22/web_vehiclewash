<?php
session_start();
include '../config/koneksi.php';
require('../lib/fpdf/fpdf.php'); 

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$tanggal_awal = $_GET['tanggal_awal'] ?? date('Y-m-d');
$tanggal_akhir = $_GET['tanggal_akhir'] ?? date('Y-m-d');

$query = "SELECT b.*, l.harga_mobil, l.harga_motor 
          FROM booking b
          JOIN layanan l ON b.layanan = l.nama_layanan
          WHERE b.status = 'Selesai' 
          AND (b.tanggal_booking BETWEEN '$tanggal_awal' AND '$tanggal_akhir')";
          
$result_laporan = mysqli_query($conn, $query);

$total_pendapatan = 0;
$total_booking = 0;
$data_laporan = []; 

if ($result_laporan) {
    while ($row = mysqli_fetch_assoc($result_laporan)) {
        $data_laporan[] = $row; 
        $total_booking++;
        if ($row['jenis_kendaraan'] == 'Mobil') {
            $total_pendapatan += $row['harga_mobil'];
        } else {
            $total_pendapatan += $row['harga_motor'];
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pendapatan - ProClean</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
   <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="sidebar">
    <h4 class="mb-4"><i class="bi bi-droplet-fill"></i> ProClean</h4>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link" href="dashboard.php"><i class="bi bi-grid-1x2-fill me-2"></i> Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="manajemen_layanan.php"><i class="bi bi-gear-fill me-2"></i> Manajemen Layanan</a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="laporan.php"><i class="bi bi-file-earmark-bar-graph-fill me-2"></i> Laporan</a>
        </li>
        <li class="nav-item mt-auto">
             <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
        </li>
    </ul>
</div>

<div class="main-content">
    <h2 class="mb-4">Laporan Pendapatan</h2>

    <div class="card-form mb-4">
        <form method="GET" action="laporan.php">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="tanggal_awal" class="form-control" value="<?= $tanggal_awal ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="tanggal_akhir" class="form-control" value="<?= $tanggal_akhir ?>">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary w-100">Tampilkan</button>
                    <a href="cetak_laporan.php?tanggal_awal=<?= $tanggal_awal ?>&tanggal_akhir=<?= $tanggal_akhir ?>" 
                       target="_blank" class="btn btn-danger w-100">
                       <i class="bi bi-filetype-pdf me-2"></i>Cetak PDF
                    </a>
                </div>
            </div>
        </form>
    </div>

    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card kpi-card">
                <div class="card-body">
                    <h5 class="card-title text-muted">Total Booking Selesai</h5>
                    <h2 class="fw-bold"><?= $total_booking ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card kpi-card">
                <div class="card-body">
                    <h5 class="card-title text-muted">Total Pendapatan</h5>
                    <h2 class="fw-bold text-success">Rp <?= number_format($total_pendapatan) ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="table-wrapper">
        <h5 class="mb-3">Detail Booking Selesai</h5>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Pelanggan</th>
                    <th>Tanggal</th>
                    <th>Kendaraan</th>
                    <th>Layanan</th>
                    <th>Harga</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_booking > 0): ?>
                    <?php $no = 1; foreach ($data_laporan as $row): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($row['nama']) ?></td>
                            <td><?= date('d M Y', strtotime($row['tanggal_booking'])) ?></td>
                            <td><?= htmlspecialchars($row['jenis_kendaraan']) ?></td>
                            <td><?= htmlspecialchars($row['layanan']) ?></td>
                            <td>
                                <?php
                                $harga = ($row['jenis_kendaraan'] == 'Mobil') ? $row['harga_mobil'] : $row['harga_motor'];
                                echo "Rp " . number_format($harga);
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center p-4">Tidak ada data untuk rentang tanggal ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<footer class="text-center text-muted mt-5 mb-3">
    <small>&copy; 2025 ProClean Vehicle Wash</small>
</footer>
</body>
</html>