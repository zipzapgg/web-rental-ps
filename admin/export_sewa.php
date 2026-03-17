<?php
require_once '../config/koneksi.php';
require_admin('login.php');
csrf_get_check();

$filter     = $_GET['filter']     ?? 'semua';
$tgl_dari   = $_GET['tgl_dari']   ?? '';
$tgl_sampai = $_GET['tgl_sampai'] ?? '';

$conditions = [];
$status_cond = match($filter){
    'pending'  => "status_pengajuan='Pending'",
    'terima'   => "status_pengajuan='Disetujui'",
    'tolak'    => "status_pengajuan='Ditolak'",
    'selesai'  => "status_pengajuan='Selesai'",
    default    => ''
};
if($status_cond) $conditions[] = $status_cond;
if($tgl_dari)    $conditions[] = "DATE(tgl_pengajuan) >= '".mysqli_real_escape_string($koneksi,$tgl_dari)."'";
if($tgl_sampai)  $conditions[] = "DATE(tgl_pengajuan) <= '".mysqli_real_escape_string($koneksi,$tgl_sampai)."'";
$where = $conditions ? 'WHERE '.implode(' AND ',$conditions) : '';

$data = $koneksi->query(
    "SELECT p.id_pengajuan, p.tgl_pengajuan, p.nama_penyewa, p.no_wa, p.alamat,
            u.nama_unit, u.kategori, p.durasi, p.harga, p.pakai_playbox, p.status_pengajuan
     FROM pengajuan p JOIN units u ON p.id_unit=u.id_unit
     $where ORDER BY tgl_pengajuan DESC"
);

$filename = 'sewa_violet_'.date('Ymd_His').'.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="'.$filename.'"');
header('Cache-Control: no-cache');

$out = fopen('php://output','w');
fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel

fputcsv($out, ['ID','Tanggal','Nama Penyewa','No WA','Alamat','Unit','Kategori','Durasi','Harga','Playbox','Status'], ';');

while($d = $data->fetch_assoc()){
    fputcsv($out, [
        $d['id_pengajuan'],
        date('d/m/Y H:i', strtotime($d['tgl_pengajuan'])),
        $d['nama_penyewa'],
        $d['no_wa'],
        $d['alamat'],
        $d['nama_unit'],
        $d['kategori'],
        $d['durasi'],
        $d['harga'] ? 'Rp '.number_format($d['harga'],0,',','.') : '-',
        $d['pakai_playbox'] ? 'Ya' : 'Tidak',
        $d['status_pengajuan'],
    ], ';');
}
fclose($out);
exit();