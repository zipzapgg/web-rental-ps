<?php
error_reporting(E_ALL);
ini_set('display_errors', 1); // Tampilkan error langsung untuk debug

require_once 'config/koneksi.php';
require_once 'config/promo.php';

// Debug mode - tampilkan semua data yang masuk
if (isset($_GET['debug']) && $_GET['debug'] === '1') {
    echo '<pre>';
    echo "POST:
"; print_r($_POST);
    echo "
SESSION:
"; print_r($_SESSION);
    echo "
FILES:
"; print_r($_FILES);
    echo '</pre>'; exit();
}

// CSRF - tampilkan pesan jelas kalau gagal
$csrf_sent   = $_POST['csrf_token'] ?? '';
$csrf_stored = $_SESSION['csrf_token'] ?? '';
if (!isset($_POST['kirim'])) {
    header("Location: sewa.php"); exit();
}
if (empty($csrf_sent) || empty($csrf_stored) || !hash_equals($csrf_stored, $csrf_sent)) {
    echo "<script>alert('Sesi expired. Silakan refresh halaman sewa dan coba lagi.'); window.location.href='sewa.php';</script>"; exit();
}

// Input
$nama          = trim($_POST['nama']      ?? '');
$wa            = trim($_POST['wa']        ?? '');
$alamat        = trim($_POST['alamat']    ?? '');
$id_unit       = intval($_POST['id_unit'] ?? 0);
$hari_bayar    = intval($_POST['durasi']  ?? 1);
$pakai_playbox = isset($_POST['pakai_playbox']) ? 1 : 0;
$tgl_ambil     = trim($_POST['tgl_ambil'] ?? '');

error_log("[Violet PS] Submit: nama=$nama wa=$wa id_unit=$id_unit hari=$hari_bayar tgl=$tgl_ambil");

// Validasi
if (!$nama || !$wa || !$alamat || !$id_unit || !$hari_bayar || !$tgl_ambil) {
    echo "<script>alert('Semua field wajib diisi.'); window.history.back();</script>"; exit();
}
if ($tgl_ambil < date('Y-m-d')) {
    echo "<script>alert('Tanggal pengambilan tidak valid.'); window.history.back();</script>"; exit();
}
if (mb_strlen($nama) > 100) {
    echo "<script>alert('Nama terlalu panjang.'); window.history.back();</script>"; exit();
}
if (mb_strlen($alamat) > 300) {
    echo "<script>alert('Alamat terlalu panjang.'); window.history.back();</script>"; exit();
}
if (!preg_match('/^[0-9]{10,15}$/', $wa)) {
    echo "<script>alert('Nomor WhatsApp tidak valid. Masukkan 10-15 angka tanpa spasi atau tanda hubung.'); window.history.back();</script>"; exit();
}
if (!in_array($hari_bayar, [1, 2, 3])) {
    echo "<script>alert('Durasi tidak valid.'); window.history.back();</script>"; exit();
}

$nama   = htmlspecialchars($nama,   ENT_QUOTES, 'UTF-8');
$alamat = htmlspecialchars($alamat, ENT_QUOTES, 'UTF-8');

// Cek unit
$stmt = $koneksi->prepare(
    "SELECT id_unit, nama_unit, kategori FROM units
     WHERE id_unit=? AND tipe_layanan='Sewa Luar' AND status='Tersedia'"
);
$stmt->bind_param("i", $id_unit);
$stmt->execute();
$unit_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$unit_data) {
    error_log("[Violet PS] Unit tidak tersedia: $id_unit");
    echo "<script>alert('Unit tidak tersedia atau sudah disewa orang lain.'); window.history.back();</script>"; exit();
}

$nama_unit = $unit_data['nama_unit'];
$kategori  = $unit_data['kategori'];
error_log("[Violet PS] Unit OK: $nama_unit ($kategori)");

// Hitung harga
$hpp      = match($kategori){ 'PS5' => 195000, 'Nintendo' => 100000, default => 100000 };
$hpp     += $pakai_playbox ? 30000 : 0;
$is_promo = is_promo_weekday($koneksi, $tgl_ambil);
$sewa     = hitung_sewa($hari_bayar, $hpp, $is_promo && $hari_bayar >= 2);

$durasi       = $sewa['durasi_str'];
$harga        = $sewa['harga'];
$is_promo_int = $is_promo ? 1 : 0;
error_log("[Violet PS] Harga: $harga durasi=$durasi promo=$is_promo_int");

// Upload
function violet_upload(string $key, string $prefix): array {
    $allowed = ['image/jpeg', 'image/png', 'image/webp'];

    if (!isset($_FILES[$key]) || $_FILES[$key]['error'] === UPLOAD_ERR_NO_FILE) {
        return ['error' => "File $key wajib diupload."];
    }
    $file = $_FILES[$key];
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => "Upload $key gagal (kode {$file['error']})."];
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['error' => "File $key terlalu besar (maks 5MB)."];
    }
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $allowed)) {
        return ['error' => "Format $key tidak diizinkan. Gunakan JPG atau PNG."];
    }
    $exts  = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $ext   = $exts[$mime] ?? 'jpg';
    $folder = UPLOAD_PATH . 'berkas' . DIRECTORY_SEPARATOR;
    if (!is_dir($folder)) mkdir($folder, 0750, true);
    $fname = $prefix . '_' . bin2hex(random_bytes(12)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $folder . $fname)) {
        return ['error' => "Gagal menyimpan $key. Cek permission folder uploads/."];
    }
    return ['filename' => $fname];
}

$ktp  = violet_upload('ktp',  'ktp');
$stnk = violet_upload('stnk', 'stnk');

if (isset($ktp['error'])) {
    error_log("[Violet PS] KTP error: {$ktp['error']}");
    echo "<script>alert('" . addslashes($ktp['error']) . "'); window.history.back();</script>"; exit();
}
if (isset($stnk['error'])) {
    error_log("[Violet PS] STNK error: {$stnk['error']}");
    echo "<script>alert('" . addslashes($stnk['error']) . "'); window.history.back();</script>"; exit();
}

error_log("[Violet PS] Upload OK: ktp={$ktp['filename']} stnk={$stnk['filename']}");

// INSERT
$stmt = $koneksi->prepare(
    "INSERT INTO pengajuan
     (nama_penyewa, no_wa, alamat, id_unit, durasi, harga, pakai_playbox, tgl_ambil, is_promo, foto_ktp, foto_stnk, status_pengajuan)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')"
);

if (!$stmt) {
    error_log("[Violet PS] Prepare gagal: " . $koneksi->error);
    echo "<script>alert('Kesalahan database: " . addslashes($koneksi->error) . "'); window.history.back();</script>"; exit();
}

$stmt->bind_param(
    "sssisiisiss",
    $nama, $wa, $alamat, $id_unit,
    $durasi, $harga, $pakai_playbox,
    $tgl_ambil, $is_promo_int,
    $ktp['filename'], $stnk['filename']
);

if ($stmt->execute()) {
    $id_pengajuan = $koneksi->insert_id;
    $stmt->close();

    $upd = $koneksi->prepare("UPDATE units SET status='Disewa' WHERE id_unit=?");
    $upd->bind_param("i", $id_unit);
    $upd->execute();
    $upd->close();

    error_log("[Violet PS] Sukses! id=$id_pengajuan");

    $_SESSION['last_pengajuan'] = [
        'id'          => $id_pengajuan,
        'nama'        => $nama,
        'wa'          => $wa,
        'unit'        => $nama_unit,
        'kategori'    => $kategori,
        'hari_bayar'  => $hari_bayar,
        'hari_dapat'  => $sewa['hari_dapat'],
        'durasi'      => $durasi,
        'tgl_ambil'   => $tgl_ambil,
        'harga'       => $harga,
        'playbox'     => $pakai_playbox,
        'is_promo'    => ($is_promo && $hari_bayar >= 2),
        'promo_label' => $sewa['label'],
    ];

    header("Location: sukses_sewa.php");
    exit();

} else {
    $err = $koneksi->error;
    $stmt->close();
    error_log("[Violet PS] INSERT gagal: $err");

    $msg = "Terjadi kesalahan sistem.";
    if (str_contains($err, 'tgl_ambil') || str_contains($err, 'is_promo') ||
        str_contains($err, 'pakai_playbox') || str_contains($err, 'harga')) {
        $msg = 'Database belum diupdate. Jalankan file migrasi_semua.sql di phpMyAdmin.';
    } elseif (str_contains($err, "Unknown column") || str_contains($err, "doesn't exist")) {
        $msg = "Kolom database tidak ditemukan: $err — Jalankan migrasi_semua.sql.";
    } else {
        $msg = "Gagal menyimpan: $err";
    }
    echo "<script>alert('" . addslashes($msg) . "'); window.history.back();</script>";
}