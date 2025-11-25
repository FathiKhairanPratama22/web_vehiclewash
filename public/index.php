<?php
include '../config/koneksi.php';
$result_layanan = mysqli_query($conn, "SELECT * FROM layanan ORDER BY id_layanan ASC");
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Online - ProClean Vehicle Wash</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            overflow-x: hidden;
        }

        .info-side {
            background: linear-gradient(135deg, #004aad, #0078d7);
            /* Ocean Blue Gradient */
            color: white;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 4rem;
        }

        .form-side {
            min-height: 100vh;
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 3rem;
            /* Padding dikurangi biar lega */
        }

        .service-card {
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            height: 100%;
        }

        /* Sembunyikan radio button asli */
        .service-card input[type="radio"] {
            display: none;
        }

        /* Efek saat dipilih */
        .service-card.selected {
            border-color: #004aad;
            background-color: #f0f7ff;
            color: #004aad;
            box-shadow: 0 4px 12px rgba(0, 74, 173, 0.15);
        }

        .service-card:hover {
            transform: translateY(-5px);
        }

        /* Styling Tombol Kendaraan */
        .vehicle-btn+label {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            font-weight: 600;
            color: #6c757d;
        }

        .vehicle-btn:checked+label {
            border-color: #004aad;
            background-color: #004aad;
            color: white;
        }
    </style>
</head>

<body>

    <div class="container-fluid p-0">
        <div class="row g-0">

            <div class="col-lg-6 d-none d-lg-flex info-side">
                <div class="mb-4">
                    <i class="bi bi-tsunami" style="font-size: 4rem;"></i>
                </div>
                <h1 class="display-4 fw-bold mb-3">Premium Wash<br>for Your Ride</h1>
                <p class="fs-5 opacity-75 mb-5">Rasakan perawatan terbaik untuk kendaraan Anda. Cepat, profesional, dan
                    hasil bersih mengkilap setiap saat.</p>

                <div class="mt-auto">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-patch-check-fill text-info fs-4"></i>
                        <span class="fs-5">Produk Ramah Lingkungan</span>
                    </div>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <i class="bi bi-patch-check-fill text-info fs-4"></i>
                        <span class="fs-5">Peralatan Profesional</span>
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <i class="bi bi-patch-check-fill text-info fs-4"></i>
                        <span class="fs-5">Jaminan Kepuasan</span>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 form-side">
                <div class="mx-auto w-100" style="max-width: 600px;">
                    <h2 class="fw-bold text-dark mb-2">Book Your Slot</h2>
                    <p class="text-muted mb-4">Isi detail di bawah ini untuk memesan jadwal.</p>

                    <form action="proses_booking.php" method="POST" id="bookingForm">

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="nama" class="form-control bg-light border-0" id="nama"
                                        placeholder="Nama Anda" required>
                                    <label for="nama">Nama Lengkap</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="text" name="no_hp" class="form-control bg-light border-0" id="no_hp"
                                        placeholder="Nomor HP" required>
                                    <label for="no_hp">Nomor HP</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Jenis
                                Kendaraan</label>
                            <div class="row g-3">
                                <div class="col-6">
                                    <input type="radio" class="btn-check vehicle-btn" name="jenis_kendaraan" id="mobil"
                                        value="Mobil" checked onchange="updatePrices()">
                                    <label class="btn w-100 py-3" for="mobil">
                                        <i class="bi bi-car-front-fill d-block fs-3 mb-1"></i> Mobil
                                    </label>
                                </div>
                                <div class="col-6">
                                    <input type="radio" class="btn-check vehicle-btn" name="jenis_kendaraan" id="motor"
                                        value="Motor" onchange="updatePrices()">
                                    <label class="btn w-100 py-3" for="motor">
                                        <i class="bi bi-bicycle d-block fs-3 mb-1"></i> Motor
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary small text-uppercase">Pilih Layanan</label>
                            <div class="row g-2">
                                <?php while ($layanan = mysqli_fetch_assoc($result_layanan)): ?>
                                    <div class="col-4"> <label class="service-card">
                                            <input type="radio" name="layanan" value="<?= $layanan['id_layanan'] ?>"
                                                data-harga-mobil="<?= $layanan['harga_mobil'] ?>"
                                                data-harga-motor="<?= $layanan['harga_motor'] ?>" required>
                                            <i class="bi bi-stars fs-4 mb-2 text-warning"></i>
                                            <span class="d-block lh-sm mb-2"
                                                style="font-size: 0.9rem;"><?= htmlspecialchars($layanan['nama_layanan']) ?></span>
                                            <small class="harga-layanan fw-bold text-primary"></small>
                                        </label>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <input type="date" id="tanggal_booking" name="tanggal_booking"
                                        class="form-control bg-light border-0" required>
                                    <label for="tanggal_booking">Tanggal</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-floating">
                                    <select id="jam_booking" name="jam_booking" class="form-select bg-light border-0"
                                        required>
                                        <option value="">Pilih tanggal dulu...</option>
                                    </select>
                                    <label for="jam_booking">Jam Tersedia</label>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 rounded-3 fw-bold fs-5 shadow-sm">
                            Booking Sekarang <i class="bi bi-arrow-right-short fs-4 align-middle ms-1"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="successModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-success text-white border-0 justify-content-center py-4">
                    <i class="bi bi-check-circle-fill" style="font-size: 3rem;"></i>
                </div>
                <div class="modal-body text-center p-4">
                    <h3 class="fw-bold mb-2">Booking Berhasil!</h3>
                    <p class="text-muted">Terima kasih telah melakukan booking. Silakan datang tepat waktu sesuai
                        jadwal.</p>
                    <button type="button" class="btn btn-success w-100 py-2 mt-3 rounded-pill"
                        data-bs-dismiss="modal">Selesai</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        $(document).ready(function () {
            // 1. Load Jam via AJAX
            $('#tanggal_booking').on('change', function () {
                var tanggal = $(this).val();
                if (tanggal) { $.get('get_jam.php', { tanggal: tanggal }, function (data) { $('#jam_booking').html(data); }); }
            });

            // 2. Update Harga Dinamis
            window.updatePrices = function () {
                const vehicleType = $('input[name="jenis_kendaraan"]:checked').val().toLowerCase();
                $('input[name="layanan"]').each(function () {
                    const price = $(this).data(`harga-${vehicleType}`);
                    const formattedPrice = new Intl.NumberFormat('id-ID', {
                        style: 'currency', currency: 'IDR', minimumFractionDigits: 0
                    }).format(price);
                    $(this).closest('label').find('.harga-layanan').text(formattedPrice);
                });
            }
            updatePrices(); // Jalankan saat loading

            // 3. Visual Effect untuk Service Card (Biar ada border biru saat dipilih)
            $('input[name="layanan"]').on('change', function () {
                $('.service-card').removeClass('selected');
                if ($(this).is(':checked')) { $(this).closest('.service-card').addClass('selected'); }
            });

            // 4. Validasi No HP (Hanya Angka)
            $('#bookingForm').on('submit', function (e) {
                var no_hp = $('input[name="no_hp"]').val();
                if (!/^[0-9]+$/.test(no_hp) || no_hp.length < 10 || no_hp.length > 13) {
                    alert('Nomor HP tidak valid (Harus angka 10-13 digit).');
                    e.preventDefault();
                    return false;
                }
            });

            // 5. Show Modal Sukses
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('status') === 'sukses') {
                var myModal = new bootstrap.Modal(document.getElementById('successModal'));
                myModal.show();
            }
        });
    </script>
</body>

</html>