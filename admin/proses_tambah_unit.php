<?php
require_once '../config/koneksi.php';
require_admin('login.php');
csrf_check();

$nama_unit   = trim($_POST['nama_unit'] ?? '');
$kategori    = in_array($_POST['kategori'], ['PS4','PS5','Nintendo']) ? $_POST['kategori'] : null;
$tipe        = in_array($_POST['tipe_layanan'], ['Main di Tempat','Sewa Luar']) ? $_POST['tipe_layanan'] : null;

if (!$nama_unit || !$kategori || !$tipe) {
    header("Location: index.php?msg=error&error_text=" . urlencode("Semua field wajib diisi."));
    exit();
}

$stmt = $koneksi->prepare("INSERT INTO units (nama_unit, kategori, tipe_layanan, status) VALUES (?,?,?,'Tersedia')");
$stmt->bind_param("sss", $nama_unit, $kategori, $tipe);

if (!$stmt->execute()) {
    header("Location: index.php?msg=error&error_text=" . urlencode("Gagal menyimpan unit."));
    exit();
}

$id_unit_baru = $koneksi->insert_id;
$stmt->close();

if (!empty($_POST['game_ids'])) {
    $ins = $koneksi->prepare("INSERT IGNORE INTO unit_games (id_unit, id_game) VALUES (?,?)");
    foreach ($_POST['game_ids'] as $id_game) {
        $id_game = intval($id_game);
        $ins->bind_param("ii", $id_unit_baru, $id_game);
        $ins->execute();
    }
    $ins->close();
}

$jml_game = count($_POST['game_ids'] ?? []);
header("Location: index.php?msg=tambah_unit_ok&error_text=" . urlencode("Unit \"$nama_unit\" berhasil ditambahkan dengan $jml_game game!"));
exit();