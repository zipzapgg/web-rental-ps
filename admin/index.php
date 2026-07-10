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

// ── FITUR LOG AKTIVITAS (KHUSUS ADMIN) ────────────────────────────────────
$data_log = null;
if ($is_admin) {
    $cek_tabel = $koneksi->query("SHOW TABLES LIKE 'activity_logs'");
    if ($cek_tabel->num_rows > 0) {
        $sql_log = "SELECT l.*, a.username FROM activity_logs l 
                    JOIN admin a ON l.id_admin = a.id_admin 
                    ORDER BY l.created_at DESC LIMIT 20";
        $data_log = $koneksi->query($sql_log);
    }
}

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

// ── LIVE MONITORING SEWA AKTIF ────────────────────────────────────────────
$q_aktif = $koneksi->query("
    SELECT p.id_pengajuan, p.nama_penyewa, p.durasi, p.tgl_ambil, p.tgl_pengajuan, u.nama_unit, u.kategori 
    FROM pengajuan p
    JOIN units u ON p.id_unit = u.id_unit
    WHERE p.status_pengajuan = 'Disetujui'
    ORDER BY p.tgl_ambil ASC
");
$sewa_aktif = [];
while ($r = $q_aktif->fetch_assoc()) {
    $sewa_aktif[] = $r;
}

// ── Libur aktif & mendatang ─────────────────────────────────────────────────
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
  <title>Dashboard  Violet PlayStation</title>
  <link rel="stylesheet" href="../assets/css/violet.css?v=<?php echo time(); ?>">
  <script src="../assets/app.js" defer></script>
</head>
<body>
<?php include_once "../config/svg_sprite_admin.php"; ?>
<div class="admin-topbar">
  <div style="display:flex;align-items:center;gap:.6rem;">
    <img src="../assets/images/logo-violet.jpeg" alt="Logo" style="height:28px;filter:drop-shadow(0 0 6px rgba(157, 86, 255,.5));">
    <span class="admin-topbar-brand">VIOLET <span class="neon">PS</span></span>
  </div>
  <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Menu"><span></span><span></span><span></span></button>
</div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
<?php $active_page = 'dashboard'; include __DIR__.'/sidebar.php'; ?>

<main class="main-content">
  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'edit_ok'): ?>
    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#34d399;border-radius:8px;padding:.75rem 1rem;font-family:var(--font-ui);font-size:.85rem;margin-bottom:1.25rem;">✓ Perubahan berhasil disimpan.</div>
  <?php endif; ?>
  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'maint_gagal'): ?>
    <div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);color:#f87171;border-radius:8px;padding:.75rem 1rem;font-family:var(--font-ui);font-size:.85rem;margin-bottom:1.25rem;">✕ Keterangan maintenance wajib diisi!</div>
  <?php endif; ?>
  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'hapus_ok'): ?>
    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#34d399;border-radius:8px;padding:.75rem 1rem;font-family:var(--font-ui);font-size:.85rem;margin-bottom:1.25rem;">✓ Unit berhasil dihapus.</div>
  <?php endif; ?>
  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'qe_ok'): ?>
    <div style="background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#34d399;border-radius:8px;padding:.75rem 1rem;font-family:var(--font-ui);font-size:.85rem;margin-bottom:1.25rem;">
        ⚡ Quick Entry Berhasil! Unit langsung masuk ke Live Monitoring.
    </div>
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
    
    <div style="display:flex; gap: .75rem; flex-wrap:wrap;">
      <button onclick="document.getElementById('modalQuick').classList.add('open')" class="btn-sm btn-green" style="padding:.6rem 1.25rem; font-size:.85rem;">⚡ Quick Entry</button>
      <?php if ($is_admin): ?><a href="tambah_unit.php" class="btn-violet" style="text-decoration:none;display:flex;"><span>+ Tambah Unit</span></a><?php endif; ?>
    </div>
  </div>

  <div class="stats-grid">
    <div class="stat-card purple"><div class="stat-num"><?php echo $total_units; ?></div><div class="stat-lbl">Total Unit</div></div>
    <div class="stat-card blue"><div class="stat-num"><?php echo $total_units - $unit_disewa; ?></div><div class="stat-lbl">Unit Tersedia</div></div>
    <div class="stat-card orange"><div class="stat-num"><?php echo $unit_disewa; ?></div><div class="stat-lbl">Sedang Disewa</div></div>
    <div class="stat-card green"><div class="stat-num"><?php echo $total_pending; ?></div><div class="stat-lbl">Pending</div></div>
  </div>

  <?php if (!empty($sewa_aktif)): ?>
  <div style="margin-bottom: 2.5rem;">
    <div style="display:flex; align-items:center; gap:.75rem; margin-bottom:1.25rem;">
      <div style="width:12px; height:12px; background:#ef4444; border-radius:50%; animation: pulseAlert 1.5s infinite;"></div>
      <h3 style="font-family:var(--font-display); font-size:1.3rem; font-weight:800; letter-spacing:2px; text-transform:uppercase; color:var(--v-white); margin:0;">Live <span style="color:#ef4444;">Monitoring</span></h3>
    </div>
    
    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap:1rem;">
     <?php foreach ($sewa_aktif as $ra): 
        preg_match('/(\d+)/', $ra['durasi'], $m);
        $hari = intval($m[1] ?? 1);
        
        // ── PERBAIKAN: Hitung waktu cerdas berdasarkan tipe sewa ──
        $tgl_ambil_date = $ra['tgl_ambil']; 
        $tgl_input_date = date('Y-m-d', strtotime($ra['tgl_pengajuan']));
        
        if ($tgl_ambil_date === $tgl_input_date) {
            // Skenario 1: Quick Entry / Langsung Ambil. 
            // Titik mulai (Start) adalah jam, menit, detik saat itu juga.
            $waktu_start = $ra['tgl_pengajuan'];
        } else {
            // Skenario 2: Booking via Web untuk masa depan.
            // Titik mulai (Start) diasumsikan jam 12:00 Siang di hari H.
            $waktu_start = $tgl_ambil_date . ' 12:00:00'; 
        }
        
        $start_ts  = strtotime($waktu_start) * 1000;
        // Target Selesai = Waktu Mulai + (Jumlah Hari * 24 Jam)
        $target_ts = (strtotime($waktu_start) + ($hari * 86400)) * 1000;
        
        $kat_badge = $ra['kategori'] === 'PS5' ? 'v-badge-ps5' : ($ra['kategori'] === 'Nintendo' ? 'v-badge-nin' : 'v-badge-ps4');
      ?>
      <div class="v-card rental-card" data-start="<?php echo $start_ts; ?>" data-target="<?php echo $target_ts; ?>" style="padding:1.25rem; transition: border-color 0.3s, box-shadow 0.3s;">
        <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:.75rem;">
          <div>
            <span class="v-badge <?php echo $kat_badge; ?>" style="font-size:.65rem; padding:.15rem .4rem;"><?php echo $ra['kategori']; ?></span>
            <div style="font-family:var(--font-ui); font-size:1.05rem; font-weight:700; color:var(--v-white); margin-top:.35rem;"><?php echo htmlspecialchars($ra['nama_unit']); ?></div>
          </div>
          <div style="text-align:right;">
            <div style="font-family:var(--font-ui); font-size:.75rem; color:var(--v-muted); text-transform:uppercase; letter-spacing:1px;">Penyewa</div>
            <strong style="font-size:.85rem; color:var(--v-lavender);"><?php echo htmlspecialchars($ra['nama_penyewa']); ?></strong>
          </div>
        </div>
        
        <div style="background:rgba(255,255,255,.03); border:1px solid var(--v-border); border-radius:8px; padding:.75rem; margin-bottom:1rem;">
          <div style="display:flex; justify-content:space-between; font-family:var(--font-ui); font-size:.85rem; margin-bottom:.5rem;">
            <span style="color:var(--v-muted);">Sisa Waktu</span>
            <strong class="time-left" style="color:var(--v-white); letter-spacing: 0.5px;">Menghitung...</strong>
          </div>
          <div style="height:6px; background:rgba(255,255,255,.05); border-radius:3px; overflow:hidden;">
            <div class="prog-bar" style="height:100%; width:0%; background:var(--v-violet); transition:width 1s linear, background-color .3s;"></div>
          </div>
        </div>
        
        <a href="data_sewa.php?filter=terima" class="btn-sm btn-purple" style="width:100%; justify-content:center; padding: .5rem;">Proses Transaksi →</a>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <div style="display:flex;gap:.6rem;margin-bottom:1.25rem;flex-wrap:wrap;">
    <button class="ftab active" id="tab-sewa" onclick="switchTab('sewa')">🎮 Unit Sewa</button>
    <button class="ftab" id="tab-tempat" onclick="switchTab('tempat')">🏠 Main di Tempat</button>
  </div>

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
          <?php if ($aktif_skrg): ?><span style="background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);font-family:var(--font-ui);font-size:.65rem;padding:.1rem .4rem;border-radius:4px;margin-left:.4rem;">Aktif  No Promo</span><?php endif; ?>
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
$q = $koneksi->query("SELECT * FROM units WHERE tipe_layanan='Sewa Luar' OR (tipe_layanan='Main di Tempat' AND kategori='PS5')");
$all_sewa = [];
while ($r = $q->fetch_assoc()) $all_sewa[] = $r;

// ── PERBAIKAN: Natural Sort untuk Unit Sewa ──
usort($all_sewa, function($a, $b) {
    $cmp = strcmp($a['kategori'], $b['kategori']); // Urutkan Kategori dulu (Nintendo, PS4, PS5)
    if ($cmp === 0) {
        // Jika kategorinya sama, gunakan Natural Sort untuk Nama Unit (1, 2, 3 ... 10, 11)
        return strnatcasecmp($a['nama_unit'], $b['nama_unit']);
    }
    return $cmp;
});

$total_sewa_cnt = count($all_sewa);
          foreach ($all_sewa as $idx_s => $d):
            $kat = $d['kategori'];
            $bc  = $kat === 'PS5' ? 'v-badge-ps5' : ($kat === 'Nintendo' ? 'v-badge-nin' : 'v-badge-ps4');
            $st  = $d['status'];
            $sc  = $st === 'Tersedia' ? 's-tersedia' : ($st === 'Disewa' ? 's-disewa' : 's-maint');
            $hidden = $idx_s >= 5 ? 'class="row-extra row-extra-sewa" style="display:none;"' : '';
          ?>
          <tr <?php echo $hidden; ?>>
            <td style="color:var(--v-muted);font-weight:600;"><?php echo $idx_s + 1; ?></td>
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
              
              <?php if ($st === 'Tersedia'): ?>
                <button class="btn-sm btn-red" onclick="bukaMaint(<?php echo $d['id_unit']; ?>,'<?php echo htmlspecialchars(addslashes($d['nama_unit'])); ?>')">🔧 Maintenance</button>
              <?php elseif ($st === 'Maintenance'): ?>
                <a href="proses_maintenance.php?aksi=selesai&id=<?php echo $d['id_unit']; ?>&_token=<?php echo csrf_get_token(); ?>" class="btn-sm btn-green" onclick="return confirm('Unit sudah selesai diperbaiki dan siap disewa?')">✓ Selesai Maint</a>
              <?php endif; ?>

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
          <?php if ($aktif_skrg): ?><span style="background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);font-family:var(--font-ui);font-size:.65rem;padding:.1rem .4rem;border-radius:4px;margin-left:.4rem;">Aktif  No Promo</span><?php endif; ?>
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
$q  = $koneksi->query("SELECT u.*, COUNT(ug.id_game) as jml_game FROM units u LEFT JOIN unit_games ug ON u.id_unit=ug.id_unit WHERE u.tipe_layanan='Main di Tempat' AND u.kategori != 'PS5' GROUP BY u.id_unit");
$all_tempat = [];
while ($r = $q->fetch_assoc()) $all_tempat[] = $r;

// ── PERBAIKAN: Natural Sort untuk Unit Tempat ──
usort($all_tempat, function($a, $b) {
    $cmp = strcmp($a['kategori'], $b['kategori']);
    if ($cmp === 0) {
        return strnatcasecmp($a['nama_unit'], $b['nama_unit']);
    }
    return $cmp;
});

$total_tempat_cnt = count($all_tempat);
          $total_tempat_cnt = count($all_tempat);
          foreach ($all_tempat as $idx_t => $d):
            $kat      = $d['kategori'];
            $bc       = $kat === 'Nintendo' ? 'v-badge-nin' : 'v-badge-ps4';
            $st_t     = $d['status']; 
            $hidden_t = $idx_t >= 5 ? 'class="row-extra row-extra-tempat" style="display:none;"' : '';
          ?>
          <tr <?php echo $hidden_t; ?>>
            <td style="color:var(--v-muted);font-weight:600;"><?php echo $idx_t + 1; ?></td>
            <td>
              <strong style="color:var(--v-white);"><?php echo htmlspecialchars($d['nama_unit']); ?></strong>
              <?php if ($st_t === 'Maintenance'): ?>
                <span style="font-size:.65rem;background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);padding:.1rem .4rem;border-radius:4px;margin-left:.4rem;font-weight:700;">MAINTENANCE</span>
              <?php endif; ?>
            </td>
            <td><span class="v-badge <?php echo $bc; ?>"><?php echo $kat; ?></span></td>
            <td style="display:flex;gap:.4rem;flex-wrap:wrap;">
              <button class="btn-sm btn-purple" onclick="lihatGame(<?php echo $d['id_unit']; ?>,'<?php echo htmlspecialchars(addslashes($d['nama_unit'])); ?>')"><svg width="12" height="12" aria-hidden="true"><use href="#ico-gamepad"/></svg> Game (<?php echo $d['jml_game']; ?>)</button>
              <?php if ($is_admin): ?>
              
              <?php if ($st_t === 'Tersedia'): ?>
                <button class="btn-sm btn-red" onclick="bukaMaint(<?php echo $d['id_unit']; ?>,'<?php echo htmlspecialchars(addslashes($d['nama_unit'])); ?>')">🔧 Maintenance</button>
              <?php elseif ($st_t === 'Maintenance'): ?>
                <a href="proses_maintenance.php?aksi=selesai&id=<?php echo $d['id_unit']; ?>&_token=<?php echo csrf_get_token(); ?>" class="btn-sm btn-green" onclick="return confirm('Unit sudah selesai diperbaiki dan siap dimainkan?')">✓ Selesai Maint</a>
              <?php endif; ?>

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

  <?php if ($is_admin): ?>
  <div class="table-card" style="margin-top: 2.5rem; border-color: rgba(157, 86, 255,.3);">
    <div class="table-card-header" style="background: rgba(157, 86, 255,.05);">
      <h3><span class="neon">Log Aktivitas</span> Sistem</h3>
      <span style="font-family:var(--font-ui);font-size:.75rem;color:var(--v-muted);">20 Aktivitas Terakhir</span>
    </div>
    <div class="table-wrap">
      <table class="v-table">
        <thead>
          <tr>
            <th>Waktu</th>
            <th>Pengguna</th>
            <th>Aksi</th>
            <th>Detail Aktivitas</th>
            <th>IP Address</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($data_log && $data_log->num_rows > 0): ?>
            <?php while($log = $data_log->fetch_assoc()): ?>
            <tr>
              <td style="font-size:.8rem;color:var(--v-muted);white-space:nowrap;">
                <?php echo date('d/m/Y', strtotime($log['created_at'])); ?><br>
                <span style="font-size:.75rem;color:#7C6D8A;"><?php echo date('H:i:s', strtotime($log['created_at'])); ?></span>
              </td>
              <td>
                <strong style="color:var(--v-lavender); font-size:.9rem;"><?php echo htmlspecialchars($log['username']); ?></strong>
              </td>
              <td>
                <span class="v-badge v-badge-ps4" style="font-size:.65rem; letter-spacing: 1px; padding: .2rem .5rem;">
                  <?php echo htmlspecialchars(str_replace('_', ' ', $log['aksi'])); ?>
                </span>
              </td>
              <td style="color:var(--v-white);font-size:.85rem;">
                <?php echo htmlspecialchars($log['deskripsi']); ?>
              </td>
              <td style="font-family:monospace;font-size:.75rem;color:var(--v-muted);">
                <?php echo htmlspecialchars($log['ip_address']); ?>
              </td>
            </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="empty-td" style="text-align:center;padding:2.5rem;color:var(--v-muted);font-family:var(--font-ui);font-size:.85rem;">
                Belum ada aktivitas yang tercatat.
              </td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
  <?php endif; ?>

</main>

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

<div class="modal-overlay" id="modalMaint" role="dialog" aria-modal="true">
  <div class="modal-box" style="max-width:400px;">
    <div style="font-family:var(--font-display);font-size:1.3rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;margin-bottom:1.5rem;color:#f87171;">🔧 Maintenance Unit</div>
    <form action="proses_maintenance.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="aksi" value="mulai">
      <input type="hidden" name="id_unit" id="maint-id">
      
      <p style="color:var(--v-muted);font-size:.9rem;margin-bottom:1rem;">
        Pindahkan <strong id="maint-nama" style="color:var(--v-white);"></strong> ke mode perbaikan. Unit ini otomatis disembunyikan dari halaman pelanggan.
      </p>
      
      <div style="margin-bottom: 1.5rem;">
        <label style="font-family:var(--font-ui);font-size:.9rem;font-weight:600;letter-spacing:1px;color:var(--v-muted);text-transform:uppercase;display:block;margin-bottom:.4rem;">Keterangan / Kendala (Wajib)</label>
        <input type="text" name="keterangan" class="v-input" style="background:#0D0D1A;border:1px solid var(--v-border);border-radius:8px;color:var(--v-white);padding:.75rem 1rem;width:100%;" placeholder="Misal: Stick drift, Overheat..." required>
      </div>

      <div style="display:flex;gap:.75rem;justify-content:flex-end;">
        <button type="button" onclick="document.getElementById('modalMaint').classList.remove('open')" class="btn-sm btn-blue" style="padding:.6rem 1.25rem;">Batal</button>
        <button type="submit" class="btn-sm btn-red" style="padding:.6rem 1.25rem;">Simpan</button>
      </div>
    </form>
  </div>
</div>

<?php
$all_unit_games = [];
$qug = $koneksi->query("SELECT ug.id_unit,g.judul_game,g.kategori_game FROM unit_games ug JOIN games g ON ug.id_game=g.id_game ORDER BY g.judul_game");
while ($r = $qug->fetch_assoc()) $all_unit_games[$r['id_unit']][] = $r;
?>
<div class="modal-overlay" id="modalQuick" role="dialog" aria-modal="true">
  <div class="modal-box" style="max-width:500px;">
    <div style="font-family:var(--font-display);font-size:1.3rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;margin-bottom:1.5rem;color:#34d399;">⚡ Quick Entry</div>
    <form action="proses_quick_entry.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="foto_ktp" id="qe-ktp">
      <input type="hidden" name="foto_stnk" id="qe-stnk">

      <div style="margin-bottom: 1.25rem;">
        <label style="font-family:var(--font-ui);font-size:.85rem;color:var(--v-muted);text-transform:uppercase;display:block;margin-bottom:.4rem;">1. Cari Nomor WA (Wajib)</label>
        <div style="display:flex; gap:.5rem;">
          <input type="text" name="no_wa" id="qe-wa" class="v-input" style="background:#0D0D1A;border:1px solid var(--v-border);color:var(--v-white);" placeholder="Contoh: 08123..." required>
          <button type="button" onclick="cariPelanggan()" class="btn-sm btn-blue" style="padding:0 1.25rem;">Cari</button>
        </div>
        <div id="qe-msg" style="font-size:.8rem; margin-top:.4rem; font-family:var(--font-ui);"></div>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="font-family:var(--font-ui);font-size:.85rem;color:var(--v-muted);text-transform:uppercase;display:block;margin-bottom:.4rem;">Nama Pelanggan</label>
        <input type="text" name="nama_penyewa" id="qe-nama" class="v-input" style="background:#0D0D1A;border:1px solid var(--v-border);color:var(--v-white);" required readonly>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="font-family:var(--font-ui);font-size:.85rem;color:var(--v-muted);text-transform:uppercase;display:block;margin-bottom:.4rem;">Alamat</label>
        <input type="text" name="alamat" id="qe-alamat" class="v-input" style="background:#0D0D1A;border:1px solid var(--v-border);color:var(--v-white);" required readonly>
      </div>

      <div style="margin-bottom: 1.25rem;">
        <label style="font-family:var(--font-ui);font-size:.85rem;color:var(--v-muted);text-transform:uppercase;display:block;margin-bottom:.4rem;">2. Pilih Unit Tersedia</label>
        <select name="id_unit" class="v-input" style="background:#0D0D1A;border:1px solid var(--v-border);color:var(--v-white);" required>
          <option value="">-- Pilih Unit --</option>
          <?php
          $qu = $koneksi->query("SELECT id_unit, nama_unit, kategori FROM units WHERE status='Tersedia' AND (tipe_layanan='Sewa Luar' OR kategori='PS5') ORDER BY kategori, nama_unit");
          while($u = $qu->fetch_assoc()){
              echo "<option value='{$u['id_unit']}'>{$u['kategori']} - {$u['nama_unit']}</option>";
          }
          ?>
        </select>
      </div>

      <div style="display:flex; gap:1rem; margin-bottom: 1.5rem;">
        <div style="flex:1;">
            <label style="font-family:var(--font-ui);font-size:.85rem;color:var(--v-muted);text-transform:uppercase;display:block;margin-bottom:.4rem;">Durasi (Hari)</label>
            <input type="number" name="durasi" class="v-input" style="background:#0D0D1A;border:1px solid var(--v-border);color:var(--v-white);" min="1" max="14" value="1" required>
        </div>
        <div style="flex:1; display:flex; align-items:center; padding-top:1.5rem;">
            <label style="display:flex; align-items:center; gap:.5rem; cursor:pointer; color:var(--v-white); font-family:var(--font-ui); font-size:.9rem;">
                <input type="checkbox" name="pakai_playbox" value="1" style="width:18px; height:18px; accent-color:var(--v-violet);">
                + Playbox
            </label>
        </div>
      </div>

      <div style="display:flex;gap:.75rem;justify-content:flex-end;">
        <button type="button" onclick="document.getElementById('modalQuick').classList.remove('open')" class="btn-sm btn-red" style="padding:.6rem 1.25rem;">Batal</button>
        <button type="submit" id="qe-submit" class="btn-sm btn-green" style="padding:.6rem 1.25rem;" disabled>✓ Proses Langsung</button>
      </div>
    </form>
  </div>
</div>
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

function bukaMaint(id, nama) {
    document.getElementById('maint-id').value = id;
    document.getElementById('maint-nama').textContent = nama;
    document.getElementById('modalMaint').classList.add('open');
}
document.getElementById('modalMaint').addEventListener('click', function(e) { 
    if (e.target === this) this.classList.remove('open'); 
});

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

// ── LIVE MONITORING TICKER ──
function updateLiveMonitors() {
    const now = new Date().getTime();
    document.querySelectorAll('.rental-card').forEach(card => {
        const start  = parseInt(card.getAttribute('data-start'));
        const target = parseInt(card.getAttribute('data-target'));
        const timeEl = card.querySelector('.time-left');
        const barEl  = card.querySelector('.prog-bar');
        
        const totalDuration = target - start;
        const elapsed = now - start;
        let progress = (elapsed / totalDuration) * 100;
        
        if (progress < 0) progress = 0;
        if (progress > 100) progress = 100;
        
        const diff = target - now;
        
        if (diff <= 0) {
            timeEl.innerHTML = '<span style="color:#ef4444;">Waktu Habis!</span>';
            barEl.style.width = '100%';
            barEl.style.background = '#ef4444';
            card.style.borderColor = 'rgba(239,68,68,.4)';
            card.style.boxShadow = '0 0 15px rgba(239,68,68,.15)';
        } else {
            const d = Math.floor(diff / (1000 * 60 * 60 * 24));
            const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
            
            let text = '';
            if (d > 0) text += `${d} Hari `;
            text += `${h}j ${m}m`;
            timeEl.textContent = text;
            
            barEl.style.width = progress + '%';
            
            // Warning: Sisa waktu kurang dari 5 jam
            if (d === 0 && h < 5) {
                timeEl.style.color = '#f59e0b';
                barEl.style.background = '#f59e0b';
                card.style.borderColor = 'rgba(245,158,11,.4)';
            }
        }
    });
}

// Jalankan ticker jika ada kartu monitoring
if (document.querySelector('.rental-card')) {
    updateLiveMonitors();
    setInterval(updateLiveMonitors, 60000); // Update setiap 1 menit
}
// ── AJAX QUICK ENTRY ──
document.getElementById('modalQuick').addEventListener('click', function(e) { 
    if (e.target === this) this.classList.remove('open'); 
});

function cariPelanggan() {
    const wa = document.getElementById('qe-wa').value;
    const msg = document.getElementById('qe-msg');
    if (!wa) return;

    msg.textContent = 'Mencari data...';
    msg.style.color = 'var(--v-muted)';

    fetch('api_pelanggan.php?wa=' + wa)
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('qe-nama').value = data.data.nama_penyewa;
            document.getElementById('qe-alamat').value = data.data.alamat;
            document.getElementById('qe-ktp').value = data.data.foto_ktp;
            document.getElementById('qe-stnk').value = data.data.foto_stnk;

            // Buka kunci form jika pelanggan ditemukan
            document.getElementById('qe-nama').removeAttribute('readonly');
            document.getElementById('qe-alamat').removeAttribute('readonly');
            document.getElementById('qe-submit').removeAttribute('disabled');

            msg.textContent = '✓ Data langganan ditemukan!';
            msg.style.color = '#34d399';
        } else {
            msg.textContent = '✕ Pelanggan belum pernah menyewa. Harus sewa manual lewat depan.';
            msg.style.color = '#f87171';
            
            // Kunci form demi keamanan
            document.getElementById('qe-nama').value = '';
            document.getElementById('qe-alamat').value = '';
            document.getElementById('qe-nama').setAttribute('readonly', true);
            document.getElementById('qe-alamat').setAttribute('readonly', true);
            document.getElementById('qe-submit').setAttribute('disabled', true);
        }
    })
    .catch(err => {
        msg.textContent = 'Terjadi kesalahan jaringan.';
        msg.style.color = '#f87171';
    });
}
</script>
</body>
</html>