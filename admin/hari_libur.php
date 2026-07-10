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
<?php $active_page = 'libur'; include __DIR__.'/sidebar.php'; ?>

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