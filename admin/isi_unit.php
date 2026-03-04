<?php 
session_start();
if (!isset($_SESSION['status']) || $_SESSION['status'] != "login") {
    header("location:login.php?pesan=belum_login");
    exit();
}
include '../config/koneksi.php'; 

$id_unit = $_GET['id'];
// Mengambil info unit dari database
$u = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM units WHERE id_unit = '$id_unit'"));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Unit - <?php echo $u['nama_unit']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h3>Daftar Game di: <span class="text-primary"><?php echo $u['nama_unit']; ?></span></h3>
    <a href="index.php" class="btn btn-secondary mb-4">Kembali</a>
    <div class="row">
        <?php
        $sql_games = mysqli_query($koneksi, "SELECT * FROM games");
        while($g = mysqli_fetch_assoc($sql_games)){
            $id_g = $g['id_game'];
            // Cek relasi di tabel unit_games
            $cek = mysqli_query($koneksi, "SELECT id_relasi FROM unit_games WHERE id_unit = '$id_unit' AND id_game = '$id_g'");
            $ada = mysqli_num_rows($cek);
        ?>
        <div class="col-md-3 mb-3">
            <div class="card <?php echo ($ada > 0) ? 'border-primary' : ''; ?>">
                <div class="card-body text-center p-2">
                    <h6 class="small"><?php echo $g['judul_game']; ?></h6>
                    <a href="proses_isi_unit.php?act=<?php echo ($ada > 0) ? 'hapus' : 'tambah'; ?>&unit=<?php echo $id_unit; ?>&game=<?php echo $id_g; ?>" 
                       class="btn btn-sm <?php echo ($ada > 0) ? 'btn-danger' : 'btn-success'; ?> w-100">
                       <?php echo ($ada > 0) ? 'Hapus' : 'Tambah'; ?>
                    </a>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
</div>
</body>
</html>