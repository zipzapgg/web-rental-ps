<?php
include '../config/koneksi.php'; // Koneksi ke db_violet_ps

$act  = $_GET['act'];
$unit = $_GET['unit'];
$game = $_GET['game'];

if ($act == 'tambah') {
    // Menambah relasi baru antara unit dan game
    mysqli_query($koneksi, "INSERT INTO unit_games (id_unit, id_game) VALUES ('$unit', '$game')");
} elseif ($act == 'hapus') {
    // Menghapus relasi saja, bukan menghapus judul gamenya
    mysqli_query($koneksi, "DELETE FROM unit_games WHERE id_unit = '$unit' AND id_game = '$game'");
}

// Kembalikan ke halaman kelola unit tadi
header("location:isi_unit.php?id=" . $unit);
?>