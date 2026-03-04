<?php
require_once '../config/koneksi.php';
require_login('login.php');

$id_sewa = intval($_GET['id'] ?? 0);
$id_unit = intval($_GET['unit'] ?? 0);

if (!$id_sewa || !$id_unit) {
    header("Location: data_sewa.php"); exit();
}

// Update status pengajuan
$stmt = $koneksi->prepare("UPDATE pengajuan SET status_pengajuan = 'Selesai' WHERE id_pengajuan = ?");
$stmt->bind_param("i", $id_sewa);
$stmt->execute();
$stmt->close();

// Kembalikan status unit
$stmt = $koneksi->prepare("UPDATE units SET status = 'Tersedia' WHERE id_unit = ?");
$stmt->bind_param("i", $id_unit);
$stmt->execute();
$stmt->close();

echo "<script>alert('Transaksi selesai. Unit kini tersedia kembali.'); window.location='data_sewa.php';</script>";