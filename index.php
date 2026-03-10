<?php include 'config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Violet Playstation — Best Gaming Experience di Jagakarsa</title>
  <link rel="stylesheet" href="assets/css/violet.css">
  <style>
    .hero{min-height:100vh;display:flex;align-items:center;position:relative;overflow:hidden;padding:6rem 0 4rem;}
    .hero-bg{position:absolute;inset:0;z-index:0;background:radial-gradient(ellipse 70% 60% at 70% 50%,rgba(123,47,190,.25) 0%,transparent 70%),radial-gradient(ellipse 40% 40% at 20% 80%,rgba(168,85,247,.1) 0%,transparent 60%),var(--v-black);}
    .hero-grid-lines{position:absolute;inset:0;z-index:0;background-image:linear-gradient(rgba(123,47,190,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(123,47,190,.06) 1px,transparent 1px);background-size:60px 60px;}
    .hero-content{position:relative;z-index:1;}
    .hero-eyebrow{font-family:var(--font-ui);font-size:.85rem;font-weight:700;letter-spacing:4px;text-transform:uppercase;color:var(--v-violet);border:1px solid rgba(168,85,247,.3);display:inline-block;padding:.3rem 1rem;border-radius:4px;margin-bottom:1.5rem;background:rgba(168,85,247,.08);}
    .hero-title{font-family:var(--font-display);font-size:clamp(3.5rem,10vw,7rem);font-weight:800;letter-spacing:4px;text-transform:uppercase;line-height:.95;margin-bottom:1.5rem;}
    .hero-title .line2{color:var(--v-lavender);text-shadow:0 0 30px var(--v-violet);}
    .hero-sub{font-size:1.05rem;color:var(--v-muted);max-width:440px;line-height:1.7;margin-bottom:2.5rem;}
    .hero-cta{display:flex;gap:1rem;flex-wrap:wrap;}
    .hero-logo-wrap{position:relative;z-index:1;display:flex;justify-content:center;align-items:center;}
    .hero-logo-wrap img{width:min(420px,90%);filter:drop-shadow(0 0 40px rgba(168,85,247,.6));animation:floatY 5s ease-in-out infinite;}
    .hero-logo-glow{position:absolute;width:320px;height:320px;background:radial-gradient(circle,rgba(168,85,247,.35) 0%,transparent 70%);border-radius:50%;animation:pulseGlow 3s ease-in-out infinite;}
    .stats-bar{background:var(--v-card);border-top:1px solid var(--v-border);border-bottom:1px solid var(--v-border);padding:1.5rem 0;}
    .stat-item{text-align:center;}
    .stat-num{font-family:var(--font-display);font-size:2rem;font-weight:800;color:var(--v-lavender);text-shadow:0 0 10px rgba(168,85,247,.5);}
    .stat-label{font-family:var(--font-ui);font-size:.8rem;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);}
    .price-section{padding:6rem 0;}
    .price-card{background:var(--v-card);border-radius:16px;padding:2.5rem;position:relative;overflow:hidden;}
    .price-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;}
    .price-card.ps4::before{background:linear-gradient(90deg,var(--v-purple),var(--v-violet));}
    .price-card.ps5::before{background:linear-gradient(90deg,#3b82f6,#60a5fa);}
    .price-card-title{font-family:var(--font-display);font-size:2rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;margin-bottom:.25rem;}
    .price-card.ps4 .price-card-title{color:var(--v-lavender);}
    .price-card.ps5 .price-card-title{color:#60a5fa;}
    .price-tag{font-family:var(--font-ui);font-size:.8rem;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);margin-bottom:2rem;}
    .price-row{display:flex;justify-content:space-between;align-items:center;padding:.75rem 0;border-bottom:1px solid rgba(255,255,255,.05);font-family:var(--font-ui);font-size:1.05rem;}
    .price-row:last-child{border-bottom:none;}
    .price-row .label{color:#9d8bb0;}.price-row .price{font-weight:700;color:var(--v-white);}
    .price-row.best .label{color:var(--v-lavender);}.price-row.best .price{color:var(--v-lavender);text-shadow:0 0 8px var(--v-violet);}
    .best-badge{background:rgba(168,85,247,.2);border:1px solid rgba(168,85,247,.4);color:var(--v-lavender);font-size:.7rem;letter-spacing:1.5px;padding:.15rem .5rem;border-radius:4px;font-family:var(--font-ui);font-weight:700;text-transform:uppercase;margin-left:.5rem;}
    .price-note{margin-top:1.5rem;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:8px;padding:.75rem 1rem;font-size:.8rem;color:#f87171;font-family:var(--font-ui);letter-spacing:1px;text-transform:uppercase;text-align:center;}
    .units-section{padding:6rem 0;background:rgba(255,255,255,.015);}
    .unit-tabs{display:flex;gap:.75rem;margin-bottom:2.5rem;flex-wrap:wrap;}
    .unit-tab{font-family:var(--font-ui);font-size:.85rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:.5rem 1.25rem;border-radius:6px;border:1px solid var(--v-border);background:transparent;color:var(--v-muted);cursor:pointer;transition:all .2s;}
    .unit-tab:hover{border-color:var(--v-violet);color:var(--v-lavender);}
    .unit-tab.active{background:rgba(168,85,247,.15);border-color:var(--v-violet);color:var(--v-lavender);}
    .units-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:1.25rem;}
    .unit-card{background:var(--v-card);border:1px solid var(--v-border);border-radius:14px;padding:1.5rem;cursor:pointer;transition:all .3s;position:relative;overflow:hidden;}
    .unit-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,var(--v-purple),var(--v-violet));opacity:0;transition:opacity .3s;}
    .unit-card:hover{border-color:var(--v-purple);box-shadow:0 8px 30px rgba(123,47,190,.25);transform:translateY(-4px);}
    .unit-card:hover::before{opacity:1;}
    .unit-card.disewa{opacity:.5;cursor:not-allowed;pointer-events:none;}
    .unit-icon{font-size:2rem;margin-bottom:.75rem;}
    .unit-name{font-family:var(--font-display);font-size:1rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:var(--v-white);margin-bottom:.5rem;}
    .unit-meta{display:flex;gap:.5rem;align-items:center;margin-bottom:.75rem;flex-wrap:wrap;}
    .unit-card::after{content:'Lihat Game →';position:absolute;bottom:0;left:0;right:0;background:linear-gradient(135deg,var(--v-purple),var(--v-violet));color:#fff;font-family:var(--font-ui);font-size:.75rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;text-align:center;padding:.6rem;opacity:0;transform:translateY(100%);transition:opacity .25s,transform .25s;}
    .unit-card:not(.disewa):hover::after{opacity:1;transform:translateY(0);}
    .games-section{padding:6rem 0;}
    .game-card{background:var(--v-card);border:1px solid var(--v-border);border-radius:12px;overflow:hidden;transition:transform .3s,box-shadow .3s,border-color .3s;}
    .game-card:hover{transform:translateY(-6px);border-color:var(--v-purple);box-shadow:0 12px 40px rgba(123,47,190,.3);}
    .game-card img{width:100%;height:200px;object-fit:cover;display:block;}
    .game-card-body{padding:.75rem 1rem;font-family:var(--font-ui);font-size:.95rem;font-weight:600;color:#C4B5D4;}
    .col-6game{flex:1 1 160px;max-width:calc(16.66% - 1.25rem);}
    .sewa-section{padding:6rem 0;position:relative;overflow:hidden;}
    .sewa-section::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse 60% 80% at 50% 50%,rgba(123,47,190,.1) 0%,transparent 70%);pointer-events:none;}
    .sewa-feature{display:flex;gap:1rem;align-items:flex-start;margin-bottom:1.5rem;}
    .sewa-icon{width:44px;height:44px;flex-shrink:0;background:rgba(168,85,247,.12);border:1px solid rgba(168,85,247,.25);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.2rem;}
    .sewa-feature-text h6{font-family:var(--font-ui);font-size:1rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--v-white);margin-bottom:.25rem;}
    .sewa-feature-text p{font-size:.85rem;color:var(--v-muted);line-height:1.5;}
    .map-section{padding:6rem 0;}
    .map-wrap{border-radius:16px;overflow:hidden;border:1px solid var(--v-border);box-shadow:0 0 40px rgba(123,47,190,.2);}
    .map-wrap iframe{display:block;width:100%;height:420px;border:none;filter:invert(90%) hue-rotate(180deg);}
    .map-info{background:var(--v-card);border:1px solid var(--v-border);border-radius:16px;padding:2rem;}
    .map-info h4{font-family:var(--font-display);font-size:1.6rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:var(--v-lavender);margin-bottom:1.5rem;}
    .map-detail{display:flex;gap:.85rem;margin-bottom:1.25rem;align-items:flex-start;}
    .map-detail-icon{font-size:1.3rem;margin-top:.1rem;}
    .map-detail-text p{font-size:.85rem;color:var(--v-muted);line-height:1.5;}
    .map-detail-text strong{font-family:var(--font-ui);font-size:.95rem;color:var(--v-white);display:block;margin-bottom:.2rem;}
    .v-footer{background:var(--v-dark);border-top:1px solid var(--v-border);padding:4rem 0 2rem;}
    .footer-copy{color:var(--v-muted);font-size:.8rem;text-align:center;margin-top:3rem;padding-top:2rem;border-top:1px solid var(--v-border);}
    .ig-btn,.wa-btn{display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:10px;border:1px solid rgba(168,85,247,.35);background:rgba(168,85,247,.08);color:var(--v-violet);transition:all .25s;text-decoration:none;}
    .ig-btn:hover{background:linear-gradient(135deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888);border-color:transparent;color:#fff;box-shadow:0 0 20px rgba(220,39,67,.4);transform:translateY(-2px);}
    .wa-btn:hover{background:#25d366;border-color:transparent;color:#fff;box-shadow:0 0 20px rgba(37,211,102,.4);transform:translateY(-2px);}
    .modal-overlay{position:fixed;inset:0;z-index:300;background:rgba(0,0,0,.8);backdrop-filter:blur(8px);display:none;align-items:center;justify-content:center;padding:1.5rem;}
    .modal-overlay.open{display:flex;}
    .modal-box{background:var(--v-card);border:1px solid var(--v-border);border-radius:20px;width:100%;max-width:660px;max-height:88vh;overflow-y:auto;position:relative;animation:fadeUp .3s ease both;}
    .modal-header{padding:2rem 2rem 1.25rem;border-bottom:1px solid var(--v-border);display:flex;justify-content:space-between;align-items:flex-start;position:sticky;top:0;background:var(--v-card);z-index:1;}
    .modal-unit-name{font-family:var(--font-display);font-size:1.5rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;color:var(--v-lavender);}
    .modal-close-btn{background:rgba(255,255,255,.05);border:1px solid var(--v-border);border-radius:8px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1.1rem;color:var(--v-muted);flex-shrink:0;transition:color .2s;}
    .modal-close-btn:hover{color:var(--v-white);}
    .modal-body{padding:1.5rem 2rem 2rem;}
    .modal-games-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(130px,1fr));gap:1rem;margin-top:1rem;}
    .modal-game-item{background:rgba(255,255,255,.03);border:1px solid var(--v-border);border-radius:10px;overflow:hidden;transition:border-color .2s;}
    .modal-game-item:hover{border-color:var(--v-purple);}
    .modal-game-item img{width:100%;height:100px;object-fit:cover;display:block;}
    .modal-game-item span{display:block;padding:.5rem .6rem;font-family:var(--font-ui);font-size:.78rem;font-weight:600;color:#C4B5D4;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
    .modal-empty{text-align:center;padding:3rem;color:var(--v-muted);font-family:var(--font-ui);font-size:.9rem;letter-spacing:1px;}
    .container{max-width:1200px;margin:0 auto;padding:0 1.5rem;}
    .row{display:flex;flex-wrap:wrap;gap:1.5rem;}
    .col-half{flex:1 1 400px;}
    @media(max-width:768px){.col-6game{max-width:calc(50% - .75rem);}.hero-logo-wrap{display:none;}.units-grid{grid-template-columns:repeat(auto-fill,minmax(160px,1fr));}}
  </style>
</head>
<body>

<nav class="v-navbar">
  <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
    <a href="index.php" class="brand"><img src="assets/images/logo-violet.jpeg" alt="Violet PlayStation">VIOLET <span class="neon" style="margin-left:.3rem;">PLAYSTATION</span></a>
    <div class="nav-links">
      <a href="#harga">Harga</a>
      <a href="#unit">Unit</a>
      <a href="#games">Game</a>
      <a href="#lokasi">Lokasi</a>
<a href="sewa.php" style="padding:.5rem 1.5rem;font-size:.85rem;font-family:var(--font-display);font-weight:700;letter-spacing:2px;text-transform:uppercase;border-radius:6px;text-decoration:none;background:var(--v-lavender);color:#1a0030;box-shadow:0 0 18px rgba(192,132,252,.55),0 0 40px rgba(168,85,247,.25);transition:box-shadow .2s,transform .2s;display:inline-block;position:relative;z-index:1;" onmouseover="this.style.boxShadow='0 0 28px rgba(192,132,252,.9),0 0 60px rgba(168,85,247,.5)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 0 18px rgba(192,132,252,.55),0 0 40px rgba(168,85,247,.25)';this.style.transform=''">Sewa Unit</a>
    </div>
  </div>
</nav>

<section class="hero">
  <div class="hero-bg"></div><div class="hero-grid-lines"></div>
  <div class="container" style="display:flex;align-items:center;gap:2rem;width:100%;">
    <div class="hero-content col-half animate-fade-up">
      <div class="hero-eyebrow">🎮 Jagakarsa · Jakarta Selatan</div>
      <h1 class="hero-title">READY<br>TO <span class="line2">PLAY?</span></h1>
      <p class="hero-sub">Rental PlayStation terpercaya. PS4, PS5 & Nintendo tersedia — main di tempat atau sewa bawa pulang.</p>
      <div class="hero-cta">
        <a href="#harga" class="btn-violet"><span>Lihat Harga</span></a>
        <a href="sewa.php" class="btn-violet" style="background:rgba(168,85,247,.15);border:1px solid rgba(168,85,247,.4);box-shadow:none;"><span>Sewa Bawa Pulang →</span></a>
      </div>
    </div>
    <div class="hero-logo-wrap col-half"><div class="hero-logo-glow"></div><img src="assets/images/logo-violet.jpeg" alt="Violet PlayStation Logo"></div>
  </div>
</section>

<div class="stats-bar">
  <div class="container">
    <div class="row" style="justify-content:center;gap:3rem;">
      <?php
      $total_unit = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as c FROM units"))['c'];
      $unit_tersedia = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as c FROM units WHERE status='Tersedia'"))['c'];
      $total_game = mysqli_fetch_assoc(mysqli_query($koneksi,"SELECT COUNT(*) as c FROM games"))['c'];
      ?>
      <div class="stat-item"><div class="stat-num"><?php echo $total_unit; ?></div><div class="stat-label">Total Unit</div></div>
      <div class="stat-item"><div class="stat-num"><?php echo $unit_tersedia; ?></div><div class="stat-label">Tersedia</div></div>
      <div class="stat-item"><div class="stat-num"><?php echo $total_game; ?>+</div><div class="stat-label">Koleksi Game</div></div>
      <div class="stat-item"><div class="stat-num">Jagakarsa</div><div class="stat-label">Area Layanan</div></div>
    </div>
  </div>
</div>

<section class="price-section" id="harga">
  <div class="container">
    <div class="section-title">DAFTAR <span class="neon">HARGA</span></div>
    <div class="v-divider"></div>
    <p style="color:var(--v-muted);margin-bottom:3rem;font-size:.9rem;">Harga berlaku untuk sesi Main di Tempat</p>
    <div class="row">
      <div class="col-half"><div class="price-card ps4">
        <span class="v-badge v-badge-ps4" style="margin-bottom:.75rem;display:inline-block;">Console</span>
        <div class="price-card-title">PlayStation 4</div><div class="price-tag">Main di Tempat · Per Sesi</div>
        <div class="price-row"><span class="label">1 Jam</span><span class="price">Rp 8.000</span></div>
        <div class="price-row"><span class="label">2 Jam</span><span class="price">Rp 15.000</span></div>
        <div class="price-row best"><span class="label">3 Jam <span class="best-badge">Best Value</span></span><span class="price">Rp 20.000</span></div>
        <div class="price-row"><span class="label">5 Jam</span><span class="price">Rp 35.000</span></div>
        <div class="price-note">⚠ Waktu tidak dapat disimpan / dipause</div>
      </div></div>
      <div class="col-half"><div class="price-card ps5" style="border-color:rgba(96,165,250,.25);">
        <span class="v-badge v-badge-ps5" style="margin-bottom:.75rem;display:inline-block;">Next-Gen</span>
        <div class="price-card-title" style="color:#60a5fa;">PlayStation 5</div><div class="price-tag">Main di Tempat · Per Sesi</div>
        <div class="price-row"><span class="label">1 Jam</span><span class="price">Rp 15.000</span></div>
        <div class="price-row"><span class="label">2 Jam</span><span class="price">Rp 28.000</span></div>
        <div class="price-row best"><span class="label" style="color:#60a5fa;">3 Jam <span class="best-badge" style="background:rgba(96,165,250,.15);border-color:rgba(96,165,250,.4);color:#60a5fa;">Best Value</span></span><span class="price" style="color:#60a5fa;text-shadow:0 0 8px #3b82f6;">Rp 42.000</span></div>
        <div class="price-row"><span class="label">6 Jam</span><span class="price">Rp 84.000</span></div>
        <div class="price-note" style="border-color:rgba(96,165,250,.2);color:#60a5fa;background:rgba(96,165,250,.08);">⚠ Waktu tidak dapat disimpan / dipause</div>
      </div></div>
    </div>
  </div>
</section>

<section class="units-section" id="unit">
  <div class="container">
    <div class="section-title">CEK <span class="neon">UNIT</span></div>
    <div class="v-divider"></div>
    <p style="color:var(--v-muted);margin-bottom:2rem;font-size:.9rem;">Klik unit untuk melihat game yang tersedia</p>
    <div class="unit-tabs">
      <button class="unit-tab active" onclick="filterUnit('semua',this)">Semua</button>
      <button class="unit-tab" onclick="filterUnit('Main di Tempat',this)">Main di Tempat</button>
      <button class="unit-tab" onclick="filterUnit('Sewa Luar',this)">Sewa Bawa Pulang</button>
    </div>
    <div class="units-grid">
      <?php
      $q=mysqli_query($koneksi,"SELECT u.*, COUNT(ug.id_game) as jumlah_game FROM units u LEFT JOIN unit_games ug ON u.id_unit=ug.id_unit GROUP BY u.id_unit ORDER BY u.tipe_layanan DESC, u.nama_unit ASC");
      while($u=mysqli_fetch_assoc($q)):
        $kat=$u['kategori'];
        $bc=$kat==='PS5'?'v-badge-ps5':($kat==='Nintendo'?'v-badge-nin':'v-badge-ps4');
        $disewa=$u['status']==='Disewa';
      ?>
      <div class="unit-card <?php echo $disewa?'disewa':''; ?>" data-tipe="<?php echo htmlspecialchars($u['tipe_layanan']); ?>"
        <?php if(!$disewa): ?>onclick="bukaUnit(<?php echo $u['id_unit']; ?>,'<?php echo htmlspecialchars(addslashes($u['nama_unit'])); ?>','<?php echo $kat; ?>','<?php echo $u['tipe_layanan']; ?>')"<?php endif; ?>>
        <div class="unit-icon"><?php echo $kat==='Nintendo'?'🕹️':'🎮'; ?></div>
        <div class="unit-name"><?php echo htmlspecialchars($u['nama_unit']); ?></div>
        <div class="unit-meta">
          <span class="v-badge <?php echo $bc; ?>"><?php echo $kat; ?></span>
          <?php if($disewa): ?>
          <span class="v-badge" style="background:rgba(251,191,36,.15);color:#fbbf24;border:1px solid rgba(251,191,36,.3);">Disewa</span>
          <?php else: ?>
          <span class="v-badge" style="background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);">Tersedia</span>
          <?php endif; ?>
        </div>

      </div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<section class="games-section" id="games">
  <div class="container">
    <div class="section-title">KOLEKSI <span class="neon">GAME</span></div>
    <div class="v-divider"></div>
    <div class="row" style="margin-top:.5rem;">
      <?php
      $q=mysqli_query($koneksi,"SELECT DISTINCT g.id_game,g.judul_game,g.foto_game,g.kategori_game FROM games g JOIN unit_games ug ON g.id_game=ug.id_game ORDER BY g.judul_game ASC");
      while($g=mysqli_fetch_assoc($q)):
        $kat=$g['kategori_game']??'';
        $bc=$kat==='PS5'?'v-badge-ps5':($kat==='Nintendo'?'v-badge-nin':'v-badge-ps4');
      ?>
      <div class="col-6game"><div class="game-card">
        <img src="uploads/games/<?php echo htmlspecialchars($g['foto_game']); ?>" alt="<?php echo htmlspecialchars($g['judul_game']); ?>">
        <div class="game-card-body">
          <?php if($kat): ?><span class="v-badge <?php echo $bc; ?>" style="font-size:.65rem;padding:.1rem .4rem;margin-bottom:.4rem;display:inline-block;"><?php echo $kat; ?></span><?php endif; ?>
          <div style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo htmlspecialchars($g['judul_game']); ?></div>
        </div>
      </div></div>
      <?php endwhile; ?>
    </div>
  </div>
</section>

<section class="sewa-section" id="sewa">
  <div class="container">
    <div class="row" style="align-items:center;gap:4rem;">
      <div class="col-half">
        <div class="hero-eyebrow">Layanan Sewa</div>
        <div class="section-title">SEWA <span class="neon">BAWA<br>PULANG</span></div>
        <div class="v-divider"></div>
        <div class="sewa-feature"><div class="sewa-icon">🏪</div><div class="sewa-feature-text"><h6>Ambil di Toko</h6><p>Kamu datang langsung ke toko kami di Jagakarsa untuk mengambil unit PS yang sudah dipesan.</p></div></div>
        <div class="sewa-feature"><div class="sewa-icon">🪪</div><div class="sewa-feature-text"><h6>Jaminan KTP / STNK</h6><p>Dokumen asli diserahkan saat pengambilan unit sebagai jaminan.</p></div></div>
        <div class="sewa-feature"><div class="sewa-icon">📅</div><div class="sewa-feature-text"><h6>Durasi Fleksibel</h6><p>Tersedia pilihan 1, 2, atau 3 hari sesuai kebutuhan.</p></div></div>
        <a href="sewa.php" class="btn-violet" style="display:inline-block;text-decoration:none;margin-top:1rem;"><span>Ajukan Sewa Sekarang →</span></a>
      </div>
      <div class="col-half"><div class="v-card" style="padding:2.5rem;">
        <div style="font-family:var(--font-display);font-size:1.1rem;font-weight:700;letter-spacing:2px;color:var(--v-muted);text-transform:uppercase;margin-bottom:1.5rem;">Unit Sewa Tersedia</div>
        <?php $units=mysqli_query($koneksi,"SELECT * FROM units WHERE tipe_layanan='Sewa Luar' AND status='Tersedia' ORDER BY kategori");
        while($u=mysqli_fetch_assoc($units)): $kat=$u['kategori']; $bc=$kat==='PS5'?'v-badge-ps5':($kat==='Nintendo'?'v-badge-nin':'v-badge-ps4'); ?>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.7rem 0;border-bottom:1px solid var(--v-border);">
          <span style="font-family:var(--font-ui);font-size:.95rem;color:#C4B5D4;"><?php echo htmlspecialchars($u['nama_unit']); ?></span>
          <span class="v-badge <?php echo $bc; ?>"><?php echo $kat; ?></span>
        </div>
        <?php endwhile; ?>
      </div></div>
    </div>
  </div>
</section>

<section class="map-section" id="lokasi">
  <div class="container">
    <div class="section-title">LOKASI <span class="neon">KAMI</span></div>
    <div class="v-divider"></div>
    <div class="row" style="align-items:stretch;margin-top:1rem;">
      <div class="col-half"><div class="map-info">
        <h4>📍 Violet PlayStation</h4>
        <div class="map-detail"><div class="map-detail-icon">🗺️</div><div class="map-detail-text"><strong>Alamat</strong><p>Jl. Jagakarsa II No.5D, RT.1/RW.7, Jagakarsa, Kec. Jagakarsa, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12620, Indonesia</p></div></div>
        <div class="map-detail"><div class="map-detail-icon">🕐</div><div class="map-detail-text"><strong>Jam Operasional</strong><p>Setiap hari · Hubungi via WhatsApp untuk info lebih lanjut</p></div></div>
        <div class="map-detail"><div class="map-detail-icon">📱</div><div class="map-detail-text"><strong>WhatsApp</strong><p>0858-4783-1078</p></div></div>
        <a href="https://wa.me/6285847831078" target="_blank" class="btn-violet" style="display:inline-flex;align-items:center;gap:.5rem;text-decoration:none;margin-top:1.5rem;width:100%;justify-content:center;"><span>💬 Chat WhatsApp</span></a>
      </div></div>
      <div class="col-half"><div class="map-wrap" style="height:100%;min-height:350px;">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.5!2d106.8198065!3d-6.3269265!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ed005178c647%3A0x884731391d96c010!2sViolet%20PlayStation!5e0!3m2!1sid!2sid!4v1" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
      </div></div>
    </div>
  </div>
</section>

<footer class="v-footer">
  <div class="container">
    <div style="display:flex;flex-wrap:wrap;gap:3rem;justify-content:space-between;">
      <div>
        <div style="font-family:var(--font-display);font-size:2rem;font-weight:800;letter-spacing:4px;text-transform:uppercase;">VIOLET <span class="neon">PLAYSTATION</span></div>
        <div style="color:var(--v-muted);font-size:.9rem;margin-top:.5rem;">Best Gaming Experience di Jagakarsa</div>
        <div style="display:flex;gap:.75rem;margin-top:1.5rem;align-items:center;">
        <a href="https://wa.me/6285847831078" target="_blank" class="wa-btn" title="WhatsApp Violet PlayStation"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>
        <a href="https://www.instagram.com/violetplaystation/" target="_blank" class="ig-btn" title="Instagram" style="width:44px;height:44px;"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a>
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
        </div>
      </div>
    </div>
    <div class="footer-copy">© 2026 Violet PlayStation. All Rights Reserved.</div>
  </div>
</footer>

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
        <a href="sewa.php" class="btn-violet" style="display:block;text-align:center;text-decoration:none;"><span>🎮 Sewa Unit Ini Sekarang</span></a>
      </div>
    </div>
  </div>
</div>

<?php
$all_unit_games = [];
$qug = mysqli_query($koneksi,"SELECT ug.id_unit,g.judul_game,g.foto_game,g.kategori_game FROM unit_games ug JOIN games g ON ug.id_game=g.id_game");
while($row=mysqli_fetch_assoc($qug)) $all_unit_games[$row['id_unit']][]=$row;
?>
<script>
const unitGames = <?php echo json_encode($all_unit_games); ?>;

function bukaUnit(id, nama, kat, tipe) {
  document.getElementById('modal-nama').textContent = nama;
  document.getElementById('modal-meta').textContent = kat + ' · ' + tipe;
  const games = unitGames[id] || [];
  const el = document.getElementById('modal-games');
  if (!games.length) {
    el.innerHTML = '<div class="modal-empty">🎮 Belum ada game di unit ini.</div>';
  } else {
    let h = '<div class="modal-games-grid">';
    games.forEach(g => {
      h += `<div class="modal-game-item"><img src="uploads/games/${g.foto_game}" alt="${g.judul_game}" onerror="this.parentElement.querySelector('img').style.display='none'"><span>${g.judul_game}</span></div>`;
    });
    h += '</div>';
    el.innerHTML = h;
  }
  document.getElementById('modal-sewa-wrap').style.display = tipe==='Sewa Luar' ? 'block' : 'none';
  document.getElementById('modalUnit').classList.add('open');
  document.body.style.overflow = 'hidden';
}

function tutupModal() {
  document.getElementById('modalUnit').classList.remove('open');
  document.body.style.overflow = '';
}

document.getElementById('modalUnit').addEventListener('click', e => { if(e.target===document.getElementById('modalUnit')) tutupModal(); });

function filterUnit(tipe, btn) {
  document.querySelectorAll('.unit-tab').forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
  document.querySelectorAll('.unit-card').forEach(c => {
    c.style.display = (tipe==='semua' || c.dataset.tipe===tipe) ? '' : 'none';
  });
}
</script>

<script>
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
</script>
</body>
</html>