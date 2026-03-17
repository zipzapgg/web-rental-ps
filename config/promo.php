<?php
/**
 * Cek apakah tanggal berhak dapat promo weekday.
 * Promo berlaku jika: hari Senin–Kamis DAN tidak dalam range libur manapun.
 */
function is_promo_weekday(mysqli $db, string $tgl): bool {
    if (!$tgl) return false;
    $hari = intval(date('N', strtotime($tgl))); // 1=Sen ... 7=Min
    if ($hari < 1 || $hari > 4) return false;

    $s = $db->prepare(
        "SELECT id_libur FROM hari_libur WHERE ? BETWEEN tgl_mulai AND tgl_selesai LIMIT 1"
    );
    $s->bind_param("s", $tgl);
    $s->execute();
    $ada = $s->get_result()->num_rows > 0;
    $s->close();
    return !$ada;
}

/**
 * Hitung durasi aktual (hari yang didapat) dan harga dari promo.
 * Promo weekday: bayar N hari → dapat N + (N-1) hari = 2N-1 hari
 *   - bayar 2 → dapat 3 hari
 *   - bayar 3 → dapat 5 hari
 * Tanpa promo: dapat = bayar
 */
function hitung_sewa(int $hari_bayar, int $hpp, bool $promo): array {
    $hari_dapat = $promo ? (2 * $hari_bayar - 1) : $hari_bayar;
    $harga      = $hpp * $hari_bayar;
    return [
        'hari_bayar' => $hari_bayar,
        'hari_dapat' => $hari_dapat,
        'harga'      => $harga,
        'durasi_str' => $hari_dapat . ' Hari',
        'label'      => $promo ? "Bayar $hari_bayar hari, dapat $hari_dapat hari 🎁" : "$hari_bayar Hari",
    ];
}

/**
 * Ambil semua range libur yang aktif/mendatang untuk JS
 */
function get_libur_ranges(mysqli $db): array {
    $r = $db->query(
        "SELECT tgl_mulai, tgl_selesai, keterangan FROM hari_libur
         WHERE tgl_selesai >= CURDATE() ORDER BY tgl_mulai"
    );
    $result = [];
    while ($row = $r->fetch_assoc()) $result[] = $row;
    return $result;
}