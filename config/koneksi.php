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
    ini_set('session.gc_maxlifetime', 7200);
    session_start();
}

// ── CSRF Token ────────────────────────────────────────
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check() {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
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
// Sesuaikan path ini dengan struktur server kamu.
// Untuk XAMPP Windows: D:\uploads_violet\ (di luar htdocs)
define('UPLOAD_PATH', dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'uploads_violet' . DIRECTORY_SEPARATOR);

// ── Validasi UPLOAD_PATH saat boot (dev/staging) ─────
if (!defined('UPLOAD_PATH_CHECKED')) {
    define('UPLOAD_PATH_CHECKED', true);
    if (!is_dir(UPLOAD_PATH)) {
        // Coba buat folder secara otomatis
        if (!@mkdir(UPLOAD_PATH, 0750, true)) {
            // Kalau gagal (misal di production path-nya beda), log saja — jangan crash
            error_log('[VioletPS] UPLOAD_PATH tidak ditemukan dan gagal dibuat: ' . UPLOAD_PATH);
        }
    }
}