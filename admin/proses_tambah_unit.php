<?php
require_once '../config/koneksi.php';
require_admin('login.php');
csrf_check();

$nama_unit   = trim($_POST['nama_unit'] ?? '');
$kategori    = in_array($_POST['kategori'], ['PS4','PS5','Nintendo']) ? $_POST['kategori'] : null;
$tipe        = in_array($_POST['tipe_layanan'], ['Main di Tempat','Sewa Luar']) ? $_POST['tipe_layanan'] : null;

if (!$nama_unit || !$kategori || !$tipe) {
    echo "<script>alert('Semua field wajib diisi.'); window.history.back();</script>"; exit();
}

// Simpan unit baru
$stmt = $koneksi->prepare("INSERT INTO units (nama_unit, kategori, tipe_layanan, status) VALUES (?,?,?,'Tersedia')");
$stmt->bind_param("sss", $nama_unit, $kategori, $tipe);

if (!$stmt->execute()) {
    echo "<script>alert('Gagal menyimpan unit.'); window.history.back();</script>"; exit();
}

$id_unit_baru = $koneksi->insert_id;
$stmt->close();

// Assign game ke unit
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
echo "<script>alert('Unit \"$nama_unit\" berhasil ditambahkan dengan $jml_game game!'); window.location='index.php';</script>";