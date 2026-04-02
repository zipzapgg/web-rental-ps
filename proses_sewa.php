<?php
require_once 'config/koneksi.php';
require_once 'config/promo.php';

if (!isset($_POST['kirim'])) {
    header("Location: sewa.php");
    exit();
}

// ── CSRF ──────────────────────────────────────────────────────────────────
$csrf_sent   = $_POST['csrf_token'] ?? '';
$csrf_stored = $_SESSION['csrf_token'] ?? '';
if (empty($csrf_sent) || empty($csrf_stored) || !hash_equals($csrf_stored, $csrf_sent)) {
    $_SESSION['form_error'] = 'Sesi expired. Silakan refresh halaman dan coba lagi.';
    header("Location: sewa.php");
    exit();
}

// ── Input & sanitasi ──────────────────────────────────────────────────────
// PERBAIKAN: Jangan htmlspecialchars sebelum masuk DB — lakukan saat output
$nama          = trim($_POST['nama']      ?? '');
$wa            = preg_replace('/[^0-9]/', '', trim($_POST['wa'] ?? ''));
$alamat        = trim($_POST['alamat']    ?? '');
$id_unit       = intval($_POST['id_unit'] ?? 0);
$hari_bayar    = intval($_POST['durasi']  ?? 1);
$pakai_playbox = isset($_POST['pakai_playbox']) ? 1 : 0;
$tgl_ambil     = trim($_POST['tgl_ambil'] ?? '');

error_log("[Violet PS] Submit: nama=$nama wa=$wa id_unit=$id_unit hari=$hari_bayar tgl=$tgl_ambil");

// ── Validasi ──────────────────────────────────────────────────────────────
$errors = [];

if (!$nama)    $errors[] = 'Nama wajib diisi.';
if (!$wa)      $errors[] = 'Nomor WhatsApp wajib diisi.';
if (!$alamat)  $errors[] = 'Alamat wajib diisi.';
if (!$id_unit) $errors[] = 'Unit wajib dipilih.';
if (!$tgl_ambil) $errors[] = 'Tanggal ambil wajib diisi.';

if ($nama   && mb_strlen($nama)   > 100) $errors[] = 'Nama maksimal 100 karakter.';
if ($alamat && mb_strlen($alamat) > 300) $errors[] = 'Alamat maksimal 300 karakter.';

// Validasi karakter nama — hanya huruf, spasi, titik, tanda hubung
if ($nama && !preg_match('/^[\p{L}\s\.\-\']+$/u', $nama)) {
    $errors[] = 'Nama hanya boleh berisi huruf, spasi, titik, atau tanda hubung.';
}

if ($wa && !preg_match('/^[0-9]{10,15}$/', $wa)) {
    $errors[] = 'Nomor WhatsApp tidak valid (10–15 angka).';
}

if ($tgl_ambil && $tgl_ambil < date('Y-m-d')) {
    $errors[] = 'Tanggal pengambilan tidak boleh di masa lalu.';
}

if (!in_array($hari_bayar, range(1, MAX_DURASI_HARI))) {
    $errors[] = 'Durasi tidak valid.';
}

if ($errors) {
    $_SESSION['form_error'] = implode(' ', $errors);
    header("Location: sewa.php");
    exit();
}

// ── Cek batas pengajuan aktif per nomor WA ────────────────────────────────
$stmt = $koneksi->prepare(
    "SELECT COUNT(*) as c FROM pengajuan
     WHERE REGEXP_REPLACE(no_wa,'[^0-9]','') = ?
     AND status_pengajuan IN ('Pending','Disetujui')"
);
$stmt->bind_param("s", $wa);
$stmt->execute();
$aktif_wa = $stmt->get_result()->fetch_assoc()['c'];
$stmt->close();

if ($aktif_wa >= 2) {
    $_SESSION['form_error'] = 'Nomor WhatsApp ini sudah memiliki pengajuan aktif yang sedang diproses. Selesaikan dulu sebelum mengajukan yang baru.';
    header("Location: sewa.php");
    exit();
}

// ── Cek ketersediaan unit ─────────────────────────────────────────────────
$stmt = $koneksi->prepare(
    "SELECT id_unit, nama_unit, kategori FROM units
     WHERE id_unit = ? AND (tipe_layanan='Sewa Luar' OR (tipe_layanan='Main di Tempat' AND kategori='PS5')) AND status='Tersedia'"
);
$stmt->bind_param("i", $id_unit);
$stmt->execute();
$unit_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$unit_data) {
    $_SESSION['form_error'] = 'Unit tidak tersedia atau sudah disewa orang lain. Pilih unit lain.';
    header("Location: sewa.php");
    exit();
}

$nama_unit = $unit_data['nama_unit'];
$kategori  = $unit_data['kategori'];

// ── Playbox hanya untuk PS4 ───────────────────────────────────────────────
if ($kategori !== 'PS4') {
    $pakai_playbox = 0;
}

// ── Cek Status Libur Manual ───────────────────────────────────────────────
$stmt_libur = $koneksi->prepare("SELECT 1 FROM hari_libur WHERE ? BETWEEN tgl_mulai AND tgl_selesai");
$stmt_libur->bind_param("s", $tgl_ambil);
$stmt_libur->execute();
$is_libur_manual = $stmt_libur->get_result()->num_rows > 0;
$stmt_libur->close();

// ── Validasi Irisan Tanggal Playbox (Anti-Bypass) ─────────────────────────
if ($pakai_playbox) {
    $tgl_kembali = date('Y-m-d', strtotime($tgl_ambil . " + $hari_bayar days"));
    $stmt_pb = $koneksi->prepare("
        SELECT COUNT(*) as c FROM pengajuan
        WHERE pakai_playbox = 1 AND status_pengajuan IN ('Pending', 'Disetujui')
        AND tgl_ambil < ? AND DATE_ADD(tgl_ambil, INTERVAL CAST(SUBSTRING_INDEX(durasi, ' ', 1) AS UNSIGNED) DAY) > ?
    ");
    // Syarat overlapping: StartA < EndB AND EndA > StartB
    $stmt_pb->bind_param("ss", $tgl_kembali, $tgl_ambil);
    $stmt_pb->execute();
    $pb_pakai = $stmt_pb->get_result()->fetch_assoc()['c'];
    $stmt_pb->close();

    if ($pb_pakai >= TOTAL_PLAYBOX) {
        $_SESSION['form_error'] = 'Playbox sudah dibooking orang lain pada rentang tanggal tersebut. Silakan ganti tanggal atau hapus centang Playbox.';
        header("Location: sewa.php");
        exit();
    }
}

// ── Hitung harga (pakai konstanta & status libur) ─────────────────────────
$hpp      = get_hpp($kategori, (bool)$pakai_playbox, $is_libur_manual);
$is_promo = !$is_libur_manual && is_promo_weekday($koneksi, $tgl_ambil);
$promo_applicable = $is_promo && $hari_bayar >= 2;
$sewa = hitung_sewa($hari_bayar, $hpp, $promo_applicable);
$is_promo_int = $promo_applicable ? 1 : 0;
$durasi       = $sewa['hari_dapat'] . " Hari"; 
$harga        = $sewa['harga_total'];

// ── Upload berkas ─────────────────────────────────────────────────────────
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

    $ext    = ext_from_mime($mime) ?? 'jpg';
    $folder = UPLOAD_PATH . 'berkas' . DIRECTORY_SEPARATOR;
    if (!is_dir($folder)) mkdir($folder, 0750, true);

    $fname = $prefix . '_' . bin2hex(random_bytes(12)) . '.' . $ext;
    if (!move_uploaded_file($file['tmp_name'], $folder . $fname)) {
        return ['error' => "Gagal menyimpan $key. Hubungi admin."];
    }
    return ['filename' => $fname];
}

$ktp  = violet_upload('ktp',  'ktp');
$stnk = violet_upload('stnk', 'stnk');

if (isset($ktp['error'])) {
    $_SESSION['form_error'] = $ktp['error'];
    header("Location: sewa.php");
    exit();
}
if (isset($stnk['error'])) {
    $_SESSION['form_error'] = $stnk['error'];
    header("Location: sewa.php");
    exit();
}

// ── Insert ke DB (transaksi) ──────────────────────────────────────────────
// PERBAIKAN: Simpan $nama dan $alamat mentah (tanpa htmlspecialchars)
// htmlspecialchars hanya dilakukan saat OUTPUT (di template PHP), bukan saat simpan ke DB
$koneksi->begin_transaction();

try {
    $stmt = $koneksi->prepare(
        "INSERT INTO pengajuan
         (nama_penyewa, no_wa, alamat, id_unit, durasi, harga, pakai_playbox, tgl_ambil, is_promo, foto_ktp, foto_stnk, status_pengajuan)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Pending')"
    );
    $stmt->bind_param(
        "sssisiisiss",
        $nama, $wa, $alamat, $id_unit,
        $durasi, $harga, $pakai_playbox,
        $tgl_ambil, $is_promo_int,
        $ktp['filename'], $stnk['filename']
    );
    $stmt->execute();
    $id_pengajuan = $koneksi->insert_id;
    $stmt->close();

    $upd = $koneksi->prepare("UPDATE units SET status='Disewa' WHERE id_unit=?");
    $upd->bind_param("i", $id_unit);
    $upd->execute();
    $upd->close();

    $koneksi->commit();
    error_log("[Violet PS] Sukses! id=$id_pengajuan");

} catch (Exception $e) {
    $koneksi->rollback();
    error_log("[Violet PS] Transaksi gagal: " . $e->getMessage());
    $_SESSION['form_error'] = 'Terjadi kesalahan sistem. Silakan coba lagi.';
    header("Location: sewa.php");
    exit();
}

// ── Simpan ke session untuk halaman sukses ────────────────────────────────
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
    'is_promo'    => (bool)$is_promo_int,
    'promo_label' => $sewa['label'],
];

header("Location: sukses_sewa.php");
exit();