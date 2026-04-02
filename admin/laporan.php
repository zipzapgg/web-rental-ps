<?php
require_once '../config/koneksi.php';
require_login('login.php');
$is_admin = is_admin();

$bulan = intval($_GET['bulan'] ?? date('m'));
$tahun = intval($_GET['tahun'] ?? date('Y'));
if($bulan<1||$bulan>12) $bulan=intval(date('m'));
if($tahun<2020||$tahun>2099) $tahun=intval(date('Y'));

$bln_str = sprintf('%04d-%02d', $tahun, $bulan);

// Stats bulan ini
$total_transaksi  = $koneksi->query("SELECT COUNT(*) as c FROM pengajuan WHERE DATE_FORMAT(tgl_pengajuan,'%Y-%m')='$bln_str' AND status_pengajuan='Selesai'")->fetch_assoc()['c'];
$total_pendapatan = $koneksi->query("SELECT COALESCE(SUM(harga),0) as t FROM pengajuan WHERE DATE_FORMAT(tgl_pengajuan,'%Y-%m')='$bln_str' AND status_pengajuan='Selesai'")->fetch_assoc()['t'];
$total_denda      = $koneksi->query("SELECT COUNT(*) as c FROM pengajuan WHERE DATE_FORMAT(tgl_pengajuan,'%Y-%m')='$bln_str' AND status_pengajuan='Selesai' AND harga > (SELECT COALESCE(p2.harga,0) FROM pengajuan p2 WHERE p2.id_pengajuan=pengajuan.id_pengajuan LIMIT 1)")->fetch_assoc()['c'];
$total_pending    = $koneksi->query("SELECT COUNT(*) as c FROM pengajuan WHERE DATE_FORMAT(tgl_pengajuan,'%Y-%m')='$bln_str' AND status_pengajuan='Pending'")->fetch_assoc()['c'];
$total_ditolak    = $koneksi->query("SELECT COUNT(*) as c FROM pengajuan WHERE DATE_FORMAT(tgl_pengajuan,'%Y-%m')='$bln_str' AND status_pengajuan='Ditolak'")->fetch_assoc()['c'];

// Unit tersering disewa bulan ini
$top_units = $koneksi->query(
    "SELECT u.nama_unit, u.kategori, COUNT(*) as jml, COALESCE(SUM(p.harga),0) as pendapatan
     FROM pengajuan p JOIN units u ON p.id_unit=u.id_unit
     WHERE DATE_FORMAT(p.tgl_pengajuan,'%Y-%m')='$bln_str' AND p.status_pengajuan='Selesai'
     GROUP BY p.id_unit ORDER BY jml DESC LIMIT 5"
);

// Pendapatan per kategori
$per_kat = $koneksi->query(
    "SELECT u.kategori, COUNT(*) as jml, COALESCE(SUM(p.harga),0) as pendapatan
     FROM pengajuan p JOIN units u ON p.id_unit=u.id_unit
     WHERE DATE_FORMAT(p.tgl_pengajuan,'%Y-%m')='$bln_str' AND p.status_pengajuan='Selesai'
     GROUP BY u.kategori ORDER BY pendapatan DESC"
);

// Transaksi per hari bulan ini (untuk chart)
$per_hari = $koneksi->query(
    "SELECT DAY(tgl_pengajuan) as hari, COUNT(*) as jml, COALESCE(SUM(harga),0) as total
     FROM pengajuan WHERE DATE_FORMAT(tgl_pengajuan,'%Y-%m')='$bln_str' AND status_pengajuan='Selesai'
     GROUP BY DAY(tgl_pengajuan) ORDER BY hari ASC"
);
$chart_data = array_fill(1, cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun), 0);
while($r=$per_hari->fetch_assoc()) $chart_data[intval($r['hari'])] = intval($r['total']);

// Bulan-bulan untuk dropdown
$bulan_list = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Laporan Violet PlayStation</title>
<link rel="stylesheet" href="../assets/css/violet.css?v=<?php echo time(); ?>">

<style>
@media (max-width: 768px) {
  /* 1. Fix Hamburger Menu & Topbar (Solusi Burger Kiri Tengah) */
  body { flex-direction: column !important; }
  .admin-topbar { width: 100% !important; }

  /* 2. Paksa tabel agar bisa digeser ke samping (Scroll) */
  .table-card { max-width: 100vw !important; overflow: hidden !important; }
  .table-wrap { 
    overflow-x: auto !important; 
    display: block !important; 
    width: 100% !important; 
    -webkit-overflow-scrolling: touch; 
    padding-bottom: 10px;
  }
  
  /* 3. Kunci ukuran tabel dan larang teks melipat ke bawah */
  .v-table { min-width: 900px !important; }
  .v-table th, .v-table td { white-space: nowrap !important; }
  
  /* 4. Kembalikan tombol agar berjejer rapi ke samping */
  .v-table td[style*="display:flex"], .actions-wrap { 
    flex-direction: row !important; 
    flex-wrap: nowrap !important; 
    gap: 0.5rem !important; 
  }
  .v-table td .btn-sm { width: auto !important; padding: 0.5rem 0.75rem !important; }
  
  /* 5. Amankan Tab & Header */
  .filter-tabs, div[style*="display:flex;gap:.6rem;margin-bottom:1.25rem;flex-wrap:wrap;"] {
    flex-wrap: nowrap !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
  }
  .ftab { flex-shrink: 0; }
}
</style>
  <script src="../assets/app.js" defer></script>
<style>
body{display:flex;min-height:100vh;}
.main-content{margin-left:240px;flex:1;padding:2.5rem;background:var(--v-black);}
.page-title{font-family:var(--font-display);font-size:2rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;margin-bottom:2rem;}
.stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(160px,1fr));gap:1.25rem;margin-bottom:2rem;}
.stat-card{background:var(--v-card);border:1px solid var(--v-border);border-radius:12px;padding:1.5rem;position:relative;overflow:hidden;}
.stat-card::after{content:'';position:absolute;bottom:0;left:0;right:0;height:2px;}
.stat-card.purple::after{background:linear-gradient(90deg,var(--v-purple),var(--v-violet));}
.stat-card.green::after{background:linear-gradient(90deg,#10b981,#34d399);}
.stat-card.yellow::after{background:linear-gradient(90deg,#f59e0b,#fbbf24);}
.stat-card.red::after{background:linear-gradient(90deg,#ef4444,#f87171);}
.stat-card.blue::after{background:linear-gradient(90deg,#3b82f6,#60a5fa);}
.stat-num{font-family:var(--font-display);font-size:1.8rem;font-weight:800;}
.stat-card.purple .stat-num{color:var(--v-lavender);}
.stat-card.green .stat-num{color:#34d399;}
.stat-card.yellow .stat-num{color:#fbbf24;}
.stat-card.red .stat-num{color:#f87171;}
.stat-card.blue .stat-num{color:#60a5fa;}
.stat-lbl{font-family:var(--font-ui);font-size:.75rem;letter-spacing:1.5px;text-transform:uppercase;color:var(--v-muted);margin-top:.25rem;}
.card{background:var(--v-card);border:1px solid var(--v-border);border-radius:16px;overflow:hidden;margin-bottom:1.5rem;}
.card-header{padding:1.25rem 1.5rem;border-bottom:1px solid var(--v-border);}
.card-header h3{font-family:var(--font-display);font-size:1rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-lavender);}
.card-body{padding:1.5rem;}
.table-wrap{overflow-x:auto;}
.v-table td,.v-table th{padding:.75rem 1rem;}
.bar-wrap{display:flex;align-items:flex-end;gap:3px;height:120px;padding:0 .5rem;}
.bar{flex:1;background:linear-gradient(180deg,var(--v-violet),var(--v-purple));border-radius:3px 3px 0 0;min-height:2px;transition:opacity .2s;cursor:default;position:relative;}
.bar:hover{opacity:.75;}
.bar-tooltip{position:absolute;bottom:calc(100% + 4px);left:50%;transform:translateX(-50%);background:var(--v-dark);border:1px solid var(--v-border);border-radius:6px;padding:.25rem .5rem;font-family:var(--font-ui);font-size:.7rem;color:var(--v-white);white-space:nowrap;pointer-events:none;display:none;}
.bar:hover .bar-tooltip{display:block;}
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;}
@media(max-width:900px){.two-col{grid-template-columns:1fr;}}
@media(max-width:768px){.main-content{margin-left:0;}}
</style>
</head>
<body>
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
  <a href="index.php" class="nav-item"><svg width="16" height="16"><use href="../assets/icons.svg#ico-home"/></svg> Dashboard</a>
  <a href="data_sewa.php" class="nav-item" style="justify-content:space-between;">
    <span style="display:inline-flex;align-items:center;gap:.5rem;"><svg width="16" height="16"><use href="../assets/icons.svg#ico-clipboard"/></svg> Data Sewa</span>
    <?php if($total_pending??0>0): ?><span class="nav-badge"><?php echo $total_pending; ?></span><?php endif; ?>
  </a>
  <a href="laporan.php" class="nav-item active"><svg width="16" height="16"><use href="../assets/icons.svg#ico-chart"/></svg> Laporan</a>
  <?php if(is_admin()): ?>
  <div class="nav-section">Admin Only</div>
  <a href="master_game.php" class="nav-item"><svg width="16" height="16"><use href="../assets/icons.svg#ico-gamepad"/></svg> Master Game</a>
  <a href="hari_libur.php" class="nav-item"><svg width="16" height="16"><use href="../assets/icons.svg#ico-calendar"/></svg> Hari Libur</a>
  <a href="kelola_akun.php" class="nav-item"><svg width="16" height="16"><use href="../assets/icons.svg#ico-users"/></svg> Kelola Akun</a>
  <?php endif; ?>
  <div class="sidebar-bottom">
    <div class="user-chip">Login sebagai
      <strong><?php echo htmlspecialchars($_SESSION["nama"]??$_SESSION["user"]); ?></strong>
      <span class="role-badge role-<?php echo $_SESSION["role"]; ?>"><?php echo ucfirst($_SESSION["role"]); ?></span>
    </div>
    <a href="logout.php" class="btn-violet" style="display:flex;align-items:center;justify-content:center;gap:.5rem;text-decoration:none;padding:.6rem;font-size:.8rem;letter-spacing:2px;" onclick="return confirm('Yakin ingin keluar?')" >
      <svg width="14" height="14"><use href="../assets/icons.svg#ico-logout"/></svg>
      <span>Logout</span>
    </a>
  </div>
</aside>

<main class="main-content">
  <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;margin-bottom:2rem;">
    <div class="page-title" style="margin-bottom:0;">LAPORAN <span class="neon">BULANAN</span></div>
    <form method="GET" style="display:flex;gap:.6rem;align-items:center;">
      <select name="bulan" class="v-input" style="padding:.5rem .75rem;font-size:.85rem;width:auto;">
        <?php foreach($bulan_list as $i=>$bl): ?>
        <option value="<?php echo $i+1; ?>" <?php echo ($bulan===$i+1)?'selected':''; ?>><?php echo $bl; ?></option>
        <?php endforeach; ?>
      </select>
      <select name="tahun" class="v-input" style="padding:.5rem .75rem;font-size:.85rem;width:auto;">
        <?php for($y=date('Y');$y>=2024;$y--): ?>
        <option value="<?php echo $y; ?>" <?php echo ($tahun===$y)?'selected':''; ?>><?php echo $y; ?></option>
        <?php endfor; ?>
      </select>
      <button type="submit" class="btn-sm btn-purple" style="padding:.55rem 1rem;">Tampilkan</button>
      <?php if($is_admin): ?>
      <a href="export_sewa.php?filter=selesai&tgl_dari=<?php echo $bln_str.'-01'; ?>&tgl_sampai=<?php echo $bln_str.'-'.cal_days_in_month(CAL_GREGORIAN,$bulan,$tahun); ?>&_token=<?php echo csrf_get_token(); ?>" class="btn-sm btn-green" style="padding:.55rem 1rem;">⬇ Export CSV</a>
      <?php endif; ?>
    </form>
  </div>

  <!-- Stats -->
  <div class="stats-grid">
    <div class="stat-card green"><div class="stat-num">Rp <?php echo number_format($total_pendapatan,0,',','.'); ?></div><div class="stat-lbl">Total Pendapatan</div></div>
    <div class="stat-card purple"><div class="stat-num"><?php echo $total_transaksi; ?></div><div class="stat-lbl">Transaksi Selesai</div></div>
    <div class="stat-card yellow"><div class="stat-num"><?php echo $total_pending; ?></div><div class="stat-lbl">Masih Pending</div></div>
    <div class="stat-card red"><div class="stat-num"><?php echo $total_ditolak; ?></div><div class="stat-lbl">Ditolak</div></div>
    <div class="stat-card blue"><div class="stat-num"><?php echo $total_transaksi>0?'Rp '.number_format(intdiv($total_pendapatan,$total_transaksi),0,',','.'):'—'; ?></div><div class="stat-lbl">Rata-rata per Transaksi</div></div>
  </div>

  <!-- Chart pendapatan per hari -->
  <div class="card">
    <div class="card-header"><h3>Pendapatan Harian <?php echo $bulan_list[$bulan-1].' '.$tahun; ?></h3></div>
    <div class="card-body">
      <?php
      $max_val = max(array_values($chart_data)) ?: 1;
      ?>
      <div class="bar-wrap">
        <?php foreach($chart_data as $hari=>$val): ?>
        <div class="bar"
          style="height:<?php echo max(2,round($val/$max_val*100)); ?>%;"
          data-val="<?php echo number_format($val,0,',','.'); ?>"
          title="<?php echo $hari.'/'.$bulan; ?>: Rp <?php echo number_format($val,0,',','.'); ?>">
        </div>
        <?php endforeach; ?>
      </div>
      <div style="display:flex;justify-content:space-between;font-family:var(--font-ui);font-size:.72rem;color:var(--v-muted);margin-top:.35rem;padding:0 .5rem;">
        <span>1</span><span><?php echo ceil(count($chart_data)/4); ?></span><span><?php echo ceil(count($chart_data)/2); ?></span><span><?php echo ceil(count($chart_data)*3/4); ?></span><span><?php echo count($chart_data); ?></span>
      </div>
    </div>
  </div>

  <div class="two-col">
    <!-- Top unit -->
    <div class="card">
      <div class="card-header"><h3>Unit Tersering Disewa</h3></div>
      <div class="table-wrap">
        <table class="v-table">
          <thead><tr><th>Unit</th><th>Kategori</th><th>Sewa</th><th>Pendapatan</th></tr></thead>
          <tbody>
          <?php if($top_units->num_rows===0): ?>
          <tr><td colspan="4" style="text-align:center;color:var(--v-muted);padding:1.5rem;">Belum ada data</td></tr>
          <?php endif; ?>
          <?php while($r=$top_units->fetch_assoc()):
            $bc=$r['kategori']==='PS5'?'v-badge-ps5':($r['kategori']==='Nintendo'?'v-badge-nin':'v-badge-ps4');
          ?>
          <tr>
            <td style="color:var(--v-white);"><?php echo htmlspecialchars($r['nama_unit']); ?></td>
            <td><span class="v-badge <?php echo $bc; ?>"><?php echo $r['kategori']; ?></span></td>
            <td style="font-family:var(--font-ui);color:#fbbf24;font-weight:700;"><?php echo $r['jml']; ?>×</td>
            <td style="font-family:var(--font-ui);color:#34d399;font-weight:700;">Rp <?php echo number_format($r['pendapatan'],0,',','.'); ?></td>
          </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Per kategori -->
    <div class="card">
      <div class="card-header"><h3>Pendapatan per Kategori</h3></div>
      <div class="table-wrap">
        <table class="v-table">
          <thead><tr><th>Kategori</th><th>Transaksi</th><th>Pendapatan</th></tr></thead>
          <tbody>
          <?php if($per_kat->num_rows===0): ?>
          <tr><td colspan="3" style="text-align:center;color:var(--v-muted);padding:1.5rem;">Belum ada data</td></tr>
          <?php endif; ?>
          <?php while($r=$per_kat->fetch_assoc()):
            $bc=$r['kategori']==='PS5'?'v-badge-ps5':($r['kategori']==='Nintendo'?'v-badge-nin':'v-badge-ps4');
          ?>
          <tr>
            <td><span class="v-badge <?php echo $bc; ?>"><?php echo $r['kategori']; ?></span></td>
            <td style="font-family:var(--font-ui);color:#fbbf24;font-weight:700;"><?php echo $r['jml']; ?>×</td>
            <td style="font-family:var(--font-ui);color:#34d399;font-weight:700;">Rp <?php echo number_format($r['pendapatan'],0,',','.'); ?></td>
          </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<script>
function toggleSidebar(){document.querySelector('.sidebar').classList.toggle('mobile-open');document.getElementById('sidebarOverlay').classList.toggle('open');document.body.style.overflow=document.querySelector('.sidebar').classList.contains('mobile-open')?'hidden':'';}
function closeSidebar(){document.querySelector('.sidebar').classList.remove('mobile-open');document.getElementById('sidebarOverlay').classList.remove('open');document.body.style.overflow='';}
</script>
</body></html>