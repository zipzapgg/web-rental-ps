<?php
require_once '../config/koneksi.php';
// Panggil rumus promo dan harga
if (file_exists('../config/promo.php')) {
    require_once '../config/promo.php';
}

require_admin('login.php');
csrf_check();

$wa            = preg_replace('/[^0-9]/', '', $_POST['no_wa'] ?? '');
$nama          = trim($_POST['nama_penyewa'] ?? '');
$alamat        = trim($_POST['alamat'] ?? '');
$id_unit       = intval($_POST['id_unit'] ?? 0);
$hari_bayar    = intval($_POST['durasi'] ?? 1);
$pakai_playbox = isset($_POST['pakai_playbox']) ? 1 : 0;
$foto_ktp      = $_POST['foto_ktp'] ?? '';
$foto_stnk     = $_POST['foto_stnk'] ?? '';
$tgl_ambil     = date('Y-m-d'); // Karena pelanggan ada di toko, tanggal ambil otomatis HARI INI

if (!$wa || !$nama || !$id_unit) {
    die("Akses ditolak: Data tidak lengkap.");
}

// 1. Cek Ketersediaan Unit
$stmt = $koneksi->prepare("SELECT nama_unit, kategori FROM units WHERE id_unit = ? AND status = 'Tersedia'");
$stmt->bind_param("i", $id_unit);
$stmt->execute();
$unit_data = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$unit_data) {
    header("Location: index.php?msg=qe_not_avail");
    exit();
}

$nama_unit = $unit_data['nama_unit'];
$kategori  = $unit_data['kategori'];

if ($kategori !== 'PS4') {
    $pakai_playbox = 0; // Pastikan Playbox hanya untuk PS4
}

// 2. Kalkulasi Harga & Promo
$stmt_libur = $koneksi->prepare("SELECT 1 FROM hari_libur WHERE ? BETWEEN tgl_mulai AND tgl_selesai");
$stmt_libur->bind_param("s", $tgl_ambil);
$stmt_libur->execute();
$is_libur = $stmt_libur->get_result()->num_rows > 0;
$stmt_libur->close();

$hpp = get_hpp($kategori, (bool)$pakai_playbox, $is_libur);
$is_promo = !$is_libur && is_promo_weekday($koneksi, $tgl_ambil);
$promo_applicable = $is_promo && $hari_bayar >= 2;

$sewa = hitung_sewa($hari_bayar, $hpp, $promo_applicable);
$is_promo_int = $promo_applicable ? 1 : 0;
$durasi_str   = $sewa['durasi_str'] ?? ($sewa['hari_dapat'] . " Hari"); 
$harga_total  = $sewa['harga'];

// 3. Eksekusi ke Database (Transaction)
$koneksi->begin_transaction();

try {
    // 1. Kunci unit terlebih dahulu dengan syarat ketat
    $upd = $koneksi->prepare("UPDATE units SET status='Disewa' WHERE id_unit=? AND status='Tersedia'");
    $upd->bind_param("i", $id_unit);
    $upd->execute();
    
    // 2. Cek apakah update berhasil
    if ($upd->affected_rows === 0) {
        $upd->close();
        throw new Exception("Gagal! Unit sudah disewa melalui web beberapa detik yang lalu.");
    }
    $upd->close();

    // 3. Masukkan ke pengajuan dengan status langsung 'Disetujui'
    $stmt = $koneksi->prepare(
        "INSERT INTO pengajuan 
         (nama_penyewa, no_wa, alamat, id_unit, durasi, harga, pakai_playbox, tgl_ambil, is_promo, foto_ktp, foto_stnk, status_pengajuan) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Disetujui')"
    );
    $stmt->bind_param(
        "sssisiisiss", 
        $nama, $wa, $alamat, $id_unit, $durasi_str, $harga_total, 
        $pakai_playbox, $tgl_ambil, $is_promo_int, $foto_ktp, $foto_stnk
    );
    $stmt->execute();
    $id_pengajuan = $koneksi->insert_id;
    $stmt->close();

    // 4. Catat ke Log Aktivitas
    if (function_exists('log_activity')) {
        log_activity($koneksi, 'QUICK_ENTRY', "Sewa kilat (Walk-in): $nama menyewa $nama_unit selama $durasi_str");
    }

    $koneksi->commit();
    header("Location: index.php?msg=qe_ok");
    exit();

} catch (Exception $e) {
    $koneksi->rollback();
    $pesan_error = $e->getMessage();
    header("Location: index.php?msg=qe_error&error_text=" . urlencode($pesan_error));
    exit();
}