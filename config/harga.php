<?php
/**
 * Konstanta harga terpusat — ubah di sini, berlaku di seluruh aplikasi.
 */

define('HARGA_PS4',        100000);
define('HARGA_PS5',        195000);
define('HARGA_NINTENDO',   100000);
define('HARGA_PLAYBOX',     30000);   // tambahan per hari jika pakai playbox
define('DENDA_PER_JAM',     10000);   // denda keterlambatan per jam (non-playbox)
define('DENDA_PER_JAM_PB',  20000);   // denda keterlambatan per jam (playbox)
define('BATAS_JAM_DENDA',       6);   // lebih dari N jam = dianggap +1 hari sewa
define('MAX_PERPANJANG_HARI',   7);   // maksimum hari perpanjangan sekaligus
define('MAX_DURASI_HARI',       3);   // maksimum durasi sewa awal

/**
 * Ambil HPP (harga pokok per hari) berdasarkan kategori unit.
 */
function get_hpp(string $kategori, bool $pakai_playbox = false): int {
    $base = match(strtoupper($kategori)) {
        'PS5'      => HARGA_PS5,
        'NINTENDO' => HARGA_NINTENDO,
        default    => HARGA_PS4,
    };
    return $base + ($pakai_playbox ? HARGA_PLAYBOX : 0);
}

/**
 * Hitung denda keterlambatan.
 * @param int  $jam_telat    Jumlah jam terlambat
 * @param int  $hpp          Harga pokok per hari (untuk kasus > BATAS_JAM_DENDA)
 * @param bool $is_playbox   Apakah unit playbox (tarif denda berbeda)
 * @return int               Nominal denda dalam rupiah
 */
function hitung_denda(int $jam_telat, int $hpp, bool $is_playbox = false): int {
    if ($jam_telat <= 0) return 0;
    $denda_per_jam = $is_playbox ? DENDA_PER_JAM_PB : DENDA_PER_JAM;
    return $jam_telat > BATAS_JAM_DENDA ? $hpp : $jam_telat * $denda_per_jam;
}
