<?php
require_once 'config/koneksi.php';
require_once 'config/promo.php';
csrf_check();

if (!isset($_POST['kirim'])) { header("Location: sewa.php"); exit(); }

$nama          = trim($_POST['nama']          ?? '');
$wa            = trim($_POST['wa']            ?? '');
$alamat        = trim($_POST['alamat']        ?? '');
$id_unit       = intval($_POST['id_unit']     ?? 0);
$hari_bayar    = intval($_POST['durasi']      ?? 1);
$pakai_playbox = isset($_POST['pakai_playbox']) ? 1 : 0;
$tgl_ambil     = trim($_POST['tgl_ambil']     ?? '');

if (!$nama || !$wa || !$alamat || !$id_unit || !$hari_bayar || !$tgl_ambil) {
    echo "<script>alert('Semua field wajib diisi.'); window.history.back();</script>"; exit();
}
if ($tgl_ambil < date('Y-m-d')) {
    echo "<script>alert('Tanggal pengambilan tidak valid.'); window.history.back();</script>"; exit();
}
if (mb_strlen($nama)   > 100) { echo "<script>alert('Nama terlalu panjang.'); window.history.back();</script>"; exit(); }
if (mb_strlen($alamat) > 300) { echo "<script>alert('Alamat terlalu panjang.'); window.history.back();</script>"; exit(); }
if (!preg_match('/^[0-9]{10,15}$/', $wa)) {
    echo "<script>alert('Nomor WhatsApp tidak valid.'); window.history.back();</script>"; exit();
}
if (!in_array($hari_bayar, [1, 2, 3])) {
    echo "<script>alert('Durasi tidak valid.'); window.history.back();</script>"; exit();
}

$nama   = htmlspecialchars($nama,   ENT_QUOTES, 'UTF-8');
$alamat = htmlspecialchars($alamat, ENT_QUOTES, 'UTF-8');

// Ambil data unit
$stmt = $koneksi->prepare(
    "SELECT id_unit, nama_unit, kategori FROM units
     WHERE id_unit=? AND (tipe_layanan='Sewa Luar' OR (tipe_layanan='Main di Tempat' AND kategori='PS5'))
     AND status='Tersedia'"
);
$stmt->bind_param("i", $id_unit); $stmt->execute();
$unit_data = $stmt->get_result()->fetch_assoc(); $stmt->close();
if (!$unit_data) { echo "<script>alert('Unit tidak tersedia.'); window.history.back();</script>"; exit(); }

$nama_unit = $unit_data['nama_unit'];
$kategori  = $unit_data['kategori'];

// Hitung harga & durasi dengan logika promo
$hpp       = match($kategori){ 'PS5' => 195000, 'Nintendo' => 100000, default => 100000 };
$hpp      += $pakai_playbox ? 30000 : 0;
$is_promo  = is_promo_weekday($koneksi, $tgl_ambil);
$sewa      = hitung_sewa($hari_bayar, $hpp, $is_promo && $hari_bayar >= 2);

$durasi       = $sewa['durasi_str'];  // "3 Hari" atau "5 Hari"
$harga        = $sewa['harga'];
$is_promo_int = $is_promo ? 1 : 0;

// Upload files
$allowed_mime = ['image/jpeg', 'image/png', 'image/webp'];
$max_size     = 5 * 1024 * 1024;

function upload_secure($file_key, $prefix) {
    global $allowed_mime, $max_size;
    $file = $_FILES[$file_key];
    if ($file['error'] !== UPLOAD_ERR_OK)           return ['error' => "Gagal upload $file_key."];
    if ($file['size']  > $max_size)                 return ['error' => "File $file_key terlalu besar (maks 5MB)."];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $GLOBALS['allowed_mime'])) return ['error' => "Format $file_key tidak diizinkan."];
    $ext = ext_from_mime($mime);
    if (!$ext) return ['error' => "Tipe file $file_key tidak dikenali."];
    $nama_file = $prefix . '_' . bin2hex(random_bytes(16)) . '.' . $ext;
    $folder    = UPLOAD_PATH . 'berkas' . DIRECTORY_SEPARATOR;
    if (!is_dir($folder)) mkdir($folder, 0750, true);
    if (!move_uploaded_file($file['tmp_name'], $folder . $nama_file)) return ['error' => "Gagal menyimpan $file_key."];
    return ['filename' => $nama_file];
}

$ktp  = upload_secure('ktp',  'ktp');
$stnk = upload_secure('stnk', 'stnk');
if (isset($ktp['error']))  { echo "<script>alert('" . addslashes($ktp['error'])  . "'); window.history.back();</script>"; exit(); }
if (isset($stnk['error'])) { echo "<script>alert('" . addslashes($stnk['error']) . "'); window.history.back();</script>"; exit(); }

// Insert: s s s i s i i s i s s = 11 params
$stmt = $koneksi->prepare(
    "INSERT INTO pengajuan
     (nama_penyewa, no_wa, alamat, id_unit, durasi, harga, pakai_playbox, tgl_ambil, is_promo, foto_ktp, foto_stnk, status_pengajuan)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')"
);
$stmt->bind_param(
    "sssisiisiss",
    $nama, $wa, $alamat, $id_unit, $durasi, $harga, $pakai_playbox, $tgl_ambil, $is_promo_int, $ktp['filename'], $stnk['filename']
);

if ($stmt->execute()) {
    $id_pengajuan = $koneksi->insert_id;

    $upd = $koneksi->prepare("UPDATE units SET status='Disewa' WHERE id_unit=?");
    $upd->bind_param("i", $id_unit); $upd->execute(); $upd->close();

    $tgl_fmt   = date('d/m/Y', strtotime($tgl_ambil));
    $harga_fmt = 'Rp ' . number_format($harga, 0, ',', '.');
    $promo_txt = $is_promo && $hari_bayar >= 2 ? " 🎁 ({$sewa['label']})" : '';

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
        'is_promo'    => $is_promo && $hari_bayar >= 2,
        'promo_label' => $sewa['label'],
    ];
    header("Location: sukses_sewa.php"); exit();

} else {
    $err = $koneksi->error;
    error_log("[Violet PS] Gagal insert pengajuan: " . $err);
    // Cek apakah error karena kolom tidak ada (belum run migrasi)
    $msg = 'Terjadi kesalahan sistem.';
    if(strpos($err, 'tgl_ambil') !== false || strpos($err, 'is_promo') !== false || strpos($err, 'pakai_playbox') !== false || strpos($err, 'harga') !== false){
        $msg = 'Struktur database belum diperbarui. Jalankan migrasi_v4.sql, migrasi_v5.sql, dan migrasi_v6.sql di phpMyAdmin.';
    }
    echo "<script>alert('".addslashes($msg)."'); window.history.back();</script>";
}
$stmt->close();