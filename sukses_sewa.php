<?php
require_once 'config/koneksi.php';

$p = $_SESSION['last_pengajuan'] ?? null;
if (!$p) { header("Location: index.php"); exit(); }
unset($_SESSION['last_pengajuan']);

$kat       = $p['kategori']  ?? 'PS4';
$bc        = $kat==='PS5' ? '#60a5fa' : ($kat==='Nintendo' ? '#f87171' : '#c084fc');
$tgl_fmt   = !empty($p['tgl_ambil']) ? date('l, d F Y', strtotime($p['tgl_ambil'])) : '-';
$harga_fmt = 'Rp ' . number_format($p['harga'] ?? 0, 0, ',', '.');
$is_promo  = $p['is_promo']  ?? false;
$hari_bayar= $p['hari_bayar'] ?? 1;
$hari_dapat= $p['hari_dapat'] ?? $hari_bayar;
?>
<!DOCTYPE html><html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <meta name="description" content="Pengajuan sewa berhasil dikirim Violet PlayStation Jagakarsa.">
  <title>Pengajuan Berhasil Violet PlayStation</title>
  <link rel="icon" type="image/jpeg" href="assets/images/logo-violet.jpeg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="assets/css/violet.css">
  <script src="assets/app.js" defer></script>
</head>
<body>
<?php include_once "config/svg_sprite.php"; ?>
<nav class="v-navbar">
  <div class="container nav-container">
    <a href="index.php" class="brand">
      <img src="assets/images/logo-violet.jpeg" alt="Violet PlayStation">
      VIOLET <span class="neon">PLAYSTATION</span>
    </a>
  </div>
</nav>

<div class="sukses-wrap">
  <div class="sukses-card">
    <div class="check-icon">✓</div>
    <div class="sukses-title">Pengajuan Terkirim!</div>
    <p class="sukses-sub">Pengajuan sewa kamu sudah kami terima. Kami akan menghubungi kamu via WhatsApp untuk konfirmasi.</p>

    <div class="detail-box">
      <div class="d-row">
        <span class="lbl">Nama</span>
        <span class="val"><?php echo htmlspecialchars($p['nama']); ?></span>
      </div>
      <div class="d-row">
        <span class="lbl">Unit</span>
        <span class="val">
          <?php echo htmlspecialchars($p['unit']); ?>
          <span style="display:inline-block;font-size:.7rem;padding:.1rem .4rem;border-radius:4px;margin-left:.4rem;font-family:var(--font-ui);font-weight:700;letter-spacing:1px;background:rgba(255,255,255,.07);color:<?php echo $bc; ?>;"><?php echo $kat; ?></span>
        </span>
      </div>
      <div class="d-row">
        <span class="lbl">Durasi Sewa</span>
        <span class="val">
          <?php echo htmlspecialchars($p['durasi']); ?>
          <?php if($p['playbox'] ?? 0): ?><span style="color:var(--c-green);font-size:.8rem;"> + Playbox</span><?php endif; ?>
          <?php if($is_promo): ?>
          <span style="display:block;font-size:.75rem;color:var(--c-yellow);font-family:var(--font-ui);margin-top:.2rem;">
            🎁 Bayar <?php echo $hari_bayar; ?> hari, dapat <?php echo $hari_dapat; ?> hari
          </span>
          <?php endif; ?>
        </span>
      </div>
      <div class="d-row">
        <span class="lbl">Rencana Ambil</span>
        <span class="val">
          <span style="color:var(--c-yellow);"><?php echo $tgl_fmt; ?></span>
          <?php if($is_promo): ?>
          <span style="display:inline-block;font-size:.72rem;background:rgba(251,191,36,.12);border:1px solid rgba(251,191,36,.3);color:var(--c-yellow);padding:.1rem .5rem;border-radius:20px;margin-left:.4rem;">🎉 Promo Weekday</span>
          <?php endif; ?>
        </span>
      </div>
      <div class="d-row">
        <span class="lbl">Estimasi Biaya</span>
        <span class="val" style="color:var(--c-green);font-size:1rem;">
          <?php echo $harga_fmt; ?>
          <?php if($is_promo): ?><span style="display:block;font-size:.72rem;color:var(--c-yellow);font-family:var(--font-ui);font-weight:700;margin-top:.15rem;">🎁 PROMO WEEKDAY</span><?php endif; ?>
        </span>
      </div>
    </div>

    <div class="info-yellow">
      💳 <strong style="color:var(--c-yellow);">Pembayaran dilakukan di lokasi</strong> saat kamu mengambil unit, setelah pengajuan disetujui admin.
    </div>

    <div class="cek-box">
      <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:1rem;">
        <svg width="16" height="16" style="color:var(--c-blue);flex-shrink:0;"><use href="#ico-search"/></svg>
        <div class="cek-title" style="margin-bottom:0;">Cara Cek Status Pengajuan</div>
      </div>
      <div class="cek-step">
        <span class="step-num">1</span>
        <div>Buka <strong style="color:var(--v-white);">violetplaystation.com</strong>, klik menu <strong style="color:var(--v-white);">Cek Status</strong> di bagian atas halaman.</div>
      </div>
      <div class="cek-step">
        <span class="step-num">2</span>
        <div>Masukkan nomor WhatsApp kamu:
          <div style="margin-top:.3rem;background:rgba(255,255,255,.05);border:1px solid var(--v-border);border-radius:6px;padding:.35rem .75rem;font-family:var(--font-ui);font-weight:700;color:var(--v-white);letter-spacing:.5px;display:inline-block;">
            <?php echo htmlspecialchars($p['wa']); ?>
          </div>
        </div>
      </div>
      <div class="cek-step">
        <span class="step-num">3</span>
        <div>Status pengajuanmu akan tampil:
          <div style="display:flex;align-items:center;gap:.5rem;margin-top:.4rem;flex-wrap:wrap;">
            <span style="background:rgba(251,191,36,.15);color:var(--c-yellow);border:1px solid rgba(251,191,36,.3);font-family:var(--font-ui);font-size:.78rem;font-weight:700;padding:.2rem .65rem;border-radius:20px;">Pending</span>
            <span style="color:var(--v-muted);font-size:.8rem;">→</span>
            <span style="background:rgba(16,185,129,.15);color:var(--c-green);border:1px solid rgba(16,185,129,.3);font-family:var(--font-ui);font-size:.78rem;font-weight:700;padding:.2rem .65rem;border-radius:20px;">Disetujui</span>
            <span style="color:var(--v-muted);font-size:.8rem;">→</span>
            <span style="background:rgba(96,165,250,.15);color:var(--c-blue);border:1px solid rgba(96,165,250,.3);font-family:var(--font-ui);font-size:.78rem;font-weight:700;padding:.2rem .65rem;border-radius:20px;">Selesai</span>
          </div>
        </div>
      </div>
    </div>

    <a href="index.php" class="btn-back">← Kembali ke Halaman Utama</a>
  </div>
</div>
</body></html>