<?php
require_once '../config/koneksi.php';
require_login('login.php');

if (isset($_SESSION['login_at']) && (time() - $_SESSION['login_at']) > 7200) {
    session_destroy();
    header("Location: login.php?pesan=timeout");
    exit();
}
$_SESSION['login_at'] = time();

$is_admin = is_admin();

$total_units   = $koneksi->query("SELECT COUNT(*) as c FROM units")->fetch_assoc()['c'];
$unit_disewa   = $koneksi->query("SELECT COUNT(*) as c FROM units WHERE status='Disewa'")->fetch_assoc()['c'];
$total_pending = $koneksi->query("SELECT COUNT(*) as c FROM pengajuan WHERE status_pengajuan='Pending'")->fetch_assoc()['c'];
$total_games   = $koneksi->query("SELECT COUNT(*) as c FROM games")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Violet PlayStation</title>
  <link rel="stylesheet" href="../assets/css/violet.css">
  <style>
    body{display:flex;min-height:100vh;}
    .sidebar{width:240px;flex-shrink:0;background:var(--v-dark);border-right:1px solid var(--v-border);display:flex;flex-direction:column;padding:1.5rem 0;position:fixed;top:0;left:0;bottom:0;z-index:50;transition:transform .3s;}
    .sidebar-brand{padding:0 1.5rem 2rem;border-bottom:1px solid var(--v-border);margin-bottom:1.5rem;}
    .sidebar-brand h2{font-family:var(--font-display);font-size:1.4rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;}
    .sidebar-brand p{font-family:var(--font-ui);font-size:.75rem;letter-spacing:1.5px;text-transform:uppercase;color:var(--v-muted);margin-top:.2rem;}
    .sidebar-brand img{height:40px;margin-bottom:.75rem;filter:drop-shadow(0 0 8px rgba(168,85,247,.5));}
    .nav-item{display:flex;align-items:center;gap:.75rem;padding:.75rem 1.5rem;font-family:var(--font-ui);font-size:.95rem;font-weight:600;letter-spacing:1px;text-transform:uppercase;color:var(--v-muted);text-decoration:none;transition:color .2s,background .2s;border-left:3px solid transparent;}
    .nav-item:hover,.nav-item.active{color:var(--v-lavender);background:rgba(168,85,247,.08);border-left-color:var(--v-violet);}
    .nav-section{font-family:var(--font-ui);font-size:.65rem;letter-spacing:2px;text-transform:uppercase;color:#3D3050;padding:.5rem 1.5rem;margin-top:.5rem;}
    .sidebar-bottom{margin-top:auto;padding:1.5rem;border-top:1px solid var(--v-border);}
    .user-chip{font-family:var(--font-ui);font-size:.85rem;color:var(--v-muted);margin-bottom:1rem;}
    .user-chip strong{color:var(--v-lavender);display:block;}
    .role-badge{display:inline-block;font-family:var(--font-ui);font-size:.7rem;letter-spacing:1.5px;text-transform:uppercase;padding:.15rem .5rem;border-radius:4px;margin-top:.25rem;}
    .role-admin{background:rgba(168,85,247,.2);color:var(--v-lavender);border:1px solid rgba(168,85,247,.3);}
    .role-karyawan{background:rgba(96,165,250,.15);color:#60a5fa;border:1px solid rgba(96,165,250,.3);}
    .main-content{margin-left:240px;flex:1;padding:2.5rem;background:var(--v-black);}
    .page-title{font-family:var(--font-display);font-size:2rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;margin-bottom:2rem;}
    .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:1.25rem;margin-bottom:2.5rem;}
    .stat-card{background:var(--v-card);border:1px solid var(--v-border);border-radius:12px;padding:1.5rem;position:relative;overflow:hidden;}
    .stat-card::after{content:'';position:absolute;bottom:0;left:0;right:0;height:2px;}
    .stat-card.purple::after{background:linear-gradient(90deg,var(--v-purple),var(--v-violet));}
    .stat-card.blue::after{background:linear-gradient(90deg,#3b82f6,#60a5fa);}
    .stat-card.green::after{background:linear-gradient(90deg,#10b981,#34d399);}
    .stat-card.orange::after{background:linear-gradient(90deg,#f59e0b,#fbbf24);}
    .stat-num{font-family:var(--font-display);font-size:2.2rem;font-weight:800;}
    .stat-card.purple .stat-num{color:var(--v-lavender);}
    .stat-card.blue .stat-num{color:#60a5fa;}
    .stat-card.green .stat-num{color:#34d399;}
    .stat-card.orange .stat-num{color:#fbbf24;}
    .stat-lbl{font-family:var(--font-ui);font-size:.75rem;letter-spacing:1.5px;text-transform:uppercase;color:var(--v-muted);margin-top:.25rem;}
    .table-card{background:var(--v-card);border:1px solid var(--v-border);border-radius:16px;overflow:hidden;}
    .table-card-header{padding:1.25rem 1.5rem;border-bottom:1px solid var(--v-border);display:flex;justify-content:space-between;align-items:center;}
    .table-card-header h3{font-family:var(--font-display);font-size:1.1rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-lavender);}
    .table-wrap{overflow-x:auto;}
    .s-tersedia{background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);}
    .s-disewa{background:rgba(251,191,36,.15);color:#fbbf24;border:1px solid rgba(251,191,36,.3);}
    .s-maint{background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);}
    .btn-sm{font-family:var(--font-ui);font-size:.75rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:.35rem .9rem;border-radius:6px;text-decoration:none;display:inline-block;transition:opacity .2s;}
    .btn-sm:hover{opacity:.8;}
    .btn-green{background:rgba(16,185,129,.2);color:#34d399;border:1px solid rgba(16,185,129,.3);}
    @media(max-width:768px){.sidebar{display:none;}.main-content{margin-left:0;}}
  </style>
</head>
<body>
<!-- Mobile topbar -->
<div class="admin-topbar">
  <div style="display:flex;align-items:center;gap:.6rem;">
    <img src="../assets/images/logo-violet.jpeg" alt="Logo" style="height:28px;filter:drop-shadow(0 0 6px rgba(168,85,247,.5));">
    <span class="admin-topbar-brand">VIOLET <span class="neon">PS</span></span>
  </div>
  <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Menu">
    <span></span><span></span><span></span>
  </button>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
<aside class="sidebar">
  <div class="sidebar-brand">
    <img src="../assets/images/logo-violet.jpeg" alt="Logo">
    <h2>VIOLET <span class="neon">PLAYSTATION</span></h2>
    <p>Admin Panel</p>
  </div>
  <div class="nav-section">Menu</div>
  <a href="index.php" class="nav-item active"><span class="icon">🏠</span> Dashboard</a>
  <a href="data_sewa.php" class="nav-item"><span class="icon">📋</span> Data Sewa</a>
  <?php if($is_admin): ?>
  <div class="nav-section">Admin Only</div>
  <a href="master_game.php" class="nav-item"><span class="icon">🎮</span> Master Game</a>
  <a href="kelola_akun.php" class="nav-item"><span class="icon">👥</span> Kelola Akun</a>
  <?php endif; ?>
  <div class="sidebar-bottom">
    <div class="user-chip">
      Login sebagai
      <strong><?php echo htmlspecialchars($_SESSION['nama'] ?: $_SESSION['user']); ?></strong>
      <span class="role-badge role-<?php echo $_SESSION['role']; ?>"><?php echo ucfirst($_SESSION['role']); ?></span>
    </div>
    <a href="logout.php" class="btn-violet" style="display:block;text-align:center;text-decoration:none;padding:.6rem;font-size:.8rem;letter-spacing:2px;" onclick="return confirm('Yakin ingin keluar?')"><span>Logout</span></a>
  </div>
</aside>
<main class="main-content">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;"><div class="page-title" style="margin-bottom:0;">DASHBOARD <span class="neon"><?php echo strtoupper($_SESSION['role']); ?></span></div><?php if($is_admin): ?><a href="tambah_unit.php" class="btn-violet" style="text-decoration:none;"><span>+ Tambah Unit</span></a><?php endif; ?></div>
  <div class="stats-grid">
    <div class="stat-card purple"><div class="stat-num"><?php echo $total_units; ?></div><div class="stat-lbl">Total Unit</div></div>
    <div class="stat-card blue"><div class="stat-num"><?php echo $total_units - $unit_disewa; ?></div><div class="stat-lbl">Unit Tersedia</div></div>
    <div class="stat-card orange"><div class="stat-num"><?php echo $unit_disewa; ?></div><div class="stat-lbl">Sedang Disewa</div></div>
    <div class="stat-card green"><div class="stat-num"><?php echo $total_pending; ?></div><div class="stat-lbl">Pending</div></div>
  </div>
  <div class="table-card">
    <div class="table-card-header">
      <h3>Semua Unit</h3>
      <span style="font-family:var(--font-ui);font-size:.8rem;color:var(--v-muted);"><?php echo $total_games; ?> Game Terdaftar</span>
    </div>
    <div class="table-wrap">
      <table class="v-table">
        <thead><tr><th>#</th><th>Nama Unit</th><th>Kategori</th><th>Layanan</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php
        $no=1; $q=$koneksi->query("SELECT * FROM units ORDER BY tipe_layanan DESC, nama_unit ASC");
        while($d=$q->fetch_assoc()):
          $kat=$d['kategori']; $bc=$kat==='PS5'?'v-badge-ps5':($kat==='Nintendo'?'v-badge-nin':'v-badge-ps4');
          $st=$d['status']; $sc=$st==='Tersedia'?'s-tersedia':($st==='Disewa'?'s-disewa':'s-maint');
        ?>
        <tr>
          <td style="color:var(--v-muted);"><?php echo $no++; ?></td>
          <td><strong style="color:var(--v-white);"><?php echo htmlspecialchars($d['nama_unit']); ?></strong></td>
          <td><span class="v-badge <?php echo $bc; ?>"><?php echo $kat; ?></span></td>
          <td style="color:var(--v-muted);font-size:.85rem;"><?php echo $d['tipe_layanan']; ?></td>
          <td><span class="v-badge <?php echo $sc; ?>"><?php echo $st; ?></span></td>
          <td><a href="histori_unit.php?id=<?php echo $d['id_unit']; ?>" class="btn-sm btn-blue">📋 Histori</a><?php if($is_admin): ?>&nbsp;<a href="isi_unit.php?id=<?php echo $d['id_unit']; ?>" class="btn-sm btn-green">🎮 Game</a><?php endif; ?></td>
        </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
</body>
</html>