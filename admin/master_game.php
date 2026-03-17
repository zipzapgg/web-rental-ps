<?php
require_once '../config/koneksi.php';
require_admin('login.php');

if (isset($_GET['hapus'])) {
    csrf_get_check(); // [FIX #1] CSRF on GET
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

// Bulk unassign game dari semua/beberapa unit
if(isset($_POST['aksi']) && $_POST['aksi']==='unassign'){
    csrf_check();
    $id_game = intval($_POST['id_game'] ?? 0);
    if($id_game){
        $unit_ids = array_map('intval', $_POST['unit_ids'] ?? []);
        if(!empty($unit_ids)){
            $placeholders = implode(',', array_fill(0, count($unit_ids), '?'));
            $types = str_repeat('i', count($unit_ids)+1);
            $params = array_merge([$id_game], $unit_ids);
            $stmt = $koneksi->prepare("DELETE FROM unit_games WHERE id_game=? AND id_unit IN ($placeholders)");
            $stmt->bind_param($types, ...$params);
            $stmt->execute(); $stmt->close();
        }
    }
    header("Location: master_game.php?msg=unassign_ok"); exit();
}
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Master Game — Violet PlayStation</title>
<link rel="stylesheet" href="../assets/css/violet.css">
<style>
body{display:flex;min-height:100vh;}
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
@media(max-width:768px){.main-content{margin-left:0;}}
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
   <a href="laporan.php" class="nav-item">📊 Laporan</a>
  <div class="nav-section">Admin Only</div>
  <a href="master_game.php" class="nav-item active"><span class="icon">🎮</span> Master Game</a>
  <a href="hari_libur.php" class="nav-item"><span class="icon">📅</span> Hari Libur</a>
  <a href="kelola_akun.php" class="nav-item"><span class="icon">👥</span> Kelola Akun</a>
  <div class="sidebar-bottom">
    <div class="user-chip">Login sebagai<strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong></div>
    <a href="logout.php" class="btn-violet" style="display:block;text-align:center;text-decoration:none;padding:.6rem;font-size:.8rem;letter-spacing:2px;" onclick="return confirm('Yakin ingin keluar?')"><span>Logout</span></a>
  </div>
</aside>
<main class="main-content">
  <?php if($msg==='hapus_ok'): ?><div class="alert-msg alert-success">✓ Game berhasil dihapus.</div><?php endif; ?>
<?php if($msg==='unassign_ok'): ?><div class="alert-msg alert-success">✓ Game berhasil dihapus dari unit yang dipilih.</div><?php endif; ?>
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
        <a href="master_game.php?hapus=<?php echo $g['id_game']; ?>&_token=<?php echo csrf_get_token(); ?>" class="btn-hapus" onclick="return confirm('Hapus game ini?')">🗑 Hapus</a>
        <button type="button" onclick="bukaUnassign(<?php echo $g['id_game']; ?>,'<?php echo htmlspecialchars(addslashes($g['judul_game'])); ?>')" style="display:block;width:100%;font-family:var(--font-ui);font-size:.72rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:.35rem;border-radius:6px;text-align:center;background:rgba(251,191,36,.1);color:#fbbf24;border:1px solid rgba(251,191,36,.25);cursor:pointer;transition:background .2s;margin-top:.35rem;">🔗 Kelola Unit</button>
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
<!-- MODAL UNASSIGN -->
<div class="modal-overlay" id="modalUnassign">
  <div class="modal-box">
    <button class="modal-close" onclick="document.getElementById('modalUnassign').classList.remove('open')">✕</button>
    <div class="modal-title">🔗 KELOLA UNIT — <span id="ua-judul" style="color:#fbbf24;"></span></div>
    <p style="color:var(--v-muted);font-size:.85rem;margin-bottom:1.25rem;">Centang unit yang ingin <strong style="color:#f87171;">dihapus</strong> game ini, lalu klik Simpan.</p>
    <form method="POST" id="unassignForm">
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="aksi" value="unassign">
      <input type="hidden" name="id_game" id="ua-id-game" value="">
      <div id="ua-unit-list" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:.6rem;max-height:260px;overflow-y:auto;padding:.25rem;margin-bottom:1.5rem;"></div>
      <div style="display:flex;gap:.75rem;justify-content:flex-end;">
        <button type="button" onclick="document.getElementById('modalUnassign').classList.remove('open')" class="btn-sm btn-blue" style="padding:.6rem 1.25rem;">Batal</button>
        <button type="submit" class="btn-sm btn-red" style="padding:.6rem 1.25rem;">🗑 Hapus dari Unit Terpilih</button>
      </div>
    </form>
  </div>
</div>

<script>
<?php
// Kirim data: game_id → list unit yang sudah assign
$game_units = [];
$qu = $koneksi->query("SELECT ug.id_game, u.id_unit, u.nama_unit FROM unit_games ug JOIN units u ON ug.id_unit=u.id_unit ORDER BY u.nama_unit");
while($r=$qu->fetch_assoc()) $game_units[$r['id_game']][] = ['id'=>$r['id_unit'],'nama'=>$r['nama_unit']];
echo "const gameUnits=".json_encode($game_units).";
";
?>

function bukaUnassign(id, judul){
  document.getElementById('ua-judul').textContent=judul;
  document.getElementById('ua-id-game').value=id;
  const units=gameUnits[id]||[];
  const list=document.getElementById('ua-unit-list');
  if(!units.length){
    list.innerHTML='<div style="color:var(--v-muted);font-family:var(--font-ui);font-size:.85rem;grid-column:1/-1;">Game ini belum di-assign ke unit manapun.</div>';
  } else {
    list.innerHTML=units.map(u=>`
      <label style="display:flex;align-items:center;gap:.6rem;background:rgba(255,255,255,.03);border:1px solid var(--v-border);border-radius:8px;padding:.5rem .75rem;cursor:pointer;transition:background .2s;">
        <input type="checkbox" name="unit_ids[]" value="${u.id}" style="accent-color:#f87171;width:14px;height:14px;">
        <span style="font-family:var(--font-ui);font-size:.85rem;color:#C4B5D4;">${u.nama}</span>
      </label>`).join('');
  }
  document.getElementById('modalUnassign').classList.add('open');
}
document.getElementById('modalUnassign').addEventListener('click',e=>{if(e.target===document.getElementById('modalUnassign'))document.getElementById('modalUnassign').classList.remove('open');});
</script>
</body></html>