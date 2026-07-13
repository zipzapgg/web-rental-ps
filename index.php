<?php include 'config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Violet PlayStation Sewa PS & Playbox Jagakarsa</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="icon" type="image/jpeg" href="assets/images/logo-violet.jpeg">
  <meta name="description" content="Sewa PS4, PS5, Nintendo Switch & Playbox di Jagakarsa, Jakarta Selatan. Bawa pulang harian, harga terjangkau, promo weekday!">
  <meta property="og:title" content="Violet PlayStation Sewa PS & Playbox Jagakarsa">
  <meta property="og:description" content="Sewa PS4, PS5, Nintendo Switch & Playbox harian. Booking H-1 via WA. Promo weekday: sewa 2 hari gratis 1 hari!">
  <meta property="og:image" content="https://violetplaystation.com/assets/images/logo-violet.jpeg">
  <meta property="og:type" content="website">
  <meta name="theme-color" content="#8B5CF6">
  <link rel="stylesheet" href="assets/css/violet.css">
  <script src="assets/app.js" defer></script>
</head>
<body>

<?php include_once "config/svg_sprite.php"; ?>

<!-- NAVBAR -->
<nav class="v-navbar">
  <div class="container nav-container">
    <a href="index.php" class="brand">
      <img src="assets/images/logo-violet.jpeg" alt="Violet PlayStation">
    </a>
    <div class="nav-links">
      <a href="#harga"><svg width="16" height="16" aria-hidden="true"><use href="#ico-tag"/></svg><span class="nav-label">Harga</span></a>
      <a href="#unit"><svg width="16" height="16" aria-hidden="true"><use href="#ico-gamepad"/></svg><span class="nav-label">Unit</span></a>
      <a href="#games"><svg width="16" height="16" aria-hidden="true"><use href="#ico-monitor"/></svg><span class="nav-label">Game</span></a>
      <a href="#lokasi"><svg width="16" height="16" aria-hidden="true"><use href="#ico-pin"/></svg><span class="nav-label">Lokasi</span></a>
      <a href="cek_status.php" class="nav-link"><svg width="14" height="14"><use href="#ico-search"/></svg><span class="nav-label">Cek Status</span></a>
      <a href="sewa.php" class="nav-btn-sewa"><svg width="14" height="14" aria-hidden="true"><use href="#ico-calendar"/></svg><span class="nav-label">Sewa Unit</span></a>
    </div>
    <button class="nav-hamburger" id="hamburger" aria-label="Menu" onclick="toggleDrawer()">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>
<div class="nav-drawer" id="navDrawer">
  <a href="#harga" onclick="closeDrawer()"><svg width="22" height="22" aria-hidden="true"><use href="#ico-tag"/></svg>Harga</a>
  <a href="#unit" onclick="closeDrawer()"><svg width="22" height="22" aria-hidden="true"><use href="#ico-gamepad"/></svg>Unit</a>
  <a href="#games" onclick="closeDrawer()"><svg width="22" height="22" aria-hidden="true"><use href="#ico-monitor"/></svg>Game</a>
  <a href="#lokasi" onclick="closeDrawer()"><svg width="22" height="22" aria-hidden="true"><use href="#ico-pin"/></svg>Lokasi</a>
  <a href="#faq" onclick="closeDrawer()"><svg width="22" height="22" aria-hidden="true"><use href="#ico-shield"/></svg>FAQ</a>
  <a href="cek_status.php" onclick="closeDrawer()"><svg width="22" height="22" aria-hidden="true"><use href="#ico-calendar"/></svg>Cek Status</a>
  <div class="drawer-cta">
    <a href="sewa.php" class="btn-violet" style="display:inline-flex;align-items:center;justify-content:center;gap:.5rem;width:100%;text-decoration:none;font-size:1.1rem;padding:1rem;border-radius:10px;"><svg width="18" height="18"><use href="#ico-gamepad"/></svg><span>Sewa Unit</span></a>
  </div>
</div>

<!-- HERO -->
<section class="hero">
  <div id="cursor-glow"></div>
  <div class="hero-bg"></div>
  <div class="hero-grid-lines"></div>
  <div class="floating-symbols" aria-hidden="true">
    <div class="symbol symbol-triangle">▲</div>
    <div class="symbol symbol-circle">●</div>
    <div class="symbol symbol-cross">✕</div>
    <div class="symbol symbol-square">■</div>
  </div>
  <div class="container hero-container">
    <div class="hero-content col-half animate-fade-up">
      <div class="hero-eyebrow">
        <svg width="14" height="14" aria-hidden="true" style="opacity:.8"><use href="#ico-pin"/></svg>
        <span>Jagakarsa · Jakarta Selatan</span>
      </div>
      <h1 class="hero-title">SEWA PS<br><span class="line2">BAWA PULANG</span></h1>
      <p class="hero-sub">PS4, PS5, Nintendo Switch & Playbox sewa harian, bawa ke rumah. Booking H-1 via WhatsApp, jaminan KTP & STNK.</p>
      <div class="hero-cta">
        <a href="sewa.php" class="btn-violet">
          <svg width="18" height="18"><use href="#ico-gamepad"/></svg>
          <span>Sewa Sekarang</span>
        </a>
        <a href="#harga" class="btn-ghost">
          <span>Lihat Harga</span>
        </a>
      </div>
    </div>
    <div class="hero-logo-wrap col-half">
      <div class="tilt-3d hero-logo-only">
        <img src="assets/images/logo-violet.jpeg" alt="Violet PlayStation" class="hero-logo-img-solo">
      </div>
    </div>
  </div>
</section>

<!-- STATS -->
<div class="stats-bar">
  <div class="container">
    <div class="row" style="justify-content:center;gap:3rem;">
      <?php
      $total_unit    = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as c FROM units"))['c'];
      $unit_tersedia = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as c FROM units WHERE status='Tersedia'"))['c'];
      $total_game    = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as c FROM games"))['c'];
      ?>
      <div class="stat-item"><div class="stat-num"><?php echo $total_unit; ?></div><div class="stat-label">Total Unit</div></div>
      <div class="stat-item"><div class="stat-num"><?php echo $unit_tersedia; ?></div><div class="stat-label">Tersedia</div></div>
      <div class="stat-item"><div class="stat-num"><?php echo $total_game; ?>+</div><div class="stat-label">Koleksi Game</div></div>
      <div class="stat-item"><div class="stat-num">H-1</div><div class="stat-label">Booking via WA</div></div>
    </div>
  </div>
</div>

<!-- HARGA -->
<section class="price-section" id="harga">
  <div class="container">
    <div class="section-title">DAFTAR <span class="neon">HARGA</span></div>
    <div class="v-divider"></div>
    <div class="price-tab-nav">
      <button class="price-tab-btn active" onclick="switchPriceTab('sewa',this)">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
        <span>Sewa Bawa Pulang</span>
      </button>
      <button class="price-tab-btn" onclick="switchPriceTab('tempat',this)">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        <span>Main di Tempat</span>
      </button>
      <button class="price-tab-btn" onclick="switchPriceTab('playbox',this)">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        <span>Playbox</span>
      </button>
    </div>

    <!-- Sewa Panel -->
    <div class="price-tab-panel active" id="panel-sewa">
      <div class="promo-banner">
        <svg width="28" height="28" style="flex-shrink:0;color:#fbbf24"><use href="#ico-gift"/></svg>
        <div>
          <div class="promo-banner-text">SPECIAL PROMO WEEKDAY Senin s/d Kamis</div>
          <div class="promo-banner-sub">Sewa 2 hari gratis 1 hari &nbsp;·&nbsp; Sewa 3 hari gratis 2 hari</div>
        </div>
      </div>
      <div class="row" style="margin-bottom:1.25rem;">
        <div class="col-half"><div class="price-card ps4">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;">
            <span class="v-badge v-badge-ps4" style="display:inline-block;">Console</span>
            <img src="assets/images/ps4.png" alt="PS4 Logo" style="height:14px;width:auto;opacity:0.8;filter:drop-shadow(0 0 8px rgba(139,92,246,0.3));">
          </div>
          <div class="price-card-title">PlayStation 4</div><div class="price-tag">Sewa Bawa Pulang · Per Hari</div>
          <div class="price-row"><span class="label">1 Hari</span><span class="price">Rp 100.000</span></div>
          <div class="price-row"><span class="label">2 Hari <span class="free-badge">Free 1 hari</span></span><span class="price">Rp 200.000</span></div>
          <div class="price-row"><span class="label">3 Hari <span class="free-badge">Free 2 hari</span></span><span class="price">Rp 300.000</span></div>
        </div></div>
        <div class="col-half"><div class="price-card ps5">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;">
            <span class="v-badge v-badge-ps5" style="display:inline-block;">Next-Gen</span>
            <img src="assets/images/ps5.png" alt="PS5 Logo" style="height:14px;width:auto;opacity:0.8;filter:drop-shadow(0 0 8px rgba(139,92,246,0.3));">
          </div>
          <div class="price-card-title">PlayStation 5</div><div class="price-tag">Sewa Bawa Pulang · Per Hari</div>
          <div class="price-row"><span class="label">1 Hari</span><span class="price">Rp 195.000</span></div>
          <div class="price-row"><span class="label">2 Hari <span class="free-badge">Free 1 hari</span></span><span class="price">Rp 390.000</span></div>
          <div class="price-row"><span class="label">3 Hari <span class="free-badge">Free 2 hari</span></span><span class="price">Rp 585.000</span></div>
          <div class="price-note blue">ℹ️ Unit PS5 yang disewa adalah unit yang ada di tempat hubungi WA dulu untuk konfirmasi</div>
        </div></div>
      </div>
      <div class="syarat-box">
        <h6>⚠ Syarat & Ketentuan Sewa</h6>
        <ul class="syarat-list">
          <li><strong>Booking minimal H-1</strong> via WhatsApp ke 0858-4783-1078</li>
          <li>KTP dan STNK <strong>Jagakarsa, alamat wajib sama</strong> sebagai jaminan</li>
          <li><strong>Kerusakan & kehilangan</strong> tanggung jawab penyewa</li>
          <li>Jika <strong>segel rusak</strong> dianggap membeli</li>
          <li><strong>Terlambat</strong> denda Rp 10.000/jam · lebih dari 6 jam dianggap sewa harian</li>
          <li><strong>Dilarang</strong> memindahkan unit ke pihak lain</li>
        </ul>
      </div>
    </div>

    <!-- Tempat Panel -->
    <div class="price-tab-panel" id="panel-tempat">
      <p style="color:var(--v-muted);margin-bottom:2rem;font-size:.9rem;">Harga berlaku untuk sesi bermain langsung di toko kami</p>
      <div class="row">
        <div class="col-half"><div class="price-card ps4">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;">
            <span class="v-badge v-badge-ps4" style="display:inline-block;">Console</span>
            <img src="assets/images/ps4.png" alt="PS4 Logo" style="height:14px;width:auto;opacity:0.8;filter:drop-shadow(0 0 8px rgba(139,92,246,0.3));">
          </div>
          <div class="price-card-title">PlayStation 4</div><div class="price-tag">Main di Tempat · Per Sesi</div>
          <div class="price-row"><span class="label">1 Jam</span><span class="price">Rp 8.000</span></div>
          <div class="price-row"><span class="label">2 Jam</span><span class="price">Rp 15.000</span></div>
          <div class="price-row"><span class="label">3 Jam</span><span class="price">Rp 20.000</span></div>
          <div class="price-row"><span class="label">5 Jam</span><span class="price">Rp 35.000</span></div>
          <div class="price-note">⚠ Waktu tidak dapat disimpan / dipause</div>
        </div></div>
        <div class="col-half"><div class="price-card ps5">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;">
            <span class="v-badge v-badge-ps5" style="display:inline-block;">Next-Gen</span>
            <img src="assets/images/ps5.png" alt="PS5 Logo" style="height:14px;width:auto;opacity:0.8;filter:drop-shadow(0 0 8px rgba(139,92,246,0.3));">
          </div>
          <div class="price-card-title">PlayStation 5</div><div class="price-tag">Main di Tempat · Per Sesi</div>
          <div class="price-row"><span class="label">1 Jam</span><span class="price">Rp 15.000</span></div>
          <div class="price-row"><span class="label">2 Jam</span><span class="price">Rp 28.000</span></div>
          <div class="price-row"><span class="label">3 Jam</span><span class="price">Rp 42.000</span></div>
          <div class="price-row"><span class="label">5 Jam</span><span class="price">Rp 57.000</span></div>
          <div class="price-note blue">⚠ Waktu tidak dapat disimpan / dipause</div>
        </div></div>
      </div>
    </div>

    <!-- Playbox Panel -->
    <div class="price-tab-panel" id="panel-playbox">
      <div style="background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.2);border-radius:14px;padding:1.5rem 2rem;margin-bottom:2rem;display:flex;gap:1.5rem;align-items:flex-start;flex-wrap:wrap;">
        <div style="font-size:2.2rem;flex-shrink:0;"><svg width="36" height="36"><use href="#ico-case"/></svg></div>
        <div>
          <div style="font-family:var(--font-display);font-size:1.2rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#34d399;margin-bottom:.5rem;">Apa itu Playbox?</div>
          <p style="font-size:.88rem;color:var(--v-muted);line-height:1.7;max-width:560px;">Playbox adalah <strong style="color:#C4B5D4;">koper gaming all-in-one</strong> yang berisi monitor, speaker, dan PlayStation. Tinggal buka koper, colok listrik langsung bisa main.</p>
          <div style="display:flex;gap:.6rem;flex-wrap:wrap;margin-top:1rem;">
            <span style="font-family:var(--font-ui);font-size:.75rem;letter-spacing:1px;background:rgba(16,185,129,.12);color:#34d399;border:1px solid rgba(16,185,129,.25);padding:.2rem .75rem;border-radius:20px;">Monitor built-in</span>
            <span style="font-family:var(--font-ui);font-size:.75rem;letter-spacing:1px;background:rgba(16,185,129,.12);color:#34d399;border:1px solid rgba(16,185,129,.25);padding:.2rem .75rem;border-radius:20px;">🔊 Speaker built-in</span>
            <span style="font-family:var(--font-ui);font-size:.75rem;letter-spacing:1px;background:rgba(16,185,129,.12);color:#34d399;border:1px solid rgba(16,185,129,.25);padding:.2rem .75rem;border-radius:20px;">Khusus PS4</span>
            <span style="font-family:var(--font-ui);font-size:.75rem;letter-spacing:1px;background:rgba(16,185,129,.12);color:#34d399;border:1px solid rgba(16,185,129,.25);padding:.2rem .75rem;border-radius:20px;">⚡ Plug & Play</span>
          </div>
        </div>
      </div>
      <div class="row" style="margin-bottom:1.25rem;">
        <div class="col-half"><div class="price-card playbox">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;">
            <div style="display:flex;gap:.5rem;">
              <span class="v-badge" style="background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);">Playbox</span>
              <span class="v-badge v-badge-ps4">PS4</span>
            </div>
            <img src="assets/images/ps4.png" alt="PS4 Logo" style="height:14px;width:auto;opacity:0.8;filter:drop-shadow(0 0 8px rgba(139,92,246,0.3));">
          </div>
          <div class="price-card-title">Playbox PS4</div>
          <div class="price-tag">Sewa Bawa Pulang · Per 24 Jam</div>
          <div class="price-row"><span class="label">1 Hari</span><span class="price">Rp 130.000</span></div>
          <div class="price-row"><span class="label">2 Hari <span class="free-badge">Free 1 hari</span></span><span class="price">Rp 260.000</span></div>
          <div class="price-row"><span class="label">3 Hari <span class="free-badge">Free 2 hari</span></span><span class="price">Rp 390.000</span></div>
          <div class="price-note green">Monitor + speaker + 2 controller included</div>
        </div></div>
     <div class="col-half" style="display:none;"><div class="price-card ps5">
          <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:.75rem;">
            <div style="display:flex;gap:.5rem;">
              <span class="v-badge" style="background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);">Playbox</span>
              <span class="v-badge v-badge-ps5">PS5</span>
            </div>
            <img src="assets/images/ps5.png" alt="PS5 Logo" style="height:14px;width:auto;opacity:0.8;filter:drop-shadow(0 0 8px rgba(139,92,246,0.3));">
          </div>
          <div class="price-card-title">Playbox PS5</div>
          <div class="price-tag">Sewa Bawa Pulang · Per 24 Jam</div>
          <div class="price-row"><span class="label">1 Hari</span><span class="price">Rp 225.000</span></div>
          <div class="price-row"><span class="label">2 Hari <span class="free-badge">Free 1 hari</span></span><span class="price">Rp 450.000</span></div>
          <div class="price-row"><span class="label">3 Hari <span class="free-badge">Free 2 hari</span></span><span class="price">Rp 675.000</span></div>
          <div class="price-note blue">Monitor + speaker + 2 controller included</div>
        </div></div>
      </div>
      <div class="syarat-box">
        <h6>⚠ Syarat & Ketentuan Sewa Playbox</h6>
        <ul class="syarat-list">
          <li><strong>Pengambilan wajib 2 orang</strong> jika menggunakan motor</li>
          <li>KTP & STNK aktif <strong>sekitar Jagakarsa</strong> ditahan selama sewa</li>
          <li>Nomor HP <strong>dicek via GetContact</strong> minimal 50 tag</li>
          <li><strong>Terlambat</strong> Rp 10.000/jam · lebih dari 6 jam = sewa 1 hari lagi</li>
          <li>PS4 dan Nintendo <strong>khusus offline</strong> denda Rp 4.500.000 jika dikonek ke internet</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- UNIT -->
<section class="units-section" id="unit">
  <div class="container">
    <div class="section-title">CEK <span class="neon">UNIT</span></div>
    <div class="v-divider"></div>
    <p style="color:var(--v-muted);margin-bottom:2rem;font-size:.9rem;">Klik unit untuk melihat game yang tersedia</p>

    <div class="unit-tabs">
      <button class="unit-tab active" onclick="switchUnitTab('sewa',this)">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>
        <span>Sewa Bawa Pulang</span>
      </button>
      <button class="unit-tab" onclick="switchUnitTab('tempat',this)">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="tab-icon"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
        <span>Main di Tempat</span>
      </button>
    </div>

    <!-- Panel Sewa -->
    <?php
    $q_sewa = mysqli_query($koneksi, "SELECT u.*, COUNT(ug.id_game) as jumlah_game FROM units u LEFT JOIN unit_games ug ON u.id_unit=ug.id_unit WHERE u.tipe_layanan='Sewa Luar' OR (u.tipe_layanan='Main di Tempat' AND u.kategori='PS5') GROUP BY u.id_unit ORDER BY u.kategori, u.nama_unit ASC");
    $arr_sewa = [];
    while ($u = mysqli_fetch_assoc($q_sewa)) $arr_sewa[] = $u;
    $total_sewa = count($arr_sewa);
    $preview_count = 3; // tampilkan 3 unit dulu
    $hidden_sewa   = max(0, $total_sewa - $preview_count);
    ?>
    <div class="units-panel active" id="upanel-sewa">
      <div class="ps5-note"><svg width="16" height="16" style="flex-shrink:0;margin-top:.1rem;"><use href="#ico-warn"/></svg><span>Unit PS5 di bawah adalah unit yang biasa dipakai main di tempat. <strong style="color:#93c5fd;">Hubungi WA dulu sebelum booking</strong> untuk pastikan unit tidak sedang terpakai.</span></div>

      <!-- Layout: grid 3 unit + tombol lihat semua di kanan -->
      <div class="units-with-toggle">
        <div class="units-grid-wrap">
          <div class="units-grid" id="grid-sewa-pub">
            <?php foreach ($arr_sewa as $i => $u):
              $kat     = $u['kategori'];
              $price_str = $kat === 'PS5' ? 'Rp 195.000 / Hari' : ($kat === 'Nintendo' ? 'Rp 75.000 / Hari' : 'Rp 100.000 / Hari');
              $is_unavailable = ($u['status'] === 'Disewa' || $u['status'] === 'Maintenance');
              $hidden  = $i >= $preview_count;
            ?>
            <div class="unit-card tilt-3d <?php echo $is_unavailable && $u['status'] === 'Maintenance' ? 'in-maintenance' : ''; ?>"
                 id="sewa-unit-<?php echo $i; ?>"
                 style="<?php echo $hidden ? 'display:none;' : ''; ?>">
              
              <!-- Huge Watermark -->
              <div class="unit-watermark">PLAYSTATION</div>
              
              <!-- Logo Area -->
              <div class="unit-logo-area">
                <div class="unit-logo-glow-ring"></div>
                <?php if ($kat === 'PS5'): ?>
                  <img src="assets/images/ps5.png" alt="PS5 Logo" class="unit-ps-logo">
                <?php else: ?>
                  <img src="assets/images/ps4.png" alt="PS4 Logo" class="unit-ps-logo">
                <?php endif; ?>
              </div>

              <!-- Content Area -->
              <div class="unit-card-content">
                <div class="unit-card-header">
                  <div class="unit-card-title"><?php echo htmlspecialchars($u['nama_unit']); ?></div>
                  <div class="unit-card-subtitle"><?php echo $kat === 'PS5' ? 'PlayStation 5' : ($kat === 'Nintendo' ? 'Nintendo Switch' : 'PlayStation 4'); ?></div>
                </div>
                
                <!-- Status Badge -->
                <div class="unit-status-row">
                  <?php if ($u['status'] === 'Maintenance'): ?>
                    <span class="status-badge maint">Maintenance</span>
                  <?php elseif ($u['status'] === 'Disewa'): ?>
                    <span class="status-badge in-use">In Use</span>
                  <?php else: ?>
                    <span class="status-badge avail">Available</span>
                  <?php endif; ?>
                </div>

                <!-- Specs List -->
                <div class="unit-specs-list">
                  <?php if ($kat === 'PS5'): ?>
                    <span>✦ 4K HDR</span>
                    <span>✦ Ultra SSD</span>
                    <span>✦ Ray Tracing</span>
                  <?php elseif ($kat === 'Nintendo'): ?>
                    <span>✦ OLED Screen</span>
                    <span>✦ Joy-Con™</span>
                    <span>✦ TV Mode</span>
                  <?php else: ?>
                    <span>✦ Full HD 1080p</span>
                    <span>✦ 1TB Storage</span>
                    <span>✦ DualShock®4</span>
                  <?php endif; ?>
                </div>

                <!-- Price -->
                <div class="unit-card-price">
                  <?php echo $price_str; ?>
                </div>

                <!-- Action Buttons -->
                <div class="unit-card-actions">
                  <?php if ($u['status'] === 'Maintenance'): ?>
                    <button class="btn-card-primary disabled" disabled>Booking</button>
                  <?php else: ?>
                    <a href="sewa.php?unit=<?php echo $u['id_unit']; ?>" class="btn-card-primary">Booking</a>
                  <?php endif; ?>
                  <button class="btn-card-secondary" onclick="bukaUnit(<?php echo $u['id_unit']; ?>,'<?php echo htmlspecialchars(addslashes($u['nama_unit'])); ?>','<?php echo $kat; ?>','Sewa Luar')">Detail</button>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <?php if ($hidden_sewa > 0): ?>
        <!-- Tombol lihat semua di samping grid -->
        <div class="units-toggle-col">
          <button id="btn-toggle-sewa" class="btn-lihat-semua" onclick="toggleSemuaUnit('sewa')" aria-expanded="false" aria-label="Lihat semua unit sewa" title="Lihat <?php echo $hidden_sewa; ?> unit lainnya">
            <svg class="toggle-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Panel Tempat -->
    <?php
    $q_tempat = mysqli_query($koneksi, "SELECT u.*, COUNT(ug.id_game) as jumlah_game FROM units u LEFT JOIN unit_games ug ON u.id_unit=ug.id_unit WHERE u.tipe_layanan='Main di Tempat' GROUP BY u.id_unit ORDER BY u.kategori, u.nama_unit ASC");
    $arr_tempat = [];
    while ($u = mysqli_fetch_assoc($q_tempat)) $arr_tempat[] = $u;
    $total_tempat   = count($arr_tempat);
    $hidden_tempat  = max(0, $total_tempat - $preview_count);
    ?>
    <div class="units-panel" id="upanel-tempat">
      <div class="units-with-toggle">
        <div class="units-grid-wrap">
          <div class="units-grid" id="grid-tempat-pub">
            <?php foreach ($arr_tempat as $i => $u):
              $kat    = $u['kategori'];
              $price_str = $kat === 'PS5' ? 'Rp 15.000 / Jam' : ($kat === 'Nintendo' ? 'Rp 10.000 / Jam' : 'Rp 8.000 / Jam');
              $is_unavailable = ($u['status'] === 'Disewa' || $u['status'] === 'Maintenance');
              $hidden = $i >= $preview_count;
            ?>
            <div class="unit-card tilt-3d <?php echo $is_unavailable && $u['status'] === 'Maintenance' ? 'in-maintenance' : ''; ?>"
                 id="tempat-unit-<?php echo $i; ?>"
                 style="<?php echo $hidden ? 'display:none;' : ''; ?>">
              
              <!-- Huge Watermark -->
              <div class="unit-watermark">PLAYSTATION</div>
              
              <!-- Logo Area -->
              <div class="unit-logo-area">
                <div class="unit-logo-glow-ring"></div>
                <?php if ($kat === 'PS5'): ?>
                  <img src="assets/images/ps5.png" alt="PS5 Logo" class="unit-ps-logo">
                <?php else: ?>
                  <img src="assets/images/ps4.png" alt="PS4 Logo" class="unit-ps-logo">
                <?php endif; ?>
              </div>

              <!-- Content Area -->
              <div class="unit-card-content">
                <div class="unit-card-header">
                  <div class="unit-card-title"><?php echo htmlspecialchars($u['nama_unit']); ?></div>
                  <div class="unit-card-subtitle"><?php echo $kat === 'PS5' ? 'PlayStation 5' : ($kat === 'Nintendo' ? 'Nintendo Switch' : 'PlayStation 4'); ?></div>
                </div>
                
                <!-- Status Badge -->
                <div class="unit-status-row">
                  <?php if ($u['status'] === 'Maintenance'): ?>
                    <span class="status-badge maint">Maintenance</span>
                  <?php elseif ($u['status'] === 'Disewa'): ?>
                    <span class="status-badge in-use">In Use</span>
                  <?php else: ?>
                    <span class="status-badge avail">Available</span>
                  <?php endif; ?>
                </div>

                <!-- Specs List -->
                <div class="unit-specs-list">
                  <?php if ($kat === 'PS5'): ?>
                    <span>✦ 4K HDR</span>
                    <span>✦ Ultra SSD</span>
                    <span>✦ Ray Tracing</span>
                  <?php elseif ($kat === 'Nintendo'): ?>
                    <span>✦ OLED Screen</span>
                    <span>✦ Joy-Con™</span>
                    <span>✦ TV Mode</span>
                  <?php else: ?>
                    <span>✦ Full HD 1080p</span>
                    <span>✦ 1TB Storage</span>
                    <span>✦ DualShock®4</span>
                  <?php endif; ?>
                </div>

                <!-- Price -->
                <div class="unit-card-price">
                  <?php echo $price_str; ?>
                </div>

                <!-- Action Buttons -->
                <div class="unit-card-actions">
                  <?php if ($u['status'] === 'Maintenance'): ?>
                    <button class="btn-card-primary disabled" disabled>Booking</button>
                  <?php else: ?>
                    <a href="https://wa.me/6285847831078?text=<?php echo urlencode("Halo Violet PlayStation, saya ingin booking unit " . $u['nama_unit'] . " (" . $kat . ") untuk main di tempat."); ?>" class="btn-card-primary" target="_blank" rel="noopener">Booking</a>
                  <?php endif; ?>
                  <button class="btn-card-secondary" onclick="bukaUnit(<?php echo $u['id_unit']; ?>,'<?php echo htmlspecialchars(addslashes($u['nama_unit'])); ?>','<?php echo $kat; ?>','Main di Tempat')">Detail</button>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <?php if ($hidden_tempat > 0): ?>
        <div class="units-toggle-col">
          <button id="btn-toggle-tempat" class="btn-lihat-semua" onclick="toggleSemuaUnit('tempat')" aria-expanded="false" aria-label="Lihat semua unit tempat" title="Lihat <?php echo $hidden_tempat; ?> unit lainnya">
            <svg class="toggle-chevron" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
          </button>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>

<!-- GAMES -->
<?php
// Initialize games data early at the top of games section
if (!function_exists('getGameMeta')) {
  function getGameMeta($title, $kat) {
    $title_lower = strtolower($title);
    $genre = 'Action / Adventure';
    $players = '1-2 Players';
    
    if (str_contains($title_lower, 'fc') || str_contains($title_lower, 'fifa') || str_contains($title_lower, 'pes') || str_contains($title_lower, 'efootball') || str_contains($title_lower, 'gran turismo') || str_contains($title_lower, 'motogp') || str_contains($title_lower, 'wwe')) {
      $genre = 'Sports / Racing';
      $players = '1-4 Players';
    } elseif (str_contains($title_lower, 'crash') || str_contains($title_lower, 'sonic') || str_contains($title_lower, 'mario') || str_contains($title_lower, 'sackboy')) {
      $genre = 'Platformer / Arcade';
      $players = '1-4 Players';
    } elseif (str_contains($title_lower, 'tekken') || str_contains($title_lower, 'mortal kombat') || str_contains($title_lower, 'street fighter') || str_contains($title_lower, 'naruto')) {
      $genre = 'Fighting / Action';
      $players = '1-2 Players';
    } elseif (str_contains($title_lower, 'god of war') || str_contains($title_lower, 'elden ring') || str_contains($title_lower, 'spiderman') || str_contains($title_lower, 'ghost of tsushima') || str_contains($title_lower, 'horizon') || str_contains($title_lower, 'gta') || str_contains($title_lower, 'last of us') || str_contains($title_lower, 'red dead')) {
      $genre = 'Action RPG / Open World';
      $players = '1 Player';
    }
    
    return ['genre' => $genre, 'players' => $players];
  }
}

$q_games = mysqli_query($koneksi, "SELECT id_game, judul_game, foto_game, kategori_game, genre_game, players_game FROM games ORDER BY judul_game ASC");
$arr_games = [];
while ($g = mysqli_fetch_assoc($q_games)) $arr_games[] = $g;
$total_games = count($arr_games);
$limit_games = 6;
?>
<section class="games-section" id="games">
  <div class="container">
    <!-- Section Header -->
    <div class="location-header">
      <div class="games-system-label">// DATA_LOAD_SYS_SEC</div>
      <h2 class="games-title">KOLEKSI <span class="games-gradient-text">GAME</span></h2>
      <div class="games-divider"></div>
      <p class="games-subtitle">Browse all available PlayStation games ready to play.</p>
    </div>

    <!-- Search Bar Wrapper -->
    <div class="games-search-wrap" style="margin-bottom: 1.25rem;">
      <input type="text" id="game-search" class="games-search-input" placeholder="Cari game favorit..." oninput="cariGame(this.value)">
      <svg class="games-search-icon" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    </div>

    <!-- Meta Filter Controls -->
    <div class="games-filter-container" style="margin-bottom: 2.25rem; display: flex; gap: 1rem; flex-wrap: wrap;">
      <!-- Genre Selector -->
      <div class="games-select-wrap">
        <select id="filter-genre" onchange="applyFilters()" class="games-select-input">
          <option value="ALL">Semua Genre</option>
          <?php
          $genres = [];
          foreach ($arr_games as $g) {
            $kat = $g['kategori_game'] ?? '';
            $fb = getGameMeta($g['judul_game'], $kat);
            $gen = !empty($g['genre_game']) ? $g['genre_game'] : $fb['genre'];
            if (!in_array($gen, $genres)) {
              $genres[] = $gen;
            }
          }
          sort($genres);
          foreach ($genres as $gen) {
            echo '<option value="' . htmlspecialchars($gen) . '">' . htmlspecialchars($gen) . '</option>';
          }
          ?>
        </select>
        <svg class="games-select-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
      </div>

      <!-- Players Selector -->
      <div class="games-select-wrap">
        <select id="filter-players" onchange="applyFilters()" class="games-select-input">
          <option value="ALL">Semua Jumlah Pemain</option>
          <?php
          $players_list = [];
          foreach ($arr_games as $g) {
            $kat = $g['kategori_game'] ?? '';
            $fb = getGameMeta($g['judul_game'], $kat);
            $pl = !empty($g['players_game']) ? $g['players_game'] : $fb['players'];
            if (!in_array($pl, $players_list)) {
              $players_list[] = $pl;
            }
          }
          sort($players_list);
          foreach ($players_list as $pl) {
            echo '<option value="' . htmlspecialchars($pl) . '">' . htmlspecialchars($pl) . '</option>';
          }
          ?>
        </select>
        <svg class="games-select-chevron" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
      </div>
    </div>

    <div id="search-result" style="display:none;background:var(--v-card);border:1px solid var(--v-border);border-radius:14px;padding:1.5rem;margin-bottom:1.5rem;animation:fadeUp .25s ease both;">
      <div style="font-family:var(--font-ui);font-size:.8rem;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);margin-bottom:1rem;">Hasil Pencarian: <strong id="search-keyword" style="color:var(--v-lavender);"></strong></div>
      <div id="search-list"></div>
    </div>

    <div class="row" style="margin-top:.5rem;" id="games-grid">
      <?php foreach ($arr_games as $i => $g):
        $kat    = $g['kategori_game'] ?? '';
        $bc     = $kat === 'PS5' ? 'v-badge-ps5' : ($kat === 'Nintendo' ? 'v-badge-nin' : 'v-badge-ps4');
        $hidden = $i >= $limit_games;
        
        $class_extra = $hidden ? ' pub-game-extra' : '';
        $style_extra = $hidden ? ' style="display:none;"' : '';
        
        $fallback = getGameMeta($g['judul_game'], $kat);
        $genre = !empty($g['genre_game']) ? htmlspecialchars($g['genre_game']) : $fallback['genre'];
        $players = !empty($g['players_game']) ? htmlspecialchars($g['players_game']) : $fallback['players'];
      ?>
      <div class="col-6game<?php echo $class_extra; ?>"<?php echo $style_extra; ?> data-platform="<?php echo htmlspecialchars($kat); ?>" data-genre="<?php echo $genre; ?>" data-players="<?php echo $players; ?>">
        <div class="game-card">
          <div class="game-card-img-wrap">
            <img src="uploads/games/<?php echo htmlspecialchars($g['foto_game']); ?>" alt="<?php echo htmlspecialchars($g['judul_game']); ?>" class="game-cover-img">
            <?php if ($kat): ?><span class="game-platform-badge <?php echo $bc; ?>"><?php echo $kat; ?></span><?php endif; ?>
          </div>
          <div class="game-card-body">
            <div class="game-card-title-text" title="<?php echo htmlspecialchars($g['judul_game']); ?>"><?php echo htmlspecialchars($g['judul_game']); ?></div>
            <div class="game-card-meta">
              <span class="game-meta-genre"><?php echo $genre; ?></span>
              <span class="game-meta-players">👥 <?php echo $players; ?></span>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if ($total_games > $limit_games): ?>
    <div style="text-align:center;margin-top:1.75rem;">
      <button onclick="togglePubGames()" id="btn-pub-games" class="btn-lihat-semua-game" aria-label="Lihat semua game" title="Lihat <?php echo $total_games - $limit_games; ?> game lainnya">
        <svg class="toggle-chevron" id="ico-pub-games" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
      </button>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- SEWA -->
<section class="sewa-section" id="sewa">
  <div class="container">
    <div class="row" style="align-items:center;gap:4rem;">
      <!-- Left Column: Description & Features -->
      <div class="col-half">
        <div class="sewa-system-label">// RENTAL_SERVICE_MODULE</div>
        <h2 class="sewa-title">SEWA <span class="sewa-gradient-text">BAWA PULANG</span></h2>
        <div class="sewa-divider"></div>
        <p class="sewa-description">
          Nikmati layanan sewa PlayStation harian dengan proses cepat, jaminan aman, dan reservasi mudah melalui WhatsApp.
        </p>

        <!-- Feature Rows -->
        <div class="sewa-feature">
          <div class="sewa-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          </div>
          <div class="sewa-feature-text">
            <h6>Booking via WhatsApp</h6>
            <p>Hubungi minimal H-1 sebelum tanggal pengambilan untuk reservasi unit.</p>
          </div>
        </div>
        
        <div class="sewa-feature">
          <div class="sewa-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
          </div>
          <div class="sewa-feature-text">
            <h6>Ambil di Toko</h6>
            <p>Datang langsung ke toko kami di Jagakarsa karena unit tidak bisa diantar.</p>
          </div>
        </div>

        <div class="sewa-feature">
          <div class="sewa-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="16" rx="2"/><line x1="7" y1="8" x2="17" y2="8"/><line x1="7" y1="12" x2="17" y2="12"/><line x1="7" y1="16" x2="13" y2="16"/></svg>
          </div>
          <div class="sewa-feature-text">
            <h6>Jaminan KTP & STNK</h6>
            <p>Dokumen asli diserahkan saat pengambilan. Alamat KTP & STNK harus Jagakarsa.</p>
          </div>
        </div>

        <div class="sewa-feature">
          <div class="sewa-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/></svg>
          </div>
          <div class="sewa-feature-text">
            <h6>Promo Weekday</h6>
            <p>Sewa 2 hari gratis 1 hari, sewa 3 hari gratis 2 hari berlaku Senin s/d Kamis!</p>
          </div>
        </div>

        <a href="sewa.php" class="btn-wa-sewa">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="flex-shrink:0;"><use href="#ico-wa"/></svg>
          <span>Ajukan Sewa Sekarang</span>
        </a>
      </div>

      <!-- Right Column: Live Status Card -->
      <div class="col-half">
        <?php
        $q_ready = mysqli_query($koneksi, "SELECT COUNT(*) as c FROM units WHERE tipe_layanan='Sewa Luar' AND status='Tersedia'");
        $ready_count = mysqli_fetch_assoc($q_ready)['c'];
        ?>
        <div class="sewa-status-card">
          <div class="status-card-header">
            <h5 class="status-card-title">Unit Sewa Tersedia</h5>
            <span class="status-ready-badge"><?php echo $ready_count; ?> Unit Ready</span>
          </div>
          <div class="sewa-unit-list">
            <?php
            $q_units = mysqli_query($koneksi, "SELECT * FROM units WHERE tipe_layanan='Sewa Luar' ORDER BY kategori, nama_unit");
            $ada = false;
            while ($u = mysqli_fetch_assoc($q_units)):
              $ada = true;
              $status = $u['status']; // Tersedia, Disewa, Maintenance
              
              if ($status === 'Tersedia') {
                $status_html = '<span class="status-pill status-ready">🟢 Ready</span>';
              } elseif ($status === 'Disewa') {
                $status_html = '<span class="status-pill status-reserved">🟡 Reserved</span>';
              } else {
                $status_html = '<span class="status-pill status-maint">🔴 Maint</span>';
              }
            ?>
            <div class="sewa-unit-row">
              <span class="unit-name"><?php echo htmlspecialchars($u['nama_unit']); ?></span>
              <?php echo $status_html; ?>
            </div>
            <?php endwhile;
            if (!$ada): ?>
              <div class="empty-units-text">Tidak ada unit terdaftar 😊</div>
            <?php endif; ?>
          </div>
          
          <!-- Info Alert Box -->
          <div class="sewa-premium-alert">
            <span class="alert-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            </span>
            <p class="alert-text">PS5 juga bisa disewa, silakan hubungi WhatsApp untuk cek ketersediaan terbaru.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section style="padding:6rem 0;background:rgba(255,255,255,.015);" id="faq">
  <div class="container">
    <div class="section-title">PERTANYAAN <span class="neon">UMUM</span></div>
    <div class="v-divider"></div>
    <div style="max-width:720px;margin-top:1rem;" id="faq-list">
      <?php
      $faqs = [
        ['q'=>'Apakah unit diantar ke rumah?','a'=>'Tidak. Unit harus diambil langsung ke toko kami di Jagakarsa. Ini berlaku untuk semua jenis sewa, termasuk Playbox.'],
        ['q'=>'Apa syarat untuk sewa?','a'=>'KTP dan STNK asli dengan alamat Jagakarsa yang sama serta nomor aktif minimal 50 tag di GetContacts. KTP dan STNK ditahan sebagai jaminan selama sewa berlangsung.'],
        ['q'=>'Apa itu Playbox?','a'=>'Playbox adalah koper gaming all-in-one berisi PS4/PS5/Nintendo, monitor, dan speaker. Tinggal buka koper dan colok listrik langsung bisa main, tanpa TV atau monitor tambahan.'],
        ['q'=>'Bagaimana cara booking PS5?','a'=>'PS5 perlu konfirmasi via WhatsApp dulu karena unit yang sama dipakai untuk main di tempat. Hubungi kami di 0858-4783-1078 untuk cek ketersediaan sebelum isi form.'],
        ['q'=>'Kapan promo weekday berlaku?','a'=>'Setiap Senin sampai Kamis (tidak termasuk tanggal libur nasional). Sewa 2 hari gratis 1 hari, sewa 3 hari gratis 2 hari. Berlaku untuk semua kategori unit Sewa Bawa Pulang.'],
        ['q'=>'Bagaimana kalau unit rusak saat di tangan saya?','a'=>'Kerusakan dan kehilangan sepenuhnya menjadi tanggung jawab penyewa. Jika segel rusak, dianggap membeli unit.'],
        ['q'=>'Berapa denda keterlambatan?','a'=>'Rp 10.000 per jam untuk keterlambatan 1–6 jam. Lebih dari 6 jam dianggap sewa 1 hari lagi.'],
        ['q'=>'Apakah bisa bayar DP atau transfer?','a'=>'Pembayaran dilakukan di lokasi saat pengambilan unit, setelah pengajuan disetujui. Belum tersedia pembayaran di muka atau transfer.'],
      ];
      foreach ($faqs as $i => $f):
      ?>
      <div style="border-bottom:1px solid var(--v-border);">
        <button onclick="toggleFaq(<?php echo $i; ?>)" style="width:100%;background:none;border:none;padding:1.25rem 0;display:flex;justify-content:space-between;align-items:center;gap:1rem;cursor:pointer;text-align:left;">
          <span style="font-family:var(--font-ui);font-size:.95rem;font-weight:700;color:var(--v-white);letter-spacing:.5px;"><?php echo htmlspecialchars($f['q']); ?></span>
          <span id="faq-icon-<?php echo $i; ?>" style="color:var(--v-violet);font-size:1.3rem;flex-shrink:0;transition:transform .2s;line-height:1;">+</span>
        </button>
        <div id="faq-ans-<?php echo $i; ?>" style="display:none;padding-bottom:1.25rem;">
          <p style="color:var(--v-muted);font-size:.88rem;line-height:1.7;"><?php echo htmlspecialchars($f['a']); ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- LOKASI -->
<section class="map-section" id="lokasi">
  <div class="container">
    <!-- Section Header -->
    <div class="location-header">
      <div class="location-system-label">// LOCATION_MODULE_ACTIVE</div>
      <h2 class="location-title">LOKASI <span class="location-gradient-text">KAMI</span></h2>
      <div class="location-divider"></div>
      <p class="location-description">
        Temukan Violet PlayStation dengan mudah. Kami siap melayani setiap hari di Jagakarsa, Jakarta Selatan.
      </p>
    </div>

    <div class="row" style="align-items:stretch;margin-top:1rem;">
      <!-- Left Info Card -->
      <div class="col-half">
        <div class="location-card">
          <div>
            <h4 class="location-card-title">VIOLET PLAYSTATION</h4>
            <div class="location-card-subtitle">Premium PlayStation Rental Center</div>
            
            <div class="location-detail-list">
              <div class="location-detail-row">
                <div class="location-icon-wrapper">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                </div>
                <div class="location-info-text">
                  <strong>Alamat</strong>
                  <p>Jl. Jagakarsa II No.5D, RT.1/RW.7, Jagakarsa, Kec. Jagakarsa, Jakarta Selatan 12620</p>
                </div>
              </div>
              <div class="location-detail-row">
                <div class="location-icon-wrapper">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div class="location-info-text">
                  <strong>Jam Operasional</strong>
                  <p>Senin-Kamis (09.00-22.00) · Jumat (13.00-23.00) · Sabtu-Minggu (09.00-23.00)</p>
                </div>
              </div>
              <div class="location-detail-row">
                <div class="location-icon-wrapper">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
                </div>
                <div class="location-info-text">
                  <strong>WhatsApp</strong>
                  <p>0858-4783-1078 (Respons Cepat)</p>
                </div>
              </div>
              <div class="location-detail-row">
                <div class="location-icon-wrapper">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                </div>
                <div class="location-info-text">
                  <strong>Penting</strong>
                  <p>Booking H-1 via WA. KTP & STNK Jagakarsa wajib dibawa sebagai jaminan sewa.</p>
                </div>
              </div>
            </div>
          </div>
          
          <a href="https://wa.me/6285847831078" target="_blank" class="btn-wa-location">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor" style="flex-shrink:0;"><use href="#ico-wa"/></svg>
            <span>Chat WhatsApp</span>
          </a>
        </div>
      </div>

      <!-- Right Map Frame -->
      <div class="col-half">
        <div class="location-map-wrap">
          <iframe loading="lazy" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.5!2d106.8198065!3d-6.3269265!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ed005178c647%3A0x884731391d96c010!2sViolet%20PlayStation!5e0!3m2!1sid!2sid!4v1" allowfullscreen="" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="v-footer">
  <!-- Huge Watermark -->
  <div class="footer-watermark">VIOLET</div>
  
  <div class="container">
    <div class="footer-grid">
      <!-- Left Section -->
      <div class="footer-sec-left">
        <div class="footer-brand">VIOLET <span class="neon">PLAYSTATION</span></div>
        <div class="footer-sub-brand">Premium PlayStation Rental <span class="sub-sep">•</span> PS4 • PS5 • Playbox</div>
        <p class="footer-description">
          Providing premium PlayStation rental services in Jagakarsa, Jakarta Selatan with modern booking experience.
        </p>
        <div class="footer-social-wrap">
          <a href="https://wa.me/6285847831078" target="_blank" class="footer-social-btn" title="WhatsApp">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><use href="#ico-wa"/></svg>
          </a>
          <a href="https://www.instagram.com/violetplaystation/" target="_blank" class="footer-social-btn" title="Instagram">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg>
          </a>
        </div>
      </div>

      <!-- Center Section -->
      <div class="footer-sec-center">
        <h5 class="footer-sec-title">Quick Links</h5>
        <div class="footer-links-grid">
          <a href="#harga" class="footer-link">Daftar Harga</a>
          <a href="#unit" class="footer-link">Cek Unit</a>
          <a href="#games" class="footer-link">Koleksi Game</a>
          <a href="sewa.php" class="footer-link">Form Sewa</a>
          <a href="#lokasi" class="footer-link">Lokasi</a>
          <a href="#faq" class="footer-link">FAQ</a>
          <a href="cek_status.php" class="footer-link">Cek Status</a>
        </div>
      </div>

      <!-- Right Section -->
      <div class="footer-sec-right">
        <h5 class="footer-sec-title">Information</h5>
        <div class="footer-info-list">
          <div class="footer-info-item">
            <span class="info-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
            </span>
            <div>
              <strong>Jagakarsa</strong>
              <span>Jakarta Selatan</span>
            </div>
          </div>
          <div class="footer-info-item">
            <span class="info-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </span>
            <div>
              <strong>Open Daily</strong>
              <span>09:00 - 23:00</span>
            </div>
          </div>
          <div class="footer-info-item">
            <span class="info-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><line x1="6" y1="12" x2="10" y2="12"/><line x1="8" y1="10" x2="8" y2="14"/><line x1="15" y1="13" x2="15.01" y2="13"/><line x1="18" y1="11" x2="18.01" y2="11"/><rect x="2" y="6" width="20" height="12" rx="3"/><path d="M12 12h.01"/></svg>
            </span>
            <div>
              <strong>Platform</strong>
              <span>PS4 • PS5 • Playbox</span>
            </div>
          </div>
          <div class="footer-info-item">
            <span class="info-icon">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
            </span>
            <div>
              <strong>WhatsApp</strong>
              <span>Available Every Day</span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Glowing Divider -->
    <div class="footer-glow-divider"></div>

    <!-- Bottom Bar -->
    <div class="footer-bottom-bar">
      <div class="copyright">© 2026 Violet PlayStation</div>
      <div class="credits">Made with ❤️ in Jakarta Selatan</div>
      <div class="version">V-SYS v2.0</div>
    </div>
  </div>
</footer>

<button id="scroll-top" onclick="window.scrollTo({top:0,behavior:'smooth'})" aria-label="Scroll ke atas">
  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
</button>

<!-- MODAL UNIT -->
<div class="modal-overlay" id="modalUnit">
  <div class="modal-box">
    <div class="modal-header">
      <div>
        <div style="font-family:var(--font-ui);font-size:.75rem;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);margin-bottom:.4rem;" id="modal-meta"></div>
        <div class="modal-unit-name" id="modal-nama">—</div>
      </div>
      <button class="modal-close-btn" onclick="tutupModal()">✕</button>
    </div>
    <div class="modal-body">
      <div style="font-family:var(--font-ui);font-size:.8rem;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);margin-bottom:.5rem;">Daftar Game di Unit Ini</div>
      <div id="modal-games"></div>
      <div id="modal-sewa-wrap" style="display:none;margin-top:1.5rem;">
        <a id="modal-sewa-btn" href="sewa.php" class="btn-violet" style="display:block;text-align:center;text-decoration:none;"><span>Sewa Unit Ini Sekarang</span></a>
      </div>
    </div>
  </div>
</div>

<?php
$all_unit_games = [];
$qug = mysqli_query($koneksi, "SELECT ug.id_unit,g.judul_game,g.foto_game,g.kategori_game FROM unit_games ug JOIN games g ON ug.id_game=g.id_game ORDER BY g.judul_game ASC");
while ($row = mysqli_fetch_assoc($qug)) $all_unit_games[$row['id_unit']][] = $row;
?>
<script>
const unitGames = <?php echo json_encode($all_unit_games); ?>;

// ── Unit modal ──────────────────────────────────────────────────────────────
function bukaUnit(id, nama, kat, tipe) {
  document.getElementById('modal-nama').textContent = nama;
  document.getElementById('modal-meta').textContent = kat + ' · ' + tipe;
  const games = unitGames[id] || [];
  const el    = document.getElementById('modal-games');
  if (!games.length) {
    el.innerHTML = '<div class="modal-empty">Belum ada game di unit ini.</div>';
  } else {
    let h = '<div class="modal-games-grid">';
    games.forEach(g => {
      h += `<div class="modal-game-item"><img src="uploads/games/${g.foto_game}" alt="${g.judul_game}"><span>${g.judul_game}</span></div>`;
    });
    h += '</div>';
    el.innerHTML = h;
  }
  document.getElementById('modal-sewa-wrap').style.display = tipe === 'Sewa Luar' ? 'block' : 'none';
  if (tipe === 'Sewa Luar') document.getElementById('modal-sewa-btn').href = 'sewa.php?unit=' + id;
  document.getElementById('modalUnit').classList.add('open');
  document.body.style.overflow = 'hidden';
}
function tutupModal() {
  document.getElementById('modalUnit').classList.remove('open');
  document.body.style.overflow = '';
}
document.getElementById('modalUnit').addEventListener('click', e => {
  if (e.target === document.getElementById('modalUnit')) tutupModal();
});

// ── Toggle unit (BARU: tombol di samping, buka semua sekaligus) ─────────────
function toggleSemuaUnit(group) {
  const btn    = document.getElementById('btn-toggle-' + group);
  const preview = 3;
  const isOpen  = btn.getAttribute('aria-expanded') === 'true';
  const grid    = document.getElementById(group === 'sewa' ? 'grid-sewa-pub' : 'grid-tempat-pub');
  const cards   = grid ? grid.querySelectorAll('.unit-card') : [];

  cards.forEach((card, i) => {
    if (i >= preview) card.style.display = isOpen ? 'none' : '';
  });

  btn.setAttribute('aria-expanded', isOpen ? 'false' : 'true');
  btn.classList.toggle('all-shown', !isOpen);
}

// ── Tab switcher ────────────────────────────────────────────────────────────
function switchPriceTab(tab, btn) {
  document.querySelectorAll('.price-tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.price-tab-panel').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('panel-' + tab).classList.add('active');
}
function switchUnitTab(tab, btn) {
  document.querySelectorAll('.unit-tab').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.units-panel').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('upanel-' + tab).classList.add('active');
}

// ── Nav drawer ──────────────────────────────────────────────────────────────
function toggleDrawer() {
  const d = document.getElementById('navDrawer');
  const h = document.getElementById('hamburger');
  d.classList.toggle('open');
  h.classList.toggle('open');
  document.body.style.overflow = d.classList.contains('open') ? 'hidden' : '';
}
function closeDrawer() {
  document.getElementById('navDrawer').classList.remove('open');
  document.getElementById('hamburger').classList.remove('open');
  document.body.style.overflow = '';
}

// ── Games toggle ────────────────────────────────────────────────────────────
function togglePubGames() {
  const items = document.querySelectorAll('.pub-game-extra');
  const btn   = document.getElementById('btn-pub-games');
  if (!items.length) return;
  const isOpen = items[0].style.display !== 'none';
  items.forEach(el => el.style.display = isOpen ? 'none' : '');
  btn.classList.toggle('all-shown', !isOpen);
}

function applyFilters() {
  const genreVal = document.getElementById('filter-genre').value;
  const playersVal = document.getElementById('filter-players').value;
  
  const cards = document.querySelectorAll('#games-grid .col-6game');
  const showMoreBtnWrapper = document.getElementById('btn-pub-games')?.parentElement;
  
  cards.forEach(card => {
    const genre = card.getAttribute('data-genre') || '';
    const players = card.getAttribute('data-players') || '';
    
    const matchesGenre = (genreVal === 'ALL' || genre === genreVal);
    const matchesPlayers = (playersVal === 'ALL' || players === playersVal);
    
    if (matchesGenre && matchesPlayers) {
      const isExtra = card.classList.contains('pub-game-extra');
      const isExpanded = document.getElementById('btn-pub-games')?.classList.contains('all-shown');
      
      if (genreVal === 'ALL' && playersVal === 'ALL') {
        if (isExtra && !isExpanded) {
          card.style.display = 'none';
        } else {
          card.style.display = '';
        }
      } else {
        card.style.display = '';
      }
    } else {
      card.style.display = 'none';
    }
  });
  
  // Hide or show 'Show More' button
  if (showMoreBtnWrapper) {
    showMoreBtnWrapper.style.display = (genreVal === 'ALL' && playersVal === 'ALL') ? '' : 'none';
  }
}

// ── Scroll to top ────────────────────────────────────────────────────────────
window.addEventListener('scroll', function() {
  const btn = document.getElementById('scroll-top');
  if (btn) btn.classList.toggle('show', window.scrollY > 400);
}, { passive: true });

// ── FAQ ──────────────────────────────────────────────────────────────────────
function toggleFaq(i) {
  const ans = document.getElementById('faq-ans-' + i);
  const ico = document.getElementById('faq-icon-' + i);
  const open = ans.style.display === 'block';
  ans.style.display = open ? 'none' : 'block';
  ico.textContent   = open ? '+' : '−';
  ico.style.transform = open ? '' : 'rotate(45deg)';
}

// ── Search game ──────────────────────────────────────────────────────────────
const allGames = <?php
$gdata = [];
$qg    = mysqli_query($koneksi, "SELECT DISTINCT g.id_game,g.judul_game,g.kategori_game,u.nama_unit,u.id_unit FROM games g JOIN unit_games ug ON g.id_game=ug.id_game JOIN units u ON ug.id_unit=u.id_unit ORDER BY g.judul_game,u.nama_unit");
while ($r = mysqli_fetch_assoc($qg)) {
  $gdata[$r['id_game']]['judul'] = $r['judul_game'];
  $gdata[$r['id_game']]['kat']   = $r['kategori_game'] ?? '';
  $gdata[$r['id_game']]['units'][] = ['id' => $r['id_unit'], 'nama' => $r['nama_unit']];
}
echo json_encode(array_values($gdata));
?>;

function cariGame(q) {
  const keyword = q.trim().toLowerCase();
  const box     = document.getElementById('search-result');
  const list    = document.getElementById('search-list');
  document.getElementById('search-keyword').textContent = q.trim();
  if (!keyword) { box.style.display = 'none'; return; }
  const hasil = allGames.filter(g => g.judul.toLowerCase().includes(keyword));
  if (!hasil.length) {
    list.innerHTML = '<div style="color:var(--v-muted);font-family:var(--font-ui);font-size:.85rem;">Game tidak ditemukan.</div>';
    box.style.display = 'block';
    return;
  }
  let h = '';
  hasil.forEach(g => {
    const units = g.units.map(u => `<span style="background:rgba(255,255,255,.04);border:1px solid var(--v-border);border-radius:6px;padding:.2rem .6rem;font-size:.75rem;font-family:var(--font-ui);color:#C4B5D4;">${u.nama}</span>`).join(' ');
    const katStyle = g.kat === 'PS5'
      ? 'background:rgba(96,165,250,.15);color:#60a5fa;border:1px solid rgba(96,165,250,.3);'
      : g.kat === 'Nintendo'
        ? 'background:rgba(248,113,113,.15);color:#f87171;border:1px solid rgba(248,113,113,.3);'
        : 'background:rgba(157, 86, 255,.15);color:#D6C2FF;border:1px solid rgba(157, 86, 255,.3);';
    h += `<div style="padding:.75rem 0;border-bottom:1px solid var(--v-border);">
      <div style="display:flex;align-items:center;gap:.5rem;margin-bottom:.5rem;">
        <span style="font-family:var(--font-ui);font-size:.95rem;font-weight:700;color:var(--v-white);">${g.judul}</span>
        ${g.kat ? `<span style="font-family:var(--font-ui);font-size:.65rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:.15rem .5rem;border-radius:4px;${katStyle}">${g.kat}</span>` : ''}
      </div>
      <div style="display:flex;flex-wrap:wrap;gap:.4rem;">${units}</div>
    </div>`;
  });
  list.innerHTML = h;
  box.style.display = 'block';
}
</script>
</body>
</html>