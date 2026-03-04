<?php 
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    exit();
}
include '../config/koneksi.php'; 
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Data Sewa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4">Data Pengajuan Sewa Luar</h2>
    <div class="card shadow border-0">
        <div class="card-body">
            <table class="table table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Tgl</th>
                        <th>Nama</th>
                        <th>Unit</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Relasi antar tabel pengajuan dan units
                    $sql = "SELECT pengajuan.*, units.nama_unit FROM pengajuan JOIN units ON pengajuan.id_unit = units.id_unit ORDER BY tgl_pengajuan DESC";
                    $query = mysqli_query($koneksi, $sql);
                    while($d = mysqli_fetch_assoc($query)){
                    ?>
                    <tr>
                        <td><?php echo $d['tgl_pengajuan']; ?></td>
                        <td><strong><?php echo $d['nama_penyewa']; ?></strong></td>
                        <td><?php echo $d['nama_unit']; ?></td>
                        <td><span class="badge bg-warning"><?php echo $d['status_pengajuan']; ?></span></td>
                        <td><a href="proses_konfirmasi.php?id=<?php echo $d['id_pengajuan']; ?>&unit=<?php echo $d['id_unit']; ?>" class="btn btn-sm btn-primary">Selesaikan</a></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>