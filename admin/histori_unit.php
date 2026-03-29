<?php
require_once '../config/koneksi.php';
require_login('login.php');
$is_admin = is_admin();

$id_unit = intval($_GET['id'] ?? 0);
if (!$id_unit) { header("Location: index.php"); exit(); }

// Ambil info unit
$stmt = $koneksi->prepare("SELECT * FROM units WHERE id_unit = ?");
$stmt->bind_param("i", $id_unit);
$stmt->execute();
$unit = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$unit) { header("Location: index.php"); exit(); }

// Ambil histori sewa unit ini
$stmt = $koneksi->prepare(
    "SELECT * FROM pengajuan WHERE id_unit = ? ORDER BY tgl_pengajuan DESC"
);
$stmt->bind_param("i", $id_unit);
$stmt->execute();
$histori = $stmt->get_result();
$stmt->close();

// ── PERBAIKAN: Gunakan prepared statement untuk semua query stats ──────────
$stmt = $koneksi->prepare("SELECT COUNT(*) as c FROM pengajuan WHERE id_unit=?");
$stmt->bind_param("i", $id_unit);
$stmt->execute();
$unit_total_sewa = $stmt->get_result()->fetch_assoc()['c'];
$stmt->close();

$stmt = $koneksi->prepare("SELECT COUNT(*) as c FROM pengajuan WHERE id_unit=? AND status_pengajuan='Selesai'");
$stmt->bind_param("i", $id_unit);
$stmt->execute();
$unit_total_selesai = $stmt->get_result()->fetch_assoc()['c'];
$stmt->close();

$stmt = $koneksi->prepare("SELECT COUNT(*) as c FROM pengajuan WHERE id_unit=? AND status_pengajuan='Pending'");
$stmt->bind_param("i", $id_unit);
$stmt->execute();
$unit_total_pending = $stmt->get_result()->fetch_assoc()['c'];
$stmt->close();

$stmt = $koneksi->prepare("SELECT COUNT(*) as c FROM pengajuan WHERE id_unit=? AND status_pengajuan='Ditolak'");
$stmt->bind_param("i", $id_unit);
$stmt->execute();
$unit_total_ditolak = $stmt->get_result()->fetch_assoc()['c'];
$stmt->close();

$stmt = $koneksi->prepare("SELECT COALESCE(SUM(harga),0) as t FROM pengajuan WHERE id_unit=? AND status_pengajuan='Selesai'");
$stmt->bind_param("i", $id_unit);
$stmt->execute();
$unit_total_pendapatan = $stmt->get_result()->fetch_assoc()['t'];
$stmt->close();

$kat = $unit['kategori'];
$bc  = $kat === 'PS5' ? 'v-badge-ps5' : ($kat === 'Nintendo' ? 'v-badge-nin' : 'v-badge-ps4');
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Histori <?php echo htmlspecialchars($unit['nama_unit']); ?> Violet PlayStation</title>
<link rel="stylesheet" href="../assets/css/violet.css">
<script src="../assets/app.js" defer></script>
<style>
body{display:flex;min-height:100vh;}
.main-content{margin-left:240px;flex:1;padding:2.5rem;background:var(--v-black);}
.unit-header{background:var(--v-card);border:1px solid var(--v-border);border-radius:16px;padding:2rem;margin-bottom:2rem;display:flex;align-items:center;gap:2rem;flex-wrap:wrap;}
.unit-icon-big{width:70px;height:70px;background:rgba(168,85,247,.1);border:1px solid rgba(168,85,247,.25);border-radius:16px;display:flex;align-items:center;justify-content:center;font-size:2rem;flex-shrink:0;}
.unit-header-info h2{font-family:var(--font-display);font-size:1.8rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;color:var(--v-lavender);}
.unit-header-info p{color:var(--v-muted);font-family:var(--font-ui);font-size:.85rem;margin-top:.3rem;}
.stats-row{display:flex;gap:1.25rem;margin-bottom:2rem;flex-wrap:wrap;}
.stat-mini{background:var(--v-card);border:1px solid var(--v-border);border-radius:12px;padding:1.25rem 1.5rem;flex:1;min-width:130px;}
.stat-mini .num{font-family:var(--font-display);font-size:1.8rem;font-weight:800;color:var(--v-lavender);}
.stat-mini .lbl{font-family:var(--font-ui);font-size:.75rem;letter-spacing:1.5px;text-transform:uppercase;color:var(--v-muted);margin-top:.2rem;}
.table-card{background:var(--v-card);border:1px solid var(--v-border);border-radius:16px;overflow:hidden;}
.table-card-header{padding:1.25rem 1.5rem;border-bottom:1px solid var(--v-border);display:flex;justify-content:space-between;align-items:center;}
.table-card-header h3{font-family:var(--font-display);font-size:1.1rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-lavender);}
.table-wrap{overflow-x:auto;}
.s-pending{background:rgba(251,191,36,.15);color:#fbbf24;border:1px solid rgba(251,191,36,.3);}
.s-disetujui{background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);}
.s-ditolak{background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);}
.s-selesai{background:rgba(96,165,250,.15);color:#60a5fa;border:1px solid rgba(96,165,250,.3);}
.btn-sm{font-family:var(--font-ui);font-size:.72rem;font-weight:700;letter-spacing:.8px;text-transform:uppercase;padding:.3rem .75rem;border-radius:6px;text-decoration:none;display:inline-flex;align-items:center;gap:.35rem;transition:opacity .2s;}
.btn-sm:hover{opacity:.8;}
.btn-wa{background:rgba(37,211,102,.12);color:#25d366;border:1px solid rgba(37,211,102,.3);}
.btn-wa:hover{background:#25d366;color:#fff;opacity:1;}
.btn-purple{background:rgba(168,85,247,.15);color:var(--v-lavender);border:1px solid rgba(168,85,247,.3);}
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
<?php include_once "../config/svg_sprite_admin.php"; ?>
<?php $active_page = 'dashboard'; include __DIR__.'/sidebar.php'; ?>

<main class="main-content">
  <div style="display:flex;align-items:center;gap:.5rem;font-family:var(--font-ui);font-size:.78rem;letter-spacing:1px;text-transform:uppercase;color:var(--v-muted);margin-bottom:1.5rem;flex-wrap:wrap;">
    <a href="index.php" style="color:var(--v-muted);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='var(--v-lavender)'" onmouseout="this.style.color='var(--v-muted)'">Dashboard</a>
    <span style="opacity:.4;">›</span>
    <span style="color:var(--v-lavender);">Histori <?php echo htmlspecialchars($unit['nama_unit']); ?></span>
  </div>

  <div class="unit-header">
    <div class="unit-icon-big"><?php echo $kat === 'Nintendo' ? '🕹️' : '🎮'; ?></div>
    <div class="unit-header-info">
      <h2><?php echo htmlspecialchars($unit['nama_unit']); ?></h2>
      <p>
        <span class="v-badge <?php echo $bc; ?>" style="margin-right:.5rem;"><?php echo $kat; ?></span>
        <span style="color:var(--v-muted);"><?php echo $unit['tipe_layanan']; ?></span>
        &nbsp;·&nbsp;
        <?php if ($unit['status'] === 'Tersedia'): ?>
          <span style="color:#34d399;">● Tersedia</span>
        <?php else: ?>
          <span style="color:#fbbf24;">● Sedang Disewa</span>
        <?php endif; ?>
      </p>
    </div>
  </div>

  <div class="stats-row">
    <div class="stat-mini"><div class="num"><?php echo $unit_total_sewa; ?></div><div class="lbl">Total Pengajuan</div></div>
    <div class="stat-mini"><div class="num" style="color:#34d399;"><?php echo $unit_total_selesai; ?></div><div class="lbl">Selesai</div></div>
    <div class="stat-mini"><div class="num" style="color:#fbbf24;"><?php echo $unit_total_pending; ?></div><div class="lbl">Pending</div></div>
    <div class="stat-mini"><div class="num" style="color:#f87171;"><?php echo $unit_total_ditolak; ?></div><div class="lbl">Ditolak</div></div>
    <div class="stat-mini" style="border-color:rgba(16,185,129,.25);">
      <div class="num" style="color:#34d399;font-size:1.3rem;">Rp <?php echo number_format($unit_total_pendapatan, 0, ',', '.'); ?></div>
      <div class="lbl">Total Pendapatan</div>
    </div>
  </div>

  <div class="table-card">
    <div class="table-card-header">
      <h3>Histori Sewa</h3>
      <span style="font-family:var(--font-ui);font-size:.8rem;color:var(--v-muted);"><?php echo $unit_total_sewa; ?> transaksi</span>
    </div>
    <div class="table-wrap">
      <table class="v-table">
        <thead>
          <tr><th>Tanggal</th><th>Penyewa</th><th>No. WA</th><th>Durasi</th><th>Harga</th><th>Promo</th><th>Dokumen</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
        <?php if ($histori->num_rows === 0): ?>
          <tr><td colspan="9" class="empty-td">Belum ada histori sewa untuk unit ini.</td></tr>
        <?php endif; ?>
        <?php while ($h = $histori->fetch_assoc()):
          $st = $h['status_pengajuan'];
          $sc = match($st) { 'Pending' => 's-pending', 'Disetujui' => 's-disetujui', 'Ditolak' => 's-ditolak', 'Selesai' => 's-selesai', default => 's-pending' };
          $no_wa_bersih = preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $h['no_wa']));
        ?>
        <tr>
          <td style="font-size:.8rem;color:var(--v-muted);white-space:nowrap;">
            <?php echo date('d/m/Y', strtotime($h['tgl_pengajuan'])); ?><br>
            <span style="font-size:.75rem;"><?php echo date('H:i', strtotime($h['tgl_pengajuan'])); ?></span>
          </td>
          <td>
            <strong style="color:var(--v-white);font-size:.9rem;"><?php echo htmlspecialchars($h['nama_penyewa']); ?></strong><br>
            <span style="font-size:.78rem;color:var(--v-muted);max-width:160px;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?php echo htmlspecialchars($h['alamat']); ?></span>
          </td>
          <td style="font-size:.85rem;font-family:var(--font-ui);color:#9d8bb0;"><?php echo htmlspecialchars($h['no_wa']); ?></td>
          <td style="font-family:var(--font-ui);font-size:.85rem;color:var(--v-white);white-space:nowrap;"><?php echo htmlspecialchars($h['durasi'] ?? '-'); ?></td>
          <td style="font-family:var(--font-ui);font-size:.85rem;white-space:nowrap;">
            <?php if (in_array($st, ['Selesai', 'Disetujui']) && $h['harga']): ?>
              <span style="color:#34d399;font-weight:700;">Rp <?php echo number_format($h['harga'], 0, ',', '.'); ?></span>
              <?php if ($h['pakai_playbox'] ?? 0): ?><br><span style="font-size:.7rem;color:#6ee7b7;">+ Playbox</span><?php endif; ?>
            <?php else: ?>
              <span style="color:var(--v-muted);">—</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($h['is_promo'] ?? 0): ?>
              <span style="background:rgba(251,191,36,.12);color:#fbbf24;border:1px solid rgba(251,191,36,.3);font-family:var(--font-ui);font-size:.7rem;font-weight:700;padding:.1rem .45rem;border-radius:4px;">🎁 Promo</span>
            <?php else: ?>
              <span style="color:var(--v-muted);font-size:.8rem;">—</span>
            <?php endif; ?>
          </td>
          <td>
            <div style="display:flex;flex-direction:column;gap:.3rem;">
              <a href="lihat_berkas.php?file=<?php echo urlencode($h['foto_ktp']); ?>" class="btn-sm btn-purple" target="_blank" aria-label="Lihat KTP">🪪 KTP</a>
              <a href="lihat_berkas.php?file=<?php echo urlencode($h['foto_stnk']); ?>" class="btn-sm btn-purple" target="_blank" aria-label="Lihat STNK">🚗 STNK</a>
            </div>
          </td>
          <td><span class="v-badge <?php echo $sc; ?>"><?php echo $st; ?></span></td>
          <td>
            <?php if ($st !== 'Pending'): ?>
              <?php $pesan_wa = urlencode("Halo *{$h['nama_penyewa']}* 👋, kami dari Violet PlayStation ingin menghubungi kamu terkait sewa *{$unit['nama_unit']}*. Ada yang bisa kami bantu?"); ?>
              <a href="https://wa.me/<?php echo $no_wa_bersih; ?>?text=<?php echo $pesan_wa; ?>" target="_blank" class="btn-sm btn-wa" rel="noopener noreferrer">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                Chat
              </a>
            <?php else: ?>
              <span style="font-size:.75rem;color:var(--v-muted);font-family:var(--font-ui);">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
<script>
function toggleSidebar(){document.querySelector('.sidebar').classList.toggle('mobile-open');document.getElementById('sidebarOverlay').classList.toggle('open');document.body.style.overflow=document.querySelector('.sidebar').classList.contains('mobile-open')?'hidden':'';}
function closeSidebar(){document.querySelector('.sidebar').classList.remove('mobile-open');document.getElementById('sidebarOverlay').classList.remove('open');document.body.style.overflow='';}
</script>
</body></html>