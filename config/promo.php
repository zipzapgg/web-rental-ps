<?php
/**
 * Cek apakah tanggal berhak dapat promo weekday.
 * Promo berlaku: Senin–Kamis DAN tidak dalam periode libur.
 */
function is_promo_weekday(mysqli $db, string $tgl): bool {
    if (!$tgl) return false;

    $ts = strtotime($tgl);
    if ($ts === false) return false;

    // date('N'): 1=Senin, 2=Selasa, 3=Rabu, 4=Kamis, 5=Jumat, 6=Sabtu, 7=Minggu
    $hari = intval(date('N', $ts));

    // Hanya Senin–Kamis (1–4) yang dapat promo
    if ($hari < 1 || $hari > 4) return false;

    // Cek apakah tanggal masuk periode libur
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
 * Hitung durasi dan harga sewa.
 * Promo: bayar N hari → dapat 2N-1 hari.
 * Tanpa promo: dapat = bayar.
 */
function hitung_sewa(int $hari_bayar, int $hpp, bool $promo): array {
    $hari_dapat = $promo ? (2 * $hari_bayar - 1) : $hari_bayar;
    $harga      = $hpp * $hari_bayar;
    return [
        'hari_bayar' => $hari_bayar,
        'hari_dapat' => $hari_dapat,
        'harga'      => $harga,
        'durasi_str' => $hari_dapat . ' Hari',
        'label'      => $promo
            ? "Bayar $hari_bayar hari, dapat $hari_dapat hari 🎁"
            : "$hari_bayar Hari",
    ];
}

/**
 * Ambil semua range libur aktif/mendatang untuk kalkulasi JS.
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