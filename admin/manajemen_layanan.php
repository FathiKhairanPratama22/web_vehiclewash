<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$pesan = '';
$nama_layanan = '';
$harga_mobil = '';
$harga_motor = '';
$edit_id = null;
$edit_mode = false;

if (isset($_POST['simpan'])) {
    $nama_layanan = mysqli_real_escape_string($conn, $_POST['nama_layanan']);
    $harga_mobil = (int)$_POST['harga_mobil'];
    $harga_motor = (int)$_POST['harga_motor'];
    $id_layanan = $_POST['id_layanan'] ?? null;

    if ($id_layanan) {
        
        $query = "UPDATE layanan SET nama_layanan='$nama_layanan', harga_mobil=$harga_mobil, harga_motor=$harga_motor WHERE id_layanan=$id_layanan";
        $pesan = "Data layanan berhasil diperbarui!";
    } else {
        
        $query = "INSERT INTO layanan (nama_layanan, harga_mobil, harga_motor) VALUES ('$nama_layanan', $harga_mobil, $harga_motor)";
        $pesan = "Layanan baru berhasil ditambahkan!";
    }
    
    mysqli_query($conn, $query);
    header("Location: manajemen_layanan.php?sukses=" . urlencode($pesan));
    exit;
}

if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    mysqli_query($conn, "DELETE FROM layanan WHERE id_layanan=$id");
    header("Location: manajemen_layanan.php?sukses=" . urlencode("Data layanan berhasil dihapus!"));
    exit;
}

if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_mode = true;
    $result_edit = mysqli_query($conn, "SELECT * FROM layanan WHERE id_layanan=$edit_id");
    $data_edit = mysqli_fetch_assoc($result_edit);
    $nama_layanan = $data_edit['nama_layanan'];
    $harga_mobil = $data_edit['harga_mobil'];
    $harga_motor = $data_edit['harga_motor'];
}

$result_layanan = mysqli_query($conn, "SELECT * FROM layanan ORDER BY id_layanan ASC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manajemen Layanan - ProClean</title>
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
    <h2 class="mb-4">Manajemen Layanan</h2>
    
    <?php if (isset($_GET['sukses'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= htmlspecialchars($_GET['sukses']) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="card-form mb-4">
        <h5 class="mb-3"><?= $edit_mode ? 'Edit Layanan' : 'Tambah Layanan Baru' ?></h5>
        <form method="POST" action="manajemen_layanan.php">
            <input type="hidden" name="id_layanan" value="<?= $edit_id ?>">
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">Nama Layanan</label>
                    <input type="text" name="nama_layanan" class="form-control" value="<?= htmlspecialchars($nama_layanan) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Harga Mobil (Rp)</label>
                    <input type="number" name="harga_mobil" class="form-control" value="<?= htmlspecialchars($harga_mobil) ?>" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Harga Motor (Rp)</label>
                    <input type="number" name="harga_motor" class="form-control" value="<?= htmlspecialchars($harga_motor) ?>" required>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" name="simpan" class="btn btn-primary w-100"><?= $edit_mode ? 'Update' : 'Simpan' ?></button>
                </div>
                <?php if ($edit_mode): ?>
                <div class="col-md-1 d-flex align-items-end">
                    <a href="manajemen_layanan.php" class="btn btn-secondary w-100">Batal</a>
                </div>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <div class="table-wrapper">
        <h5 class="mb-3">Daftar Layanan Saat Ini</h5>
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Layanan</th>
                    <th>Harga Mobil</th>
                    <th>Harga Motor</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; while($row = mysqli_fetch_assoc($result_layanan)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($row['nama_layanan']) ?></td>
                    <td>Rp <?= number_format($row['harga_mobil']) ?></td>
                    <td>Rp <?= number_format($row['harga_motor']) ?></td>
                    <td>
                        <a href="?edit=<?= $row['id_layanan'] ?>" class="btn btn-sm btn-outline-warning" title="Edit"><i class="bi bi-pencil-fill"></i></a>
                        <a href="?hapus=<?= $row['id_layanan'] ?>" class="btn btn-sm btn-outline-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus layanan ini?')"><i class="bi bi-trash-fill"></i></a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>