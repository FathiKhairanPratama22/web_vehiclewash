<?php
session_start();
include '../config/koneksi.php';


if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}


if (isset($_GET['konfirmasi'])) {
    $id = $_GET['konfirmasi'];
    mysqli_query($conn, "UPDATE booking SET status='Selesai' WHERE id_booking='$id'");
    header("Location: dashboard.php");
    exit;
}


if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM booking WHERE id_booking='$id'");
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
    <title>Dashboard Admin - ProClean</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <div class="sidebar">
        <div class="sidebar-brand">
            <i class="bi bi-droplet-fill text-info"></i> ProClean
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link active" href="dashboard.php"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="manajemen_layanan.php"><i class="bi bi-gear-fill"></i> Services</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="laporan.php"><i class="bi bi-file-earmark-bar-graph-fill"></i> Reports</a>
            </li>
            <li class="nav-item mt-auto">
                <a class="nav-link text-danger" href="logout.php"><i class="bi bi-box-arrow-left"></i> Logout</a>
            </li>
        </ul>
    </div>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Dashboard Overview</h2>
            <div class="d-flex align-items-center gap-3">
                <span class="text-muted"><?= date('l, d F Y') ?></span>
                <div class="user-avatar bg-primary">A</div>
            </div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="kpi-info">
                        <h4 class="fw-bold"><?= $kpi_today['total'] ?></h4>
                        <small>Bookings Today</small>
                    </div>
                    <div class="kpi-icon bg-primary-soft">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="kpi-info">
                        <h4 class="fw-bold"><?= $kpi_pending['total'] ?></h4>
                        <small>Pending Wash</small>
                    </div>
                    <div class="kpi-icon bg-warning-soft">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="kpi-card">
                    <div class="kpi-info">
                        <h4 class="fw-bold"><?= $kpi_total['total'] ?></h4>
                        <small>Total Bookings</small>
                    </div>
                    <div class="kpi-icon bg-success-soft">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-wrapper">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0 fw-bold">Recent Bookings</h5>
                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filterCollapse"><i class="bi bi-funnel"></i> Filter</button>
            </div>

            <div class="collapse mb-4" id="filterCollapse">
                <div class="card card-body bg-light border-0">
                    <form method="GET" action="dashboard.php">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3"><label class="form-label small">Name</label><input type="text"
                                    class="form-control form-control-sm" name="nama"
                                    value="<?= htmlspecialchars($search_nama) ?>"></div>
                            <div class="col-md-3"><label class="form-label small">Date</label><input type="date"
                                    class="form-control form-control-sm" name="tanggal"
                                    value="<?= htmlspecialchars($filter_tanggal) ?>"></div>
                            <div class="col-md-3"><label class="form-label small">Status</label><select name="status"
                                    class="form-select form-select-sm">
                                    <option value="">All Status</option>
                                    <option value="Belum Dicuci" <?= ($filter_status == 'Belum Dicuci') ? 'selected' : '' ?>>Pending</option>
                                    <option value="Selesai" <?= ($filter_status == 'Selesai') ? 'selected' : '' ?>>
                                        Completed</option>
                                </select></div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-sm btn-primary w-100">Apply</button>
                                <a href="dashboard.php" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Vehicle</th>
                            <th>Service</th>
                            <th>Schedule</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0):
                            while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="user-avatar"><?= strtoupper(substr($row['nama'], 0, 1)) ?></div>
                                            <span class="fw-medium"><?= htmlspecialchars($row['nama']) ?></span>
                                        </div>
                                    </td>
                                    <td><?= htmlspecialchars($row['no_hp']) ?></td>
                                    <td>
                                        <?php if ($row['jenis_kendaraan'] == 'Mobil'): ?>
                                            <i class="bi bi-car-front text-primary me-1"></i>
                                        <?php else: ?>
                                            <i class="bi bi-bicycle text-info me-1"></i>
                                        <?php endif; ?>
                                        <?= htmlspecialchars($row['jenis_kendaraan']) ?>
                                    </td>
                                    <td><?= htmlspecialchars($row['layanan']) ?></td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span
                                                class="fw-medium"><?= date('d M Y', strtotime($row['tanggal_booking'])) ?></span>
                                            <small class="text-muted"><?= $row['jam_booking'] ?></small>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($row['status'] == 'Selesai'): ?>
                                            <span class="badge bg-success-subtle text-success badge-pill">Completed</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning-subtle text-warning badge-pill">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($row['status'] != 'Selesai'): ?>
                                            <button type="button" class="btn btn-sm btn-success btn-konfirmasi rounded-circle"
                                                title="Complete" data-bs-toggle="modal" data-bs-target="#konfirmasiModal"
                                                data-id="<?= $row['id_booking'] ?>"
                                                style="width: 32px; height: 32px; padding: 0;"><i
                                                    class="bi bi-check-lg"></i></button>
                                        <?php endif; ?>
                                        <button type="button" class="btn btn-sm btn-danger btn-hapus rounded-circle"
                                            title="Delete" data-bs-toggle="modal" data-bs-target="#hapusModal"
                                            data-id="<?= $row['id_booking'] ?>"
                                            style="width: 32px; height: 32px; padding: 0;"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="7" class="text-center p-5 text-muted">No bookings found matching your criteria.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="modal fade" id="konfirmasiModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">Complete Booking?</h5><button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to mark this booking as "Completed"?</p>
                </div>
                <div class="modal-footer border-0"><button type="button" class="btn btn-light"
                        data-bs-dismiss="modal">Cancel</button><a href="#" id="btnModalKonfirmasi"
                        class="btn btn-success px-4">Yes, Complete</a></div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="hapusModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-danger">Delete Booking?</h5><button type="button"
                        class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this booking? This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0"><button type="button" class="btn btn-light"
                        data-bs-dismiss="modal">Cancel</button><a href="#" id="btnModalHapus"
                        class="btn btn-danger px-4">Yes, Delete</a></div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {

            $('.btn-konfirmasi').on('click', function () {
                const bookingId = $(this).data('id');
                $('#btnModalKonfirmasi').attr('href', `?konfirmasi=${bookingId}`);
            });


            $('.btn-hapus').on('click', function () {
                const bookingId = $(this).data('id');
                $('#btnModalHapus').attr('href', `?hapus=${bookingId}`);
            });
        });
    </script>

</body>

</html>