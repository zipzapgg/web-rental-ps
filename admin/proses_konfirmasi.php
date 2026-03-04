<?php
include '../config/koneksi.php';

$id_sewa = $_GET['id'];
$id_unit = $_GET['unit'];

// 1. Update status pengajuan menjadi 'Selesai'
mysqli_query($koneksi, "UPDATE pengajuan SET status_pengajuan = 'Selesai' WHERE id_pengajuan = '$id_sewa'");

// 2. Kembalikan status unit PS menjadi 'Tersedia' agar muncul lagi di form sewa
mysqli_query($koneksi, "UPDATE units SET status = 'Tersedia' WHERE id_unit = '$id_unit'");

echo "<script>alert('Transaksi Selesai. Unit PS kini berstatus Tersedia kembali.'); window.location='data_sewa.php';</script>";
?>