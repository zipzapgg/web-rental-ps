<?php
require_once '../config/koneksi.php';
require_admin('login.php');

$aksi = $_GET['aksi'] ?? $_POST['aksi'] ?? '';

// 1. MULAI MAINTENANCE (Dari Form Modal)
if ($aksi === 'mulai') {
    csrf_check(); // Keamanan Form
    $id_unit = intval($_POST['id_unit']);
    $ket     = trim($_POST['keterangan']);

    if (!$ket) {
        header("Location: index.php?msg=maint_gagal");
        exit();
    }

    $s = $koneksi->prepare("UPDATE units SET status='Maintenance', keterangan_maint=? WHERE id_unit=?");
    $s->bind_param("si", $ket, $id_unit);
    $s->execute();
    $s->close();

    // Catat ke Log Aktivitas
    if (function_exists('log_activity')) {
        log_activity($koneksi, 'MULAI_MAINTENANCE', "Unit ID $id_unit masuk maintenance. Ket: $ket");
    }

    header("Location: index.php?msg=edit_ok");
    exit();
} 

// 2. SELESAI MAINTENANCE (Dari Link Tombol)
elseif ($aksi === 'selesai') {
    csrf_get_check(); // Keamanan Link GET
    $id_unit = intval($_GET['id']);

    $s = $koneksi->prepare("UPDATE units SET status='Tersedia', keterangan_maint=NULL WHERE id_unit=?");
    $s->bind_param("i", $id_unit);
    $s->execute();
    $s->close();

    // Catat ke Log Aktivitas
    if (function_exists('log_activity')) {
        log_activity($koneksi, 'SELESAI_MAINTENANCE', "Unit ID $id_unit selesai diperbaiki dan siap disewa");
    }

    header("Location: index.php?msg=edit_ok");
    exit();
}

header("Location: index.php");