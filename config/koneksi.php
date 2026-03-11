<?php
// ══════════════════════════════════════════════════════
//  koneksi.php  —  Security core Violet PlayStation
// ══════════════════════════════════════════════════════

// ── [FIX #8] Security headers ─────────────────────────
if (!headers_sent()) {
    header("X-Frame-Options: DENY");
    header("X-Content-Type-Options: nosniff");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: blob: https://www.google.com; frame-src https://www.google.com; connect-src 'self';");
}

// ── [FIX #4] Sembunyikan error dari layar ─────────────
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

// ── [FIX #5 + #6] Session hardening + timeout aktif ──
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly',  1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 1 : 0);
    ini_set('session.use_strict_mode',  1); // Tolak session ID asing (cegah fixation)
    ini_set('session.gc_maxlifetime',   7200);
    ini_set('session.use_only_cookies', 1);
    session_start();
}

// Timeout: kick setelah 2 jam idle
if (isset($_SESSION['login_at'])) {
    if ((time() - $_SESSION['login_at']) > 7200) {
        session_unset();
        session_destroy();
        $is_in_admin = strpos($_SERVER['PHP_SELF'] ?? '', '/admin/') !== false;
        header("Location: " . ($is_in_admin ? 'login.php' : 'admin/login.php') . "?pesan=timeout");
        exit();
    }
    $_SESSION['login_at'] = time(); // refresh dari aktivitas terakhir
}

// ── Database ──────────────────────────────────────────
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_violet_ps";

$koneksi = new mysqli($host, $user, $pass, $db);
if ($koneksi->connect_error) {
    // [FIX #4] Log ke file, jangan tampilkan ke layar
    error_log("[Violet PS] DB connect error: " . $koneksi->connect_error);
    http_response_code(503);
    die("Layanan sementara tidak tersedia. Silakan coba lagi nanti.");
}
$koneksi->set_charset("utf8mb4");

// ── [FIX #1] CSRF untuk POST ──────────────────────────
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check() {
    $sent    = $_POST['csrf_token'] ?? '';
    $stored  = $_SESSION['csrf_token'] ?? '';
    if (empty($sent) || empty($stored) || !hash_equals($stored, $sent)) {
        http_response_code(403);
        die("Request tidak valid.");
    }
}

// [FIX #1] CSRF untuk aksi GET (hapus, terima, tolak)
function csrf_get_token() {
    if (empty($_SESSION['csrf_get_token'])) {
        $_SESSION['csrf_get_token'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf_get_token'];
}

function csrf_get_check() {
    $sent   = $_GET['_token'] ?? '';
    $stored = $_SESSION['csrf_get_token'] ?? '';
    if (empty($sent) || empty($stored) || !hash_equals($stored, $sent)) {
        http_response_code(403);
        die("Request tidak valid.");
    }
}

// ── Role helpers ──────────────────────────────────────
function is_logged_in() {
    return isset($_SESSION['status']) && $_SESSION['status'] === 'login';
}

function is_admin() {
    return is_logged_in() && ($_SESSION['role'] ?? '') === 'admin';
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

// ── [FIX #2] Helper: ekstensi aman dari MIME ──────────
function ext_from_mime(string $mime): ?string {
    return [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ][$mime] ?? null;
}

// ── UPLOAD_PATH ───────────────────────────────────────
define('UPLOAD_PATH', dirname(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'uploads_violet' . DIRECTORY_SEPARATOR);