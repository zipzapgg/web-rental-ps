<?php
require_once '../config/koneksi.php';
require_admin('login.php');
csrf_check();

if (!isset($_POST['judul'])) { header("Location: master_game.php"); exit(); }

$judul    = trim($_POST['judul']);
$kategori = in_array($_POST['kategori'], ['PS4','PS5','Nintendo']) ? $_POST['kategori'] : null;

// Upload foto dengan validasi ketat
$allowed_mime = ['image/jpeg','image/png','image/webp'];
$max_size     = 5 * 1024 * 1024;
$file         = $_FILES['foto'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    echo "<script>alert('Gagal upload foto.'); window.history.back();</script>"; exit();
}
if ($file['size'] > $max_size) {
    echo "<script>alert('Foto terlalu besar (max 5MB).'); window.history.back();</script>"; exit();
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, $allowed_mime)) {
    echo "<script>alert('Format foto tidak diizinkan.'); window.history.back();</script>"; exit();
}

$ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
$filename = 'game_' . bin2hex(random_bytes(8)) . '.' . $ext;

$folder = UPLOAD_PATH . 'games' . DIRECTORY_SEPARATOR;
if (!is_dir($folder)) mkdir($folder, 0750, true);

// Untuk tampil di publik, simpan juga di uploads/games/ dalam htdocs
$public_folder = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'games' . DIRECTORY_SEPARATOR;
if (!is_dir($public_folder)) mkdir($public_folder, 0755, true);

if (!move_uploaded_file($file['tmp_name'], $public_folder . $filename)) {
    echo "<script>alert('Gagal menyimpan foto. Periksa folder uploads/games!'); window.history.back();</script>"; exit();
}

// Simpan ke DB
$stmt = $koneksi->prepare("INSERT INTO games (judul_game, foto_game, kategori_game) VALUES (?,?,?)");
$stmt->bind_param("sss", $judul, $filename, $kategori);
$stmt->execute();
$id_game_baru = $koneksi->insert_id;
$stmt->close();

// Relasi ke unit
if (!empty($_POST['unit_dipilih'])) {
    $ins = $koneksi->prepare("INSERT IGNORE INTO unit_games (id_unit, id_game) VALUES (?,?)");
    foreach ($_POST['unit_dipilih'] as $id_unit) {
        $id_unit = intval($id_unit);
        $ins->bind_param("ii", $id_unit, $id_game_baru);
        $ins->execute();
    }
    $ins->close();
}

echo "<script>alert('Game berhasil ditambahkan!'); window.location='master_game.php';</script>";