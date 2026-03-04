<?php
// ── Database ──────────────────────────────────────────
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_violet_ps";

$koneksi = new mysqli($host, $user, $pass, $db);
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}
$koneksi->set_charset("utf8mb4");

// ── Session hardening ─────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.gc_maxlifetime', 7200); // 2 jam auto logout
    session_start();
}

// ── CSRF Token generator ──────────────────────────────
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check() {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        http_response_code(403);
        die("Request tidak valid.");
    }
}

// ── Role check helpers ────────────────────────────────
function is_logged_in() {
    return isset($_SESSION['status']) && $_SESSION['status'] === 'login';
}

function is_admin() {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function require_login($redirect = 'login.php') {
    if (!is_logged_in()) {
        header("Location: $redirect?pesan=belum_login");
        exit();
    }
}

function require_admin($redirect = 'index.php') {
    require_login();
    if (!is_admin()) {
        header("Location: $redirect?pesan=akses_ditolak");
        exit();
    }
}

// ── Path ke folder upload aman (di luar htdocs) ───────
// Sesuaikan path ini dengan struktur server kamu
// Untuk XAMPP Windows: letakkan folder di D:\uploads_violet\ (di luar htdocs)
define('UPLOAD_PATH', dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'uploads_violet' . DIRECTORY_SEPARATOR);
// Fallback jika folder luar tidak bisa dibuat (development):
// define('UPLOAD_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR . 'private_uploads' . DIRECTORY_SEPARATOR); 