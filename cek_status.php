<?php
require_once 'config/koneksi.php';

// ── Rate limiting sederhana untuk cek status ──────────────────────────────
if (isset($_GET['wa']) && $_GET['wa'] !== '') {
    $ip      = $_SERVER['REMOTE_ADDR'];
    $window  = 60;   // detik
    $max_req = 10;   // maksimum request per window per IP

    // Simpan di session sederhana dan tidak butuh tabel baru
    $key_ts  = 'cek_status_ts';
    $key_cnt = 'cek_status_cnt';
    $now     = time();

    if (!isset($_SESSION[$key_ts]) || ($now - $_SESSION[$key_ts]) > $window) {
        $_SESSION[$key_ts]  = $now;
        $_SESSION[$key_cnt] = 0;
    }
    $_SESSION[$key_cnt]++;

    if ($_SESSION[$key_cnt] > $max_req) {
        http_response_code(429);
        // Tampilkan pesan rate limit sebagai flash, bukan die
        $rate_limited = true;
    }
}
?>
<!DOCTYPE html><html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <meta name="description" content="Cek status pengajuan sewa PS di Violet PlayStation Jagakarsa dengan nomor WhatsApp kamu.">
  <meta property="og:title" content="Cek Status Sewa Violet PlayStation">
  <meta property="og:description" content="Masukkan nomor WhatsApp untuk cek status pengajuanmu.">
  <link rel="icon" type="image/jpeg" href="assets/images/logo-violet.jpeg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <title>Cek Status Pengajuan Violet PlayStation</title>
  <link rel="stylesheet" href="assets/css/violet.css">
  <script src="assets/app.js" defer></script>
</head>
<body>
<?php include_once "config/svg_sprite.php"; ?>
<nav class="v-navbar">
  <div class="container nav-container">
    <a href="index.php" class="brand"><img src="assets/images/logo-violet.jpeg" alt="Violet PlayStation">VIOLET <span class="neon">PLAYSTATION</span></a>
    <a href="index.php" class="back-link">← Kembali</a>
  </div>
</nav>

<div class="wrap">
  <div class="page-title">CEK <span class="neon">STATUS</span></div>
  <p class="page-sub">Masukkan nomor WhatsApp yang kamu daftarkan saat pengajuan sewa.</p>

  <?php if (!empty($rate_limited)): ?>
  <div class="alert-rate" role="alert">
    ⚠ Terlalu banyak pencarian dalam waktu singkat. Coba lagi sebentar.
  </div>
  <?php endif; ?>

  <div class="search-card">
    <form method="GET">
      <label class="v-label" for="inp-wa-cek">Nomor WhatsApp</label>
      <div style="display:flex;gap:.75rem;margin-top:.4rem;">
        <input type="tel" name="wa" id="inp-wa-cek" inputmode="numeric" class="v-input" placeholder="08xxxxxxxxxx" autocomplete="off" value="<?php echo isset($_GET['wa']) ? htmlspecialchars($_GET['wa']) : ''; ?>" required style="flex:1;">
        <button type="submit" class="btn-violet" style="padding:.75rem 1.5rem;white-space:nowrap;"><span>Cek</span></button>
      </div>
    </form>
  </div>

<?php
if (!empty($rate_limited)) {
    // Tidak tampilkan hasil jika rate limited
} elseif (isset($_GET['wa']) && $_GET['wa'] !== '') {
    $wa_input = preg_replace('/[^0-9]/', '', $_GET['wa']);

    if (!preg_match('/^[0-9]{10,15}$/', $wa_input)) {
        echo '<div style="background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.2);border-radius:12px;padding:1.5rem;text-align:center;color:var(--v-muted);font-family:var(--font-ui);font-size:.9rem;">Format nomor tidak valid. Masukkan 10-15 angka.</div>';
    } else {
        $stmt = $koneksi->prepare(
            "SELECT p.*, u.nama_unit, u.kategori FROM pengajuan p
             JOIN units u ON p.id_unit=u.id_unit
             WHERE REGEXP_REPLACE(p.no_wa,'[^0-9]','') = ?
             ORDER BY p.tgl_pengajuan DESC LIMIT 5"
        );
        $stmt->bind_param("s", $wa_input);
        $stmt->execute();
        $results = $stmt->get_result();
        $stmt->close();

        if ($results->num_rows === 0):
?>
  <div style="background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.2);border-radius:12px;padding:2rem;text-align:center;color:var(--v-muted);font-family:var(--font-ui);font-size:.9rem;">
    Tidak ada pengajuan dengan nomor tersebut.<br>
    <a href="sewa.php" style="color:var(--v-violet);margin-top:.5rem;display:inline-block;">Ajukan sewa baru →</a>
  </div>
<?php else: while ($d = $results->fetch_assoc()):
    $st = $d['status_pengajuan'];
    $sc = match($st) { 'Pending' => 's-pending', 'Disetujui' => 's-disetujui', 'Ditolak' => 's-ditolak', 'Selesai' => 's-selesai', default => 's-pending' };
    $kat = $d['kategori'];
    $bc  = $kat === 'PS5' ? 'v-badge-ps5' : ($kat === 'Nintendo' ? 'v-badge-nin' : 'v-badge-ps4');
    $status_info = match($st) {
        'Pending'   => ['icon' => '⏳', 'msg' => 'Pengajuanmu sedang menunggu konfirmasi admin. Harap bersabar ya!'],
        'Disetujui' => ['icon' => '✅', 'msg' => 'Pengajuanmu sudah disetujui! Silakan ambil unit ke toko sesuai tanggal yang disepakati.'],
        'Ditolak'   => ['icon' => '❌', 'msg' => 'Maaf, pengajuanmu tidak dapat diproses. Silakan hubungi WA kami untuk info lebih lanjut.'],
        'Selesai'   => ['icon' => '🎉', 'msg' => 'Transaksi selesai. Terima kasih sudah menyewa di Violet PlayStation!'],
        default     => ['icon' => '❓', 'msg' => '']
    };
?>
  <div class="result-card" style="margin-bottom:1rem;">
    <div class="result-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;">
      <div>
        <div style="font-family:var(--font-display);font-size:1rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-lavender);"><?php echo htmlspecialchars($d['nama_unit']); ?></div>
        <div style="font-size:.78rem;color:var(--v-muted);font-family:var(--font-ui);margin-top:.15rem;"><?php echo date('d/m/Y H:i', strtotime($d['tgl_pengajuan'])); ?></div>
      </div>
      <span class="v-badge <?php echo $sc; ?>" style="font-size:.8rem;padding:.3rem .8rem;"><?php echo $st; ?></span>
    </div>

    <div style="padding:1rem 1.5rem;background:rgba(255,255,255,.02);border-bottom:1px solid var(--v-border);display:flex;gap:.75rem;align-items:flex-start;">
      <span style="font-size:1.4rem;flex-shrink:0;" aria-hidden="true"><?php echo $status_info['icon']; ?></span>
      <p style="font-size:.85rem;color:var(--v-muted);font-family:var(--font-ui);line-height:1.6;"><?php echo $status_info['msg']; ?></p>
    </div>

    <div class="detail-row"><span class="lbl">Kategori</span><span class="val"><span class="v-badge <?php echo $bc; ?>"><?php echo $kat; ?></span></span></div>
    <div class="detail-row"><span class="lbl">Durasi</span><span class="val"><?php echo htmlspecialchars($d['durasi'] ?? '-'); ?><?php echo ($d['pakai_playbox'] ?? 0) ? ' + Playbox' : ''; ?></span></div>
    <?php if ($d['tgl_ambil'] ?? ''): ?><div class="detail-row"><span class="lbl">Rencana Ambil</span><span class="val" style="color:#fbbf24;"><?php echo date('d/m/Y', strtotime($d['tgl_ambil'])); ?></span></div><?php endif; ?>
    <?php if (in_array($st, ['Disetujui', 'Selesai']) && $d['harga']): ?><div class="detail-row"><span class="lbl">Biaya</span><span class="val" style="color:#34d399;">Rp <?php echo number_format($d['harga'], 0, ',', '.'); ?></span></div><?php endif; ?>

    <?php if (in_array($st, ['Pending', 'Disetujui'])):
      $wa_admin = '6285847831078';
      $pesan    = urlencode("Halo Violet PlayStation, saya *{$d['nama_penyewa']}* ingin menanyakan status pengajuan sewa *{$d['nama_unit']}*. Terima kasih.");
    ?>
    <div style="padding:1.25rem 1.5rem;">
      <a href="https://wa.me/<?php echo $wa_admin; ?>?text=<?php echo $pesan; ?>" target="_blank" rel="noopener noreferrer" class="btn-wa">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        Hubungi Admin
      </a>
    </div>
    <?php endif; ?>
  </div>
<?php endwhile; endif;
    } // end else nomor valid
} // end if isset wa
?>
</div>
</body></html>
