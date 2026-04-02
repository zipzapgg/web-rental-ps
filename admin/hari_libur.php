<?php
require_once '../config/koneksi.php';
require_admin('login.php');

if(isset($_POST['aksi']) && $_POST['aksi']==='tambah'){
    csrf_check();
    $tgl_mulai   = trim($_POST['tgl_mulai']   ?? '');
    $tgl_selesai = trim($_POST['tgl_selesai'] ?? '');
    $ket         = trim($_POST['keterangan']  ?? '');
    if($tgl_mulai && $tgl_selesai && $ket && $tgl_selesai >= $tgl_mulai && strlen($ket)<=100){
        $s = $koneksi->prepare("INSERT INTO hari_libur (tgl_mulai, tgl_selesai, keterangan) VALUES (?,?,?)");
        $s->bind_param("sss", $tgl_mulai, $tgl_selesai, $ket); $s->execute(); $s->close();
        header("Location: hari_libur.php?msg=tambah"); exit();
    }
    header("Location: hari_libur.php?msg=error"); exit();
}

if(isset($_GET['hapus'])){
    csrf_get_check();
    $id = intval($_GET['hapus']);
    $s  = $koneksi->prepare("DELETE FROM hari_libur WHERE id_libur=?");
    $s->bind_param("i",$id); $s->execute(); $s->close();
    header("Location: hari_libur.php?msg=hapus"); exit();
}

$msg   = $_GET['msg'] ?? '';
$libur = $koneksi->query("SELECT * FROM hari_libur ORDER BY tgl_mulai ASC");
$nama_hari = ['','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Hari Libur Violet PlayStation</title>
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
.two-col{display:grid;grid-template-columns:320px 1fr;gap:2rem;align-items:start;}
.form-card{background:var(--v-card);border:1px solid var(--v-border);border-radius:16px;padding:2rem;}
.form-card h3{font-family:var(--font-display);font-size:1.1rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-lavender);margin-bottom:1.5rem;}
.form-group{margin-bottom:1.1rem;}
.table-card{background:var(--v-card);border:1px solid var(--v-border);border-radius:16px;overflow:hidden;}
.table-card-header{padding:1.25rem 1.5rem;border-bottom:1px solid var(--v-border);}
.table-card-header h3{font-family:var(--font-display);font-size:1.1rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-lavender);}
.table-wrap{overflow-x:auto;}
.alert-msg{border-radius:8px;padding:.75rem 1rem;font-family:var(--font-ui);font-size:.85rem;letter-spacing:1px;margin-bottom:1.5rem;}
.alert-success{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#34d399;}
.alert-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#f87171;}
.btn-sm{font-family:var(--font-ui);font-size:.75rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:.35rem .9rem;border-radius:6px;text-decoration:none;display:inline-flex;align-items:center;transition:opacity .2s;cursor:pointer;border:none;}
.btn-sm:hover{opacity:.8;}
.btn-red{background:rgba(239,68,68,.2);color:#f87171;border:1px solid rgba(239,68,68,.3);}
.info-box{background:rgba(251,191,36,.06);border:1px solid rgba(251,191,36,.2);border-radius:10px;padding:.85rem 1.1rem;margin-bottom:1.5rem;font-size:.83rem;color:#fbbf24;font-family:var(--font-ui);line-height:1.6;}

@media(max-width:900px){.two-col{grid-template-columns:1fr;}}
@media(max-width:768px){.main-content{margin-left:0;}}
</style>
</head>
<body>
<div class="admin-topbar">
  <div style="display:flex;align-items:center;gap:.6rem;">
    <img src="../assets/images/logo-violet.jpeg" alt="Logo" style="height:28px;">
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
  <a href="laporan.php" class="nav-item"><svg width="16" height="16"><use href="../assets/icons.svg#ico-chart"/></svg> Laporan</a>
  <?php if(is_admin()): ?>
  <div class="nav-section">Admin Only</div>
  <a href="master_game.php" class="nav-item"><svg width="16" height="16"><use href="../assets/icons.svg#ico-gamepad"/></svg> Master Game</a>
  <a href="hari_libur.php" class="nav-item active"><svg width="16" height="16"><use href="../assets/icons.svg#ico-calendar"/></svg> Hari Libur</a>
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
  <div style="font-family:var(--font-display);font-size:2rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;margin-bottom:2rem;">HARI <span class="neon">LIBUR</span></div>

  <?php if($msg==='tambah'): ?><div class="alert-msg alert-success">✓ Periode libur berhasil ditambahkan.</div><?php endif; ?>
  <?php if($msg==='hapus'):  ?><div class="alert-msg alert-success">✓ Periode libur berhasil dihapus.</div><?php endif; ?>
  <?php if($msg==='error'):  ?><div class="alert-msg alert-error">✕ Data tidak valid. Pastikan tanggal selesai ≥ tanggal mulai.</div><?php endif; ?>

  <div class="info-box">
    ⚠ Tanggal dalam periode yang terdaftar <strong>tidak mendapatkan promo weekday</strong>, meskipun jatuh pada Senin–Kamis. Gunakan untuk libur nasional, cuti bersama, atau libur khusus.
  </div>

  <div class="two-col">
    <div class="form-card">
      <h3>➕ Tambah Periode Libur</h3>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="aksi" value="tambah">
        <div class="form-group">
          <label class="v-label">Keterangan</label>
          <input type="text" name="keterangan" class="v-input" placeholder="Contoh: Libur Lebaran 2025" required maxlength="100">
        </div>
        <div class="form-group">
          <label class="v-label">Dari Tanggal</label>
          <input type="date" name="tgl_mulai" id="inp_mulai" class="v-input" required onchange="autoSelesai()">
        </div>
        <div class="form-group">
          <label class="v-label">Sampai Tanggal</label>
          <input type="date" name="tgl_selesai" id="inp_selesai" class="v-input" required>
        </div>
        <div id="durasi_info" style="font-family:var(--font-ui);font-size:.78rem;color:var(--v-muted);margin-top:-.5rem;margin-bottom:1rem;"></div>
        <button type="submit" class="btn-violet" style="width:100%;padding:.75rem;letter-spacing:2px;border-radius:8px;"><span>Simpan</span></button>
      </form>


    </div>

    <div class="table-card">
      <div class="table-card-header"><h3>Daftar Periode Libur</h3></div>
      <div class="table-wrap">
        <table class="v-table">
          <thead><tr><th>Keterangan</th><th>Dari</th><th>Sampai</th><th>Durasi</th><th>Aksi</th></tr></thead>
          <tbody>
          <?php if($libur->num_rows===0): ?>
          <tr><td colspan="5" style="text-align:center;color:var(--v-muted);font-family:var(--font-ui);padding:2rem;">Belum ada periode libur terdaftar.</td></tr>
          <?php endif; ?>
          <?php while($l=$libur->fetch_assoc()):
            $tgl_m = strtotime($l['tgl_mulai']);
            $tgl_s = strtotime($l['tgl_selesai']);
            $n_hari = (int)(($tgl_s - $tgl_m) / 86400) + 1;
            $sudah_lewat = $l['tgl_selesai'] < date('Y-m-d');
            $aktif = !$sudah_lewat && $l['tgl_mulai'] <= date('Y-m-d');
          ?>
          <tr style="<?php echo $sudah_lewat?'opacity:.5':''; ?>">
            <td style="color:var(--v-white);font-weight:600;">
              <?php echo htmlspecialchars($l['keterangan']); ?>
              <?php if($aktif): ?><span style="background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);font-family:var(--font-ui);font-size:.68rem;padding:.1rem .4rem;border-radius:4px;margin-left:.4rem;">Aktif</span><?php endif; ?>
              <?php if($sudah_lewat): ?><span style="color:var(--v-muted);font-size:.75rem;font-family:var(--font-ui);"> (lewat)</span><?php endif; ?>
            </td>
            <td style="font-family:var(--font-ui);color:#C4B5D4;white-space:nowrap;">
              <?php echo date('d/m/Y',$tgl_m); ?><br>
              <span style="font-size:.72rem;color:var(--v-muted);"><?php echo $nama_hari[intval(date('N',$tgl_m))]; ?></span>
            </td>
            <td style="font-family:var(--font-ui);color:#C4B5D4;white-space:nowrap;">
              <?php echo date('d/m/Y',$tgl_s); ?><br>
              <span style="font-size:.72rem;color:var(--v-muted);"><?php echo $nama_hari[intval(date('N',$tgl_s))]; ?></span>
            </td>
            <td style="font-family:var(--font-ui);color:#fbbf24;font-weight:700;"><?php echo $n_hari; ?> hari</td>
            <td><a href="hari_libur.php?hapus=<?php echo $l['id_libur']; ?>&_token=<?php echo csrf_get_token(); ?>" class="btn-sm btn-red" onclick="return confirm('Hapus periode libur ini?')">Hapus</a></td>
          </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<script>
function autoSelesai(){
  const m = document.getElementById('inp_mulai').value;
  const s = document.getElementById('inp_selesai').value;
  if(m && (!s || s < m)) document.getElementById('inp_selesai').value = m;
  updateDurasiInfo();
}
document.getElementById('inp_selesai').addEventListener('change', updateDurasiInfo);
function updateDurasiInfo(){
  const m = document.getElementById('inp_mulai').value;
  const s = document.getElementById('inp_selesai').value;
  const el = document.getElementById('durasi_info');
  if(m && s && s >= m){
    const diff = Math.round((new Date(s) - new Date(m)) / 86400000) + 1;
    el.textContent = '📅 ' + diff + ' hari (' + m + ' s/d ' + s + ')';
  } else { el.textContent = ''; }
}
function toggleSidebar(){document.querySelector('.sidebar').classList.toggle('mobile-open');document.getElementById('sidebarOverlay').classList.toggle('open');document.body.style.overflow=document.querySelector('.sidebar').classList.contains('mobile-open')?'hidden':'';}
function closeSidebar(){document.querySelector('.sidebar').classList.remove('mobile-open');document.getElementById('sidebarOverlay').classList.remove('open');document.body.style.overflow='';}
</script>
</body></html>