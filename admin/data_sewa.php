<?php
require_once '../config/koneksi.php';
require_login('login.php');
$is_admin = is_admin();

// ── Aksi ───────────────────────────────────────────────────────────────────
if (isset($_GET['aksi'], $_GET['id'])) {
    csrf_get_check();
    $id   = intval($_GET['id']);
    $aksi = $_GET['aksi'];

    if ($aksi === 'terima') {
        $s = $koneksi->prepare("UPDATE pengajuan SET status_pengajuan='Disetujui' WHERE id_pengajuan=?");
        $s->bind_param("i", $id);
        $s->execute();
        $s->close();
        
        // --- LOG AKTIVITAS ---
        if (function_exists('log_activity')) log_activity($koneksi, 'TERIMA_SEWA', "Menyetujui pengajuan sewa ID: $id");

        header("Location: data_sewa.php?msg=terima");
        exit();

    } elseif ($aksi === 'tolak') {
        $s = $koneksi->prepare("SELECT id_unit FROM pengajuan WHERE id_pengajuan=?");
        $s->bind_param("i", $id);
        $s->execute();
        $id_unit = $s->get_result()->fetch_assoc()['id_unit'] ?? 0;
        $s->close();

        $s = $koneksi->prepare("UPDATE pengajuan SET status_pengajuan='Ditolak' WHERE id_pengajuan=?");
        $s->bind_param("i", $id);
        $s->execute();
        $s->close();

        if ($id_unit) {
            $s = $koneksi->prepare("UPDATE units SET status='Tersedia' WHERE id_unit=?");
            $s->bind_param("i", $id_unit);
            $s->execute();
            $s->close();
        }
        
        // --- LOG AKTIVITAS ---
        if (function_exists('log_activity')) log_activity($koneksi, 'TOLAK_SEWA', "Menolak pengajuan sewa ID: $id");

        header("Location: data_sewa.php?msg=tolak");
        exit();

    } elseif ($aksi === 'selesai') {
        $jam_telat = intval($_GET['telat'] ?? 0);

        // 1. Ambil data lengkap transaksi dari DB (Termasuk Kategori dari JOIN)
        $s = $koneksi->prepare(
            "SELECT p.id_unit, p.harga, p.pakai_playbox, u.kategori, p.tgl_ambil
             FROM pengajuan p
             JOIN units u ON p.id_unit = u.id_unit
             WHERE p.id_pengajuan = ?"
        );
        $s->bind_param("i", $id);
        $s->execute();
        $row = $s->get_result()->fetch_assoc();
        $s->close();

        if (!$row) {
            header("Location: data_sewa.php");
            exit();
        }

        // 2. Definisikan variabel berdasarkan data DB
        $id_unit         = $row['id_unit'];
        $harga_awal      = intval($row['harga']);
        $kategori_unit   = $row['kategori'];
        $pakai_playbox   = (bool)($row['pakai_playbox'] ?? false);
        $tgl_ambil_db    = $row['tgl_ambil'];

        // 3. Cek apakah hari pengambilannya dulu adalah hari libur (untuk HPP denda)
        $s_libur = $koneksi->prepare("SELECT 1 FROM hari_libur WHERE ? BETWEEN tgl_mulai AND tgl_selesai");
        $s_libur->bind_param("s", $tgl_ambil_db);
        $s_libur->execute();
        $is_libur_db = $s_libur->get_result()->num_rows > 0;
        $s_libur->close();

        // 4. Hitung HPP dan Denda secara akurat
        $hpp_final   = get_hpp($kategori_unit, $pakai_playbox, $is_libur_db);
        $denda       = hitung_denda($jam_telat, $hpp_final, $pakai_playbox);
        $harga_final = $harga_awal + $denda;

        // 5. Update status dan harga akhir (Termasuk denda)
        $s = $koneksi->prepare("UPDATE pengajuan SET status_pengajuan='Selesai', harga=? WHERE id_pengajuan=?");
        $s->bind_param("ii", $harga_final, $id);
        $s->execute();
        $s->close();

        // 6. Kembalikan status unit jadi Tersedia
        if ($id_unit) {
            $s = $koneksi->prepare("UPDATE units SET status='Tersedia' WHERE id_unit=?");
            $s->bind_param("i", $id_unit);
            $s->execute();
            $s->close();
        }

        // --- LOG AKTIVITAS ---
        if (function_exists('log_activity')) {
            $ket_denda = $denda > 0 ? " dengan denda Rp " . number_format($denda,0,',','.') : " tepat waktu";
            log_activity($koneksi, 'SELESAI_SEWA', "Menyelesaikan transaksi sewa ID: $id" . $ket_denda);
        }

        $qs = $denda > 0 ? '&denda=' . $denda : '';
        header("Location: data_sewa.php?msg=selesai$qs");
        exit();

    } elseif ($aksi === 'perpanjang') {
        $tambah_hari = intval($_GET['tambah'] ?? 0);
        if ($tambah_hari < 1 || $tambah_hari > MAX_PERPANJANG_HARI) {
            header("Location: data_sewa.php");
            exit();
        }

        $s = $koneksi->prepare(
            "SELECT p.durasi, p.harga, p.pakai_playbox, u.kategori
             FROM pengajuan p
             JOIN units u ON p.id_unit = u.id_unit
             WHERE p.id_pengajuan = ? AND p.status_pengajuan = 'Disetujui'"
        );
        $s->bind_param("i", $id);
        $s->execute();
        $row = $s->get_result()->fetch_assoc();
        $s->close();

        if (!$row) {
            header("Location: data_sewa.php");
            exit();
        }

        preg_match('/(\d+)/', $row['durasi'] ?? '1', $m);
        $durasi_lama   = intval($m[1] ?? 1);
        $durasi_baru   = $durasi_lama + $tambah_hari;
        $pakai_playbox = (bool)($row['pakai_playbox'] ?? false);
        
        $hpp           = get_hpp($row['kategori'], $pakai_playbox);
        $harga_baru    = $hpp * $durasi_baru;
        $durasi_str    = $durasi_baru . ' Hari';

        $s = $koneksi->prepare("UPDATE pengajuan SET durasi=?, harga=? WHERE id_pengajuan=?");
        $s->bind_param("sii", $durasi_str, $harga_baru, $id);
        $s->execute();
        $s->close();

        // --- LOG AKTIVITAS ---
        if (function_exists('log_activity')) log_activity($koneksi, 'PERPANJANG_SEWA', "Memperpanjang sewa ID: $id tambahan $tambah_hari Hari");

        header("Location: data_sewa.php?msg=perpanjang&filter=terima");
        exit();
    }
}

// ── Filter & tampilan ──────────────────────────────────────────────────────
$msg        = $_GET['msg']        ?? '';
$filter     = $_GET['filter']     ?? 'semua';
$tgl_dari   = $_GET['tgl_dari']   ?? '';
$tgl_sampai = $_GET['tgl_sampai'] ?? '';

$where_parts  = [];
$bind_types   = '';
$bind_params  = [];

$status_cond = match($filter) {
    'pending' => "p.status_pengajuan='Pending'",
    'terima'  => "p.status_pengajuan='Disetujui'",
    'tolak'   => "p.status_pengajuan='Ditolak'",
    'selesai' => "p.status_pengajuan='Selesai'",
    default   => ''
};
if ($status_cond) $where_parts[] = $status_cond;

if ($tgl_dari) {
    $where_parts[] = "DATE(p.tgl_pengajuan) >= ?";
    $bind_types   .= 's';
    $bind_params[] = $tgl_dari;
}
if ($tgl_sampai) {
    $where_parts[] = "DATE(p.tgl_pengajuan) <= ?";
    $bind_types   .= 's';
    $bind_params[] = $tgl_sampai;
}

$where = $where_parts ? 'WHERE ' . implode(' AND ', $where_parts) : '';

$sql  = "SELECT p.*, u.nama_unit, u.kategori FROM pengajuan p
         JOIN units u ON p.id_unit=u.id_unit $where ORDER BY p.tgl_pengajuan DESC";
$stmt = $koneksi->prepare($sql);
if ($bind_types) {
    $stmt->bind_param($bind_types, ...$bind_params);
}
$stmt->execute();
$data = $stmt->get_result();
$stmt->close();

// Count per status
$counts = [];
foreach (['Pending', 'Disetujui', 'Ditolak', 'Selesai'] as $st) {
    $s = $koneksi->prepare("SELECT COUNT(*) as c FROM pengajuan WHERE status_pengajuan=?");
    $s->bind_param("s", $st);
    $s->execute();
    $counts[$st] = $s->get_result()->fetch_assoc()['c'];
    $s->close();
}
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Data Sewa Violet PlayStation</title>
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
.filter-tabs{display:flex;gap:.6rem;margin-bottom:1.5rem;flex-wrap:wrap;}
.ftab{font-family:var(--font-ui);font-size:.8rem;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;padding:.45rem 1rem;border-radius:6px;border:1px solid var(--v-border);background:transparent;color:var(--v-muted);cursor:pointer;text-decoration:none;transition:all .2s;display:inline-flex;align-items:center;gap:.5rem;}
.ftab:hover,.ftab.active{border-color:var(--v-violet);color:var(--v-lavender);background:rgba(168,85,247,.15);}
.ftab .cnt{background:rgba(168,85,247,.25);color:var(--v-lavender);font-size:.7rem;padding:.05rem .4rem;border-radius:10px;}
.ftab.f-terima.active{background:rgba(16,185,129,.12);border-color:rgba(16,185,129,.4);color:#34d399;}
.ftab.f-terima .cnt{background:rgba(16,185,129,.2);color:#34d399;}
.ftab.f-tolak.active{background:rgba(239,68,68,.1);border-color:rgba(239,68,68,.3);color:#f87171;}
.ftab.f-tolak .cnt{background:rgba(239,68,68,.15);color:#f87171;}
.ftab.f-selesai.active{background:rgba(96,165,250,.1);border-color:rgba(96,165,250,.3);color:#60a5fa;}
.ftab.f-selesai .cnt{background:rgba(96,165,250,.15);color:#60a5fa;}
.table-card{background:var(--v-card);border:1px solid var(--v-border);border-radius:16px;overflow:hidden;}
.table-card-header{padding:1.25rem 1.5rem;border-bottom:1px solid var(--v-border);}
.table-card-header h3{font-family:var(--font-display);font-size:1.1rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-lavender);}
.table-wrap{overflow-x:auto;}
.s-pending{background:rgba(251,191,36,.15);color:#fbbf24;border:1px solid rgba(251,191,36,.3);}
.s-disetujui{background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);}
.s-ditolak{background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);}
.s-selesai{background:rgba(96,165,250,.15);color:#60a5fa;border:1px solid rgba(96,165,250,.3);}
.btn-sm{font-family:var(--font-ui);font-size:.72rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;padding:.3rem .75rem;border-radius:6px;text-decoration:none;display:inline-flex;align-items:center;gap:.35rem;transition:opacity .2s,transform .15s;cursor:pointer;border:none;white-space:nowrap;}
.btn-sm:hover{opacity:.8;transform:translateY(-1px);}
.btn-green{background:rgba(16,185,129,.2);color:#34d399;border:1px solid rgba(16,185,129,.3);}
.btn-red{background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);}
.btn-blue{background:rgba(96,165,250,.15);color:#60a5fa;border:1px solid rgba(96,165,250,.3);}
.btn-purple{background:rgba(168,85,247,.15);color:var(--v-lavender);border:1px solid rgba(168,85,247,.3);}
.btn-wa{background:rgba(37,211,102,.12);color:#25d366;border:1px solid rgba(37,211,102,.3);}
.btn-wa:hover{background:#25d366;color:#fff;opacity:1;}
.actions-wrap{display:flex;gap:.4rem;flex-wrap:wrap;}
.alert-msg{border-radius:8px;padding:.75rem 1rem;font-family:var(--font-ui);font-size:.85rem;letter-spacing:1px;margin-bottom:1.5rem;}
.alert-success{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#34d399;}
.alert-warn{background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);color:#f87171;}
.modal-overlay{position:fixed;inset:0;z-index:200;background:rgba(0,0,0,.7);backdrop-filter:blur(6px);display:none;align-items:center;justify-content:center;padding:1.5rem;}
.modal-overlay.open{display:flex;}
.modal-box{background:var(--v-card);border:1px solid var(--v-border);border-radius:20px;padding:2.5rem;width:100%;max-width:460px;animation:fadeUp .3s ease both;}
.modal-title{font-family:var(--font-display);font-size:1.3rem;font-weight:800;letter-spacing:2px;text-transform:uppercase;margin-bottom:1.5rem;}
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
<?php $active_page = 'sewa'; include __DIR__.'/sidebar.php'; ?>

<main class="main-content">
  <div class="page-title">DATA <span class="neon">SEWA</span></div>

  <?php if ($msg === 'terima'): ?>
    <div class="alert-msg alert-success">✓ Pengajuan disetujui.</div>
  <?php elseif ($msg === 'tolak'): ?>
    <div class="alert-msg alert-warn">✕ Pengajuan ditolak. Unit dikembalikan.</div>
  <?php elseif ($msg === 'selesai'): ?>
    <div class="alert-msg alert-success">
      ✓ Transaksi selesai. Unit tersedia kembali.
      <?php if (isset($_GET['denda']) && intval($_GET['denda']) > 0): ?>
        &nbsp;·&nbsp; Denda: <strong>Rp <?php echo number_format(intval($_GET['denda']), 0, ',', '.'); ?></strong>
      <?php endif; ?>
    </div>
  <?php elseif ($msg === 'perpanjang'): ?>
    <div class="alert-msg alert-success">✓ Sewa berhasil diperpanjang.</div>
  <?php endif; ?>

  <div class="filter-tabs">
    <a href="?filter=semua"   class="ftab <?php echo $filter === 'semua'   ? 'active' : ''; ?>">Semua</a>
    <a href="?filter=pending" class="ftab <?php echo $filter === 'pending' ? 'active' : ''; ?>">
      Pending <?php if ($counts['Pending'] > 0): ?><span class="cnt"><?php echo $counts['Pending']; ?></span><?php endif; ?>
    </a>
    <a href="?filter=terima"  class="ftab f-terima <?php echo $filter === 'terima'  ? 'active' : ''; ?>">
      Disetujui <?php if ($counts['Disetujui'] > 0): ?><span class="cnt"><?php echo $counts['Disetujui']; ?></span><?php endif; ?>
    </a>
    <a href="?filter=tolak"   class="ftab f-tolak  <?php echo $filter === 'tolak'   ? 'active' : ''; ?>">
      Ditolak <?php if ($counts['Ditolak'] > 0): ?><span class="cnt"><?php echo $counts['Ditolak']; ?></span><?php endif; ?>
    </a>
    <a href="?filter=selesai" class="ftab f-selesai <?php echo $filter === 'selesai' ? 'active' : ''; ?>">
      Selesai <?php if ($counts['Selesai'] > 0): ?><span class="cnt"><?php echo $counts['Selesai']; ?></span><?php endif; ?>
    </a>
  </div>

  <form method="GET" style="display:flex;gap:.75rem;align-items:flex-end;margin-bottom:1.5rem;flex-wrap:wrap;">
    <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
    <div>
      <label style="font-family:var(--font-ui);font-size:.75rem;letter-spacing:1.5px;text-transform:uppercase;color:var(--v-muted);display:block;margin-bottom:.35rem;">Dari Tanggal</label>
      <input type="date" name="tgl_dari" value="<?php echo htmlspecialchars($tgl_dari); ?>" class="v-input" style="padding:.5rem .75rem;font-size:.85rem;width:auto;">
    </div>
    <div>
      <label style="font-family:var(--font-ui);font-size:.75rem;letter-spacing:1.5px;text-transform:uppercase;color:var(--v-muted);display:block;margin-bottom:.35rem;">Sampai Tanggal</label>
      <input type="date" name="tgl_sampai" value="<?php echo htmlspecialchars($tgl_sampai); ?>" class="v-input" style="padding:.5rem .75rem;font-size:.85rem;width:auto;">
    </div>
    <button type="submit" class="btn-sm btn-purple" style="padding:.55rem 1.1rem;font-size:.82rem;height:fit-content;">Filter</button>
    <?php if ($tgl_dari || $tgl_sampai): ?>
      <a href="data_sewa.php?filter=<?php echo $filter; ?>" class="btn-sm" style="padding:.55rem 1.1rem;font-size:.82rem;height:fit-content;border:1px solid var(--v-border);color:var(--v-muted);">✕ Reset</a>
    <?php endif; ?>
    <?php if ($is_admin): ?>
      <a href="export_sewa.php?filter=<?php echo $filter; ?>&tgl_dari=<?php echo urlencode($tgl_dari); ?>&tgl_sampai=<?php echo urlencode($tgl_sampai); ?>&_token=<?php echo csrf_get_token(); ?>"
         class="btn-sm btn-green" style="padding:.55rem 1.1rem;font-size:.82rem;height:fit-content;margin-left:auto;">⬇ Export CSV</a>
    <?php endif; ?>
  </form>

  <div class="table-card">
    <div class="table-card-header"><h3>Pengajuan Sewa</h3></div>
    <div class="table-wrap">
      <table class="v-table">
        <thead>
          <tr>
            <th>Tanggal</th><th>Nama &amp; Alamat</th><th>Unit</th>
            <th>Durasi</th><th>Harga</th><th>Dokumen</th><th>Status</th><th>Aksi</th>
          </tr>
        </thead>
        <tbody>
        <?php if ($data->num_rows === 0): ?>
          <tr><td colspan="8" class="empty-td">Tidak ada data.</td></tr>
        <?php endif; ?>
        <?php while ($d = $data->fetch_assoc()):
          $st  = $d['status_pengajuan'];
          $sc  = match($st) { 'Pending' => 's-pending', 'Disetujui' => 's-disetujui', 'Ditolak' => 's-ditolak', 'Selesai' => 's-selesai', default => 's-pending' };
          $kat = $d['kategori'];
          $bc  = $kat === 'PS5' ? 'v-badge-ps5' : ($kat === 'Nintendo' ? 'v-badge-nin' : 'v-badge-ps4');
          $wa  = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $d['no_wa']));
          $tok = csrf_get_token();
          // HPP backend untuk modal selesai
          $hpp_be = get_hpp($kat, (bool)($d['pakai_playbox'] ?? false));
        ?>
        <tr>
          <td style="font-size:.8rem;color:var(--v-muted);white-space:nowrap;">
            <?php echo date('d/m/Y', strtotime($d['tgl_pengajuan'])); ?><br>
            <span style="font-size:.75rem;"><?php echo date('H:i', strtotime($d['tgl_pengajuan'])); ?></span>
          </td>
          <td>
            <strong style="color:var(--v-white);font-size:.9rem;"><?php echo htmlspecialchars($d['nama_penyewa']); ?></strong>
            <div style="font-size:.78rem;color:var(--v-muted);margin-top:.15rem;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo htmlspecialchars($d['alamat']); ?></div>
            <div style="font-size:.78rem;color:#7C6D8A;margin-top:.1rem;">📱 <?php echo htmlspecialchars($d['no_wa']); ?></div>
          </td>
          <td>
            <span class="v-badge <?php echo $bc; ?>" style="display:block;margin-bottom:.3rem;"><?php echo $kat; ?></span>
            <span style="font-size:.82rem;color:#9d8bb0;"><?php echo htmlspecialchars($d['nama_unit']); ?></span>
          </td>
          <td style="font-family:var(--font-ui);font-size:.85rem;color:var(--v-white);white-space:nowrap;"><?php echo htmlspecialchars($d['durasi'] ?? '-'); ?></td>
          <td style="font-family:var(--font-ui);font-size:.9rem;white-space:nowrap;">
            <?php if (in_array($st, ['Disetujui', 'Selesai'])): ?>
              <span style="color:#34d399;font-weight:700;"><?php echo $d['harga'] ? 'Rp ' . number_format($d['harga'], 0, ',', '.') : '-'; ?></span>
              <?php if ($d['pakai_playbox'] ?? 0): ?><br><span style="font-size:.72rem;color:#6ee7b7;">+ Playbox</span><?php endif; ?>
              <?php if ($d['is_promo'] ?? 0): ?><br><span style="font-size:.7rem;color:#fbbf24;background:rgba(251,191,36,.1);border:1px solid rgba(251,191,36,.25);padding:.05rem .4rem;border-radius:4px;">🎉 Promo</span><?php endif; ?>
            <?php else: ?>
              <span style="color:var(--v-muted);">—</span>
            <?php endif; ?>
          </td>
          <td>
            <div style="display:flex;flex-direction:column;gap:.35rem;">
              <a href="lihat_berkas.php?file=<?php echo urlencode($d['foto_ktp']); ?>" class="btn-sm btn-purple" target="_blank" aria-label="Lihat KTP <?php echo htmlspecialchars($d['nama_penyewa']); ?>">🪪 KTP</a>
              <a href="lihat_berkas.php?file=<?php echo urlencode($d['foto_stnk']); ?>" class="btn-sm btn-purple" target="_blank" aria-label="Lihat STNK <?php echo htmlspecialchars($d['nama_penyewa']); ?>">🚗 STNK</a>
            </div>
          </td>
          <td><span class="v-badge <?php echo $sc; ?>"><?php echo $st; ?></span></td>
          <td>
            <div class="actions-wrap">
            <?php if ($st === 'Pending'): ?>
              <a href="?aksi=terima&id=<?php echo $d['id_pengajuan']; ?>&_token=<?php echo $tok; ?>"
                 class="btn-sm btn-green"
                 onclick="return confirm('Setujui pengajuan ini?')"
                 aria-label="Setujui pengajuan <?php echo htmlspecialchars($d['nama_penyewa']); ?>">✓ Terima</a>
              <button class="btn-sm btn-red"
                      onclick="bukaModalTolak(<?php echo $d['id_pengajuan']; ?>,'<?php echo htmlspecialchars(addslashes($d['nama_penyewa'])); ?>')"
                      aria-label="Tolak pengajuan <?php echo htmlspecialchars($d['nama_penyewa']); ?>">✕ Tolak</button>

            <?php elseif ($st === 'Disetujui'): ?>
              <?php $pm = urlencode("Halo *{$d['nama_penyewa']}* 👋\n\nPengajuan sewa *{$d['nama_unit']}* kamu sudah *DISETUJUI* ✅\n\nSilakan ambil ke toko. Bawa *KTP, STNK asli, dan motor* ya.\n\nTerima kasih! Violet PlayStation"); ?>
              <a href="https://wa.me/<?php echo $wa; ?>?text=<?php echo $pm; ?>" target="_blank" class="btn-sm btn-wa" rel="noopener noreferrer">
                <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><use href="#ico-wa"/></svg>
                Chat Penyewa
              </a>
              <button class="btn-sm btn-purple"
                      onclick="bukaPerpanjang(<?php echo $d['id_pengajuan']; ?>,'<?php echo htmlspecialchars(addslashes($d['nama_penyewa'])); ?>','<?php echo htmlspecialchars(addslashes($d['nama_unit'])); ?>','<?php echo htmlspecialchars($d['durasi'] ?? '1 Hari'); ?>','<?php echo $kat; ?>',<?php echo intval($d['pakai_playbox'] ?? 0); ?>)">⏱ Perpanjang</button>
              <button class="btn-sm btn-blue"
                      onclick="bukaSelesai(<?php echo $d['id_pengajuan']; ?>,<?php echo $hpp_be; ?>,<?php echo intval($d['harga'] ?? 0); ?>)">✓ Selesai</button>

            <?php elseif ($st === 'Ditolak'): ?>
              <?php $pm = urlencode("Halo *{$d['nama_penyewa']}* 👋\n\nMohon maaf, pengajuan sewa *{$d['nama_unit']}* tidak dapat diproses saat ini.\n\nHubungi kami jika ada pertanyaan. Terima kasih 🙏"); ?>
              <a href="https://wa.me/<?php echo $wa; ?>?text=<?php echo $pm; ?>" target="_blank" class="btn-sm btn-wa" rel="noopener noreferrer">
                <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><use href="#ico-wa"/></svg>
                Beritahu Penyewa
              </a>
            <?php else: ?>
              <span style="font-family:var(--font-ui);font-size:.75rem;color:var(--v-muted);">—</span>
            <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>

<div class="modal-overlay" id="modalTolak" role="dialog" aria-modal="true" aria-labelledby="tolak-title">
  <div class="modal-box">
    <div class="modal-title" id="tolak-title" style="color:#f87171;">✕ Tolak Pengajuan</div>
    <p style="color:var(--v-muted);font-size:.9rem;margin-bottom:1.5rem;">Pengajuan dari <strong id="tolak-nama" style="color:var(--v-white);"></strong> akan ditolak dan unit dikembalikan.</p>
    <div style="display:flex;gap:.75rem;justify-content:flex-end;">
      <button onclick="document.getElementById('modalTolak').classList.remove('open')" class="btn-sm btn-blue" style="padding:.6rem 1.25rem;font-size:.85rem;">Batal</button>
      <a id="tolak-btn" href="#" class="btn-sm btn-red" style="padding:.6rem 1.25rem;font-size:.85rem;">Ya, Tolak</a>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modalSelesai" role="dialog" aria-modal="true" aria-labelledby="selesai-title">
  <div class="modal-box" style="max-width:420px;">
    <div class="modal-title" id="selesai-title" style="color:#60a5fa;">✓ Tandai Selesai</div>

    <div id="sl-step1">
      <p style="font-family:var(--font-ui);font-size:.92rem;color:var(--v-white);margin-bottom:1.25rem;">Apakah pelanggan <strong>terlambat</strong> mengembalikan unit?</p>
      <div style="display:flex;gap:.75rem;">
        <button class="btn-sm btn-red"  style="flex:1;padding:.65rem;font-size:.85rem;justify-content:center;" onclick="slStep2()">Ya, Terlambat</button>
        <a id="sl-ok-btn" href="#" class="btn-sm btn-blue" style="flex:1;padding:.65rem;font-size:.85rem;justify-content:center;text-align:center;">Tidak, Tepat Waktu ✓</a>
      </div>
    </div>

    <div id="sl-step2" style="display:none;">
      <p style="font-family:var(--font-ui);font-size:.9rem;color:var(--v-white);margin-bottom:.85rem;">Terlambat berapa jam?</p>
      <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;">
        <button onclick="slAdjust(-1)" aria-label="Kurangi jam" style="width:42px;height:42px;border-radius:8px;background:rgba(255,255,255,.06);border:1px solid var(--v-border);color:var(--v-white);font-size:1.3rem;cursor:pointer;flex-shrink:0;line-height:1;">−</button>
        <div style="flex:1;text-align:center;">
          <div id="sl-jam" style="font-family:var(--font-display);font-size:3rem;font-weight:800;color:var(--v-lavender);line-height:1;">1</div>
          <div style="font-family:var(--font-ui);font-size:.72rem;letter-spacing:1.5px;text-transform:uppercase;color:var(--v-muted);margin-top:.15rem;">JAM</div>
        </div>
        <button onclick="slAdjust(1)" aria-label="Tambah jam" style="width:42px;height:42px;border-radius:8px;background:rgba(255,255,255,.06);border:1px solid var(--v-border);color:var(--v-white);font-size:1.3rem;cursor:pointer;flex-shrink:0;line-height:1;">+</button>
      </div>

      <div style="background:rgba(239,68,68,.07);border:1px solid rgba(239,68,68,.2);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.25rem;">
        <div style="display:flex;justify-content:space-between;font-family:var(--font-ui);font-size:.85rem;margin-bottom:.35rem;">
          <span style="color:var(--v-muted);">Keterlambatan</span>
          <span id="sl-ket" style="color:#f87171;font-weight:600;"></span>
        </div>
        <div style="display:flex;justify-content:space-between;font-family:var(--font-ui);font-size:.85rem;margin-bottom:.35rem;">
          <span style="color:var(--v-muted);">Denda</span>
          <span id="sl-denda" style="color:#f87171;font-weight:700;"></span>
        </div>
        <div id="sl-hari-warn" style="font-size:.78rem;color:#fbbf24;font-family:var(--font-ui);padding:.4rem .5rem;background:rgba(251,191,36,.07);border-radius:6px;margin-bottom:.35rem;display:none;">⚠ Lebih dari <?php echo BATAS_JAM_DENDA; ?> jam = dianggap sewa 1 hari lagi</div>
        <div style="display:flex;justify-content:space-between;font-family:var(--font-display);font-size:1.1rem;font-weight:800;padding-top:.5rem;border-top:1px solid rgba(239,68,68,.15);">
          <span style="color:#f87171;">Total Bayar</span>
          <span id="sl-total" style="color:#f87171;"></span>
        </div>
      </div>

      <div style="display:flex;gap:.75rem;">
        <button onclick="slStep1()" class="btn-sm btn-purple" style="padding:.6rem 1rem;">← Kembali</button>
        <a id="sl-denda-btn" href="#" class="btn-sm btn-red" style="flex:1;padding:.6rem;font-size:.85rem;justify-content:center;text-align:center;">✓ Konfirmasi Selesai</a>
      </div>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modalPerpanjang" role="dialog" aria-modal="true" aria-labelledby="perpanjang-title">
  <div class="modal-box" style="max-width:420px;">
    <div class="modal-title" id="perpanjang-title" style="color:var(--v-lavender);">⏱ Perpanjang Sewa</div>
    <div style="margin-bottom:1.25rem;">
      <div style="font-family:var(--font-ui);font-size:.85rem;color:var(--v-muted);margin-bottom:.25rem;">Penyewa</div>
      <div id="pp-nama" style="color:var(--v-white);font-weight:700;font-family:var(--font-ui);"></div>
    </div>
    <div style="display:flex;gap:1.5rem;margin-bottom:1.5rem;flex-wrap:wrap;">
      <div>
        <div style="font-family:var(--font-ui);font-size:.8rem;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;margin-bottom:.25rem;">Unit</div>
        <div id="pp-unit" style="color:var(--v-white);font-family:var(--font-ui);font-size:.9rem;"></div>
      </div>
      <div>
        <div style="font-family:var(--font-ui);font-size:.8rem;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;margin-bottom:.25rem;">Durasi Saat Ini</div>
        <div id="pp-durasi" style="color:#fbbf24;font-family:var(--font-ui);font-size:.9rem;font-weight:700;"></div>
      </div>
    </div>
    <div style="font-family:var(--font-ui);font-size:.8rem;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;margin-bottom:.75rem;">Tambah Durasi</div>
    <div style="display:flex;gap:.5rem;margin-bottom:1.5rem;flex-wrap:wrap;" id="pp-btns"></div>
    <div style="background:rgba(168,85,247,.08);border:1px solid rgba(168,85,247,.2);border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;opacity:.4;" id="pp-preview">
      <div style="display:flex;justify-content:space-between;font-family:var(--font-ui);font-size:.85rem;margin-bottom:.4rem;">
        <span style="color:var(--v-muted);">Durasi baru</span><span id="pp-durasi-baru" style="color:var(--v-white);font-weight:700;"></span>
      </div>
      <div style="display:flex;justify-content:space-between;font-family:var(--font-ui);font-size:.85rem;margin-bottom:.4rem;">
        <span style="color:var(--v-muted);">Harga lama</span><span id="pp-harga-lama" style="color:var(--v-muted);"></span>
      </div>
      <div style="display:flex;justify-content:space-between;font-family:var(--font-ui);font-size:.85rem;margin-bottom:.4rem;">
        <span style="color:var(--v-muted);">Tambahan biaya</span><span id="pp-harga-tambah" style="color:#34d399;font-weight:700;"></span>
      </div>
      <div style="display:flex;justify-content:space-between;font-family:var(--font-display);font-size:1.1rem;font-weight:800;padding-top:.6rem;border-top:1px solid rgba(168,85,247,.2);margin-top:.2rem;">
        <span style="color:var(--v-lavender);">Total Baru</span><span id="pp-total" style="color:var(--v-lavender);"></span>
      </div>
    </div>
    <div style="display:flex;gap:.75rem;justify-content:flex-end;">
      <button onclick="document.getElementById('modalPerpanjang').classList.remove('open')" class="btn-sm btn-blue" style="padding:.6rem 1.25rem;">Batal</button>
      <a id="pp-confirm-btn" href="#" class="btn-sm btn-purple" style="padding:.6rem 1.25rem;">✓ Perpanjang</a>
    </div>
  </div>
</div>

<script>
const CSRF      = '<?php echo csrf_get_token(); ?>';
const BATAS_JAM = <?php echo BATAS_JAM_DENDA; ?>;

function bukaModalTolak(id, nama) {
    document.getElementById('tolak-nama').textContent = nama;
    document.getElementById('tolak-btn').href = '?aksi=tolak&id=' + id + '&_token=' + CSRF;
    document.getElementById('modalTolak').classList.add('open');
}
document.getElementById('modalTolak').addEventListener('click', function(e) { if (e.target === this) this.classList.remove('open'); });

let ppState = {};
let slState = {};

function bukaSelesai(id, hpp, harga) {
    slState = { id, hpp, harga, jam: 1 };
    document.getElementById('sl-jam').textContent = '1';
    document.getElementById('sl-ok-btn').href = '?aksi=selesai&id=' + id + '&telat=0&_token=' + CSRF;
    slStep1();
    slUpdate();
    document.getElementById('modalSelesai').classList.add('open');
}
function slStep1() {
    document.getElementById('sl-step1').style.display = 'block';
    document.getElementById('sl-step2').style.display = 'none';
}
function slStep2() {
    document.getElementById('sl-step1').style.display = 'none';
    document.getElementById('sl-step2').style.display = 'block';
}
function slAdjust(n) {
    slState.jam = Math.max(1, Math.min(24, slState.jam + n));
    document.getElementById('sl-jam').textContent = slState.jam;
    slUpdate();
}
function slUpdate() {
    const jam    = slState.jam;
    const isHari = jam > BATAS_JAM;
    const denda  = isHari ? slState.hpp : jam * <?php echo DENDA_PER_JAM; ?>;
    document.getElementById('sl-ket').textContent     = jam + ' jam' + (isHari ? ' (> ' + BATAS_JAM + ' jam)' : '');
    document.getElementById('sl-denda').textContent   = '+' + fmt(denda);
    document.getElementById('sl-total').textContent   = fmt(slState.harga + denda);
    document.getElementById('sl-hari-warn').style.display = isHari ? 'block' : 'none';
    // Kirim hanya telat (jam)  HPP dihitung di backend
    document.getElementById('sl-denda-btn').href = '?aksi=selesai&id=' + slState.id + '&telat=' + jam + '&_token=' + CSRF;
}
document.getElementById('modalSelesai').addEventListener('click', function(e) { if (e.target === this) this.classList.remove('open'); });

function bukaPerpanjang(id, nama, unit, durasi, kat, playbox) {
    ppState.id           = id;
    // HPP untuk preview di modal  konsisten dengan backend (get_hpp)
    const base = kat === 'PS5' ? <?php echo HARGA_PS5; ?> : <?php echo HARGA_PS4; ?>;
    ppState.hargaPerHari = base + (playbox ? <?php echo HARGA_PLAYBOX; ?> : 0);
    ppState.durasiLama   = parseInt(durasi) || 1;
    ppState.hargaLama    = ppState.durasiLama * ppState.hargaPerHari;
    document.getElementById('pp-nama').textContent   = nama;
    document.getElementById('pp-unit').textContent   = unit;
    document.getElementById('pp-durasi').textContent = durasi;

    const btns = document.getElementById('pp-btns');
    btns.innerHTML = '';
    [1, 2, 3].forEach(function(n) {
        const b = document.createElement('button');
        b.className   = 'btn-sm btn-purple';
        b.textContent = '+' + n + ' Hari';
        b.style.cssText = 'padding:.5rem 1.1rem;font-size:.82rem;';
        b.onclick = function() { pilihTambah(n, b); };
        btns.appendChild(b);
    });
    document.getElementById('pp-preview').style.opacity = '.4';
    document.getElementById('pp-confirm-btn').href = '#';
    document.getElementById('modalPerpanjang').classList.add('open');
}

function pilihTambah(n, btn) {
    document.querySelectorAll('#pp-btns .btn-sm').forEach(function(b) {
        b.style.background  = 'rgba(168,85,247,.15)';
        b.style.borderColor = 'rgba(168,85,247,.3)';
    });
    btn.style.background  = 'rgba(168,85,247,.4)';
    btn.style.borderColor = 'var(--v-violet)';
    const durasiBaru  = ppState.durasiLama + n;
    const hargaBaru   = durasiBaru * ppState.hargaPerHari;
    const tambahBiaya = hargaBaru - ppState.hargaLama;
    document.getElementById('pp-durasi-baru').textContent  = durasiBaru + ' Hari';
    document.getElementById('pp-harga-lama').textContent   = fmt(ppState.hargaLama);
    document.getElementById('pp-harga-tambah').textContent = '+' + fmt(tambahBiaya);
    document.getElementById('pp-total').textContent        = fmt(hargaBaru);
    document.getElementById('pp-preview').style.opacity   = '1';
    document.getElementById('pp-confirm-btn').href = '?aksi=perpanjang&id=' + ppState.id + '&tambah=' + n + '&_token=' + CSRF;
}
document.getElementById('modalPerpanjang').addEventListener('click', function(e) { if (e.target === this) this.classList.remove('open'); });

function fmt(n) { return 'Rp ' + n.toLocaleString('id-ID'); }

function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('mobile-open');
    document.getElementById('sidebarOverlay').classList.toggle('open');
    document.body.style.overflow = document.querySelector('.sidebar').classList.contains('mobile-open') ? 'hidden' : '';
}
function closeSidebar() {
    document.querySelector('.sidebar').classList.remove('mobile-open');
    document.getElementById('sidebarOverlay').classList.remove('open');
    document.body.style.overflow = '';
}
</script>
</body></html>