<?php include 'config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Violet PlayStation — Sewa PS & Playbox Jagakarsa</title>
  <meta name="description" content="Sewa PS4, PS5, Nintendo Switch & Playbox di Jagakarsa, Jakarta Selatan. Bawa pulang harian, harga terjangkau, promo weekday!">
  <meta property="og:title" content="Violet PlayStation — Sewa PS & Playbox Jagakarsa">
  <meta property="og:description" content="Sewa PS4, PS5, Nintendo Switch & Playbox harian. Booking H-1 via WA. Promo weekday: sewa 2 hari gratis 1 hari!">
  <meta property="og:image" content="https://violetplaystation.com/assets/images/logo-violet.jpeg">
  <meta property="og:type" content="website">
  <meta name="theme-color" content="#7B2FBE">
  <link rel="stylesheet" href="assets/css/violet.css">
  <style>
    .hero{min-height:100vh;display:flex;align-items:center;position:relative;overflow:hidden;padding:6rem 0 4rem;}
    .hero-bg{position:absolute;inset:0;z-index:0;background:radial-gradient(ellipse 70% 60% at 70% 50%,rgba(123,47,190,.25) 0%,transparent 70%),radial-gradient(ellipse 40% 40% at 20% 80%,rgba(168,85,247,.1) 0%,transparent 60%),var(--v-black);}
    .hero-grid-lines{position:absolute;inset:0;z-index:0;background-image:linear-gradient(rgba(123,47,190,.06) 1px,transparent 1px),linear-gradient(90deg,rgba(123,47,190,.06) 1px,transparent 1px);background-size:60px 60px;}
    .hero-content{position:relative;z-index:1;}
    .hero-eyebrow{font-family:var(--font-ui);font-size:.85rem;font-weight:700;letter-spacing:4px;text-transform:uppercase;color:var(--v-violet);border:1px solid rgba(168,85,247,.3);display:inline-block;padding:.3rem 1rem;border-radius:4px;margin-bottom:1.5rem;background:rgba(168,85,247,.08);}
    .hero-title{font-family:var(--font-display);font-size:clamp(3.5rem,10vw,7rem);font-weight:800;letter-spacing:4px;text-transform:uppercase;line-height:.95;margin-bottom:1.5rem;}
    .hero-title .line2{color:var(--v-lavender);text-shadow:0 0 30px var(--v-violet);}
    .hero-sub{font-size:1.05rem;color:var(--v-muted);max-width:480px;line-height:1.7;margin-bottom:2.5rem;}
    .hero-cta{display:flex;gap:1rem;flex-wrap:wrap;}
    .hero-logo-wrap{position:relative;z-index:1;display:flex;justify-content:center;align-items:center;}
    .hero-logo-wrap img{width:min(420px,90%);filter:drop-shadow(0 0 40px rgba(168,85,247,.6));animation:floatY 5s ease-in-out infinite;}
    .hero-logo-glow{position:absolute;width:320px;height:320px;background:radial-gradient(circle,rgba(168,85,247,.35) 0%,transparent 70%);border-radius:50%;animation:pulseGlow 3s ease-in-out infinite;}
    .stats-bar{background:var(--v-card);border-top:1px solid var(--v-border);border-bottom:1px solid var(--v-border);padding:1.5rem 0;}
    .stat-item{text-align:center;}
    .stat-num{font-family:var(--font-display);font-size:2rem;font-weight:800;color:var(--v-lavender);text-shadow:0 0 10px rgba(168,85,247,.5);}
    .stat-label{font-family:var(--font-ui);font-size:.8rem;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);}
    .price-section{padding:6rem 0;}
    .price-tab-nav{display:flex;gap:.6rem;margin-bottom:2.5rem;flex-wrap:wrap;border-bottom:1px solid var(--v-border);padding-bottom:1rem;}
    .price-tab-btn{font-family:var(--font-ui);font-size:.85rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:.5rem 1.4rem;border-radius:8px;border:1px solid var(--v-border);background:transparent;color:var(--v-muted);cursor:pointer;transition:all .2s;}
    .price-tab-btn:hover{border-color:var(--v-violet);color:var(--v-lavender);}
    .price-tab-btn.active{background:rgba(168,85,247,.15);border-color:var(--v-violet);color:var(--v-lavender);}
    .price-tab-panel{display:none;animation:fadeUp .3s ease both;}
    .price-tab-panel.active{display:block;}
    .price-card{background:var(--v-card);border-radius:16px;padding:2.5rem;position:relative;overflow:hidden;}
    .price-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;}
    .price-card.ps4::before{background:linear-gradient(90deg,var(--v-purple),var(--v-violet));}
    .price-card.ps5::before{background:linear-gradient(90deg,#3b82f6,#60a5fa);}
    .price-card.nin::before{background:linear-gradient(90deg,#ef4444,#f87171);}
    .price-card.playbox::before{background:linear-gradient(90deg,#10b981,#34d399);}
    .price-card-title{font-family:var(--font-display);font-size:2rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;margin-bottom:.25rem;}
    .price-card.ps4 .price-card-title{color:var(--v-lavender);}
    .price-card.ps5 .price-card-title{color:#60a5fa;}
    .price-card.nin .price-card-title{color:#f87171;}
    .price-card.playbox .price-card-title{color:#34d399;}
    .price-tag{font-family:var(--font-ui);font-size:.8rem;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);margin-bottom:2rem;}
    .price-row{display:flex;justify-content:space-between;align-items:center;padding:.75rem 0;border-bottom:1px solid rgba(255,255,255,.05);font-family:var(--font-ui);font-size:1.05rem;}
    .price-row:last-child{border-bottom:none;}
    .price-row .label{color:#9d8bb0;}
    .price-row .price{font-weight:700;color:var(--v-white);}
    .free-badge{font-size:.72rem;font-family:var(--font-ui);font-weight:700;letter-spacing:1px;color:#fbbf24;background:rgba(251,191,36,.12);border:1px solid rgba(251,191,36,.3);padding:.1rem .5rem;border-radius:4px;margin-left:.5rem;white-space:nowrap;}
    .price-note{margin-top:1.5rem;background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:8px;padding:.75rem 1rem;font-size:.8rem;color:#f87171;font-family:var(--font-ui);letter-spacing:.5px;text-align:center;}
    .price-note.blue{border-color:rgba(96,165,250,.2);color:#93c5fd;background:rgba(96,165,250,.07);}
    .price-note.green{border-color:rgba(16,185,129,.2);color:#34d399;background:rgba(16,185,129,.06);}
    .promo-banner{background:linear-gradient(135deg,rgba(251,191,36,.12),rgba(245,158,11,.08));border:1px solid rgba(251,191,36,.3);border-radius:12px;padding:1rem 1.5rem;display:flex;align-items:center;gap:1rem;margin-bottom:2rem;flex-wrap:wrap;}
    .promo-banner-text{font-family:var(--font-ui);font-size:.9rem;font-weight:700;letter-spacing:1px;color:#fbbf24;}
    .promo-banner-sub{font-family:var(--font-body);font-size:.82rem;color:#d97706;margin-top:.1rem;}
    .syarat-box{background:rgba(123,47,190,.06);border:1px solid rgba(168,85,247,.2);border-radius:12px;padding:1.5rem;margin-top:2rem;}
    .syarat-box h6{font-family:var(--font-ui);font-size:.85rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-lavender);margin-bottom:1rem;}
    .syarat-list{list-style:none;display:flex;flex-direction:column;gap:.5rem;}
    .syarat-list li{font-size:.82rem;color:var(--v-muted);padding-left:1.2rem;position:relative;line-height:1.5;}
    .syarat-list li::before{content:'—';position:absolute;left:0;color:var(--v-purple);}
    .syarat-list li strong{color:#C4B5D4;}

    .units-section{padding:6rem 0;background:rgba(255,255,255,.015);}
    .unit-tabs{display:flex;gap:.75rem;margin-bottom:2rem;flex-wrap:wrap;}
    .unit-tab{font-family:var(--font-ui);font-size:.85rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:.5rem 1.25rem;border-radius:6px;border:1px solid var(--v-border);background:transparent;color:var(--v-muted);cursor:pointer;transition:all .2s;}
    .unit-tab:hover{border-color:var(--v-violet);color:var(--v-lavender);}
    .unit-tab.active{background:rgba(168,85,247,.15);border-color:var(--v-violet);color:var(--v-lavender);}
    .units-panel{display:none;animation:fadeUp .3s ease both;}
    .units-panel.active{display:block;}
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
    .ps5-note{background:rgba(96,165,250,.08);border:1px solid rgba(96,165,250,.2);border-radius:10px;padding:.85rem 1.25rem;font-family:var(--font-ui);font-size:.82rem;color:#93c5fd;margin-bottom:1.5rem;display:flex;align-items:flex-start;gap:.75rem;}
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
    .playbox-warning{background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.3);border-radius:14px;padding:1.25rem 1.5rem;display:flex;gap:1.25rem;align-items:flex-start;margin-bottom:1.5rem;color:#fbbf24;}
    .same-price-badge{position:absolute;top:1rem;right:1rem;font-family:var(--font-ui);font-size:.68rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;background:rgba(251,191,36,.12);color:#fbbf24;border:1px solid rgba(251,191,36,.35);padding:.2rem .6rem;border-radius:20px;white-space:nowrap;}
    .playbox-intro{background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.2);border-radius:14px;padding:1.25rem 1.5rem;display:flex;gap:1.25rem;align-items:flex-start;margin-bottom:1.75rem;}
    .playbox-intro-icon{font-size:2.2rem;flex-shrink:0;margin-top:.1rem;}
    .playbox-intro-title{font-family:var(--font-ui);font-size:1rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#34d399;margin-bottom:.4rem;}
    .playbox-intro-desc{font-size:.85rem;color:var(--v-muted);line-height:1.6;}
    .playbox-intro-desc strong{color:#C4B5D4;}
    /* Playbox intro */
    .playbox-intro{display:flex;gap:1.25rem;align-items:flex-start;background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.2);border-radius:14px;padding:1.5rem;margin-bottom:1.5rem;}
    .playbox-intro-icon{font-size:2.5rem;flex-shrink:0;}
    .playbox-intro-title{font-family:var(--font-ui);font-size:1rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#34d399;margin-bottom:.5rem;}
    .playbox-intro-desc{font-size:.88rem;color:var(--v-muted);line-height:1.6;}
    .playbox-intro-desc strong{color:#C4B5D4;}
    .playbox-features{display:flex;flex-wrap:wrap;gap:.6rem;margin-bottom:2rem;}
    .pb-feat{display:flex;align-items:center;gap:.5rem;background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.15);border-radius:8px;padding:.4rem .9rem;font-family:var(--font-ui);font-size:.82rem;font-weight:600;color:#6ee7b7;letter-spacing:.5px;}
    .pb-feat-icon{font-size:1rem;}
    
    .playbox-warning{background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.3);border-radius:14px;padding:1.25rem 1.5rem;display:flex;gap:1.25rem;align-items:flex-start;margin-bottom:1.5rem;color:#fbbf24;}
    .same-price-badge{font-family:var(--font-ui);font-size:.7rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:.18rem .6rem;border-radius:4px;background:rgba(251,191,36,.1);border:1px solid rgba(251,191,36,.25);color:#fbbf24;white-space:nowrap;}
    @media(max-width:768px){
      .col-6game{max-width:calc(50% - .75rem);}
      .hero-logo-wrap{display:none;}
      .units-grid{grid-template-columns:repeat(2,1fr);}
      .price-tab-btn{font-size:.75rem;padding:.4rem .9rem;letter-spacing:.8px;}
      .nin-center-wrap{max-width:100% !important;}
      .playbox-intro{flex-direction:column;gap:.75rem;}
    }
  </style>
</head>
<body>

<!-- SVG Icon Sprite -->
<svg style="display:none" xmlns="http://www.w3.org/2000/svg">
  <!-- home (filled style) -->
  <symbol id="ico-home" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z"/><polyline points="9 21 9 12 15 12 15 21"/>
  </symbol>
  <!-- gamepad/stick -->
  <symbol id="ico-gamepad" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <rect x="2" y="6" width="20" height="12" rx="4"/><line x1="8" y1="10" x2="8" y2="14"/><line x1="6" y1="12" x2="10" y2="12"/><circle cx="16" cy="10.5" r=".8" fill="currentColor" stroke="none"/><circle cx="18.5" cy="12" r=".8" fill="currentColor" stroke="none"/><circle cx="16" cy="13.5" r=".8" fill="currentColor" stroke="none"/><circle cx="13.5" cy="12" r=".8" fill="currentColor" stroke="none"/>
  </symbol>
  <!-- tag/price -->
  <symbol id="ico-tag" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/><circle cx="7" cy="7" r="1.5" fill="currentColor" stroke="none"/>
  </symbol>
  <!-- location pin -->
  <symbol id="ico-pin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/>
  </symbol>
  <!-- clock -->
  <symbol id="ico-clock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
  </symbol>
  <!-- phone/wa -->
  <symbol id="ico-phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8 19.79 19.79 0 01.01 2.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
  </symbol>
  <!-- shield/security -->
  <symbol id="ico-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
  </symbol>
  <!-- calendar -->
  <symbol id="ico-calendar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
  </symbol>
  <!-- gift/promo -->
  <symbol id="ico-gift" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5" rx="1"/><line x1="12" y1="22" x2="12" y2="7"/><path d="M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7z"/><path d="M12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"/>
  </symbol>
  <!-- store/shop -->
  <symbol id="ico-store" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M3 9l1-6h16l1 6"/><path d="M3 9a2 2 0 002 2 2 2 0 002-2 2 2 0 002 2 2 2 0 002-2 2 2 0 002 2 2 2 0 002-2"/><path d="M5 11v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/>
  </symbol>
  <!-- id card/ktp -->
  <symbol id="ico-idcard" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="8" cy="12" r="2"/><path d="M14 9h4M14 12h4M14 15h2"/>
  </symbol>
  <!-- warning triangle -->
  <symbol id="ico-warn" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
  </symbol>
  <!-- briefcase/koper -->
  <symbol id="ico-case" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/><line x1="12" y1="12" x2="12" y2="12"/><path d="M2 12h20"/>
  </symbol>
  <!-- users/2 orang -->
  <symbol id="ico-users" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
  </symbol>
  <!-- motorcycle -->
  <symbol id="ico-motor" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="5.5" cy="17.5" r="3.5"/><circle cx="18.5" cy="17.5" r="3.5"/><path d="M15 6h-3l-2 5H5.5"/><path d="M9 11l2-5h5l2 3.5"/><path d="M15 6l2 5.5"/>
  </symbol>
  <!-- monitor -->
  <symbol id="ico-monitor" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
  </symbol>
  <!-- zap/bolt (plug&play) -->
  <symbol id="ico-zap" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
  </symbol>
</svg>
<!-- NAVBAR -->
<nav class="v-navbar">
  <div class="container" style="display:flex;justify-content:space-between;align-items:center;padding:0 1.25rem;">
    <a href="index.php" class="brand">
      <img src="assets/images/logo-violet.jpeg" alt="Violet PlayStation">
      VIOLET <span class="neon" style="margin-left:.3rem;">PLAYSTATION</span>
    </a>
    <div class="nav-links">
      <a href="#harga"><svg width="16" height="16" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-tag"/></svg>Harga</a>
      <a href="#unit"><svg width="16" height="16" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-gamepad"/></svg>Unit</a>
      <a href="#games"><svg width="16" height="16" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-monitor"/></svg>Game</a>
      <a href="#lokasi"><svg width="16" height="16" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-pin"/></svg>Lokasi</a>
      <a href="sewa.php" style="padding:.5rem 1.5rem;font-size:.85rem;font-family:var(--font-display);font-weight:700;letter-spacing:2px;text-transform:uppercase;border-radius:6px;text-decoration:none;background:var(--v-lavender);color:#1a0030;box-shadow:0 0 18px rgba(192,132,252,.55),0 0 40px rgba(168,85,247,.25);transition:box-shadow .2s,transform .2s;display:inline-block;" onmouseover="this.style.boxShadow='0 0 28px rgba(192,132,252,.9),0 0 60px rgba(168,85,247,.5)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 0 18px rgba(192,132,252,.55),0 0 40px rgba(168,85,247,.25)';this.style.transform=''">Sewa Unit</a>
    </div>
    <button class="nav-hamburger" id="hamburger" aria-label="Menu" onclick="toggleDrawer()">
      <span></span><span></span><span></span>
    </button>
  </div>
</nav>
<div class="nav-drawer" id="navDrawer">
  <a href="#harga" onclick="closeDrawer()"><svg width="22" height="22" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-tag"/></svg>Harga</a>
  <a href="#unit" onclick="closeDrawer()"><svg width="22" height="22" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-gamepad"/></svg>Unit</a>
  <a href="#games" onclick="closeDrawer()"><svg width="22" height="22" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-monitor"/></svg>Game</a>
  <a href="#lokasi" onclick="closeDrawer()"><svg width="22" height="22" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-pin"/></svg>Lokasi</a>
  <div class="drawer-cta">
    <a href="sewa.php" class="btn-violet" style="display:inline-flex;align-items:center;justify-content:center;gap:.5rem;width:100%;text-decoration:none;font-size:1.1rem;padding:1rem;border-radius:10px;"><svg width="18" height="18"><use href="#ico-gamepad"/></svg><span>Sewa Unit</span></a>
  </div>
</div>

<!-- HERO -->
<section class="hero">
  <div class="hero-bg"></div><div class="hero-grid-lines"></div>
  <div class="container" style="display:flex;align-items:center;gap:2rem;width:100%;">
    <div class="hero-content col-half animate-fade-up">
      <div class="hero-eyebrow"><svg width="14" height="14" style="vertical-align:middle;margin-right:.35rem;opacity:.8"><use href="#ico-pin"/></svg>Jagakarsa · Jakarta Selatan</div>
      <h1 class="hero-title">SEWA PS<br><span class="line2">BAWA<br>PULANG</span></h1>
      <p class="hero-sub">PS4, PS5, Nintendo Switch & Playbox — sewa harian, bawa ke rumah. Booking H-1 via WhatsApp, jaminan KTP & STNK.</p>
      <div class="hero-cta">
        <a href="sewa.php" class="btn-violet" style="display:inline-flex;align-items:center;gap:.5rem;text-decoration:none;"><svg width="18" height="18"><use href="#ico-gamepad"/></svg><span>Sewa Sekarang</span></a>
        <a href="#harga" class="btn-violet" style="background:rgba(168,85,247,.15);border:1px solid rgba(168,85,247,.4);box-shadow:none;"><span>Lihat Harga →</span></a>
      </div>
    </div>
    <div class="hero-logo-wrap col-half"><div class="hero-logo-glow"></div><img src="assets/images/logo-violet.jpeg" alt="Violet PlayStation"></div>
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
      <button class="price-tab-btn active" onclick="switchPriceTab('sewa',this)"><svg width="14" height="14" style="vertical-align:middle;margin-right:.35rem"><use href="#ico-home"/></svg>Sewa Bawa Pulang</button>
      <button class="price-tab-btn" onclick="switchPriceTab('tempat',this)"><svg width="14" height="14" style="vertical-align:middle;margin-right:.35rem"><use href="#ico-gamepad"/></svg>Main di Tempat</button>
      <button class="price-tab-btn" onclick="switchPriceTab('playbox',this)"><svg width="14" height="14" style="vertical-align:middle;margin-right:.35rem"><use href="#ico-case"/></svg>Playbox</button>
    </div>

    <!-- Sewa Panel -->
    <div class="price-tab-panel active" id="panel-sewa">
      <div class="promo-banner">
        <svg width="28" height="28" style="flex-shrink:0;color:#fbbf24"><use href="#ico-gift"/></svg>
        <div>
          <div class="promo-banner-text">SPECIAL PROMO WEEKDAY — Senin s/d Kamis</div>
          <div class="promo-banner-sub">Sewa 2 hari gratis 1 hari &nbsp;·&nbsp; Sewa 3 hari gratis 2 hari</div>
        </div>
      </div>
      <!-- Baris 1: PS4 + PS5 -->
      <div class="row" style="margin-bottom:1.25rem;">
        <div class="col-half"><div class="price-card ps4">
          <span class="v-badge v-badge-ps4" style="margin-bottom:.75rem;display:inline-block;">Console</span>
          <div class="price-card-title">PlayStation 4</div><div class="price-tag">Sewa Bawa Pulang · Per Hari</div>
          <div class="price-row"><span class="label">1 Hari</span><span class="price">Rp 100.000</span></div>
          <div class="price-row"><span class="label">2 Hari <span class="free-badge">Free 1 hari</span></span><span class="price">Rp 200.000</span></div>
          <div class="price-row"><span class="label">3 Hari <span class="free-badge">Free 2 hari</span></span><span class="price">Rp 300.000</span></div>
        </div></div>
        <div class="col-half"><div class="price-card ps5" style="border-color:rgba(96,165,250,.25);">
          <span class="v-badge v-badge-ps5" style="margin-bottom:.75rem;display:inline-block;">Next-Gen</span>
          <div class="price-card-title">PlayStation 5</div><div class="price-tag">Sewa Bawa Pulang · Per Hari</div>
          <div class="price-row"><span class="label">1 Hari</span><span class="price">Rp 195.000</span></div>
          <div class="price-row"><span class="label">2 Hari <span class="free-badge">Free 1 hari</span></span><span class="price">Rp 390.000</span></div>
          <div class="price-row"><span class="label">3 Hari <span class="free-badge">Free 2 hari</span></span><span class="price">Rp 585.000</span></div>
          <div class="price-note blue">ℹ️ Unit PS5 yang disewa adalah unit yang ada di tempat — hubungi WA dulu untuk konfirmasi</div>
        </div></div>
      </div>
      <!-- Baris 2: Nintendo ditengah (segitiga terbalik) -->
      <div style="display:flex;justify-content:center;margin-bottom:2rem;">
        <div style="width:100%;max-width:calc(50% - .75rem);"><div class="price-card nin" style="border-color:rgba(248,113,113,.25);position:relative;">
          <div class="same-price-badge" style="position:absolute;top:1rem;right:1rem;">= Harga sama dengan PS4</div>
          <span class="v-badge v-badge-nin" style="margin-bottom:.75rem;display:inline-block;">Nintendo</span>
          <div class="price-card-title">Nintendo Switch</div><div class="price-tag">Sewa Bawa Pulang · Per Hari</div>
          <div class="price-row"><span class="label">1 Hari</span><span class="price">Rp 100.000</span></div>
          <div class="price-row"><span class="label">2 Hari <span class="free-badge">Free 1 hari</span></span><span class="price">Rp 200.000</span></div>
          <div class="price-row"><span class="label">3 Hari <span class="free-badge">Free 2 hari</span></span><span class="price">Rp 300.000</span></div>
        </div></div>
      </div>
      <div class="syarat-box">
        <h6><svg width="16" height="16" style="vertical-align:middle;margin-right:.4rem;"><use href="#ico-shield"/></svg>Syarat & Ketentuan Sewa</h6>
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
        <div class="col-half"><div class="price-card ps5" style="border-color:rgba(96,165,250,.25);">
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

      <!-- Playbox intro card -->
      <div style="background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.2);border-radius:14px;padding:1.5rem 2rem;margin-bottom:2rem;display:flex;gap:1.5rem;align-items:flex-start;flex-wrap:wrap;">
        <div class="playbox-intro-icon"><svg width="36" height="36"><use href="#ico-case"/></svg></div>
        <div>
          <div style="font-family:var(--font-display);font-size:1.2rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:#34d399;margin-bottom:.5rem;">Apa itu Playbox?</div>
          <p style="font-size:.88rem;color:var(--v-muted);line-height:1.7;max-width:560px;">Playbox adalah <strong style="color:#C4B5D4;">koper gaming all-in-one</strong> yang berisi monitor, speaker, dan PlayStation (PS4 / PS5) di dalamnya. Tinggal buka koper, colok listrik — langsung bisa main. <strong style="color:#C4B5D4;">Plug and play</strong>, cocok buat acara, gathering, atau main bareng di rumah tanpa ribet setup.</p>
          <div style="display:flex;gap:.6rem;flex-wrap:wrap;margin-top:1rem;">
            <span style="font-family:var(--font-ui);font-size:.75rem;letter-spacing:1px;background:rgba(16,185,129,.12);color:#34d399;border:1px solid rgba(16,185,129,.25);padding:.2rem .75rem;border-radius:20px;">Monitor built-in</span>
            <span style="font-family:var(--font-ui);font-size:.75rem;letter-spacing:1px;background:rgba(16,185,129,.12);color:#34d399;border:1px solid rgba(16,185,129,.25);padding:.2rem .75rem;border-radius:20px;">🔊 Speaker built-in</span>
            <span style="font-family:var(--font-ui);font-size:.75rem;letter-spacing:1px;background:rgba(16,185,129,.12);color:#34d399;border:1px solid rgba(16,185,129,.25);padding:.2rem .75rem;border-radius:20px;">PS4 / PS5</span>
            <span style="font-family:var(--font-ui);font-size:.75rem;letter-spacing:1px;background:rgba(16,185,129,.12);color:#34d399;border:1px solid rgba(16,185,129,.25);padding:.2rem .75rem;border-radius:20px;">⚡ Plug & Play</span>
          </div>
        </div>
      </div>

      <!-- Baris 1: PS4 + PS5 -->
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
          <div class="price-note green" style="margin-top:1.25rem;"><svg width="13" height="13" style="vertical-align:middle;margin-right:.3rem;"><use href="#ico-monitor"/></svg>Monitor + speaker + 2 controller included</div>
        </div></div>

        <div class="col-half"><div class="price-card ps5" style="border-color:rgba(96,165,250,.25);">
          <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.75rem;">
            <span class="v-badge" style="background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);">Playbox</span>
            <span class="v-badge v-badge-ps5">PS5</span>
          </div>
          <div class="price-card-title">Playbox PS5</div>
          <div class="price-tag">Sewa Bawa Pulang · Per 24 Jam</div>
          <div class="price-row"><span class="label">1 Hari</span><span class="price">Rp 225.000</span></div>
          <div class="price-row"><span class="label">2 Hari <span class="free-badge">Free 1 hari</span></span><span class="price">Rp 450.000</span></div>
          <div class="price-row"><span class="label">3 Hari <span class="free-badge">Free 2 hari</span></span><span class="price">Rp 675.000</span></div>
          <div class="price-note blue" style="margin-top:1.25rem;"><svg width="13" height="13" style="vertical-align:middle;margin-right:.3rem;"><use href="#ico-monitor"/></svg>Monitor + speaker + 2 controller included</div>
        </div></div>

      </div>

      <!-- Baris 2: Nintendo ditengah -->
      <div style="display:flex;justify-content:center;margin-bottom:2rem;">
        <div style="width:100%;max-width:calc(50% - .75rem);">
          <div class="price-card nin" style="border-color:rgba(248,113,113,.2);position:relative;">
            <div style="display:flex;align-items:center;gap:.6rem;margin-bottom:.75rem;flex-wrap:wrap;">
              <span class="v-badge" style="background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);">Playbox</span>
              <span class="v-badge v-badge-nin">Nintendo</span>
              <span class="same-price-badge">= Harga sama dengan PS4</span>
            </div>
            <div class="price-card-title">Playbox Nintendo</div>
            <div class="price-tag">Sewa Bawa Pulang · Per 24 Jam</div>
            <div class="price-row"><span class="label">1 Hari</span><span class="price">Rp 130.000</span></div>
            <div class="price-row"><span class="label">2 Hari <span class="free-badge">Free 1 hari</span></span><span class="price">Rp 260.000</span></div>
            <div class="price-row"><span class="label">3 Hari <span class="free-badge">Free 2 hari</span></span><span class="price">Rp 390.000</span></div>
            <div class="price-note" style="margin-top:1.25rem;border-color:rgba(248,113,113,.2);color:#fca5a5;background:rgba(248,113,113,.06);"><svg width="13" height="13" style="vertical-align:middle;margin-right:.3rem;"><use href="#ico-monitor"/></svg>Monitor + speaker + controller included</div>
          </div>
        </div>
      </div>

      <!-- Syarat Playbox -->
      <div class="syarat-box">
        <h6><svg width="16" height="16" style="vertical-align:middle;margin-right:.4rem;"><use href="#ico-shield"/></svg>Syarat & Ketentuan Sewa Playbox</h6>
        <ul class="syarat-list">
          <li><strong>Pengambilan wajib 2 orang</strong> menggunakan motor — barang berat</li>
          <li>KTP & STNK aktif <strong>sekitar Jagakarsa, alamat wajib sama</strong> — ditahan selama sewa</li>
          <li>Nomor HP penyewa <strong>dicek kecocokan dengan KTP via GetContact</strong> — jika tidak sesuai, tidak bisa sewa</li>
          <li>2 stik controller dan <strong>monitor sudah termasuk</strong> dalam paket Playbox</li>
          <li><strong>Terlambat</strong> kena charge Rp 20.000/jam · lebih dari 6 jam dianggap sewa 1 hari</li>
          <li>Jika <strong>segel rusak / barang rusak / monitor pecah</strong> dianggap membeli unit</li>
          <li>PS4 dan Nintendo <strong>khusus offline</strong> — dilarang konek internet. Denda senilai unit atau <strong>Rp 4.500.000</strong></li>
          <li>Unit <strong>dijemput dan diantar sendiri</strong> ke lokasi Violet PlayStation</li>
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
      <button class="unit-tab active" onclick="switchUnitTab('sewa',this)"><svg width="14" height="14" style="vertical-align:middle;margin-right:.35rem"><use href="#ico-home"/></svg>Sewa Bawa Pulang</button>
      <button class="unit-tab" onclick="switchUnitTab('tempat',this)"><svg width="14" height="14" style="vertical-align:middle;margin-right:.35rem"><use href="#ico-gamepad"/></svg>Main di Tempat</button>
    </div>

    <!-- Panel Sewa -->
    <div class="units-panel active" id="upanel-sewa">
      <div class="ps5-note"><svg width="16" height="16" style="flex-shrink:0;margin-top:.1rem;"><use href="#ico-warn"/></svg><span>Unit PS5 di bawah adalah unit yang biasa dipakai main di tempat. Karena itu, <strong style="color:#93c5fd;">hubungi WA dulu sebelum booking</strong> untuk pastikan unit tidak sedang terpakai.</span></div>
      <div class="units-grid">
        <?php
        $q = mysqli_query($koneksi,"SELECT u.*,COUNT(ug.id_game) as jumlah_game FROM units u LEFT JOIN unit_games ug ON u.id_unit=ug.id_unit WHERE u.tipe_layanan='Sewa Luar' OR (u.tipe_layanan='Main di Tempat' AND u.kategori='PS5') GROUP BY u.id_unit ORDER BY u.kategori,u.nama_unit ASC");
        while($u=mysqli_fetch_assoc($q)):
          $kat=$u['kategori']; $bc=$kat==='PS5'?'v-badge-ps5':($kat==='Nintendo'?'v-badge-nin':'v-badge-ps4'); $disewa=$u['status']==='Disewa';
          $is_ps5_tempat = ($u['tipe_layanan']==='Main di Tempat' && $kat==='PS5');
        ?>
        <div class="unit-card <?php echo $disewa?'disewa':''; ?>" <?php if(!$disewa): ?>onclick="bukaUnit(<?php echo $u['id_unit']; ?>,'<?php echo htmlspecialchars(addslashes($u['nama_unit'])); ?>','<?php echo $kat; ?>','Sewa Luar')"<?php endif; ?>>
          <div class="unit-icon"><svg width="28" height="28" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-gamepad"/></svg></div>
          <div class="unit-name"><?php echo htmlspecialchars($u['nama_unit']); ?></div>
          <div class="unit-meta">
            <span class="v-badge <?php echo $bc; ?>"><?php echo $kat; ?></span>
            <?php if($disewa): ?><span class="v-badge" style="background:rgba(251,191,36,.15);color:#fbbf24;border:1px solid rgba(251,191,36,.3);">Sedang Disewa</span>
            <?php else: ?><span class="v-badge" style="background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);">Tersedia</span><?php endif; ?>
            <?php if($is_ps5_tempat): ?><span style="font-size:.68rem;color:#60a5fa;font-family:var(--font-ui);display:flex;align-items:center;gap:.25rem;"><svg width="10" height="10"><use href="#ico-phone"/></svg>WA dulu</span><?php endif; ?>
          </div>
        </div>
        <?php endwhile; ?>
      </div>



    </div>

    <!-- Panel Tempat -->
    <div class="units-panel" id="upanel-tempat">
      <div class="units-grid">
        <?php
        $q = mysqli_query($koneksi,"SELECT u.*,COUNT(ug.id_game) as jumlah_game FROM units u LEFT JOIN unit_games ug ON u.id_unit=ug.id_unit WHERE u.tipe_layanan='Main di Tempat' GROUP BY u.id_unit ORDER BY u.kategori,u.nama_unit ASC");
        while($u=mysqli_fetch_assoc($q)):
          $kat=$u['kategori']; $bc=$kat==='PS5'?'v-badge-ps5':($kat==='Nintendo'?'v-badge-nin':'v-badge-ps4'); $disewa=$u['status']==='Disewa';
        ?>
        <div class="unit-card" onclick="bukaUnit(<?php echo $u['id_unit']; ?>,'<?php echo htmlspecialchars(addslashes($u['nama_unit'])); ?>','<?php echo $kat; ?>','Main di Tempat')">
          <div class="unit-icon"><svg width="28" height="28" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-gamepad"/></svg></div>
          <div class="unit-name"><?php echo htmlspecialchars($u['nama_unit']); ?></div>
          <div class="unit-meta">
            <span class="v-badge <?php echo $bc; ?>"><?php echo $kat; ?></span>
          </div>
        </div>
        <?php endwhile; ?>
      </div>
    </div>
  </div>
</section>

<!-- GAMES -->
<section class="games-section" id="games">
  <div class="container">
    <div class="section-title">KOLEKSI <span class="neon">GAME</span></div>
    <div class="v-divider"></div>
    <div class="row" style="margin-top:.5rem;">
      <?php
      $q=mysqli_query($koneksi,"SELECT DISTINCT g.id_game,g.judul_game,g.foto_game,g.kategori_game FROM games g JOIN unit_games ug ON g.id_game=ug.id_game ORDER BY g.judul_game ASC");
      while($g=mysqli_fetch_assoc($q)):
        $kat=$g['kategori_game']??''; $bc=$kat==='PS5'?'v-badge-ps5':($kat==='Nintendo'?'v-badge-nin':'v-badge-ps4');
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

<!-- SEWA -->
<section class="sewa-section" id="sewa">
  <div class="container">
    <div class="row" style="align-items:center;gap:4rem;">
      <div class="col-half">
        <div class="hero-eyebrow">Layanan Sewa Harian</div>
        <div class="section-title">SEWA <span class="neon">BAWA<br>PULANG</span></div>
        <div class="v-divider"></div>
        <div class="sewa-feature"><div class="sewa-icon"><svg width="22" height="22" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-phone"/></svg></div><div class="sewa-feature-text"><h6>Booking via WhatsApp</h6><p>Hubungi minimal H-1 sebelum tanggal pengambilan untuk reservasi unit.</p></div></div>
        <div class="sewa-feature"><div class="sewa-icon"><svg width="22" height="22" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-store"/></svg></div><div class="sewa-feature-text"><h6>Ambil di Toko</h6><p>Datang langsung ke toko kami di Jagakarsa — unit tidak diantar.</p></div></div>
        <div class="sewa-feature"><div class="sewa-icon"><svg width="22" height="22" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-idcard"/></svg></div><div class="sewa-feature-text"><h6>Jaminan KTP & STNK</h6><p>Dokumen asli diserahkan saat pengambilan. Alamat KTP & STNK harus Jagakarsa.</p></div></div>
        <div class="sewa-feature"><div class="sewa-icon"><svg width="22" height="22" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-gift"/></svg></div><div class="sewa-feature-text"><h6>Promo Weekday</h6><p>Sewa 2 hari gratis 1 hari, sewa 3 hari gratis 2 hari — berlaku Senin s/d Kamis!</p></div></div>
        <a href="sewa.php" class="btn-violet" style="display:inline-block;text-decoration:none;margin-top:1rem;"><span>Ajukan Sewa Sekarang</span></a>
      </div>
      <div class="col-half"><div class="v-card" style="padding:2.5rem;">
        <div style="font-family:var(--font-display);font-size:1.1rem;font-weight:700;letter-spacing:2px;color:var(--v-muted);text-transform:uppercase;margin-bottom:1.5rem;">Unit Sewa Tersedia</div>
        <?php
        $units=mysqli_query($koneksi,"SELECT * FROM units WHERE tipe_layanan='Sewa Luar' AND status='Tersedia' ORDER BY kategori");
        $ada=false;
        while($u=mysqli_fetch_assoc($units)):
          $ada=true; $kat=$u['kategori']; $bc=$kat==='PS5'?'v-badge-ps5':($kat==='Nintendo'?'v-badge-nin':'v-badge-ps4');
        ?>
        <div style="display:flex;justify-content:space-between;align-items:center;padding:.7rem 0;border-bottom:1px solid var(--v-border);">
          <span style="font-family:var(--font-ui);font-size:.95rem;color:#C4B5D4;"><?php echo htmlspecialchars($u['nama_unit']); ?></span>
          <span class="v-badge <?php echo $bc; ?>"><?php echo $kat; ?></span>
        </div>
        <?php endwhile;
        if(!$ada): ?><div style="text-align:center;padding:2rem;color:var(--v-muted);font-family:var(--font-ui);font-size:.9rem;">Semua unit sedang disewa 😊</div><?php endif; ?>
        <div style="margin-top:1rem;font-size:.78rem;color:#93c5fd;font-family:var(--font-ui);background:rgba(96,165,250,.06);border:1px solid rgba(96,165,250,.15);border-radius:8px;padding:.6rem .85rem;">ℹ️ PS5 juga bisa disewa — hubungi WA untuk cek ketersediaan</div>
      </div></div>
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
        <h4><svg width="22" height="22" style="vertical-align:middle;margin-right:.5rem;color:var(--v-lavender)"><use href="#ico-pin"/></svg>Violet PlayStation</h4>
        <div class="map-detail"><div class="map-detail-icon"><svg width="22" height="22" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-pin"/></svg></div><div class="map-detail-text"><strong>Alamat</strong><p>Jl. Jagakarsa II No.5D, RT.1/RW.7, Jagakarsa, Kec. Jagakarsa, Jakarta Selatan 12620</p></div></div>
        <div class="map-detail"><div class="map-detail-icon"><svg width="22" height="22" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-clock"/></svg></div><div class="map-detail-text"><strong>Jam Operasional</strong><p>Setiap hari · Hubungi WA untuk info lebih lanjut</p></div></div>
        <div class="map-detail"><div class="map-detail-icon"><svg width="22" height="22" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-phone"/></svg></div><div class="map-detail-text"><strong>WhatsApp</strong><p>0858-4783-1078</p></div></div>
        <div class="map-detail"><div class="map-detail-icon"><svg width="22" height="22" aria-hidden="true" style="flex-shrink:0;"><use href="#ico-warn"/></svg></div><div class="map-detail-text"><strong>Penting</strong><p>Booking H-1 via WA. KTP & STNK Jagakarsa wajib dibawa.</p></div></div>
        <a href="https://wa.me/6285847831078" target="_blank" class="btn-violet" style="display:inline-flex;align-items:center;gap:.5rem;text-decoration:none;margin-top:1.5rem;width:100%;justify-content:center;"><span>Chat WhatsApp</span></a>
      </div></div>
      <div class="col-half"><div class="map-wrap" style="height:100%;min-height:350px;">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3965.5!2d106.8198065!3d-6.3269265!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69ed005178c647%3A0x884731391d96c010!2sViolet%20PlayStation!5e0!3m2!1sid!2sid!4v1" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
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
        <div style="color:var(--v-muted);font-size:.9rem;margin-top:.5rem;">Sewa PS & Playbox — Jagakarsa, Jakarta Selatan</div>
        <div style="display:flex;gap:.75rem;margin-top:1.5rem;align-items:center;">
          <a href="https://wa.me/6285847831078" target="_blank" class="wa-btn" title="WhatsApp"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg></a>
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
    <div class="footer-copy">© 2026 Violet PlayStation · Jagakarsa, Jakarta Selatan</div>
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
        <a href="sewa.php" class="btn-violet" style="display:block;text-align:center;text-decoration:none;"><span>Sewa Unit Ini Sekarang</span></a>
      </div>
    </div>
  </div>
</div>



<?php
$all_unit_games=[];
$qug=mysqli_query($koneksi,"SELECT ug.id_unit,g.judul_game,g.foto_game,g.kategori_game FROM unit_games ug JOIN games g ON ug.id_game=g.id_game");
while($row=mysqli_fetch_assoc($qug)) $all_unit_games[$row['id_unit']][]=$row;
?>
<script>
const unitGames=<?php echo json_encode($all_unit_games); ?>;
function bukaUnit(id,nama,kat,tipe){
  document.getElementById('modal-nama').textContent=nama;
  document.getElementById('modal-meta').textContent=kat+' · '+tipe;
  const games=unitGames[id]||[];
  const el=document.getElementById('modal-games');
  if(!games.length){el.innerHTML='<div class="modal-empty">Belum ada game di unit ini.</div>';}
  else{let h='<div class="modal-games-grid">';games.forEach(g=>{h+=`<div class="modal-game-item"><img src="uploads/games/${g.foto_game}" alt="${g.judul_game}"><span>${g.judul_game}</span></div>`;});h+='</div>';el.innerHTML=h;}
  document.getElementById('modal-sewa-wrap').style.display=tipe==='Sewa Luar'?'block':'none';
  document.getElementById('modalUnit').classList.add('open');
  document.body.style.overflow='hidden';
}
function tutupModal(){document.getElementById('modalUnit').classList.remove('open');document.body.style.overflow='';}
document.getElementById('modalUnit').addEventListener('click',e=>{if(e.target===document.getElementById('modalUnit'))tutupModal();});

function switchPriceTab(tab,btn){
  document.querySelectorAll('.price-tab-btn').forEach(b=>b.classList.remove('active'));
  document.querySelectorAll('.price-tab-panel').forEach(p=>p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('panel-'+tab).classList.add('active');
}
function switchUnitTab(tab,btn){
  document.querySelectorAll('.unit-tab').forEach(b=>b.classList.remove('active'));
  document.querySelectorAll('.units-panel').forEach(p=>p.classList.remove('active'));
  btn.classList.add('active');
  document.getElementById('upanel-'+tab).classList.add('active');
}
function toggleDrawer(){const d=document.getElementById('navDrawer');const h=document.getElementById('hamburger');d.classList.toggle('open');h.classList.toggle('open');document.body.style.overflow=d.classList.contains('open')?'hidden':'';}
function closeDrawer(){document.getElementById('navDrawer').classList.remove('open');document.getElementById('hamburger').classList.remove('open');document.body.style.overflow='';}
</script>
</body>
</html>