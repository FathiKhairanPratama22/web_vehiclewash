<?php
session_start();
include '../config/koneksi.php';


if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}


if (isset($_GET['konfirmasi'])) {
    $id = $_GET['konfirmasi'];
    $stmt = $conn->prepare("UPDATE booking SET status='Selesai' WHERE id_booking = ?");
    $stmt->bind_param("i", $id); // 'i' untuk integer
    $stmt->execute();
    exit;
}


if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $conn->prepare("DELETE FROM booking WHERE id_booking = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: dashboard.php");
    exit;
}


$today = date('Y-m-d');
$kpi_today = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE tanggal_booking = '$today'"));
$kpi_pending = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking WHERE status = 'Belum Dicuci'"));
$kpi_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM booking"));



$search_nama = $_GET['nama'] ?? '';
$filter_jenis = $_GET['jenis_kendaraan'] ?? '';
$filter_layanan = $_GET['layanan'] ?? '';
$filter_tanggal = $_GET['tanggal'] ?? '';
$filter_jam = $_GET['jam'] ?? '';
$filter_status = $_GET['status'] ?? '';

$query = "SELECT * FROM booking";
$where_clauses = [];


if (!empty($search_nama)) {
    $nama_safe = mysqli_real_escape_string($conn, $search_nama);
    $where_clauses[] = "nama LIKE '%$nama_safe%'";
}
if (!empty($filter_jenis)) {
    $jenis_safe = mysqli_real_escape_string($conn, $filter_jenis);
    $where_clauses[] = "jenis_kendaraan = '$jenis_safe'";
}
if (!empty($filter_layanan)) {
    $layanan_safe = mysqli_real_escape_string($conn, $filter_layanan);
    $where_clauses[] = "layanan = '$layanan_safe'";
}
if (!empty($filter_tanggal)) {
    $tanggal_safe = mysqli_real_escape_string($conn, $filter_tanggal);
    $where_clauses[] = "tanggal_booking = '$tanggal_safe'";
}
if (!empty($filter_jam)) {
    $jam_safe = mysqli_real_escape_string($conn, $filter_jam);
    $where_clauses[] = "jam_booking = '$jam_safe'";
}
if (!empty($filter_status)) {
    $status_safe = mysqli_real_escape_string($conn, $filter_status);
    $where_clauses[] = "status = '$status_safe'";
}

if (!empty($where_clauses)) {
    $query .= " WHERE " . implode(" AND ", $where_clauses);
}

$query .= " ORDER BY tanggal_booking DESC, jam_booking ASC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - ProClean</title>
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
        <a class="nav-link" href="laporan.php"><i class="bi bi-file-earmark-bar-graph-fill me-2"></i> Laporan</a>
    </li>
    <li class="nav-item mt-auto">
         <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-left me-2"></i> Logout</a>
    </li>
</ul>
</div>

<div class="main-content">
    <h2 class="mb-4">Dashboard Booking</h2>
    <div class="row mb-4">
        <div class="col-md-4"><div class="card kpi-card"><div class="card-body"><div class="icon bg-primary"><i class="bi bi-calendar-day"></i></div><div><h4 class="mb-0 fw-bold"><?= $kpi_today['total'] ?></h4><small class="text-muted">Booking Hari Ini</small></div></div></div></div>
        <div class="col-md-4"><div class="card kpi-card"><div class="card-body"><div class="icon bg-warning"><i class="bi bi-clock-history"></i></div><div><h4 class="mb-0 fw-bold"><?= $kpi_pending['total'] ?></h4><small class="text-muted">Antrian Belum Dicuci</small></div></div></div></div>
        <div class="col-md-4"><div class="card kpi-card"><div class="card-body"><div class="icon bg-success"><i class="bi bi-journal-check"></i></div><div><h4 class="mb-0 fw-bold"><?= $kpi_total['total'] ?></h4><small class="text-muted">Total Semua Booking</small></div></div></div></div>
    </div>
    
    <div class="table-wrapper">
        <h5 class="mb-3">Daftar Booking Cuci Kendaraan</h5>
        <form method="GET" action="dashboard.php" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4"><label class="form-label">Cari Nama</label><input type="text" class="form-control" name="nama" value="<?= htmlspecialchars($search_nama) ?>"></div>
                <div class="col-md-4"><label class="form-label">Tanggal</label><input type="date" class="form-control" name="tanggal" value="<?= htmlspecialchars($filter_tanggal) ?>"></div>
                <div class="col-md-4"><label class="form-label">Status</label><select name="status" class="form-select"><option value="">Semua</option><option value="Belum Dicuci" <?= ($filter_status == 'Belum Dicuci') ? 'selected' : '' ?>>Belum Dicuci</option><option value="Selesai" <?= ($filter_status == 'Selesai') ? 'selected' : '' ?>>Selesai</option></select></div>
                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="dashboard.php" class="btn btn-outline-secondary">Reset</a>
                    <button type="submit" class="btn btn-primary">Terapkan</button>
                </div>
            </div>
        </form>

        <table class="table table-hover">
            <thead>
                <tr><th>Pelanggan</th><th>Kontak</th><th>Kendaraan</th><th>Layanan</th><th>Jadwal</th><th>Status</th><th>Aksi</th></tr>
            </thead>
            <tbody>
                <?php if ($result && mysqli_num_rows($result) > 0): while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><div class="d-flex align-items-center"><div class="user-avatar"><?= strtoupper(substr($row['nama'], 0, 1)) ?></div><strong><?= htmlspecialchars($row['nama']) ?></strong></div></td>
                    <td><?= htmlspecialchars($row['no_hp']) ?></td>
                    <td><?= htmlspecialchars($row['jenis_kendaraan']) ?></td>
                    <td><?= htmlspecialchars($row['layanan']) ?></td>
                    <td><?= date('d M Y', strtotime($row['tanggal_booking'])) ?><br><small class="text-muted"><?= $row['jam_booking'] ?></small></td>
                    <td><?php if ($row['status'] == 'Selesai'): ?><span class="badge bg-success-subtle text-success-emphasis rounded-pill">Selesai</span><?php else: ?><span class="badge bg-warning-subtle text-warning-emphasis rounded-pill">Belum Dicuci</span><?php endif; ?></td>
                    <td>
                        <?php if ($row['status'] != 'Selesai'): ?>
                            <button type="button" class="btn btn-sm btn-outline-success btn-konfirmasi" title="Selesaikan" data-bs-toggle="modal" data-bs-target="#konfirmasiModal" data-id="<?= $row['id_booking'] ?>"><i class="bi bi-check-lg"></i></button>
                        <?php endif; ?>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-hapus" title="Hapus" data-bs-toggle="modal" data-bs-target="#hapusModal" data-id="<?= $row['id_booking'] ?>"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
                <?php endwhile; else: ?>
                <tr><td colspan="7" class="text-center p-4">Tidak ada data yang cocok dengan kriteria.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="konfirmasiModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Konfirmasi Penyelesaian</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body"><p>Apakah Anda yakin ingin menandai booking ini sebagai "Selesai"?</p></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><a href="#" id="btnModalKonfirmasi" class="btn btn-success">Ya, Selesaikan</a></div>
    </div>
  </div>
</div>
<div class="modal fade" id="hapusModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">Konfirmasi Penghapusan</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
      <div class="modal-body"><p>Apakah Anda yakin ingin menghapus booking ini? Tindakan ini tidak dapat dibatalkan.</p></div>
      <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><a href="#" id="btnModalHapus" class="btn btn-danger">Ya, Hapus</a></div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    
    $('.btn-konfirmasi').on('click', function() {
        const bookingId = $(this).data('id');
        $('#btnModalKonfirmasi').attr('href', `?konfirmasi=${bookingId}`);
    });

    
    $('.btn-hapus').on('click', function() {
        const bookingId = $(this).data('id');
        $('#btnModalHapus').attr('href', `?hapus=${bookingId}`);
    });
});
</script>
<footer class="text-center text-muted mt-5 mb-3">
    <small>&copy; 2025 ProClean Vehicle Wash</small>
</footer>

</body>
</html>