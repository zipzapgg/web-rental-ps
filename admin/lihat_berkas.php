<?php
require_once '../config/koneksi.php';
require_login('../admin/login.php');

$file = basename($_GET['file'] ?? '');

if (!$file) {
    http_response_code(400); die("File tidak ditemukan.");
}

if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $file)) {
    http_response_code(400); die("Nama file tidak valid.");
}

$ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
    http_response_code(400); die("Tipe file tidak diizinkan.");
}

$path = UPLOAD_PATH . 'berkas' . DIRECTORY_SEPARATOR . $file;

if (!file_exists($path)) {
    http_response_code(404); die("File tidak ditemukan.");
}

$stmt = $koneksi->prepare(
    "SELECT id_pengajuan FROM pengajuan WHERE foto_ktp = ? OR foto_stnk = ? LIMIT 1"
);
$stmt->bind_param("ss", $file, $file);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    http_response_code(403); die("Akses ditolak.");
}
$stmt->close();

$mime_map = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
$mime = $mime_map[$ext] ?? 'application/octet-stream';

header("Content-Type: $mime");
header("Content-Length: " . filesize($path));
header("Cache-Control: no-store, no-cache, must-revalidate");
header("X-Content-Type-Options: nosniff");
readfile($path);
exit();