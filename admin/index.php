<?php 
session_start();
// Jaring pengaman: cek apakah session status sudah ada dan isinya "login"
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    exit();
}
include '../config/koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <title>Admin Violet PS - Daftar Unit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Daftar Unit Violet PS</h2>
        <div>
            <a href="master_game.php" class="btn btn-primary">Master Game</a>
            <a href="data_sewa.php" class="btn btn-info text-white">Data Sewa</a>
            <a href="logout.php" class="btn btn-danger" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
        </div>
    </div>
    <div class="card shadow border-0">
        <div class="card-body">
            <table class="table table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Unit</th>
                        <th>Kategori</th>
                        <th>Layanan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    // Mengambil data units sesuai struktur di database
                    $query = mysqli_query($koneksi, "SELECT * FROM units ORDER BY tipe_layanan DESC, nama_unit ASC");
                    while($d = mysqli_fetch_assoc($query)){
                    ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><strong><?php echo $d['nama_unit']; ?></strong></td>
                        <td><span class="badge bg-secondary"><?php echo $d['kategori']; ?></span></td>
                        <td><?php echo $d['tipe_layanan']; ?></td>
                        <td><a href="isi_unit.php?id=<?php echo $d['id_unit']; ?>" class="btn btn-sm btn-success">Kelola Game</a></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>