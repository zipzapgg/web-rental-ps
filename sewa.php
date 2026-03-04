<?php include 'config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Sewa Bawa Pulang - Violet PS</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@700&family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { background-color: #0f051d; color: white; font-family: 'Poppins', sans-serif; }
        .gaming-font { font-family: 'Orbitron', sans-serif; }
        .form-card { background: rgba(255, 255, 255, 0.05); border: 1px solid #8a2be2; border-radius: 20px; padding: 30px; }
        .text-neon { color: #bc13fe; text-shadow: 0 0 10px #bc13fe; }
        .form-control, .form-select { background: #1a1a2e; border: 1px solid #444; color: white; }
        .form-control:focus { background: #1a1a2e; color: white; border-color: #bc13fe; box-shadow: 0 0 10px #bc13fe; }
        .btn-violet { background: #8a2be2; color: white; transition: 0.3s; }
        .btn-violet:hover { background: #bc13fe; box-shadow: 0 0 15px #bc13fe; }
    </style>
</head>
<body>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="text-center mb-4">
                <img src="assets/images/logo.png" width="100" class="mb-3">
                <h2 class="gaming-font text-neon">FORM PENGAJUAN SEWA</h2>
                <p class="text-muted small">Khusus Wilayah Jagakarsa & Sekitarnya</p>
            </div>

            <div class="form-card shadow-lg">
                <form action="proses_sewa.php" method="POST" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap (Sesuai KTP)</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor WhatsApp</label>
                            <input type="number" name="wa" class="form-control" placeholder="08..." required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap di Jagakarsa</label>
                        <textarea name="alamat" class="form-control" rows="2" required></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pilih Unit PS4</label>
                            <select name="id_unit" class="form-select" required>
                                <option value="">-- Pilih Unit Tersedia --</option>
                                <?php
                                // Hanya menampilkan unit "Sewa Luar" yang statusnya "Tersedia"
                                $units = mysqli_query($koneksi, "SELECT * FROM units WHERE tipe_layanan = 'Sewa Luar' AND status = 'Tersedia'");
                                while($u = mysqli_fetch_assoc($units)){
                                    echo "<option value='".$u['id_unit']."'>".$u['nama_unit']." (".$u['kategori'].")</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Durasi Sewa</label>
                            <select name="durasi" class="form-select" required>
                                <option value="1 Hari">1 Hari</option>
                                <option value="2 Hari">2 Hari</option>
                                <option value="3 Hari">3 Hari</option>
                            </select>
                        </div>
                    </div>

                    <hr class="my-4" style="border-color: #444;">
                    <h6 class="text-neon mb-3">Upload Dokumen (Wajib)</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label small">Foto KTP Asli</label>
                            <input type="file" name="ktp" class="form-control" accept="image/*" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label small">Foto STNK Asli</label>
                            <input type="file" name="stnk" class="form-control" accept="image/*" required>
                        </div>
                    </div>

                    <div class="alert alert-dark small mt-3" style="border: 1px dashed #bc13fe;">
                        <ul class="mb-0">
                            <li>Jaminan berupa KTP/STNK asli akan diambil saat unit diantar.</li>
                            <li>Pastikan domisili sesuai dengan area layanan kami.</li>
                        </ul>
                    </div>

                    <button type="submit" name="kirim" class="btn btn-violet w-100 py-3 gaming-font mt-3">AJUKAN SEWA SEKARANG</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>