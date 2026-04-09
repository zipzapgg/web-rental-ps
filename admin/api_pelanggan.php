<?php
require_once '../config/koneksi.php';
require_admin(); // Keamanan: Hanya admin/karyawan yang bisa mengakses API ini

header('Content-Type: application/json');
$wa = preg_replace('/[^0-9]/', '', $_GET['wa'] ?? '');

if (empty($wa)) {
    echo json_encode(['status' => 'error', 'pesan' => 'Nomor WA kosong']);
    exit();
}

// Mencari riwayat sewa terakhir yang valid berdasarkan nomor WA
$sql = "SELECT nama_penyewa, alamat, foto_ktp, foto_stnk 
        FROM pengajuan 
        WHERE REGEXP_REPLACE(no_wa,'[^0-9]','') = ? 
        AND status_pengajuan IN ('Selesai', 'Disetujui') 
        ORDER BY tgl_pengajuan DESC LIMIT 1";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $wa);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(['status' => 'success', 'data' => $row]);
} else {
    echo json_encode(['status' => 'not_found']);
}
$stmt->close();