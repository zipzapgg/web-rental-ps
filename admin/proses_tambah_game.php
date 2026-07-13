<?php
require_once '../config/koneksi.php';
require_admin('login.php');
csrf_check();

if (!isset($_POST['judul'])) { header("Location: master_game.php"); exit(); }

$judul    = trim($_POST['judul']);
if (mb_strlen($judul) > 100 || mb_strlen($judul) < 2) {
    header("Location: master_game.php?msg=error&error_text=" . urlencode("Judul game tidak valid (2-100 karakter)."));
    exit();
}
$kategori = in_array($_POST['kategori'] ?? '', ['PS4','PS5','Nintendo']) ? $_POST['kategori'] : null;
if (!$kategori) {
    header("Location: master_game.php?msg=error&error_text=" . urlencode("Kategori tidak valid."));
    exit();
}

$allowed_mime = ['image/jpeg','image/png','image/webp'];
$max_size     = 5 * 1024 * 1024;
$file         = $_FILES['foto'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    header("Location: master_game.php?msg=error&error_text=" . urlencode("Gagal upload foto."));
    exit();
}
if ($file['size'] > $max_size) {
    header("Location: master_game.php?msg=error&error_text=" . urlencode("Foto terlalu besar (max 5MB)."));
    exit();
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime  = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

if (!in_array($mime, $allowed_mime)) {
    header("Location: master_game.php?msg=error&error_text=" . urlencode("Format foto tidak diizinkan. Gunakan JPG, PNG, atau WEBP."));
    exit();
}

$ext = ext_from_mime($mime);
if (!$ext) {
    header("Location: master_game.php?msg=error&error_text=" . urlencode("Format foto tidak didukung."));
    exit();
}
$filename = 'game_' . bin2hex(random_bytes(8)) . '.' . $ext;

$folder = UPLOAD_PATH . 'games' . DIRECTORY_SEPARATOR;
if (!is_dir($folder)) mkdir($folder, 0750, true);

$public_folder = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'games' . DIRECTORY_SEPARATOR;
if (!is_dir($public_folder)) mkdir($public_folder, 0755, true);

if (!move_uploaded_file($file['tmp_name'], $public_folder . $filename)) {
    header("Location: master_game.php?msg=error&error_text=" . urlencode("Gagal menyimpan foto. Periksa folder uploads/games!"));
    exit();
}

$genre = trim($_POST['genre'] ?? '');
if (empty($genre)) { $genre = 'Action / Adventure'; }
if (mb_strlen($genre) > 100) {
    header("Location: master_game.php?msg=error&error_text=" . urlencode("Genre game terlalu panjang (max 100 karakter)."));
    exit();
}

$players = trim($_POST['players'] ?? '');
if (empty($players)) { $players = '1-2 Players'; }
if (mb_strlen($players) > 50) {
    header("Location: master_game.php?msg=error&error_text=" . urlencode("Jumlah pemain terlalu panjang (max 50 karakter)."));
    exit();
}

$stmt = $koneksi->prepare("INSERT INTO games (judul_game, foto_game, kategori_game, genre_game, players_game) VALUES (?,?,?,?,?)");
$stmt->bind_param("sssss", $judul, $filename, $kategori, $genre, $players);
$stmt->execute();
$id_game_baru = $koneksi->insert_id;
$stmt->close();

if (!empty($_POST['unit_dipilih'])) {
    $ins = $koneksi->prepare("INSERT IGNORE INTO unit_games (id_unit, id_game) VALUES (?,?)");
    foreach ($_POST['unit_dipilih'] as $id_unit) {
        $id_unit = intval($id_unit);
        $ins->bind_param("ii", $id_unit, $id_game_baru);
        $ins->execute();
    }
    $ins->close();
}

// --- FITUR LOG AKTIVITAS ---
if (function_exists('log_activity')) {
    log_activity($koneksi, 'TAMBAH_GAME', "Menambahkan master game baru: " . $judul);
}

header("Location: master_game.php?msg=tambah_game_ok");
exit();