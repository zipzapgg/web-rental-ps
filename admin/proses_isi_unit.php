<?php
include '../config/koneksi.php';

$act  = $_GET['act'];
$unit = $_GET['unit'];
$game = $_GET['game'];

if ($act == 'tambah') {
    mysqli_query($koneksi, "INSERT INTO unit_games (id_unit, id_game) VALUES ('$unit', '$game')");
} elseif ($act == 'hapus') {
    mysqli_query($koneksi, "DELETE FROM unit_games WHERE id_unit = '$unit' AND id_game = '$game'");
}

header("location:isi_unit.php?id=" . $unit);
?>