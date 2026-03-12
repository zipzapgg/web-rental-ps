<?php
require_once '../config/koneksi.php';
require_login('login.php');
csrf_get_check();

$id_sewa = intval($_GET['id'] ?? 0);
$id_unit = intval($_GET['unit'] ?? 0);

if (!$id_sewa || !$id_unit) { header("Location: data_sewa.php"); exit(); }

$s = $koneksi->prepare("UPDATE pengajuan SET status_pengajuan='Selesai' WHERE id_pengajuan=?");
$s->bind_param("i", $id_sewa); $s->execute(); $s->close();

$s = $koneksi->prepare("UPDATE units SET status='Tersedia' WHERE id_unit=?");
$s->bind_param("i", $id_unit); $s->execute(); $s->close();

header("Location: data_sewa.php?msg=selesai"); exit();