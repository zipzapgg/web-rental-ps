<?php
require_once '../config/koneksi.php';
require_admin('login.php');
csrf_get_check();

$id_unit = intval($_GET['id'] ?? 0);
if(!$id_unit){ header("Location: index.php"); exit(); }

// Cek apakah ada pengajuan aktif
$s = $koneksi->prepare("SELECT COUNT(*) as c FROM pengajuan WHERE id_unit=? AND status_pengajuan IN ('Pending','Disetujui')");
$s->bind_param("i",$id_unit); $s->execute();
$aktif = $s->get_result()->fetch_assoc()['c']; $s->close();

if($aktif > 0){
    header("Location: index.php?msg=hapus_gagal"); exit();
}

// Hapus relasi game, lalu unit
$s = $koneksi->prepare("DELETE FROM unit_games WHERE id_unit=?");
$s->bind_param("i",$id_unit); $s->execute(); $s->close();

$s = $koneksi->prepare("DELETE FROM units WHERE id_unit=?");
$s->bind_param("i",$id_unit); $s->execute(); $s->close();

header("Location: index.php?msg=hapus_ok"); exit();