<?php
require_once '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: login.php");
    exit();
}

csrf_check();

$user = trim($_POST['user'] ?? '');
$pass = trim($_POST['pass'] ?? '');
$ip   = $_SERVER['REMOTE_ADDR'];

// ── Rate limiting: max 5 percobaan per IP per 15 menit ────
$stmt = $koneksi->prepare(
    "SELECT COUNT(*) as c FROM login_attempts 
     WHERE ip_address = ? AND attempted_at > DATE_SUB(NOW(), INTERVAL 15 MINUTE)"
);
$stmt->bind_param("s", $ip);
$stmt->execute();
$attempts = $stmt->get_result()->fetch_assoc()['c'];
$stmt->close();

if ($attempts >= 5) {
    echo "<script>alert('Terlalu banyak percobaan login. Coba lagi 15 menit kemudian.'); window.location='login.php';</script>";
    exit();
}

// ── Catat percobaan login ─────────────────────────────────
$stmt = $koneksi->prepare("INSERT INTO login_attempts (ip_address) VALUES (?)");
$stmt->bind_param("s", $ip);
$stmt->execute();
$stmt->close();

// ── Cek username (prepared statement) ────────────────────
$stmt = $koneksi->prepare("SELECT * FROM admin WHERE username = ? LIMIT 1");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();
$admin  = $result->fetch_assoc();
$stmt->close();

if ($admin && password_verify($pass, $admin['password'])) {
    // Login sukses — bersihkan attempts, regenerate session
    $stmt = $koneksi->prepare("DELETE FROM login_attempts WHERE ip_address = ?");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->close();

    session_regenerate_id(true);
    $_SESSION['status']   = 'login';
    $_SESSION['user']     = $admin['username'];
    $_SESSION['role']     = $admin['role'];
    $_SESSION['nama']     = $admin['nama_lengkap'];
    $_SESSION['id_admin'] = $admin['id_admin'];
    $_SESSION['login_at'] = time();

    header("Location: index.php");
    exit();
} else {
    echo "<script>alert('Username atau password salah.'); window.location='login.php';</script>";
    exit();
}