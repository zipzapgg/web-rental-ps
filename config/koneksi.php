<?php
/**
 * VIOLET PLAYSTATION - Core Connection & Security
 * Mode: Full Adaptive (Localhost & Production Ready)
 */

if (!headers_sent()) {
    header("X-Frame-Options: DENY");
    header("X-Content-Type-Options: nosniff");
    header("Referrer-Policy: strict-origin-when-cross-origin");
    header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; img-src 'self' data: blob: https://www.google.com; frame-src https://www.google.com; connect-src 'self';");
}

$is_localhost = ($_SERVER['REMOTE_ADDR'] === '127.0.0.1' || $_SERVER['REMOTE_ADDR'] === '::1');
ini_set('display_errors', $is_localhost ? 1 : 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Asia/Jakarta');

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_samesite', $is_localhost ? 'Lax' : 'Strict');
    $is_https = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on');
    ini_set('session.cookie_secure', $is_https ? 1 : 0);
    ini_set('session.use_strict_mode', 1);
    ini_set('session.gc_maxlifetime', 7200);
    session_start();
}

if (isset($_SESSION['login_at'])) {
    if ((time() - $_SESSION['login_at']) > 7200) {
        session_unset();
        session_destroy();
        $is_in_admin = strpos($_SERVER['PHP_SELF'] ?? '', '/admin/') !== false;
        header("Location: " . ($is_in_admin ? 'login.php' : 'admin/login.php') . "?pesan=timeout");
        exit();
    }
    $_SESSION['login_at'] = time();
}

// Database Connection
$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_violet_ps";

$koneksi = new mysqli($host, $user, $pass, $db);
if ($koneksi->connect_error) {
    error_log("[Violet PS] DB Error: " . $koneksi->connect_error);
    http_response_code(503);
    die("Sistem sedang pemeliharaan.");
}
$koneksi->set_charset("utf8mb4");
$koneksi->query("SET time_zone = '+07:00'");

// --- SECURITY & CSRF FUNCTIONS ---

// Untuk Form POST
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_check(): void {
    $sent   = $_POST['csrf_token'] ?? '';
    $stored = $_SESSION['csrf_token'] ?? '';
    if (empty($sent) || empty($stored) || !hash_equals($stored, $sent)) {
        http_response_code(403);
        die("Request tidak valid (POST Token Mismatch).");
    }
}

// Untuk Link GET (Menjawab error di isi_unit.php)
function csrf_get_token(): string {
    if (empty($_SESSION['csrf_get_token'])) {
        $_SESSION['csrf_get_token'] = bin2hex(random_bytes(24));
    }
    return $_SESSION['csrf_get_token'];
}

function csrf_get_check(): void {
    $sent   = $_GET['_token'] ?? '';
    $stored = $_SESSION['csrf_get_token'] ?? '';
    if (empty($sent) || empty($stored) || !hash_equals($stored, $sent)) {
        http_response_code(403);
        die("Request tidak valid (GET Token Mismatch).");
    }
}

function csrf_rotate(): void {
    unset($_SESSION['csrf_token'], $_SESSION['csrf_get_token']);
}

// --- AUTHENTICATION FUNCTIONS ---

function is_logged_in(): bool {
    return isset($_SESSION['status']) && $_SESSION['status'] === 'login';
}

function is_admin(): bool {
    return is_logged_in() && ($_SESSION['role'] ?? '') === 'admin';
}

function require_login(string $redirect = 'login.php'): void {
    if (!is_logged_in()) {
        header("Location: $redirect?pesan=belum_login");
        exit();
    }
}

function require_admin(string $redirect = 'index.php'): void {
    require_login();
    if (!is_admin()) {
        header("Location: $redirect?pesan=akses_ditolak");
        exit();
    }
}

// --- FILE & PATH MANAGEMENT ---

$upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/violet-ps/uploads/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}
define('UPLOAD_PATH', $upload_dir);

function ext_from_mime(string $mime): ?string {
    return [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ][$mime] ?? null;
}

if (file_exists(__DIR__ . '/harga.php')) {
    require_once __DIR__ . '/harga.php';
}
/**
 * Mencatat aktivitas ke database
 */
function log_activity(mysqli $koneksi, string $aksi, string $deskripsi): void {
    $id_admin = $_SESSION['id_admin'] ?? 0;
    if (!$id_admin) return;

    $ip = $_SERVER['REMOTE_ADDR'];
    $stmt = $koneksi->prepare("INSERT INTO activity_logs (id_admin, aksi, deskripsi, ip_address) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $id_admin, $aksi, $deskripsi, $ip);
    $stmt->execute();
    $stmt->close();
}

/**
 * Menghapus file fisik KTP & STNK penyewa dari server dan meng-update DB menjadi NULL
 */
function hapus_berkas_pengajuan(int $id_pengajuan, mysqli $koneksi): void {
    $stmt = $koneksi->prepare("SELECT foto_ktp, foto_stnk FROM pengajuan WHERE id_pengajuan = ?");
    $stmt->bind_param("i", $id_pengajuan);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($res) {
        $folder = UPLOAD_PATH . 'berkas' . DIRECTORY_SEPARATOR;
        if (!empty($res['foto_ktp'])) {
            $file_ktp = $folder . $res['foto_ktp'];
            if (file_exists($file_ktp)) {
                unlink($file_ktp);
            }
        }
        if (!empty($res['foto_stnk'])) {
            $file_stnk = $folder . $res['foto_stnk'];
            if (file_exists($file_stnk)) {
                unlink($file_stnk);
            }
        }
        $stmt_up = $koneksi->prepare("UPDATE pengajuan SET foto_ktp = NULL, foto_stnk = NULL WHERE id_pengajuan = ?");
        $stmt_up->bind_param("i", $id_pengajuan);
        $stmt_up->execute();
        $stmt_up->close();
    }
}