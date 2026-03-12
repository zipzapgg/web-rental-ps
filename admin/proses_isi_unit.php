<?php
require_once '../config/koneksi.php';
require_admin('login.php');
csrf_get_check();

$act     = $_GET['act'] ?? '';
$id_unit = intval($_GET['unit'] ?? 0);
$id_game = intval($_GET['game'] ?? 0);

if (!$id_unit || !$id_game || !in_array($act, ['tambah','hapus'])) {
    header("Location: index.php"); exit();
}

if ($act === 'tambah') {
    $s = $koneksi->prepare("INSERT IGNORE INTO unit_games (id_unit, id_game) VALUES (?,?)");
    $s->bind_param("ii", $id_unit, $id_game);
} else {
    $s = $koneksi->prepare("DELETE FROM unit_games WHERE id_unit=? AND id_game=?");
    $s->bind_param("ii", $id_unit, $id_game);
}
$s->execute(); $s->close();

header("Location: isi_unit.php?id=$id_unit&msg=ok"); exit();