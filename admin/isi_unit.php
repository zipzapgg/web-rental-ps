<?php
require_once '../config/koneksi.php';
require_admin('login.php');

$id_unit = intval($_GET['id'] ?? 0);
if (!$id_unit) { header("Location: index.php"); exit(); }

$s = $koneksi->prepare("SELECT * FROM units WHERE id_unit=?");
$s->bind_param("i", $id_unit); $s->execute();
$unit = $s->get_result()->fetch_assoc(); $s->close();
if (!$unit) { header("Location: index.php"); exit(); }

$msg = $_GET['msg'] ?? '';
$kat  = $unit['kategori'];
$bc   = $kat==='PS5'?'v-badge-ps5':($kat==='Nintendo'?'v-badge-nin':'v-badge-ps4');
$games_all = $koneksi->query("SELECT * FROM games ORDER BY judul_game ASC");

$assigned = [];
$r = $koneksi->prepare("SELECT id_game FROM unit_games WHERE id_unit=?");
$r->bind_param("i", $id_unit); $r->execute();
$res = $r->get_result();
while($row=$res->fetch_assoc()) $assigned[]=$row['id_game'];
$r->close();
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Game Unit <?php echo htmlspecialchars($unit['nama_unit']); ?> — Violet PlayStation</title>
<link rel="stylesheet" href="../assets/css/violet.css">
  <script src="../assets/app.js" defer></script>
<style>
body{display:flex;min-height:100vh;}
.main-content{margin-left:240px;flex:1;padding:2.5rem;background:var(--v-black);}
.games-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:1rem;margin-top:1.5rem;}
.game-item{background:var(--v-card);border:1px solid var(--v-border);border-radius:10px;overflow:hidden;transition:border-color .2s,box-shadow .2s;}
.game-item.assigned{border-color:rgba(16,185,129,.4);box-shadow:0 0 12px rgba(16,185,129,.15);}
.game-item img{width:100%;height:130px;object-fit:cover;display:block;}
.game-item-body{padding:.6rem .75rem;}
.game-item-title{font-family:var(--font-ui);font-size:.82rem;font-weight:600;color:#C4B5D4;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-bottom:.5rem;}
.btn-toggle{display:block;width:100%;font-family:var(--font-ui);font-size:.72rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:.35rem;border-radius:6px;text-align:center;text-decoration:none;transition:all .2s;border:none;cursor:pointer;}
.btn-add{background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);}
.btn-add:hover{background:rgba(16,185,129,.3);}
.btn-remove{background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);}
.btn-remove:hover{background:rgba(239,68,68,.3);}
.unit-info{background:var(--v-card);border:1px solid var(--v-border);border-radius:14px;padding:1.5rem 2rem;margin-bottom:2rem;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;}
.alert-msg{border-radius:8px;padding:.75rem 1rem;font-family:var(--font-ui);font-size:.85rem;letter-spacing:1px;margin-bottom:1.5rem;}
.alert-success{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#34d399;}
@media(max-width:768px){.main-content{margin-left:0;}}
</style>
</head>
<body>
<svg xmlns="http://www.w3.org/2000/svg" style="display:none">
  <symbol id="ico-home" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M3 9.5L12 3l9 6.5V20a1 1 0 01-1 1H4a1 1 0 01-1-1V9.5z"/><polyline points="9 21 9 12 15 12 15 21"/>
  </symbol>
  <symbol id="ico-clipboard" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M16 4h2a2 2 0 012 2v14a2 2 0 01-2 2H6a2 2 0 01-2-2V6a2 2 0 012-2h2"/>
    <rect x="8" y="2" width="8" height="4" rx="1"/>
    <line x1="8" y1="11" x2="16" y2="11"/><line x1="8" y1="15" x2="13" y2="15"/>
  </symbol>
  <symbol id="ico-chart" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>
    <line x1="2" y1="20" x2="22" y2="20"/>
  </symbol>
  <symbol id="ico-calendar" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
  </symbol>
  <symbol id="ico-gamepad" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <rect x="2" y="6" width="20" height="12" rx="4"/>
    <line x1="8" y1="10" x2="8" y2="14"/><line x1="6" y1="12" x2="10" y2="12"/>
    <circle cx="16" cy="10.5" r=".8" fill="currentColor" stroke="none"/>
    <circle cx="18.5" cy="12" r=".8" fill="currentColor" stroke="none"/>
    <circle cx="16" cy="13.5" r=".8" fill="currentColor" stroke="none"/>
    <circle cx="13.5" cy="12" r=".8" fill="currentColor" stroke="none"/>
  </symbol>
  <symbol id="ico-users" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
    <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
  </symbol>
  <symbol id="ico-trash" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
    <path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 011-1h4a1 1 0 011 1v2"/>
  </symbol>
  <symbol id="ico-check" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
    <polyline points="20 6 9 17 4 12"/>
  </symbol>
  <symbol id="ico-x" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
  </symbol>
  <symbol id="ico-warn" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
  </symbol>
  <symbol id="ico-gift" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <polyline points="20 12 20 22 4 22 4 12"/><rect x="2" y="7" width="20" height="5" rx="1"/>
    <line x1="12" y1="22" x2="12" y2="7"/>
    <path d="M12 7H7.5a2.5 2.5 0 010-5C11 2 12 7 12 7z"/>
    <path d="M12 7h4.5a2.5 2.5 0 000-5C13 2 12 7 12 7z"/>
  </symbol>
  <symbol id="ico-search" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
  </symbol>
  <symbol id="ico-money" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <rect x="2" y="5" width="20" height="14" rx="2"/>
    <circle cx="12" cy="12" r="3"/><line x1="2" y1="10" x2="6" y2="10"/><line x1="18" y1="10" x2="22" y2="10"/>
  </symbol>
  <symbol id="ico-file" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
    <polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="13" y2="17"/>
  </symbol>
  <symbol id="ico-user" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
  </symbol>
  <symbol id="ico-key" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 11-7.778 7.778 5.5 5.5 0 017.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
  </symbol>
  <symbol id="ico-plus" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
  </symbol>
  <symbol id="ico-download" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
    <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
  </symbol>
  <symbol id="ico-card" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>
  </symbol>
  <symbol id="ico-save" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M19 21H5a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v11a2 2 0 01-2 2z"/>
    <polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/>
  </symbol>
  <symbol id="ico-phone" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 10.8 19.79 19.79 0 01.01 2.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
  </symbol>
  <symbol id="ico-motor" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="5.5" cy="17.5" r="3.5"/><circle cx="18.5" cy="17.5" r="3.5"/>
    <path d="M15 6h-3l-2 5H5.5"/><path d="M9 11l2-5h5l2 3.5"/><path d="M15 6l2 5.5"/>
  </symbol>
  <symbol id="ico-idcard" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <rect x="2" y="5" width="20" height="14" rx="2"/><circle cx="8" cy="12" r="2"/>
    <path d="M14 9h4M14 12h4M14 15h2"/>
  </symbol>
  <symbol id="ico-case" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <rect x="2" y="7" width="20" height="14" rx="2"/>
    <path d="M16 7V5a2 2 0 00-2-2h-4a2 2 0 00-2 2v2"/><path d="M2 12h20"/>
  </symbol>
  <symbol id="ico-clock2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
  </symbol>
  <symbol id="ico-image" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <rect x="3" y="3" width="18" height="18" rx="2"/>
    <circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
  </symbol>
  <symbol id="ico-link" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/>
    <path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/>
  </symbol>
  <symbol id="ico-lock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
    <circle cx="12" cy="16" r="1" fill="currentColor" stroke="none"/>
  </symbol>
  <symbol id="ico-edit" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
  </symbol>
  <symbol id="ico-tag" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M20.59 13.41l-7.17 7.17a2 2 0 01-2.83 0L2 12V2h10l8.59 8.59a2 2 0 010 2.82z"/>
    <circle cx="7" cy="7" r="1.5" fill="currentColor" stroke="none"/>
  </symbol>
  <symbol id="ico-pin" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/><circle cx="12" cy="9" r="2.5"/>
  </symbol>
  <symbol id="ico-shield" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
  </symbol>
  <symbol id="ico-monitor" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/>
  </symbol>
  <symbol id="ico-store" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M3 9l1-6h16l1 6"/><path d="M3 9a2 2 0 002 2 2 2 0 002-2 2 2 0 002 2 2 2 0 002-2 2 2 0 002 2 2 2 0 002-2"/>
    <path d="M5 11v9a1 1 0 001 1h4v-5h4v5h4a1 1 0 001-1v-9"/>
  </symbol>
  <symbol id="ico-zap" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
  </symbol>
  <symbol id="ico-clock" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
  </symbol>
  <symbol id="ico-eye" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
  </symbol>
  <symbol id="ico-eye-off" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/>
    <line x1="1" y1="1" x2="23" y2="23"/>
  </symbol>
  <symbol id="ico-logout" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>
  </symbol>
  <symbol id="ico-wa" viewBox="0 0 24 24" fill="currentColor">
    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
  </symbol>
  <symbol id="ico-ig" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
    <rect x="2" y="2" width="20" height="20" rx="5"/><path d="M16 11.37A4 4 0 1112.63 8 4 4 0 0116 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
  </symbol>
</svg>
<div class="admin-topbar">
  <div style="display:flex;align-items:center;gap:.6rem;">
    <img src="../assets/images/logo-violet.jpeg" alt="Logo" style="height:28px;filter:drop-shadow(0 0 6px rgba(168,85,247,.5));">
    <span class="admin-topbar-brand">VIOLET <span class="neon">PS</span></span>
  </div>
  <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Menu"><span></span><span></span><span></span></button>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
<aside class="sidebar">
  <div class="sidebar-brand">
    <img src="../assets/images/logo-violet.jpeg" alt="Logo">
    <h2>VIOLET <span class="neon">PLAYSTATION</span></h2>
    <p>Admin Panel</p>
  </div>
  <div class="nav-section">Menu</div>
  <a href="index.php" class="nav-item active"><svg width="16" height="16"><use href="#ico-home"/></svg> Dashboard</a>
  <a href="data_sewa.php" class="nav-item" style="justify-content:space-between;">
    <span style="display:inline-flex;align-items:center;gap:.5rem;"><svg width="16" height="16"><use href="#ico-clipboard"/></svg> Data Sewa</span>
    <?php if($total_pending??0>0): ?><span class="nav-badge"><?php echo $total_pending; ?></span><?php endif; ?>
  </a>
  <a href="laporan.php" class="nav-item"><svg width="16" height="16"><use href="#ico-chart"/></svg> Laporan</a>
  <?php if(is_admin()): ?>
  <div class="nav-section">Admin Only</div>
  <a href="master_game.php" class="nav-item"><svg width="16" height="16"><use href="#ico-gamepad"/></svg> Master Game</a>
  <a href="hari_libur.php" class="nav-item"><svg width="16" height="16"><use href="#ico-calendar"/></svg> Hari Libur</a>
  <a href="kelola_akun.php" class="nav-item"><svg width="16" height="16"><use href="#ico-users"/></svg> Kelola Akun</a>
  <?php endif; ?>
  <div class="sidebar-bottom">
    <div class="user-chip">Login sebagai
      <strong><?php echo htmlspecialchars($_SESSION["nama"]??$_SESSION["user"]); ?></strong>
      <span class="role-badge role-<?php echo $_SESSION["role"]; ?>"><?php echo ucfirst($_SESSION["role"]); ?></span>
    </div>
    <a href="logout.php" class="btn-violet" style="display:flex;align-items:center;justify-content:center;gap:.5rem;text-decoration:none;padding:.6rem;font-size:.8rem;letter-spacing:2px;" onclick="return confirm('Yakin ingin keluar?')" >
      <svg width="14" height="14"><use href="#ico-logout"/></svg>
      <span>Logout</span>
    </a>
  </div>
</aside>
<main class="main-content">
  <div style="display:flex;align-items:center;gap:.5rem;font-family:var(--font-ui);font-size:.78rem;letter-spacing:1px;text-transform:uppercase;color:var(--v-muted);margin-bottom:1.5rem;">
    <a href="index.php" style="color:var(--v-muted);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='var(--v-lavender)'" onmouseout="this.style.color='var(--v-muted)'">Dashboard</a>
    <span style="opacity:.4;">›</span>
    <span style="color:var(--v-lavender);">Game Unit — <?php echo htmlspecialchars($unit['nama_unit']); ?></span>
  </div>

  <?php if($msg==='ok'): ?><div class="alert-msg alert-success">✓ Game berhasil diperbarui.</div><?php endif; ?>

  <div class="unit-info">
    <div>
      <div style="font-family:var(--font-display);font-size:1.5rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;color:var(--v-lavender);"><?php echo htmlspecialchars($unit['nama_unit']); ?></div>
      <div style="margin-top:.5rem;display:flex;gap:.5rem;align-items:center;">
        <span class="v-badge <?php echo $bc; ?>"><?php echo $kat; ?></span>
        <span style="font-family:var(--font-ui);font-size:.8rem;color:var(--v-muted);"><?php echo $unit['tipe_layanan']; ?></span>
        <span style="font-family:var(--font-ui);font-size:.78rem;color:#34d399;"><?php echo count($assigned); ?> game terpilih</span>
      </div>
    </div>
  </div>

  <div style="font-family:var(--font-display);font-size:1.1rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-lavender);">Assign Game</div>
  <div style="font-family:var(--font-ui);font-size:.8rem;color:var(--v-muted);margin-top:.25rem;">Klik Tambah/Hapus untuk update game di unit ini</div>

  <div class="games-grid">
    <?php while($g=$games_all->fetch_assoc()):
      $is_assigned = in_array($g['id_game'], $assigned);
      $gkat=$g['kategori_game']??''; $gbc=$gkat==='PS5'?'v-badge-ps5':($gkat==='Nintendo'?'v-badge-nin':'v-badge-ps4');
      $token = csrf_get_token();
    ?>
    <div class="game-item <?php echo $is_assigned?'assigned':''; ?>">
      <img src="../uploads/games/<?php echo htmlspecialchars($g['foto_game']); ?>" alt="<?php echo htmlspecialchars($g['judul_game']); ?>">
      <div class="game-item-body">
        <?php if($gkat): ?><span class="v-badge <?php echo $gbc; ?>" style="font-size:.62rem;padding:.08rem .35rem;margin-bottom:.3rem;display:inline-block;"><?php echo $gkat; ?></span><?php endif; ?>
        <div class="game-item-title"><?php echo htmlspecialchars($g['judul_game']); ?></div>
        <a href="proses_isi_unit.php?act=<?php echo $is_assigned?'hapus':'tambah'; ?>&unit=<?php echo $id_unit; ?>&game=<?php echo $g['id_game']; ?>&_token=<?php echo $token; ?>"
           class="btn-toggle <?php echo $is_assigned?'btn-remove':'btn-add'; ?>">
          <?php echo $is_assigned?'✕ Hapus':'+ Tambah'; ?>
        </a>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</main>
<script>
function toggleSidebar(){document.querySelector('.sidebar').classList.toggle('mobile-open');document.getElementById('sidebarOverlay').classList.toggle('open');}
function closeSidebar(){document.querySelector('.sidebar').classList.remove('mobile-open');document.getElementById('sidebarOverlay').classList.remove('open');}
</script>
</body></html>