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
      <button class="price-tab-btn active" onclick="switchPriceTab('sewa',this)">🏠 Sewa Bawa Pulang</button>
      <button class="price-tab-btn" onclick="switchPriceTab('tempat',this)">🎮 Main di Tempat</button>
      <button class="price-tab-btn" onclick="switchPriceTab('playbox',this)">🎒 Playbox</button>
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
          <span class="v-badge v-badge-ps4" style="margin-bottom:.75rem;display:inline-block;">Console</span>
          <div class="price-card-title">PlayStation 4</div><div class="price-tag">Sewa Bawa Pulang · Per Hari</div>
          <div class="price-row"><span class="label">1 Hari</span><span class="price">Rp 100.000</span></div>
          <div class="price-row"><span class="label">2 Hari <span class="free-badge">Free 1 hari</span></span><span class="price">Rp 200.000</span></div>
          <div class="price-row"><span class="label">3 Hari <span class="free-badge">Free 2 hari</span></span><span class="price">Rp 300.000</span></div>
        </div></div>
        <div class="col-half"><div class="price-card ps5">
          <span class="v-badge v-badge-ps5" style="margin-bottom:.75rem;display:inline-block;">Next-Gen</span>
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
          <span class="v-badge v-badge-ps4" style="margin-bottom:.75rem;display:inline-block;">Console</span>
          <div class="price-card-title">PlayStation 4</div><div class="price-tag">Main di Tempat · Per Sesi</div>
          <div class="price-row"><span class="label">1 Jam</span><span class="price">Rp 8.000</span></div>
          <div class="price-row"><span class="label">2 Jam</span><span class="price">Rp 15.000</span></div>
          <div class="price-row"><span class="label">3 Jam</span><span class="price">Rp 20.000</span></div>
          <div class="price-row"><span class="label">5 Jam</span><span class="price">Rp 35.000</span></div>
          <div class="price-note">⚠ Waktu tidak dapat disimpan / dipause</div>
        </div></div>
        <div class="col-half"><div class="price-card ps5">
          <span class="v-badge v-badge-ps5" style="margin-bottom:.75rem;display:inline-block;">Next-Gen</span>
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
          <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.75rem;">
            <span class="v-badge" style="background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);">Playbox</span>
            <span class="v-badge v-badge-ps4">PS4</span>
          </div>
          <div class="price-card-title">Playbox PS4</div>
          <div class="price-tag">Sewa Bawa Pulang · Per 24 Jam</div>
          <div class="price-row"><span class="label">1 Hari</span><span class="price">Rp 130.000</span></div>
          <div class="price-row"><span class="label">2 Hari <span class="free-badge">Free 1 hari</span></span><span class="price">Rp 260.000</span></div>
          <div class="price-row"><span class="label">3 Hari <span class="free-badge">Free 2 hari</span></span><span class="price">Rp 390.000</span></div>
          <div class="price-note green">Monitor + speaker + 2 controller included</div>
        </div></div>
     <div class="col-half" style="display:none;"><div class="price-card ps5">
          <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.75rem;">
            <span class="v-badge" style="background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);">Playbox</span>
            <span class="v-badge v-badge-ps5">PS5</span>
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
      <button class="unit-tab active" onclick="switchUnitTab('sewa',this)">🏠 Sewa Bawa Pulang</button>
      <button class="unit-tab" onclick="switchUnitTab('tempat',this)">🎮 Main di Tempat</button>
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
<section class="games-section" id="games">
  <div class="container">
    <div class="section-title">KOLEKSI <span class="neon">GAME</span></div>
    <div class="v-divider"></div>
    <div style="margin-bottom:1.5rem;position:relative;max-width:420px;">
      <input type="text" id="game-search" class="v-input" placeholder="Cari game..." oninput="cariGame(this.value)" style="padding-left:2.75rem;">
      <svg width="18" height="18" style="position:absolute;left:.9rem;top:50%;transform:translateY(-50%);color:var(--v-muted);pointer-events:none;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
    </div>
    <div id="search-result" style="display:none;background:var(--v-card);border:1px solid var(--v-border);border-radius:14px;padding:1.5rem;margin-bottom:1.5rem;animation:fadeUp .25s ease both;">
      <div style="font-family:var(--font-ui);font-size:.8rem;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);margin-bottom:1rem;">Hasil Pencarian: <strong id="search-keyword" style="color:var(--v-lavender);"></strong></div>
      <div id="search-list"></div>
    </div>

    <?php
    // Logika Query: Bersih dari DISTINCT/JOIN agar urutan Abjad (A-Z) mutlak bekerja
    $q_games = mysqli_query($koneksi, "SELECT id_game, judul_game, foto_game, kategori_game FROM games ORDER BY judul_game ASC");
    $arr_games = [];
    while ($g = mysqli_fetch_assoc($q_games)) $arr_games[] = $g;
    $total_games = count($arr_games);
    $limit_games =6;
    ?>

    <div class="row" style="margin-top:.5rem;" id="games-grid">
      <?php foreach ($arr_games as $i => $g):
        $kat    = $g['kategori_game'] ?? '';
        $bc     = $kat === 'PS5' ? 'v-badge-ps5' : ($kat === 'Nintendo' ? 'v-badge-nin' : 'v-badge-ps4');
        $hidden = $i >= $limit_games;
        
        // Penggabungan Class & Style yang Benar (Tidak ada class ganda)
        $class_extra = $hidden ? ' pub-game-extra' : '';
        $style_extra = $hidden ? ' style="display:none;"' : '';
      ?>
      <div class="col-6game<?php echo $class_extra; ?>"<?php echo $style_extra; ?>>
        <div class="game-card">
          <img src="uploads/games/<?php echo htmlspecialchars($g['foto_game']); ?>" alt="<?php echo htmlspecialchars($g['judul_game']); ?>">
          <div class="game-card-body">
            <?php if ($kat): ?><span class="v-badge <?php echo $bc; ?>" style="font-size:.65rem;padding:.1rem .4rem;margin-bottom:.4rem;display:inline-block;"><?php echo $kat; ?></span><?php endif; ?>
            <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo htmlspecialchars($g['judul_game']); ?></div>
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
      <div class="col-half">
        <div class="hero-eyebrow">Layanan Sewa Harian</div>
        <div class="section-title">SEWA <span class="neon">BAWA<br>PULANG</span></div>
        <div class="v-divider"></div>
        <div class="sewa-feature"><div class="sewa-icon"><svg width="22" height="22" aria-hidden="true"><use href="#ico-phone"/></svg></div><div class="sewa-feature-text"><h6>Booking via WhatsApp</h6><p>Hubungi minimal H-1 sebelum tanggal pengambilan untuk reservasi unit.</p></div></div>
        <div class="sewa-feature"><div class="sewa-icon"><svg width="22" height="22" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-store"/></svg></div><div class="sewa-feature-text"><h6>Ambil di Toko</h6><p>Datang langsung ke toko kami di Jagakarsa karena unit tidak bisa diantar.</p></div></div>
        <div class="sewa-feature"><div class="sewa-icon"><svg width="22" height="22" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-idcard"/></svg></div><div class="sewa-feature-text"><h6>Jaminan KTP & STNK</h6><p>Dokumen asli diserahkan saat pengambilan. Alamat KTP & STNK harus Jagakarsa.</p></div></div>
        <div class="sewa-feature"><div class="sewa-icon"><svg width="22" height="22" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-gift"/></svg></div><div class="sewa-feature-text"><h6>Promo Weekday</h6><p>Sewa 2 hari gratis 1 hari, sewa 3 hari gratis 2 hari berlaku Senin s/d Kamis!</p></div></div>
        <a href="sewa.php" class="btn-violet" style="display:inline-block;text-decoration:none;margin-top:1rem;"><span>Ajukan Sewa Sekarang</span></a>
      </div>
      <div class="col-half"><div class="v-card" style="padding:2.5rem;">
        <div style="font-family:var(--font-display);font-size:1.1rem;font-weight:700;letter-spacing:2px;color:var(--v-muted);text-transform:uppercase;margin-bottom:1.5rem;">Unit Sewa Tersedia</div>
        <?php
        $units = mysqli_query($koneksi, "SELECT * FROM units WHERE tipe_layanan='Sewa Luar' AND status='Tersedia' ORDER BY kategori");
        $ada   = false;
        while ($u = mysqli_fetch_assoc($units)):
          $ada = true; $kat = $u['kategori']; $bc = $kat === 'PS5' ? 'v-badge-ps5' : ($kat === 'Nintendo' ? 'v-badge-nin' : 'v-badge-ps4');
        ?>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.7rem 0;border-bottom:1px solid var(--v-border);">
          <span style="font-family:var(--font-ui);font-size:.95rem;color:#C4B5D4;"><?php echo htmlspecialchars($u['nama_unit']); ?></span>
          <span class="v-badge <?php echo $bc; ?>"><?php echo $kat; ?></span>
        </div>
        <?php endwhile;
        if (!$ada): ?><div style="text-align:center;padding:2rem;color:var(--v-muted);font-family:var(--font-ui);font-size:.9rem;">Semua unit sedang disewa 😊</div><?php endif; ?>
        <div style="margin-top:1rem;font-size:.78rem;color:#93c5fd;font-family:var(--font-ui);background:rgba(96,165,250,.06);border:1px solid rgba(96,165,250,.15);border-radius:8px;padding:.6rem .85rem;">ℹ️ PS5 juga bisa disewa hubungi WA untuk cek ketersediaan</div>
      </div></div>
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
    <div class="section-title">LOKASI <span class="neon">KAMI</span></div>
    <div class="v-divider"></div>
    <div class="row" style="align-items:stretch;margin-top:1rem;">
      <div class="col-half"><div class="map-info">
        <h4>Violet PlayStation</h4>
        <div class="map-detail"><div class="map-detail-icon">📍</div><div class="map-detail-text"><strong>Alamat</strong><p>Jl. Jagakarsa II No.5D, RT.1/RW.7, Jagakarsa, Kec. Jagakarsa, Jakarta Selatan 12620</p></div></div>
        <div class="map-detail"><div class="map-detail-icon">🕐</div><div class="map-detail-text"><strong>Jam Operasional</strong><p>Setiap hari · Senin-Kamis (09.00-22.00) Jumat (13.00-23.00) Sabtu-Minggu (09.00-23.00)</p></div></div>
        <div class="map-detail"><div class="map-detail-icon">📱</div><div class="map-detail-text"><strong>WhatsApp</strong><p>0858-4783-1078</p></div></div>
        <div class="map-detail"><div class="map-detail-icon">⚠</div><div class="map-detail-text"><strong>Penting</strong><p>Booking H-1 via WA. KTP & STNK Jagakarsa wajib dibawa.</p></div></div>
        <a href="https://wa.me/6285847831078" target="_blank" class="btn-violet" style="display:inline-flex;align-items:center;gap:.5rem;text-decoration:none;margin-top:1.5rem;width:100%;justify-content:center;"><span>Chat WhatsApp</span></a>
      </div></div>
      <div class="col-half"><div class="map-wrap" style="height:100%;min-height:350px;">
        <iframe loading="lazy" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.5!2d106.8198065!3d-6.3269265!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ed005178c647%3A0x884731391d96c010!2sViolet%20PlayStation!5e0!3m2!1sid!2sid!4v1" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div></div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="v-footer">
  <div class="container">
    <div style="display:flex;flex-wrap:wrap;gap:3rem;justify-content:space-between;">
      <div>
        <div style="font-family:var(--font-display);font-size:2rem;font-weight:800;letter-spacing:4px;text-transform:uppercase;">VIOLET <span class="neon">PLAYSTATION</span></div>
        <div style="color:var(--v-muted);font-size:.9rem;margin-top:.5rem;">Sewa PS & Playbox Jagakarsa, Jakarta Selatan</div>
        <div style="display:flex;gap:.75rem;margin-top:1.5rem;align-items:center;">
          <a href="https://wa.me/6285847831078" target="_blank" class="wa-btn" title="WhatsApp"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><use href="#ico-wa"/></svg></a>
          <a href="https://www.instagram.com/violetplaystation/" target="_blank" class="ig-btn" title="Instagram" style="width:44px;height:44px;"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
        </div>
      </div>
      <div>
        <div style="font-family:var(--font-ui);font-size:.8rem;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);margin-bottom:1rem;">Menu</div>
        <div style="display:flex;flex-direction:column;gap:.6rem;">
          <a href="#harga" style="color:#9d8bb0;text-decoration:none;font-size:.9rem;">Daftar Harga</a>
          <a href="#unit" style="color:#9d8bb0;text-decoration:none;font-size:.9rem;">Cek Unit</a>
          <a href="#games" style="color:#9d8bb0;text-decoration:none;font-size:.9rem;">Koleksi Game</a>
          <a href="sewa.php" style="color:#9d8bb0;text-decoration:none;font-size:.9rem;">Form Sewa</a>
          <a href="#lokasi" style="color:#9d8bb0;text-decoration:none;font-size:.9rem;">Lokasi</a>
          <a href="#faq" style="color:#9d8bb0;text-decoration:none;font-size:.9rem;">FAQ</a>
          <a href="cek_status.php" style="color:#9d8bb0;text-decoration:none;font-size:.9rem;">Cek Status Sewa</a>
        </div>
      </div>
    </div>
    <div class="footer-copy">© 2026 Violet PlayStation · Jagakarsa, Jakarta Selatan</div>
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