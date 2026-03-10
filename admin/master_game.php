<?php
require_once '../config/koneksi.php';
require_admin('login.php');

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $g = $koneksi->prepare("SELECT foto_game FROM games WHERE id_game = ?");
    $g->bind_param("i", $id); $g->execute();
    $foto = $g->get_result()->fetch_assoc()['foto_game'] ?? ''; $g->close();
    if ($foto) { $path = UPLOAD_PATH.'games'.DIRECTORY_SEPARATOR.$foto; if(file_exists($path)) unlink($path); }
    $stmt = $koneksi->prepare("DELETE FROM games WHERE id_game = ?");
    $stmt->bind_param("i", $id); $stmt->execute(); $stmt->close();
    header("Location: master_game.php?msg=hapus_ok"); exit();
}
$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Master Game — Violet PlayStation</title>
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
.main-content{margin-left:240px;flex:1;padding:2.5rem;background:var(--v-black);}
.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:2rem;}
.page-title{font-family:var(--font-display);font-size:2rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;}
.games-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:1.25rem;}
.game-card{background:var(--v-card);border:1px solid var(--v-border);border-radius:12px;overflow:hidden;transition:border-color .3s,box-shadow .3s;}
.game-card:hover{border-color:var(--v-purple);box-shadow:0 8px 30px rgba(123,47,190,.2);}
.game-card img{width:100%;height:160px;object-fit:cover;display:block;}
.game-card-body{padding:.75rem;}
.game-card-body h6{font-family:var(--font-ui);font-size:.85rem;font-weight:700;color:#C4B5D4;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-bottom:.5rem;}
.btn-hapus{display:block;width:100%;font-family:var(--font-ui);font-size:.75rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:.35rem;border-radius:6px;text-align:center;background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);text-decoration:none;transition:background .2s;}
.btn-hapus:hover{background:rgba(239,68,68,.3);}
.modal-overlay{position:fixed;inset:0;z-index:200;background:rgba(0,0,0,.7);backdrop-filter:blur(6px);display:none;align-items:center;justify-content:center;padding:1.5rem;}
.modal-overlay.open{display:flex;}
.modal-box{background:var(--v-card);border:1px solid var(--v-border);border-radius:20px;padding:2.5rem;width:100%;max-width:580px;max-height:90vh;overflow-y:auto;position:relative;animation:fadeUp .3s ease both;}
.modal-title{font-family:var(--font-display);font-size:1.5rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;margin-bottom:2rem;}
.modal-close{position:absolute;top:1.25rem;right:1.25rem;background:rgba(255,255,255,.05);border:1px solid var(--v-border);border-radius:8px;width:36px;height:36px;display:flex;align-items:center;justify-content:center;cursor:pointer;font-size:1.2rem;color:var(--v-muted);}
.modal-close:hover{color:var(--v-white);}
.unit-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:.75rem;max-height:200px;overflow-y:auto;}
.unit-check{display:flex;align-items:center;gap:.6rem;}
.unit-check input[type=checkbox]{accent-color:var(--v-violet);width:15px;height:15px;cursor:pointer;}
.unit-check label{font-family:var(--font-ui);font-size:.85rem;color:#C4B5D4;cursor:pointer;}
.form-group{margin-bottom:1.25rem;}
.file-upload-box{position:relative;border:2px dashed var(--v-border);border-radius:10px;background:rgba(255,255,255,.02);padding:1.5rem;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;}
.file-upload-box:hover{border-color:var(--v-violet);background:rgba(168,85,247,.05);}
.file-upload-box input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
.file-upload-box .upload-icon{font-size:2rem;margin-bottom:.5rem;}
.file-upload-box .upload-text{font-family:var(--font-ui);font-size:.9rem;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;}
.alert-msg{border-radius:8px;padding:.75rem 1rem;font-family:var(--font-ui);font-size:.85rem;letter-spacing:1px;margin-bottom:1.5rem;}
.alert-success{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#34d399;}
@media(max-width:768px){.sidebar{display:none;}.main-content{margin-left:0;}}
</style></head><body>
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
  <div class="sidebar-brand"><img src="../assets/images/logo-violet.jpeg" alt="Logo"><h2>VIOLET <span class="neon">PLAYSTATION</span></h2><p>Admin Panel</p></div>
  <div class="nav-section">Menu</div>
  <a href="index.php" class="nav-item"><span class="icon">🏠</span> Dashboard</a>
  <a href="data_sewa.php" class="nav-item"><span class="icon">📋</span> Data Sewa</a>
  <div class="nav-section">Admin Only</div>
  <a href="master_game.php" class="nav-item active"><span class="icon">🎮</span> Master Game</a>
  <a href="kelola_akun.php" class="nav-item"><span class="icon">👥</span> Kelola Akun</a>
  <div class="sidebar-bottom">
    <div class="user-chip">Login sebagai<strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong></div>
    <a href="logout.php" class="btn-violet" style="display:block;text-align:center;text-decoration:none;padding:.6rem;font-size:.8rem;letter-spacing:2px;" onclick="return confirm('Yakin ingin keluar?')"><span>Logout</span></a>
  </div>
</aside>
<main class="main-content">
  <?php if($msg==='hapus_ok'): ?><div class="alert-msg alert-success">✓ Game berhasil dihapus.</div><?php endif; ?>
  <div class="page-header">
    <div class="page-title">MASTER <span class="neon">GAME</span></div>
    <button class="btn-violet" onclick="document.getElementById('modalTambah').classList.add('open')"><span>+ Tambah Game</span></button>
  </div>
  <div class="games-grid">
    <?php $q=$koneksi->query("SELECT * FROM games ORDER BY judul_game ASC"); while($g=$q->fetch_assoc()): $kat=$g['kategori_game']??''; $bc=$kat==='PS5'?'v-badge-ps5':($kat==='Nintendo'?'v-badge-nin':'v-badge-ps4'); ?>
    <div class="game-card">
      <img src="../uploads/games/<?php echo htmlspecialchars($g['foto_game']); ?>" alt="<?php echo htmlspecialchars($g['judul_game']); ?>">
      <div class="game-card-body">
        <?php if($kat): ?><span class="v-badge <?php echo $bc; ?>" style="font-size:.65rem;padding:.1rem .4rem;margin-bottom:.35rem;display:inline-block;"><?php echo $kat; ?></span><?php endif; ?>
        <h6><?php echo htmlspecialchars($g['judul_game']); ?></h6>
        <a href="master_game.php?hapus=<?php echo $g['id_game']; ?>" class="btn-hapus" onclick="return confirm('Hapus game ini?')">🗑 Hapus</a>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</main>
<div class="modal-overlay" id="modalTambah">
  <div class="modal-box">
    <button class="modal-close" onclick="document.getElementById('modalTambah').classList.remove('open')">✕</button>
    <div class="modal-title">TAMBAH <span class="neon">GAME</span></div>
    <form action="proses_tambah_game.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
      <div class="form-group"><label class="v-label">Judul Game</label><input type="text" name="judul" class="v-input" required></div>
      <div class="form-group"><label class="v-label">Kategori</label>
        <select name="kategori" class="v-input" required><option value="">-- Pilih --</option><option value="PS4">PS4</option><option value="PS5">PS5</option><option value="Nintendo">Nintendo</option></select>
      </div>
      <div class="form-group"><label class="v-label">Foto Cover</label>
        <div class="file-upload-box" id="foto-box"><input type="file" name="foto" accept="image/*" required onchange="previewFoto(this)"><div class="upload-icon" id="foto-icon">🖼️</div><div class="upload-text" id="foto-text">Klik untuk upload</div></div>
      </div>
      <div class="form-group"><label class="v-label" style="margin-bottom:.75rem;">Assign ke Unit (Opsional)</label>
        <div class="unit-grid">
          <?php $units=$koneksi->query("SELECT * FROM units ORDER BY tipe_layanan DESC, nama_unit ASC"); while($u=$units->fetch_assoc()): ?>
          <div class="unit-check"><input type="checkbox" name="unit_dipilih[]" value="<?php echo $u['id_unit']; ?>" id="u<?php echo $u['id_unit']; ?>"><label for="u<?php echo $u['id_unit']; ?>"><?php echo htmlspecialchars($u['nama_unit']); ?></label></div>
          <?php endwhile; ?>
        </div>
      </div>
      <button type="submit" class="btn-violet" style="width:100%;padding:.9rem;font-size:1rem;letter-spacing:2px;border-radius:10px;margin-top:.5rem;"><span>💾 Simpan</span></button>
    </form>
  </div>
</div>
<script>
function previewFoto(i){if(i.files[0]){document.getElementById('foto-text').textContent=i.files[0].name;document.getElementById('foto-icon').textContent='✅';document.getElementById('foto-box').style.borderColor='var(--v-violet)';}}
document.getElementById('modalTambah').addEventListener('click',function(e){if(e.target===this)this.classList.remove('open');});
</script>
</body></html>