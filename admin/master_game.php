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
    <title>Admin - Master Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="d-flex justify-content-between mb-4">
        <h2>Master Game</h2>
        <div>
            <a href="index.php" class="btn btn-secondary">Kembali</a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">Tambah Game</button>
        </div>
    </div>
    <div class="row">
        <?php
        $query = mysqli_query($koneksi, "SELECT * FROM games ORDER BY judul_game ASC");
        while($g = mysqli_fetch_assoc($query)){
        ?>
        <div class="col-md-2 mb-4">
            <div class="card h-100 shadow-sm text-center">
                <img src="../uploads/games/<?php echo $g['foto_game']; ?>" class="card-img-top" style="height: 150px; object-fit: cover;">
                <div class="card-body p-2">
                    <h6 class="small fw-bold"><?php echo $g['judul_game']; ?></h6>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>