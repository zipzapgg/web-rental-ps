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
<script src="../assets/app.js" defer></script>
</head>
<body>
<?php include_once "../config/svg_sprite_admin.php"; ?>
<div class="admin-topbar">
  <div style="display:flex;align-items:center;gap:.6rem;">
    <img src="../assets/images/logo-violet.jpeg" alt="Logo" style="height:28px;filter:drop-shadow(0 0 6px rgba(182, 255, 0, 0.3));">
    <span class="admin-topbar-brand">VIOLET <span class="neon">PS</span></span>
  </div>
  <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Menu"><span></span><span></span><span></span></button>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
<?php $active_page = 'laporan'; include __DIR__.'/sidebar.php'; ?>

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
        <div class="bar" style="height:<?php echo max(2,round($val/$max_val*100)); ?>%;">
          <div class="bar-tooltip"><?php echo $hari.'/'.$bulan; ?>: Rp <?php echo number_format($val,0,',','.'); ?></div>
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

</body></html>