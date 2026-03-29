<?php
require_once '../config/koneksi.php';
require_admin('login.php');
csrf_get_check();

$filter     = $_GET['filter']     ?? 'semua';
$tgl_dari   = $_GET['tgl_dari']   ?? '';
$tgl_sampai = $_GET['tgl_sampai'] ?? '';

// ── PERBAIKAN: Gunakan prepared statement, bukan string interpolation ──────
$where_parts = [];
$bind_types  = '';
$bind_params = [];

$status_cond = match($filter) {
    'pending' => "p.status_pengajuan='Pending'",
    'terima'  => "p.status_pengajuan='Disetujui'",
    'tolak'   => "p.status_pengajuan='Ditolak'",
    'selesai' => "p.status_pengajuan='Selesai'",
    default   => ''
};
if ($status_cond) $where_parts[] = $status_cond;

if ($tgl_dari) {
    $where_parts[] = "DATE(p.tgl_pengajuan) >= ?";
    $bind_types   .= 's';
    $bind_params[] = $tgl_dari;
}
if ($tgl_sampai) {
    $where_parts[] = "DATE(p.tgl_pengajuan) <= ?";
    $bind_types   .= 's';
    $bind_params[] = $tgl_sampai;
}

$where = $where_parts ? 'WHERE ' . implode(' AND ', $where_parts) : '';

$sql = "SELECT p.id_pengajuan, p.tgl_pengajuan, p.nama_penyewa, p.no_wa, p.alamat,
               u.nama_unit, u.kategori, p.durasi, p.harga, p.pakai_playbox,
               p.is_promo, p.status_pengajuan
        FROM pengajuan p
        JOIN units u ON p.id_unit = u.id_unit
        $where
        ORDER BY p.tgl_pengajuan DESC";

$stmt = $koneksi->prepare($sql);
if ($bind_types) {
    $stmt->bind_param($bind_types, ...$bind_params);
}
$stmt->execute();
$data = $stmt->get_result();
$stmt->close();

$filename = 'sewa_violet_' . date('Ymd_His') . '.csv';
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-cache');

$out = fopen('php://output', 'w');
fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel

fputcsv($out, ['ID', 'Tanggal', 'Nama Penyewa', 'No WA', 'Alamat', 'Unit', 'Kategori', 'Durasi', 'Harga', 'Playbox', 'Promo', 'Status'], ';');

while ($d = $data->fetch_assoc()) {
    fputcsv($out, [
        $d['id_pengajuan'],
        date('d/m/Y H:i', strtotime($d['tgl_pengajuan'])),
        $d['nama_penyewa'],
        $d['no_wa'],
        $d['alamat'],
        $d['nama_unit'],
        $d['kategori'],
        $d['durasi'],
        $d['harga'] ? 'Rp ' . number_format($d['harga'], 0, ',', '.') : '-',
        $d['pakai_playbox'] ? 'Ya' : 'Tidak',
        $d['is_promo']      ? 'Ya' : 'Tidak',
        $d['status_pengajuan'],
    ], ';');
}
fclose($out);
exit();