<?php 
include '../config/koneksi.php';
$result_layanan = mysqli_query($conn, "SELECT * FROM layanan ORDER BY id_layanan ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Booking Jadwal - ProClean Vehicle Wash</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="main-container p-3">
    <div class="booking-wrapper">
        <div class="row g-0">
            <div class="col-lg-5 d-none d-lg-block info-side">
                <i class="bi bi-car-front-fill" style="font-size: 4rem; margin-bottom: 1rem;"></i>
                <h2>Booking Cuci Kendaraan</h2>
                <p>Bersih Mengkilap, Cepat, dan Profesional. Pesan jadwal Anda sekarang.</p>
            </div>
            <div class="col-lg-7 form-side">
                <h3>Isi Detail Booking Anda</h3>
                <form action="proses_booking.php" method="POST" id="bookingForm">
                    <div class="row"><div class="col-md-6 mb-3"><label for="nama" class="form-label">Nama</label><input type="text" name="nama" class="form-control" required></div><div class="col-md-6 mb-3"><label for="no_hp" class="form-label">No HP</label><input type="text" name="no_hp" class="form-control" required></div></div>
                    <div class="mb-3"><label for="jenis_kendaraan" class="form-label">Jenis Kendaraan</label><select name="jenis_kendaraan" id="jenis_kendaraan" class="form-select" required><option value="Mobil">Mobil</option><option value="Motor">Motor</option></select></div>
                    
                    <div class="mb-4">
                        <label class="form-label">Layanan</label>
                        <div class="row g-2">
                            <?php while($layanan = mysqli_fetch_assoc($result_layanan)): ?>
                            <div class="col-md-4">
                                <label class="service-option text-center h-100">
                                    <input type="radio" name="layanan" 
                                           value="<?= $layanan['id_layanan'] ?>" 
                                           data-harga-mobil="<?= $layanan['harga_mobil'] ?>"
                                           data-harga-motor="<?= $layanan['harga_motor'] ?>"
                                           required>
                                    <strong><?= htmlspecialchars($layanan['nama_layanan']) ?></strong><br>
                                    <small class="harga-layanan text-primary fw-bold"></small>
                                </label>
                            </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <div class="row"><div class="col-md-6 mb-3"><label for="tanggal_booking" class="form-label">Tanggal Booking</label><input type="date" id="tanggal_booking" name="tanggal_booking" class="form-control" required></div><div class="col-md-6 mb-3"><label for="jam_booking" class="form-label">Jam Booking</label><select id="jam_booking" name="jam_booking" class="form-select" required><option value="">Pilih tanggal dulu...</option></select></div></div>
                    <button type="submit" class="btn btn-primary w-100 mt-3">Booking Sekarang</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="successModal" tabindex="-1"><div class="modal-dialog modal-dialog-centered"><div class="modal-content"><div class="modal-header border-0"><h5 class="modal-title fw-bold">✅ Booking Berhasil!</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>Silakan datang sesuai jam yang Anda telah pilih. Pembayaran dilakukan langsung di kasir.</p></div><div class="modal-footer border-0"><button type="button" class="btn btn-primary" data-bs-dismiss="modal">Mengerti</button></div></div></div></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
$(document).ready(function() {
    $('#bookingForm').on('submit', function(e) {
    var no_hp = $('input[name="no_hp"]').val();
    if (!/^[0-9]+$/.test(no_hp) || no_hp.length < 10 || no_hp.length > 13) {
        alert('Nomor HP tidak valid. Harap isi hanya dengan angka (10-13 digit).');
        e.preventDefault(); // Mencegah form dikirim
        return false;
    }
});
    $('#tanggal_booking').on('change', function() {
        var tanggal = $(this).val();
        if (tanggal) { $.get('get_jam.php', {tanggal: tanggal}, function(data) { $('#jam_booking').html(data); }); }
    });

    
    function updatePrices() {
        const vehicleType = $('#jenis_kendaraan').val().toLowerCase(); 
        
        $('input[name="layanan"]').each(function() {
            
            const price = $(this).data(`harga-${vehicleType}`); 
            
            
            const formattedPrice = new Intl.NumberFormat('id-ID', { 
                style: 'currency', currency: 'IDR', minimumFractionDigits: 0 
            }).format(price);
            
            
            $(this).closest('label').find('.harga-layanan').text(formattedPrice);
        });
    }

    
    $('#jenis_kendaraan').on('change', updatePrices);
    
    updatePrices();

    
    $('input[name="layanan"]').on('change', function() {
        $('.service-option').removeClass('selected');
        if ($(this).is(':checked')) { $(this).closest('.service-option').addClass('selected'); }
    });

    
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('status') === 'sukses') {
        var myModal = new bootstrap.Modal(document.getElementById('successModal'));
        myModal.show();
    }
});
</script>
<footer class="text-center text-muted mt-5 mb-3">
    <small>&copy; 2025 ProClean Vehicle Wash</small>
</footer>
</body>
</html>