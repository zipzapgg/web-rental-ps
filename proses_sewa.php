<?php
require_once 'config/koneksi.php';
csrf_check();

if (!isset($_POST['kirim'])) { header("Location: sewa.php"); exit(); }

$nama    = trim($_POST['nama'] ?? '');
$wa      = trim($_POST['wa'] ?? '');
$alamat  = trim($_POST['alamat'] ?? '');
$id_unit = intval($_POST['id_unit'] ?? 0);
$durasi  = trim($_POST['durasi'] ?? '');

if (!$nama || !$wa || !$alamat || !$id_unit || !$durasi) {
    echo "<script>alert('Semua field wajib diisi.'); window.history.back();</script>"; exit();
}
if (!preg_match('/^[0-9]{10,15}$/', $wa)) {
    echo "<script>alert('Nomor WhatsApp tidak valid.'); window.history.back();</script>"; exit();
}

// Validasi unit
$stmt = $koneksi->prepare("SELECT id_unit FROM units WHERE id_unit=? AND tipe_layanan='Sewa Luar' AND status='Tersedia'");
$stmt->bind_param("i",$id_unit); $stmt->execute();
if($stmt->get_result()->num_rows===0){echo "<script>alert('Unit tidak tersedia.'); window.history.back();</script>"; exit();}
$stmt->close();

// Upload secure
$allowed_mime = ['image/jpeg','image/png','image/webp'];
$max_size = 5*1024*1024;

function upload_secure($file_key, $prefix){
    global $allowed_mime, $max_size;
    $file = $_FILES[$file_key];
    if($file['error']!==UPLOAD_ERR_OK) return ['error'=>"Gagal upload $file_key."];
    if($file['size']>$max_size) return ['error'=>"File $file_key terlalu besar (max 5MB)."];
    $finfo=finfo_open(FILEINFO_MIME_TYPE); $mime=finfo_file($finfo,$file['tmp_name']); finfo_close($finfo);
    if(!in_array($mime,$GLOBALS['allowed_mime'])) return ['error'=>"Format $file_key tidak diizinkan."];
    $ext=strtolower(pathinfo($file['name'],PATHINFO_EXTENSION));
    $nama_file=$prefix.'_'.bin2hex(random_bytes(16)).'.'.$ext;
    $folder=UPLOAD_PATH.'berkas'.DIRECTORY_SEPARATOR;
    if(!is_dir($folder)) mkdir($folder,0750,true);
    if(!move_uploaded_file($file['tmp_name'],$folder.$nama_file)) return ['error'=>"Gagal menyimpan $file_key."];
    return ['filename'=>$nama_file];
}

$ktp  = upload_secure('ktp','ktp');
$stnk = upload_secure('stnk','stnk');
if(isset($ktp['error'])){ echo "<script>alert('".$ktp['error']."'); window.history.back();</script>"; exit(); }
if(isset($stnk['error'])){ echo "<script>alert('".$stnk['error']."'); window.history.back();</script>"; exit(); }

$stmt=$koneksi->prepare("INSERT INTO pengajuan (nama_penyewa,no_wa,alamat,id_unit,durasi,foto_ktp,foto_stnk,status_pengajuan) VALUES(?,?,?,?,?,?,?,'Pending')");
$stmt->bind_param("sssisss",$nama,$wa,$alamat,$id_unit,$durasi,$ktp['filename'],$stnk['filename']);
if($stmt->execute()){
    $upd=$koneksi->prepare("UPDATE units SET status='Disewa' WHERE id_unit=?");
    $upd->bind_param("i",$id_unit); $upd->execute(); $upd->close();
    echo "<script>alert('Pengajuan berhasil! Kami akan menghubungi kamu via WhatsApp untuk konfirmasi pengambilan.'); window.location='index.php';</script>";
} else {
    echo "<script>alert('Terjadi kesalahan. Silakan coba lagi.'); window.history.back();</script>";
}
$stmt->close();