<?php
/**
 * Konstanta harga terpusat  ubah di sini, berlaku di seluruh aplikasi.
 */

define('HARGA_PS4',        100000);
define('HARGA_PS5',        195000);
define('HARGA_NINTENDO',   100000);

define('HARGA_PS4_LIBUR',      135000);
define('HARGA_PS5_LIBUR',      230000);
define('HARGA_NINTENDO_LIBUR', 135000);

define('HARGA_PLAYBOX',     30000);   // tambahan per hari jika pakai playbox
define('TOTAL_PLAYBOX',         1);   // jumlah stok koper Playbox fisik
define('DENDA_PER_JAM',     10000);   // denda keterlambatan per jam (non-playbox)
define('DENDA_PER_JAM_PB',  10000);   // denda keterlambatan per jam (playbox)
define('BATAS_JAM_DENDA',       6);   // lebih dari N jam = dianggap +1 hari sewa
define('MAX_PERPANJANG_HARI',   7);   // maksimum hari perpanjangan sekaligus
define('MAX_DURASI_HARI',       3);   // maksimum durasi sewa awal

/**
 * Ambil HPP (harga pokok per hari) berdasarkan kategori unit & status libur.
 */
function get_hpp(string $kategori, bool $pakai_playbox = false, bool $is_libur = false): int {
    $kat = strtoupper($kategori);
    if ($is_libur) {
        $base = match($kat) {
            'PS5'      => HARGA_PS5_LIBUR,
            'NINTENDO' => HARGA_NINTENDO_LIBUR,
            default    => HARGA_PS4_LIBUR,
        };
    } else {
        $base = match($kat) {
            'PS5'      => HARGA_PS5,
            'NINTENDO' => HARGA_NINTENDO,
            default    => HARGA_PS4,
        };
    }
    return $base + ($pakai_playbox ? HARGA_PLAYBOX : 0);
}

/**
 * Hitung denda keterlambatan.
 */
function hitung_denda(int $jam_telat, int $hpp, bool $is_playbox = false): int {
    if ($jam_telat <= 0) return 0;
    $denda_per_jam = $is_playbox ? DENDA_PER_JAM_PB : DENDA_PER_JAM;
    return $jam_telat > BATAS_JAM_DENDA ? $hpp : $jam_telat * $denda_per_jam;
}