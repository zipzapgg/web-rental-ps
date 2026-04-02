<?php
require_once '../config/koneksi.php';
require_login('login.php');

// Auto logout jika idle 2 jam (sudah ditangani di koneksi.php, ini sebagai fallback)
if (isset($_SESSION['login_at']) && (time() - $_SESSION['login_at']) > 7200) {
    session_destroy();
    header("Location: login.php?pesan=timeout");
    exit();
}
$_SESSION['login_at'] = time();

$is_admin = is_admin();

// ── Stats dalam 1 query ───────────────────────────────────────────────────
$row_stats = $koneksi->query("
    SELECT
        (SELECT COUNT(*) FROM units)                                        AS total_units,
        (SELECT COUNT(*) FROM units WHERE status='Disewa')                  AS unit_disewa,
        (SELECT COUNT(*) FROM pengajuan WHERE status_pengajuan='Pending')   AS total_pending,
        (SELECT COUNT(*) FROM games)                                        AS total_games
")->fetch_assoc();

$total_units   = $row_stats['total_units'];
$unit_disewa   = $row_stats['unit_disewa'];
$total_pending = $row_stats['total_pending'];
$total_games   = $row_stats['total_games'];

// ── Libur aktif & mendatang ─────────────────────────────────────────────────
// PERBAIKAN: Simpan ke array agar bisa di-loop berkali-kali tanpa re-query
$libur_rows = [];
$q_libur = $koneksi->query(
    "SELECT * FROM hari_libur
     WHERE tgl_selesai >= CURDATE() AND tgl_mulai <= DATE_ADD(CURDATE(), INTERVAL 30 DAY)
     ORDER BY tgl_mulai ASC LIMIT 5"
);
while ($lb = $q_libur->fetch_assoc()) {
    $libur_rows[] = $lb;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Violet PlayStation</title>
  <link rel="stylesheet" href="../assets/css/violet.css?v=<?php echo time(); ?>">

<style>
@media (max-width: 768px) {
  /* 1. Paksa tabel agar bisa digeser ke samping (Scroll) */
  .table-card { max-width: 100vw !important; overflow: hidden !important; }
  .table-wrap { 
    overflow-x: auto !important; 
    display: block !important; 
    width: 100% !important; 
    -webkit-overflow-scrolling: touch; 
    padding-bottom: 10px;
  }
  
  /* 2. Kunci ukuran tabel dan larang teks melipat ke bawah */
  .v-table { min-width: 900px !important; }
  .v-table th, .v-table td { 
    white-space: nowrap !important; 
  }
  
  /* 3. Kembalikan tombol agar berjejer rapi ke samping */
  .v-table td[style*="display:flex"], .actions-wrap { 
    flex-direction: row !important; 
    flex-wrap: nowrap !important; 
    gap: 0.5rem !important; 
  }
  .v-table td .btn-sm { 
    width: auto !important; 
    padding: 0.5rem 0.75rem !important; 
  }
  
  /* 4. Amankan Tab & Header */
  .filter-tabs, div[style*="display:flex;gap:.6rem;margin-bottom:1.25rem;flex-wrap:wrap;"] {
    flex-wrap: nowrap !important;
    overflow-x: auto !important;
    -webkit-overflow-scrolling: touch;
  }
  .ftab { flex-shrink: 0; }
  .stats-grid { grid-template-columns: 1fr 1fr !important; }
}
</style>
  <script src="../assets/app.js" defer></script>
  <style>
    body{display:flex;min-height:100vh;}
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
    .btn-sm{font-family:var(--font-ui);font-size:.75rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:.35rem .9rem;border-radius:6px;text-decoration:none;display:inline-flex;align-items:center;gap:.3rem;transition:opacity .2s;cursor:pointer;border:none;white-space:nowrap;}
    .btn-sm:hover{opacity:.8;}
    .btn-blue{background:rgba(96,165,250,.15);color:#60a5fa;border:1px solid rgba(96,165,250,.3);}
    .btn-green{background:rgba(16,185,129,.2);color:#34d399;border:1px solid rgba(16,185,129,.3);}
    .btn-red{background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);}
    .btn-purple{background:rgba(168,85,247,.15);color:var(--v-lavender);border:1px solid rgba(168,85,247,.3);}
    .ftab{font-family:var(--font-ui);font-size:.8rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:.45rem 1.1rem;border-radius:6px;border:1px solid var(--v-border);background:transparent;color:var(--v-muted);cursor:pointer;transition:all .2s;}
    .ftab:hover,.ftab.active{background:rgba(168,85,247,.15);border-color:var(--v-violet);color:var(--v-lavender);}
    .modal-overlay{position:fixed;inset:0;z-index:200;background:rgba(0,0,0,.7);backdrop-filter:blur(6px);display:none;align-items:center;justify-content:center;padding:1.5rem;}
    .modal-overlay.open{display:flex;}
    .modal-box{background:var(--v-card);border:1px solid var(--v-border);border-radius:20px;width:100%;max-height:88vh;overflow-y:auto;animation:fadeUp .3s ease both;}
    .row-extra{animation:fadeUp .2s ease both;}
    tfoot td{background:transparent;}
    @media(max-width:768px){.main-content{margin-left:0;}}
  </style>
</head>
<body>
<?php include_once "../config/svg_sprite_admin.php"; ?>
<div class="admin-topbar">
  <div style="display:flex;align-items:center;gap:.6rem;">
    <img src="../assets/images/logo-violet.jpeg" alt="Logo" style="height:28px;filter:drop-shadow(0 0 6px rgba(168,85,247,.5));">
    <span class="admin-topbar-brand">VIOLET <span class="neon">PS</span></span>
  </div>
  <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Menu"><span></span><span></span><span></span></button>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
<?php $active_page = 'dashboard'; include __DIR__.'/sidebar.php'; ?>

<main class="main-content">
  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'edit_ok'): ?>
    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#34d399;border-radius:8px;padding:.75rem 1rem;font-family:var(--font-ui);font-size:.85rem;margin-bottom:1.25rem;">✓ Unit berhasil diupdate.</div>
  <?php endif; ?>
  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'hapus_ok'): ?>
    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#34d399;border-radius:8px;padding:.75rem 1rem;font-family:var(--font-ui);font-size:.85rem;margin-bottom:1.25rem;">✓ Unit berhasil dihapus.</div>
  <?php endif; ?>
  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'hapus_gagal'): ?>
    <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);color:#f87171;border-radius:8px;padding:.75rem 1rem;font-family:var(--font-ui);font-size:.85rem;margin-bottom:1.25rem;">✕ Tidak bisa hapus unit — masih ada pengajuan aktif.</div>
  <?php endif; ?>

  <?php if ($total_pending > 0): ?>
  <div style="background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.3);border-radius:10px;padding:.85rem 1.25rem;margin-bottom:1.5rem;display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;">
    <div style="display:flex;align-items:center;gap:.75rem;">
      <span style="font-size:1.2rem;">⏳</span>
      <span style="font-family:var(--font-ui);font-size:.9rem;color:#fbbf24;font-weight:700;"><?php echo $total_pending; ?> pengajuan menunggu persetujuan</span>
    </div>
    <a href="data_sewa.php?filter=pending" style="font-family:var(--font-ui);font-size:.8rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#fbbf24;text-decoration:none;border:1px solid rgba(251,191,36,.4);padding:.3rem .85rem;border-radius:6px;transition:background .2s;">Lihat Sekarang →</a>
  </div>
  <?php endif; ?>

  <div class="admin-header-wrap" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1.5rem;flex-wrap:wrap;gap:1rem;">
    <div class="page-title" style="margin-bottom:0;">DASHBOARD <span class="neon"><?php echo strtoupper($_SESSION['role']); ?></span></div>
    <?php if ($is_admin): ?><a href="tambah_unit.php" class="btn-violet" style="text-decoration:none;display:flex;"><span>+ Tambah Unit</span></a><?php endif; ?>
  </div>

  <div class="stats-grid">
    <div class="stat-card purple"><div class="stat-num"><?php echo $total_units; ?></div><div class="stat-lbl">Total Unit</div></div>
    <div class="stat-card blue"><div class="stat-num"><?php echo $total_units - $unit_disewa; ?></div><div class="stat-lbl">Unit Tersedia</div></div>
    <div class="stat-card orange"><div class="stat-num"><?php echo $unit_disewa; ?></div><div class="stat-lbl">Sedang Disewa</div></div>
    <div class="stat-card green"><div class="stat-num"><?php echo $total_pending; ?></div><div class="stat-lbl">Pending</div></div>
  </div>

  <!-- Unit Tabs -->
  <div style="display:flex;gap:.6rem;margin-bottom:1.25rem;flex-wrap:wrap;">
    <button class="ftab active" id="tab-sewa" onclick="switchTab('sewa')">🎮 Unit Sewa</button>
    <button class="ftab" id="tab-tempat" onclick="switchTab('tempat')">🏠 Main di Tempat</button>
  </div>

  <!-- Panel Sewa -->
  <div id="panel-sewa">
    <?php if (!empty($libur_rows)): ?>
    <div style="background:var(--v-card);border:1px solid rgba(251,191,36,.25);border-radius:14px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem;">
        <div style="font-family:var(--font-ui);font-size:.8rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#fbbf24;">📅 Periode Libur Aktif / Mendatang</div>
        <a href="hari_libur.php" style="font-family:var(--font-ui);font-size:.75rem;letter-spacing:1px;text-transform:uppercase;color:var(--v-muted);text-decoration:none;border:1px solid var(--v-border);padding:.25rem .75rem;border-radius:6px;transition:color .2s,border-color .2s;" onmouseover="this.style.color='var(--v-lavender)';this.style.borderColor='var(--v-violet)'" onmouseout="this.style.color='var(--v-muted)';this.style.borderColor='var(--v-border)'">Kelola →</a>
      </div>
      <div style="display:flex;flex-direction:column;gap:.6rem;">
      <?php foreach ($libur_rows as $lb):
        $n_hari     = (int)(( strtotime($lb['tgl_selesai']) - strtotime($lb['tgl_mulai']) ) / 86400) + 1;
        $aktif_skrg = $lb['tgl_mulai'] <= date('Y-m-d') && $lb['tgl_selesai'] >= date('Y-m-d');
      ?>
      <div style="display:flex;justify-content:space-between;align-items:center;background:rgba(251,191,36,.05);border:1px solid rgba(251,191,36,.15);border-radius:8px;padding:.6rem 1rem;flex-wrap:wrap;gap:.5rem;">
        <div>
          <span style="font-family:var(--font-ui);font-size:.88rem;font-weight:700;color:<?php echo $aktif_skrg ? '#fbbf24' : 'var(--v-muted)'; ?>;"><?php echo htmlspecialchars($lb['keterangan']); ?></span>
          <?php if ($aktif_skrg): ?><span style="background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);font-family:var(--font-ui);font-size:.65rem;padding:.1rem .4rem;border-radius:4px;margin-left:.4rem;">Aktif — No Promo</span><?php endif; ?>
        </div>
        <span style="font-family:var(--font-ui);font-size:.8rem;color:var(--v-muted);white-space:nowrap;">
          <?php echo date('d/m/Y', strtotime($lb['tgl_mulai'])); ?> — <?php echo date('d/m/Y', strtotime($lb['tgl_selesai'])); ?>
          <span style="color:#fbbf24;"> (<?php echo $n_hari; ?> hari)</span>
        </span>
      </div>
      <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <div class="table-card">
      <div class="table-card-header">
        <h3>Unit Sewa</h3>
        <span style="font-family:var(--font-ui);font-size:.8rem;color:var(--v-muted);"><?php echo $total_games; ?> Game Terdaftar</span>
      </div>
      <div class="table-wrap">
        <table class="v-table">
          <thead><tr><th>#</th><th>Nama Unit</th><th>Kategori</th><th>Status</th><th>Aksi</th></tr></thead>
          <tbody id="tbody-sewa">
          <?php
          $no = 1;
          $q = $koneksi->query("SELECT * FROM units WHERE tipe_layanan='Sewa Luar' OR (tipe_layanan='Main di Tempat' AND kategori='PS5') ORDER BY kategori,nama_unit ASC");
          $all_sewa = [];
          while ($r = $q->fetch_assoc()) $all_sewa[] = $r;
          $total_sewa_cnt = count($all_sewa);
          foreach ($all_sewa as $idx_s => $d):
            $kat = $d['kategori'];
            $bc  = $kat === 'PS5' ? 'v-badge-ps5' : ($kat === 'Nintendo' ? 'v-badge-nin' : 'v-badge-ps4');
            $st  = $d['status'];
            $sc  = $st === 'Tersedia' ? 's-tersedia' : ($st === 'Disewa' ? 's-disewa' : 's-maint');
            $hidden = $idx_s >= 5 ? 'class="row-extra row-extra-sewa" style="display:none;"' : '';
          ?>
          <tr <?php echo $hidden; ?>>
            <td style="color:var(--v-muted);"><?php echo $no++; ?></td>
            <td>
              <strong style="color:var(--v-white);"><?php echo htmlspecialchars($d['nama_unit']); ?></strong>
              <?php if ($d['tipe_layanan'] === 'Main di Tempat'): ?>
              <span style="font-size:.7rem;color:#60a5fa;font-family:var(--font-ui);display:block;margin-top:.1rem;">WA dulu</span>
              <?php endif; ?>
            </td>
            <td><span class="v-badge <?php echo $bc; ?>"><?php echo $kat; ?></span></td>
            <td><span class="v-badge <?php echo $sc; ?>"><?php echo $st; ?></span></td>
            <td style="display:flex;gap:.4rem;flex-wrap:wrap;">
              <a href="histori_unit.php?id=<?php echo $d['id_unit']; ?>" class="btn-sm btn-blue" aria-label="Histori <?php echo htmlspecialchars($d['nama_unit']); ?>"><svg width="12" height="12" aria-hidden="true"><use href="#ico-clipboard"/></svg> Histori</a>
              <?php if ($is_admin): ?>
              <a href="isi_unit.php?id=<?php echo $d['id_unit']; ?>" class="btn-sm btn-green" aria-label="Game <?php echo htmlspecialchars($d['nama_unit']); ?>"><svg width="12" height="12" aria-hidden="true"><use href="#ico-gamepad"/></svg> Game</a>
              <a href="edit_unit.php?id=<?php echo $d['id_unit']; ?>" class="btn-sm btn-purple" aria-label="Edit <?php echo htmlspecialchars($d['nama_unit']); ?>"><svg width="12" height="12" aria-hidden="true"><use href="#ico-edit"/></svg> Edit</a>
              <a href="hapus_unit.php?id=<?php echo $d['id_unit']; ?>&_token=<?php echo csrf_get_token(); ?>" class="btn-sm btn-red" aria-label="Hapus <?php echo htmlspecialchars($d['nama_unit']); ?>" onclick="return confirm('Hapus unit ini? Histori transaksinya tetap tersimpan.')"><svg width="12" height="12" aria-hidden="true"><use href="#ico-trash"/></svg></a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
          <?php if ($total_sewa_cnt > 5): ?>
          <tfoot>
            <tr><td colspan="5" style="padding:.75rem 1rem;text-align:center;border-top:1px solid var(--v-border);">
              <button onclick="toggleRows('sewa')" id="btn-viewall-sewa" class="btn-sm btn-purple" style="padding:.4rem 1.25rem;font-size:.78rem;">
                <svg width="12" height="12" style="transition:transform .3s;" id="ico-expand-sewa" aria-hidden="true"><use href="#ico-plus"/></svg>
                Lihat Semua <span id="lbl-sewa"><?php echo $total_sewa_cnt - 5; ?> unit lainnya</span>
              </button>
            </td></tr>
          </tfoot>
          <?php endif; ?>
        </table>
      </div>
    </div>
  </div>

  <!-- Panel Main di Tempat -->
  <div id="panel-tempat" style="display:none;">
    <?php if (!empty($libur_rows)): ?>
    <div style="background:var(--v-card);border:1px solid rgba(251,191,36,.25);border-radius:14px;padding:1.25rem 1.5rem;margin-bottom:1.5rem;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;flex-wrap:wrap;gap:.5rem;">
        <div style="font-family:var(--font-ui);font-size:.8rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:#fbbf24;">📅 Periode Libur Aktif / Mendatang</div>
        <a href="hari_libur.php" style="font-family:var(--font-ui);font-size:.75rem;letter-spacing:1px;text-transform:uppercase;color:var(--v-muted);text-decoration:none;border:1px solid var(--v-border);padding:.25rem .75rem;border-radius:6px;transition:color .2s,border-color .2s;" onmouseover="this.style.color='var(--v-lavender)';this.style.borderColor='var(--v-violet)'" onmouseout="this.style.color='var(--v-muted)';this.style.borderColor='var(--v-border)'">Kelola →</a>
      </div>
      <div style="display:flex;flex-direction:column;gap:.6rem;">
      <?php foreach ($libur_rows as $lb):
        $n_hari     = (int)(( strtotime($lb['tgl_selesai']) - strtotime($lb['tgl_mulai']) ) / 86400) + 1;
        $aktif_skrg = $lb['tgl_mulai'] <= date('Y-m-d') && $lb['tgl_selesai'] >= date('Y-m-d');
      ?>
      <div style="display:flex;justify-content:space-between;align-items:center;background:rgba(251,191,36,.05);border:1px solid rgba(251,191,36,.15);border-radius:8px;padding:.6rem 1rem;flex-wrap:wrap;gap:.5rem;">
        <div>
          <span style="font-family:var(--font-ui);font-size:.88rem;font-weight:700;color:<?php echo $aktif_skrg ? '#fbbf24' : 'var(--v-muted)'; ?>;"><?php echo htmlspecialchars($lb['keterangan']); ?></span>
          <?php if ($aktif_skrg): ?><span style="background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);font-family:var(--font-ui);font-size:.65rem;padding:.1rem .4rem;border-radius:4px;margin-left:.4rem;">Aktif — No Promo</span><?php endif; ?>
        </div>
        <span style="font-family:var(--font-ui);font-size:.8rem;color:var(--v-muted);white-space:nowrap;">
          <?php echo date('d/m/Y', strtotime($lb['tgl_mulai'])); ?> — <?php echo date('d/m/Y', strtotime($lb['tgl_selesai'])); ?>
          <span style="color:#fbbf24;"> (<?php echo $n_hari; ?> hari)</span>
        </span>
      </div>
      <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>

    <div class="table-card">
      <div class="table-card-header"><h3>Unit Main di Tempat</h3></div>
      <div class="table-wrap">
        <table class="v-table">
          <thead><tr><th>#</th><th>Nama Unit</th><th>Kategori</th><th>Aksi</th></tr></thead>
          <tbody id="tbody-tempat">
          <?php
          $no = 1;
          $q  = $koneksi->query("SELECT u.*, COUNT(ug.id_game) as jml_game FROM units u LEFT JOIN unit_games ug ON u.id_unit=ug.id_unit WHERE u.tipe_layanan='Main di Tempat' AND u.kategori != 'PS5' GROUP BY u.id_unit ORDER BY u.kategori,u.nama_unit ASC");
          $all_tempat = [];
          while ($r = $q->fetch_assoc()) $all_tempat[] = $r;
          $total_tempat_cnt = count($all_tempat);
          foreach ($all_tempat as $idx_t => $d):
            $kat      = $d['kategori'];
            $bc       = $kat === 'Nintendo' ? 'v-badge-nin' : 'v-badge-ps4';
            $hidden_t = $idx_t >= 5 ? 'class="row-extra row-extra-tempat" style="display:none;"' : '';
          ?>
          <tr <?php echo $hidden_t; ?>>
            <td style="color:var(--v-muted);"><?php echo $no++; ?></td>
            <td><strong style="color:var(--v-white);"><?php echo htmlspecialchars($d['nama_unit']); ?></strong></td>
            <td><span class="v-badge <?php echo $bc; ?>"><?php echo $kat; ?></span></td>
            <td style="display:flex;gap:.4rem;flex-wrap:wrap;">
              <button class="btn-sm btn-purple" onclick="lihatGame(<?php echo $d['id_unit']; ?>,'<?php echo htmlspecialchars(addslashes($d['nama_unit'])); ?>')"><svg width="12" height="12" aria-hidden="true"><use href="#ico-gamepad"/></svg> Game (<?php echo $d['jml_game']; ?>)</button>
              <?php if ($is_admin): ?>
              <a href="isi_unit.php?id=<?php echo $d['id_unit']; ?>" class="btn-sm btn-green"><svg width="12" height="12" aria-hidden="true"><use href="#ico-edit"/></svg> Isi</a>
              <a href="edit_unit.php?id=<?php echo $d['id_unit']; ?>" class="btn-sm btn-purple"><svg width="12" height="12" aria-hidden="true"><use href="#ico-edit"/></svg> Edit</a>
              <a href="hapus_unit.php?id=<?php echo $d['id_unit']; ?>&_token=<?php echo csrf_get_token(); ?>" class="btn-sm btn-red" aria-label="Hapus <?php echo htmlspecialchars($d['nama_unit']); ?>" onclick="return confirm('Hapus unit ini?')"><svg width="12" height="12" aria-hidden="true"><use href="#ico-trash"/></svg></a>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
          <?php if ($total_tempat_cnt > 5): ?>
          <tfoot>
            <tr><td colspan="4" style="padding:.75rem 1rem;text-align:center;border-top:1px solid var(--v-border);">
              <button onclick="toggleRows('tempat')" id="btn-viewall-tempat" class="btn-sm btn-purple" style="padding:.4rem 1.25rem;font-size:.78rem;">
                <svg width="12" height="12" style="transition:transform .3s;" id="ico-expand-tempat" aria-hidden="true"><use href="#ico-plus"/></svg>
                Lihat Semua <span id="lbl-tempat"><?php echo $total_tempat_cnt - 5; ?> unit lainnya</span>
              </button>
            </td></tr>
          </tfoot>
          <?php endif; ?>
        </table>
      </div>
    </div>
  </div>
</main>

<!-- Modal Game Unit Tempat -->
<div class="modal-overlay" id="modalGame" role="dialog" aria-modal="true" aria-labelledby="mg-nama">
  <div class="modal-box" style="max-width:580px;">
    <div style="padding:1.5rem 1.5rem 1rem;border-bottom:1px solid var(--v-border);display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;background:var(--v-card);">
      <div>
        <div style="font-family:var(--font-ui);font-size:.72rem;letter-spacing:2px;text-transform:uppercase;color:var(--v-muted);margin-bottom:.25rem;">Daftar Game</div>
        <div style="font-family:var(--font-display);font-size:1.2rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;color:var(--v-lavender);" id="mg-nama"></div>
      </div>
      <button onclick="document.getElementById('modalGame').classList.remove('open')" style="background:rgba(255,255,255,.05);border:1px solid var(--v-border);border-radius:8px;width:34px;height:34px;cursor:pointer;color:var(--v-muted);font-size:1rem;" aria-label="Tutup modal">✕</button>
    </div>
    <div style="padding:1.25rem 1.5rem;" id="mg-list"></div>
  </div>
</div>

<?php
$all_unit_games = [];
$qug = $koneksi->query("SELECT ug.id_unit,g.judul_game,g.kategori_game FROM unit_games ug JOIN games g ON ug.id_game=g.id_game ORDER BY g.judul_game");
while ($r = $qug->fetch_assoc()) $all_unit_games[$r['id_unit']][] = $r;
?>
<script>
const unitGames = <?php echo json_encode($all_unit_games); ?>;

function lihatGame(id, nama) {
    document.getElementById('mg-nama').textContent = nama;
    const games = unitGames[id] || [];
    const el    = document.getElementById('mg-list');
    if (!games.length) {
        el.innerHTML = '<div style="text-align:center;padding:2rem;color:var(--v-muted);font-family:var(--font-ui);font-size:.85rem;">Belum ada game di unit ini.</div>';
    } else {
        el.innerHTML = '<div style="display:flex;flex-wrap:wrap;gap:.5rem;">' +
            games.map(g => `<span style="background:rgba(255,255,255,.04);border:1px solid var(--v-border);border-radius:8px;padding:.35rem .75rem;font-family:var(--font-ui);font-size:.82rem;color:#C4B5D4;">${g.judul_game}</span>`).join('') +
        '</div>';
    }
    document.getElementById('modalGame').classList.add('open');
}
document.getElementById('modalGame').addEventListener('click', e => { if (e.target === document.getElementById('modalGame')) document.getElementById('modalGame').classList.remove('open'); });

function switchTab(tab) {
    document.getElementById('panel-sewa').style.display  = tab === 'sewa'   ? 'block' : 'none';
    document.getElementById('panel-tempat').style.display = tab === 'tempat' ? 'block' : 'none';
    document.getElementById('tab-sewa').classList.toggle('active',   tab === 'sewa');
    document.getElementById('tab-tempat').classList.toggle('active', tab === 'tempat');
}

function toggleRows(group) {
    const rows  = document.querySelectorAll('.row-extra-' + group);
    const btn   = document.getElementById('btn-viewall-' + group);
    const lbl   = document.getElementById('lbl-' + group);
    const ico   = document.getElementById('ico-expand-' + group);
    const isOpen = rows[0]?.style.display !== 'none';
    rows.forEach(r => { r.style.display = isOpen ? 'none' : ''; });
    if (isOpen) {
        lbl.textContent = rows.length + ' unit lainnya';
        ico.style.transform = 'rotate(0deg)';
        btn.querySelector('use').setAttribute('href', '#ico-plus');
        btn.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    } else {
        lbl.textContent = 'Sembunyikan';
        ico.style.transform = 'rotate(45deg)';
        btn.querySelector('use').setAttribute('href', '#ico-x');
    }
}


function toggleSidebar(){document.querySelector('.sidebar').classList.toggle('mobile-open');document.getElementById('sidebarOverlay').classList.toggle('open');document.body.style.overflow=document.querySelector('.sidebar').classList.contains('mobile-open')?'hidden':'';}
function closeSidebar(){document.querySelector('.sidebar').classList.remove('mobile-open');document.getElementById('sidebarOverlay').classList.remove('open');document.body.style.overflow='';}
</script>
</body>
</html>
