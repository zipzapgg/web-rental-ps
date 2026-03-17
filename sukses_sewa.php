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
  <meta name="description" content="Pengajuan sewa berhasil dikirim — Violet PlayStation Jagakarsa.">
  <title>Pengajuan Berhasil — Violet PlayStation</title>
  <link rel="icon" type="image/jpeg" href="assets/images/logo-violet.jpeg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="assets/css/violet.css">
  <style>
    body{background:var(--v-black);display:flex;flex-direction:column;min-height:100vh;}
    .sukses-wrap{flex:1;display:flex;align-items:center;justify-content:center;padding:2rem 1.5rem;}
    .sukses-card{background:var(--v-card);border:1px solid var(--v-border);border-radius:24px;padding:3rem 2.5rem;max-width:520px;width:100%;text-align:center;animation:fadeUp .6s ease both;position:relative;overflow:hidden;}
    .sukses-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,#10b981,#34d399);}
    .check-icon{width:72px;height:72px;background:rgba(16,185,129,.12);border:2px solid rgba(16,185,129,.3);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 1.5rem;font-size:2rem;animation:pulseGlow 2s ease-in-out infinite;}
    .sukses-title{font-family:var(--font-display);font-size:2rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;color:#34d399;margin-bottom:.5rem;}
    .sukses-sub{color:var(--v-muted);font-size:.9rem;margin-bottom:2rem;}
    .detail-box{background:rgba(255,255,255,.03);border:1px solid var(--v-border);border-radius:14px;padding:1.5rem;text-align:left;margin-bottom:1.5rem;}
    .d-row{display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;padding:.55rem 0;border-bottom:1px solid rgba(255,255,255,.04);font-family:var(--font-ui);font-size:.88rem;}
    .d-row:last-child{border-bottom:none;}
    .d-row .lbl{color:var(--v-muted);flex-shrink:0;}
    .d-row .val{color:var(--v-white);font-weight:600;text-align:right;}
    .info-yellow{background:rgba(251,191,36,.06);border:1px solid rgba(251,191,36,.2);border-radius:10px;padding:.85rem 1.1rem;margin-bottom:1.5rem;font-size:.83rem;color:#fbbf24;font-family:var(--font-ui);line-height:1.6;text-align:left;}
    .cek-box{background:rgba(96,165,250,.06);border:1px solid rgba(96,165,250,.2);border-radius:14px;padding:1.25rem 1.5rem;text-align:left;margin-bottom:1rem;}
    .cek-title{font-family:var(--font-ui);font-size:.8rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#60a5fa;margin-bottom:.85rem;}
    .cek-step{display:flex;gap:.75rem;align-items:flex-start;font-size:.85rem;color:var(--v-muted);font-family:var(--font-body);line-height:1.5;margin-bottom:.6rem;}
    .cek-step:last-child{margin-bottom:0;}
    .step-num{background:rgba(96,165,250,.15);color:#60a5fa;font-family:var(--font-ui);font-weight:800;font-size:.75rem;padding:.1rem .5rem;border-radius:4px;flex-shrink:0;margin-top:.1rem;}
    .btn-back{display:inline-flex;align-items:center;justify-content:center;text-decoration:none;padding:.75rem;border-radius:10px;color:var(--v-muted);font-family:var(--font-ui);font-size:.85rem;letter-spacing:1px;text-transform:uppercase;border:1px solid var(--v-border);width:100%;transition:color .2s,border-color .2s;}
    .btn-back:hover{color:var(--v-lavender);border-color:var(--v-violet);}
    @media(max-width:480px){.sukses-card{padding:1.75rem 1.25rem;}}
  </style>
</head>
<body>
<nav class="v-navbar">
  <div class="container" style="display:flex;justify-content:space-between;align-items:center;padding:0 1.25rem;">
    <a href="index.php" class="brand">
      <img src="assets/images/logo-violet.jpeg" alt="Violet PlayStation">
      VIOLET <span class="neon" style="margin-left:.3rem;">PLAYSTATION</span>
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
          <?php if($p['playbox'] ?? 0): ?><span style="color:#34d399;font-size:.8rem;"> + Playbox</span><?php endif; ?>
          <?php if($is_promo): ?>
          <span style="display:block;font-size:.75rem;color:#fbbf24;font-family:var(--font-ui);margin-top:.2rem;">
            🎁 Bayar <?php echo $hari_bayar; ?> hari, dapat <?php echo $hari_dapat; ?> hari
          </span>
          <?php endif; ?>
        </span>
      </div>
      <div class="d-row">
        <span class="lbl">Rencana Ambil</span>
        <span class="val">
          <span style="color:#fbbf24;"><?php echo $tgl_fmt; ?></span>
          <?php if($is_promo): ?>
          <span style="display:inline-block;font-size:.72rem;background:rgba(251,191,36,.12);border:1px solid rgba(251,191,36,.3);color:#fbbf24;padding:.1rem .5rem;border-radius:20px;margin-left:.4rem;">🎉 Promo Weekday</span>
          <?php endif; ?>
        </span>
      </div>
      <div class="d-row">
        <span class="lbl">Estimasi Biaya</span>
        <span class="val" style="color:#34d399;font-size:1rem;">
          <?php echo $harga_fmt; ?>
          <?php if($is_promo): ?><span style="display:block;font-size:.72rem;color:#fbbf24;font-family:var(--font-ui);font-weight:700;margin-top:.15rem;">🎁 PROMO WEEKDAY</span><?php endif; ?>
        </span>
      </div>
    </div>

    <div class="info-yellow">
      💳 <strong style="color:#fbbf24;">Pembayaran dilakukan di lokasi</strong> saat kamu mengambil unit, setelah pengajuan disetujui admin.
    </div>

    <div class="cek-box">
      <div class="cek-title">🔍 Cara Cek Status Pengajuan</div>
      <div class="cek-step">
        <span class="step-num">1</span>
        Buka <strong style="color:var(--v-white);">violetplaystation.com</strong> → klik <strong style="color:var(--v-white);">Cek Status</strong> di menu atas
      </div>
      <div class="cek-step">
        <span class="step-num">2</span>
        Masukkan nomor WA kamu: <strong style="color:var(--v-white);"><?php echo htmlspecialchars($p['wa']); ?></strong>
      </div>
      <div class="cek-step">
        <span class="step-num">3</span>
        Status: <strong style="color:#fbbf24;">Pending</strong> → <strong style="color:#34d399;">Disetujui</strong> → <strong style="color:#60a5fa;">Selesai</strong>
      </div>
    </div>

    <a href="index.php" class="btn-back">← Kembali ke Halaman Utama</a>
  </div>
</div>
</body></html>