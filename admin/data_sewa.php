<?php
require_once '../config/koneksi.php';
require_login('login.php');
$is_admin = is_admin();

if (isset($_GET['aksi'], $_GET['id'])) {
    csrf_get_check();
    $id   = intval($_GET['id']);
    $aksi = $_GET['aksi'];

    if ($aksi === 'terima') {
        $s = $koneksi->prepare("UPDATE pengajuan SET status_pengajuan='Disetujui' WHERE id_pengajuan=?");
        $s->bind_param("i",$id); $s->execute(); $s->close();
        header("Location: data_sewa.php?msg=terima"); exit();

    } elseif ($aksi === 'tolak') {
        $s = $koneksi->prepare("SELECT id_unit FROM pengajuan WHERE id_pengajuan=?");
        $s->bind_param("i",$id); $s->execute();
        $id_unit = $s->get_result()->fetch_assoc()['id_unit'] ?? 0; $s->close();
        $s = $koneksi->prepare("UPDATE pengajuan SET status_pengajuan='Ditolak' WHERE id_pengajuan=?");
        $s->bind_param("i",$id); $s->execute(); $s->close();
        if ($id_unit) {
            $s = $koneksi->prepare("UPDATE units SET status='Tersedia' WHERE id_unit=?");
            $s->bind_param("i",$id_unit); $s->execute(); $s->close();
        }
        header("Location: data_sewa.php?msg=tolak"); exit();

    } elseif ($aksi === 'selesai') {
        $jam_telat  = intval($_GET['telat'] ?? 0);
        $hpp_input  = intval($_GET['hpp']   ?? 0);

        $s = $koneksi->prepare("SELECT id_unit, harga FROM pengajuan WHERE id_pengajuan=?");
        $s->bind_param("i",$id); $s->execute();
        $row = $s->get_result()->fetch_assoc(); $s->close();
        $id_unit   = $row['id_unit'] ?? 0;
        $harga_now = intval($row['harga'] ?? 0);

        $denda = 0;
        if ($jam_telat > 0) {
            $denda = $jam_telat > 6 ? $hpp_input : $jam_telat * 10000;
        }
        $harga_final = $harga_now + $denda;

        $s = $koneksi->prepare("UPDATE pengajuan SET status_pengajuan='Selesai', harga=? WHERE id_pengajuan=?");
        $s->bind_param("ii", $harga_final, $id); $s->execute(); $s->close();
        if ($id_unit) {
            $s = $koneksi->prepare("UPDATE units SET status='Tersedia' WHERE id_unit=?");
            $s->bind_param("i",$id_unit); $s->execute(); $s->close();
        }
        $qs = $denda > 0 ? '&denda='.$denda : '';
        header("Location: data_sewa.php?msg=selesai$qs"); exit();

    } elseif ($aksi === 'perpanjang') {
        $tambah_hari = intval($_GET['tambah'] ?? 0);
        if ($tambah_hari < 1 || $tambah_hari > 7) { header("Location: data_sewa.php"); exit(); }

        $s = $koneksi->prepare("SELECT p.durasi, p.harga, p.pakai_playbox, u.kategori FROM pengajuan p JOIN units u ON p.id_unit=u.id_unit WHERE p.id_pengajuan=? AND p.status_pengajuan='Disetujui'");
        $s->bind_param("i",$id); $s->execute();
        $row = $s->get_result()->fetch_assoc(); $s->close();
        if (!$row) { header("Location: data_sewa.php"); exit(); }

        preg_match('/(\d+)/', $row['durasi'] ?? '1', $m);
        $durasi_lama = intval($m[1] ?? 1);
        $durasi_baru = $durasi_lama + $tambah_hari;
        $hpp = match($row['kategori'] ?? 'PS4'){ 'PS5' => 195000, 'Nintendo' => 100000, default => 100000 };
        $hpp += ($row['pakai_playbox'] ?? 0) ? 30000 : 0;
        $harga_baru  = $hpp * $durasi_baru;
        $durasi_str  = $durasi_baru . ' Hari';

        $s = $koneksi->prepare("UPDATE pengajuan SET durasi=?, harga=? WHERE id_pengajuan=?");
        $s->bind_param("sii", $durasi_str, $harga_baru, $id); $s->execute(); $s->close();
        header("Location: data_sewa.php?msg=perpanjang&filter=terima"); exit();
    }
}

$msg    = $_GET['msg'] ?? '';
$filter = $_GET['filter'] ?? 'semua';
$tgl_dari = $_GET['tgl_dari'] ?? '';
$tgl_sampai = $_GET['tgl_sampai'] ?? '';

$conditions = [];
$status_cond = match($filter){
    'pending'  => "status_pengajuan='Pending'",
    'terima'   => "status_pengajuan='Disetujui'",
    'tolak'    => "status_pengajuan='Ditolak'",
    'selesai'  => "status_pengajuan='Selesai'",
    default    => ''
};
if($status_cond) $conditions[] = $status_cond;
if($tgl_dari)    $conditions[] = "DATE(tgl_pengajuan) >= '".mysqli_real_escape_string($koneksi,$tgl_dari)."'";
if($tgl_sampai)  $conditions[] = "DATE(tgl_pengajuan) <= '".mysqli_real_escape_string($koneksi,$tgl_sampai)."'";
$where = $conditions ? 'WHERE '.implode(' AND ',$conditions) : '';

$data = $koneksi->query(
    "SELECT p.*, u.nama_unit, u.kategori FROM pengajuan p
     JOIN units u ON p.id_unit=u.id_unit $where ORDER BY tgl_pengajuan DESC"
);

$counts = [];
foreach (['Pending','Disetujui','Ditolak','Selesai'] as $st) {
    $counts[$st] = $koneksi->query("SELECT COUNT(*) as c FROM pengajuan WHERE status_pengajuan='$st'")->fetch_assoc()['c'];
}
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Data Sewa — Violet PlayStation</title>
<link rel="stylesheet" href="../assets/css/violet.css">
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
<svg style="display:none">
  <symbol id="ico-wa" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></symbol>
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
  <div class="sidebar-brand"><img src="../assets/images/logo-violet.jpeg" alt="Logo"><h2>VIOLET <span class="neon">PLAYSTATION</span></h2><p>Admin Panel</p></div>
  <div class="nav-section">Menu</div>
  <a href="index.php" class="nav-item">🏠 Dashboard</a>
  <a href="data_sewa.php" class="nav-item active">📋 Data Sewa</a>
  <a href="laporan.php" class="nav-item">📊 Laporan</a>
  <?php if($is_admin): ?>
  <div class="nav-section">Admin Only</div>
  <a href="master_game.php" class="nav-item">🎮 Master Game</a>
  <a href="hari_libur.php" class="nav-item">📅 Hari Libur</a>
  <a href="kelola_akun.php" class="nav-item">👥 Kelola Akun</a>
  <?php endif; ?>
  <div class="sidebar-bottom">
    <div class="user-chip">Login sebagai<strong><?php echo htmlspecialchars($_SESSION['nama']??$_SESSION['user']); ?></strong>
    <span class="role-badge role-<?php echo $_SESSION['role']; ?>"><?php echo ucfirst($_SESSION['role']); ?></span></div>
    <a href="logout.php" class="btn-violet" style="display:block;text-align:center;text-decoration:none;padding:.6rem;font-size:.8rem;letter-spacing:2px;" onclick="return confirm('Yakin ingin keluar?')"><span>Logout</span></a>
  </div>
</aside>

<main class="main-content">
  <div class="page-title">DATA <span class="neon">SEWA</span></div>

  <?php if($msg==='terima'): ?><div class="alert-msg alert-success">✓ Pengajuan disetujui.</div>
  <?php elseif($msg==='tolak'): ?><div class="alert-msg alert-warn">✕ Pengajuan ditolak. Unit dikembalikan.</div>
  <?php elseif($msg==='selesai'): ?>
  <div class="alert-msg alert-success">✓ Transaksi selesai. Unit tersedia kembali.<?php if(isset($_GET['denda'])&&$_GET['denda']>0): ?> &nbsp;·&nbsp; Denda: <strong>Rp <?php echo number_format(intval($_GET['denda']),0,',','.'); ?></strong><?php endif; ?></div>
  <?php elseif($msg==='perpanjang'): ?><div class="alert-msg alert-success">✓ Sewa berhasil diperpanjang.</div>
  <?php endif; ?>

  <div class="filter-tabs">
    <a href="?filter=semua"   class="ftab <?php echo $filter==='semua'?'active':''; ?>">Semua</a>
    <a href="?filter=pending" class="ftab <?php echo $filter==='pending'?'active':''; ?>">
      Pending <?php if($counts['Pending']>0): ?><span class="cnt"><?php echo $counts['Pending']; ?></span><?php endif; ?>
    </a>
    <a href="?filter=terima"  class="ftab f-terima <?php echo $filter==='terima'?'active':''; ?>">
      Disetujui <?php if($counts['Disetujui']>0): ?><span class="cnt"><?php echo $counts['Disetujui']; ?></span><?php endif; ?>
    </a>
    <a href="?filter=tolak"   class="ftab f-tolak <?php echo $filter==='tolak'?'active':''; ?>">
      Ditolak <?php if($counts['Ditolak']>0): ?><span class="cnt"><?php echo $counts['Ditolak']; ?></span><?php endif; ?>
    </a>
    <a href="?filter=selesai" class="ftab f-selesai <?php echo $filter==='selesai'?'active':''; ?>">
      Selesai <?php if($counts['Selesai']>0): ?><span class="cnt"><?php echo $counts['Selesai']; ?></span><?php endif; ?>
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
    <?php if($tgl_dari||$tgl_sampai): ?><a href="data_sewa.php?filter=<?php echo $filter; ?>" class="btn-sm" style="padding:.55rem 1.1rem;font-size:.82rem;height:fit-content;border:1px solid var(--v-border);color:var(--v-muted);">✕ Reset</a><?php endif; ?>
    <?php if($is_admin): ?>
    <a href="export_sewa.php?filter=<?php echo $filter; ?>&tgl_dari=<?php echo urlencode($tgl_dari); ?>&tgl_sampai=<?php echo urlencode($tgl_sampai); ?>&_token=<?php echo csrf_get_token(); ?>" class="btn-sm btn-green" style="padding:.55rem 1.1rem;font-size:.82rem;height:fit-content;margin-left:auto;">⬇ Export CSV</a>
    <?php endif; ?>
  </form>

  <div class="table-card">
    <div class="table-card-header"><h3>Pengajuan Sewa</h3></div>
    <div class="table-wrap">
      <table class="v-table">
        <thead><tr><th>Tanggal</th><th>Nama & Alamat</th><th>Unit</th><th>Durasi</th><th>Harga</th><th>Dokumen</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php if($data->num_rows===0): ?>
        <tr><td colspan="8" style="text-align:center;color:var(--v-muted);font-family:var(--font-ui);padding:2rem;">Tidak ada data.</td></tr>
        <?php endif; ?>
        <?php while($d=$data->fetch_assoc()):
          $st  = $d['status_pengajuan'];
          $sc  = match($st){ 'Pending'=>'s-pending','Disetujui'=>'s-disetujui','Ditolak'=>'s-ditolak','Selesai'=>'s-selesai',default=>'s-pending' };
          $kat = $d['kategori'];
          $bc  = $kat==='PS5'?'v-badge-ps5':($kat==='Nintendo'?'v-badge-nin':'v-badge-ps4');
          $wa  = preg_replace('/^0/','62',preg_replace('/[^0-9]/','',$d['no_wa']));
          $tok = csrf_get_token();
        ?>
        <tr>
          <td style="font-size:.8rem;color:var(--v-muted);white-space:nowrap;">
            <?php echo date('d/m/Y',strtotime($d['tgl_pengajuan'])); ?><br>
            <span style="font-size:.75rem;"><?php echo date('H:i',strtotime($d['tgl_pengajuan'])); ?></span>
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
          <td style="font-family:var(--font-ui);font-size:.85rem;color:var(--v-white);white-space:nowrap;"><?php echo htmlspecialchars($d['durasi']??'-'); ?></td>
          <td style="font-family:var(--font-ui);font-size:.9rem;white-space:nowrap;">
            <?php if(in_array($st,['Disetujui','Selesai'])): ?>
            <span style="color:#34d399;font-weight:700;"><?php echo $d['harga']?'Rp '.number_format($d['harga'],0,',','.'):'-'; ?></span>
            <?php if($d['pakai_playbox']??0): ?><br><span style="font-size:.72rem;color:#6ee7b7;">+ Playbox</span><?php endif; ?>
            <?php if($d['is_promo']??0): ?><br><span style="font-size:.7rem;color:#fbbf24;background:rgba(251,191,36,.1);border:1px solid rgba(251,191,36,.25);padding:.05rem .4rem;border-radius:4px;">🎉 Promo</span><?php endif; ?>
            <?php else: ?><span style="color:var(--v-muted);">—</span><?php endif; ?>
          </td>
          <td>
            <div style="display:flex;flex-direction:column;gap:.35rem;">
              <a href="lihat_berkas.php?file=<?php echo urlencode($d['foto_ktp']); ?>" class="btn-sm btn-purple" target="_blank">🪪 KTP</a>
              <a href="lihat_berkas.php?file=<?php echo urlencode($d['foto_stnk']); ?>" class="btn-sm btn-purple" target="_blank">🚗 STNK</a>
            </div>
          </td>
          <td><span class="v-badge <?php echo $sc; ?>"><?php echo $st; ?></span></td>
          <td>
            <div class="actions-wrap">
            <?php if($st==='Pending'): ?>
              <a href="?aksi=terima&id=<?php echo $d['id_pengajuan']; ?>&_token=<?php echo $tok; ?>" class="btn-sm btn-green" onclick="return confirm('Setujui pengajuan ini?')">✓ Terima</a>
              <button class="btn-sm btn-red" onclick="bukaModalTolak(<?php echo $d['id_pengajuan']; ?>,'<?php echo htmlspecialchars(addslashes($d['nama_penyewa'])); ?>')">✕ Tolak</button>

            <?php elseif($st==='Disetujui'): ?>
              <?php $pm=urlencode("Halo *{$d['nama_penyewa']}* 👋\n\nPengajuan sewa *{$d['nama_unit']}* kamu sudah *DISETUJUI* ✅\n\nSilakan ambil ke toko. Bawa *KTP, STNK asli, dan motor* ya.\n\nTerima kasih! — Violet PlayStation"); ?>
              <a href="https://wa.me/<?php echo $wa; ?>?text=<?php echo $pm; ?>" target="_blank" class="btn-sm btn-wa">
                <svg width="12" height="12" fill="currentColor" viewBox="0 0 24 24"><use href="#ico-wa"/></svg>
                Chat Penyewa
              </a>
              <button class="btn-sm btn-purple" onclick="bukaPerpanjang(<?php echo $d['id_pengajuan']; ?>,'<?php echo htmlspecialchars(addslashes($d['nama_penyewa'])); ?>','<?php echo htmlspecialchars(addslashes($d['nama_unit'])); ?>','<?php echo htmlspecialchars($d['durasi']??'1 Hari'); ?>','<?php echo $kat; ?>',<?php echo intval($d['pakai_playbox']??0); ?>)">⏱ Perpanjang</button>
              <button class="btn-sm btn-blue" onclick="bukaSelesai(<?php
                $hpp = match($d['kategori']??'PS4'){'PS5'=>195000,'Nintendo'=>100000,default=>100000};
                $hpp += ($d['pakai_playbox']??0) ? 30000 : 0;
                echo $d['id_pengajuan'].','.$hpp.','.intval($d['harga']??0);
              ?>)">✓ Selesai</button>

            <?php elseif($st==='Ditolak'): ?>
              <?php $pm=urlencode("Halo *{$d['nama_penyewa']}* 👋\n\nMohon maaf, pengajuan sewa *{$d['nama_unit']}* tidak dapat diproses saat ini.\n\nHubungi kami jika ada pertanyaan. Terima kasih 🙏"); ?>
              <a href="https://wa.me/<?php echo $wa; ?>?text=<?php echo $pm; ?>" target="_blank" class="btn-sm btn-wa">
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

<!-- MODAL TOLAK -->
<div class="modal-overlay" id="modalTolak">
  <div class="modal-box">
    <div class="modal-title" style="color:#f87171;">✕ Tolak Pengajuan</div>
    <p style="color:var(--v-muted);font-size:.9rem;margin-bottom:1.5rem;">Pengajuan dari <strong id="tolak-nama" style="color:var(--v-white);"></strong> akan ditolak dan unit dikembalikan.</p>
    <div style="display:flex;gap:.75rem;justify-content:flex-end;">
      <button onclick="document.getElementById('modalTolak').classList.remove('open')" class="btn-sm btn-blue" style="padding:.6rem 1.25rem;font-size:.85rem;">Batal</button>
      <a id="tolak-btn" href="#" class="btn-sm btn-red" style="padding:.6rem 1.25rem;font-size:.85rem;">Ya, Tolak</a>
    </div>
  </div>
</div>

<!-- MODAL SELESAI -->
<div class="modal-overlay" id="modalSelesai">
  <div class="modal-box" style="max-width:420px;">
    <div class="modal-title" style="color:#60a5fa;">✓ Tandai Selesai</div>

    <!-- Step 1: tanya telat -->
    <div id="sl-step1">
      <p style="font-family:var(--font-ui);font-size:.92rem;color:var(--v-white);margin-bottom:1.25rem;">Apakah pelanggan <strong>terlambat</strong> mengembalikan unit?</p>
      <div style="display:flex;gap:.75rem;">
        <button class="btn-sm btn-red"  style="flex:1;padding:.65rem;font-size:.85rem;justify-content:center;" onclick="slStep2()">Ya, Terlambat</button>
        <a id="sl-ok-btn" href="#" class="btn-sm btn-blue" style="flex:1;padding:.65rem;font-size:.85rem;justify-content:center;text-align:center;">Tidak, Tepat Waktu ✓</a>
      </div>
    </div>

    <!-- Step 2: input jam -->
    <div id="sl-step2" style="display:none;">
      <p style="font-family:var(--font-ui);font-size:.9rem;color:var(--v-white);margin-bottom:.85rem;">Terlambat berapa jam?</p>
      <div style="display:flex;align-items:center;gap:1rem;margin-bottom:1.25rem;">
        <button onclick="slAdjust(-1)" style="width:42px;height:42px;border-radius:8px;background:rgba(255,255,255,.06);border:1px solid var(--v-border);color:var(--v-white);font-size:1.3rem;cursor:pointer;flex-shrink:0;line-height:1;">−</button>
        <div style="flex:1;text-align:center;">
          <div id="sl-jam" style="font-family:var(--font-display);font-size:3rem;font-weight:800;color:var(--v-lavender);line-height:1;">1</div>
          <div style="font-family:var(--font-ui);font-size:.72rem;letter-spacing:1.5px;text-transform:uppercase;color:var(--v-muted);margin-top:.15rem;">JAM</div>
        </div>
        <button onclick="slAdjust(1)"  style="width:42px;height:42px;border-radius:8px;background:rgba(255,255,255,.06);border:1px solid var(--v-border);color:var(--v-white);font-size:1.3rem;cursor:pointer;flex-shrink:0;line-height:1;">+</button>
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
        <div id="sl-hari-warn" style="font-size:.78rem;color:#fbbf24;font-family:var(--font-ui);padding:.4rem .5rem;background:rgba(251,191,36,.07);border-radius:6px;margin-bottom:.35rem;display:none;">⚠ Lebih dari 6 jam = dianggap sewa 1 hari lagi</div>
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

<!-- MODAL PERPANJANG -->
<div class="modal-overlay" id="modalPerpanjang">
  <div class="modal-box" style="max-width:420px;">
    <div class="modal-title" style="color:var(--v-lavender);">⏱ Perpanjang Sewa</div>
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
const CSRF = '<?php echo csrf_get_token(); ?>';

function bukaModalTolak(id, nama) {
    document.getElementById('tolak-nama').textContent = nama;
    document.getElementById('tolak-btn').href = '?aksi=tolak&id=' + id + '&_token=' + CSRF;
    document.getElementById('modalTolak').classList.add('open');
}
document.getElementById('modalTolak').addEventListener('click', function(e){ if(e.target===this) this.classList.remove('open'); });

let ppState = {};
let slState = {};
function bukaSelesai(id, hpp, harga){
    slState = {id: id, hpp: hpp, harga: harga, jam: 1};
    document.getElementById('sl-jam').textContent = '1';
    document.getElementById('sl-ok-btn').href = '?aksi=selesai&id='+id+'&telat=0&hpp='+hpp+'&_token='+CSRF;
    slStep1();
    slUpdate();
    document.getElementById('modalSelesai').classList.add('open');
}
function slStep1(){
    document.getElementById('sl-step1').style.display = 'block';
    document.getElementById('sl-step2').style.display = 'none';
}
function slStep2(){
    document.getElementById('sl-step1').style.display = 'none';
    document.getElementById('sl-step2').style.display = 'block';
}
function slAdjust(n){
    slState.jam = Math.max(1, Math.min(24, slState.jam + n));
    document.getElementById('sl-jam').textContent = slState.jam;
    slUpdate();
}
function slUpdate(){
    const jam   = slState.jam;
    const isHari = jam > 6;
    const denda  = isHari ? slState.hpp : jam * 10000;
    document.getElementById('sl-ket').textContent       = jam + ' jam' + (isHari ? ' (> 6 jam)' : '');
    document.getElementById('sl-denda').textContent     = '+' + fmt(denda);
    document.getElementById('sl-total').textContent     = fmt(slState.harga + denda);
    document.getElementById('sl-hari-warn').style.display = isHari ? 'block' : 'none';
    document.getElementById('sl-denda-btn').href = '?aksi=selesai&id='+slState.id+'&telat='+jam+'&hpp='+slState.hpp+'&_token='+CSRF;
}
document.getElementById('modalSelesai').addEventListener('click', function(e){ if(e.target===this) this.classList.remove('open'); });

function bukaPerpanjang(id, nama, unit, durasi, kat, playbox) {
    ppState.id           = id;
    ppState.hargaPerHari = (kat === 'PS5' ? 195000 : 100000) + (playbox ? 30000 : 0);
    ppState.durasiLama   = parseInt(durasi) || 1;
    ppState.hargaLama    = ppState.durasiLama * ppState.hargaPerHari;
    document.getElementById('pp-nama').textContent   = nama;
    document.getElementById('pp-unit').textContent   = unit;
    document.getElementById('pp-durasi').textContent = durasi;
    const btns = document.getElementById('pp-btns');
    btns.innerHTML = '';
    [1,2,3].forEach(function(n){
        const b = document.createElement('button');
        b.className     = 'btn-sm btn-purple';
        b.textContent   = '+' + n + ' Hari';
        b.style.cssText = 'padding:.5rem 1.1rem;font-size:.82rem;';
        b.onclick = function(){ pilihTambah(n, b); };
        btns.appendChild(b);
    });
    document.getElementById('pp-preview').style.opacity = '.4';
    document.getElementById('pp-confirm-btn').href = '#';
    document.getElementById('modalPerpanjang').classList.add('open');
}
function pilihTambah(n, btn) {
    document.querySelectorAll('#pp-btns .btn-sm').forEach(function(b){
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
    document.getElementById('pp-preview').style.opacity    = '1';
    document.getElementById('pp-confirm-btn').href = '?aksi=perpanjang&id=' + ppState.id + '&tambah=' + n + '&_token=' + CSRF;
}
document.getElementById('modalPerpanjang').addEventListener('click', function(e){ if(e.target===this) this.classList.remove('open'); });

function fmt(n){ return 'Rp ' + n.toLocaleString('id-ID'); }

function toggleSidebar(){
    document.querySelector('.sidebar').classList.toggle('mobile-open');
    document.getElementById('sidebarOverlay').classList.toggle('open');
    document.body.style.overflow = document.querySelector('.sidebar').classList.contains('mobile-open') ? 'hidden' : '';
}
function closeSidebar(){
    document.querySelector('.sidebar').classList.remove('mobile-open');
    document.getElementById('sidebarOverlay').classList.remove('open');
    document.body.style.overflow = '';
}
</script>
</body></html>