<?php
require_once '../config/koneksi.php';
require_admin('login.php');

if (isset($_GET['hapus'])) {
    csrf_get_check(); // CSRF on GET
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
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Master Game Violet PlayStation</title>
  <link rel="stylesheet" href="../assets/css/violet.css?v=<?php echo time(); ?>">
  <script src="../assets/app.js" defer></script>
</head>
<body>
<?php include_once "../config/svg_sprite_admin.php"; ?>

<div class="admin-topbar">
  <div style="display:flex;align-items:center;gap:.6rem;">
    <img src="../assets/images/logo-violet.jpeg" alt="Logo" style="height:28px;filter:drop-shadow(0 0 6px rgba(182, 255, 0, 0.3)); border-radius:3px;">
    <span class="admin-topbar-brand">VIOLET <span class="neon">PS</span></span>
  </div>
  <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Menu"><span></span><span></span><span></span></button>
</div>

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<?php 
$active_page = 'game'; 
include __DIR__.'/sidebar.php'; 
?>

<main class="main-content">
  <?php if($msg==='hapus_ok'): ?>
    <div class="alert-msg alert-success">✓ Game berhasil dihapus.</div>
  <?php endif; ?>
  <?php if($msg==='unassign_ok'): ?>
    <div class="alert-msg alert-success">✓ Game berhasil dihapus dari unit yang dipilih.</div>
  <?php endif; ?>

  <div class="page-header">
    <div class="page-title">MASTER <span class="neon">GAME</span></div>
    <button class="btn-violet" onclick="document.getElementById('modalTambah').classList.add('open')">
      <span>+ Tambah Game</span>
    </button>
  </div>

  <div class="games-grid">
    <?php 
    $q = $koneksi->query("SELECT * FROM games ORDER BY judul_game ASC"); 
    while($g = $q->fetch_assoc()): 
        $kat = $g['kategori_game'] ?? ''; 
        $bc = $kat==='PS5' ? 'v-badge-ps5' : ($kat==='Nintendo' ? 'v-badge-nin' : 'v-badge-ps4'); 
    ?>
    <div class="game-card">
      <img src="../uploads/games/<?php echo htmlspecialchars($g['foto_game']); ?>" alt="<?php echo htmlspecialchars($g['judul_game']); ?>">
      <div class="game-card-body">
        <?php if($kat): ?>
          <span class="v-badge <?php echo $bc; ?>"><?php echo $kat; ?></span>
        <?php endif; ?>
        <h6><?php echo htmlspecialchars($g['judul_game']); ?></h6>
        <a href="master_game.php?hapus=<?php echo $g['id_game']; ?>&_token=<?php echo csrf_get_token(); ?>" class="btn-hapus" onclick="return confirm('Hapus game ini?')">🗑 Hapus</a>
        <button type="button" onclick="bukaUnassign(<?php echo $g['id_game']; ?>,'<?php echo htmlspecialchars(addslashes($g['judul_game'])); ?>')" class="btn-kelola">🔗 Kelola Unit</button>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</main>

<!-- MODAL TAMBAH GAME -->
<div class="modal-overlay" id="modalTambah">
  <div class="modal-box">
    <button class="modal-close" onclick="document.getElementById('modalTambah').classList.remove('open')">✕</button>
    <div class="modal-title">TAMBAH <span class="neon">GAME</span></div>
    <form action="proses_tambah_game.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
      <div class="form-group">
        <label class="v-label">Judul Game</label>
        <input type="text" name="judul" class="v-input" required>
      </div>
      <div class="form-group">
        <label class="v-label">Kategori</label>
        <select name="kategori" class="v-input" required>
          <option value="">-- Pilih --</option>
          <option value="PS4">PS4</option>
          <option value="PS5">PS5</option>
          <option value="Nintendo">Nintendo</option>
        </select>
      </div>
      <div class="form-group">
        <label class="v-label">Foto Cover</label>
        <div class="file-upload-box" id="foto-box">
          <input type="file" name="foto" accept="image/*" required onchange="previewFoto(this)">
          <div class="upload-icon" id="foto-icon">🖼️</div>
          <div class="upload-text" id="foto-text">Klik untuk upload</div>
        </div>
      </div>
      <div class="form-group">
        <label class="v-label" style="margin-bottom:.5rem;">Assign ke Unit (Opsional)</label>
        
        <div style="display:flex;gap:.4rem;margin-bottom:.85rem;flex-wrap:wrap;">
          <button type="button" class="btn-sm btn-ps4-assign" onclick="pilihKategoriUnit('PS4')">✓ Semua PS4</button>
          <button type="button" class="btn-sm btn-ps5-assign" onclick="pilihKategoriUnit('PS5')">✓ Semua PS5</button>
          <button type="button" class="btn-sm btn-nin-assign" onclick="pilihKategoriUnit('Nintendo')">✓ Semua Nintendo</button>
          <button type="button" class="btn-sm btn-reset-assign" onclick="pilihKategoriUnit('reset')">✕ Reset</button>
        </div>

        <div class="unit-grid">
          <?php 
          $units = $koneksi->query("SELECT * FROM units ORDER BY tipe_layanan DESC, nama_unit ASC"); 
          while($u = $units->fetch_assoc()): 
          ?>
          <div class="unit-check">
            <input type="checkbox" name="unit_dipilih[]" value="<?php echo $u['id_unit']; ?>" id="u<?php echo $u['id_unit']; ?>" class="chk-unit" data-kat="<?php echo $u['kategori']; ?>">
            <label for="u<?php echo $u['id_unit']; ?>"><?php echo htmlspecialchars($u['nama_unit']); ?></label>
          </div>
          <?php endwhile; ?>
        </div>
      </div>
      <button type="submit" class="btn-violet" style="width:100%;padding:.9rem;font-size:1rem;letter-spacing:2px;border-radius:10px;margin-top:.5rem;"><span>💾 Simpan</span></button>
    </form>
  </div>
</div>

<!-- MODAL UNASSIGN -->
<div class="modal-overlay" id="modalUnassign">
  <div class="modal-box">
    <button class="modal-close" onclick="document.getElementById('modalUnassign').classList.remove('open')">✕</button>
    <div class="modal-title">🔗 KELOLA UNIT <span id="ua-judul" style="color:#fbbf24;"></span></div>
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
// ── PHP Data Binding ──
<?php
$game_units = [];
$qu = $koneksi->query("SELECT ug.id_game, u.id_unit, u.nama_unit FROM unit_games ug JOIN units u ON ug.id_unit=u.id_unit ORDER BY u.nama_unit");
while($r=$qu->fetch_assoc()) {
    $game_units[$r['id_game']][] = ['id'=>$r['id_unit'],'nama'=>$r['nama_unit']];
}
echo "const gameUnits = " . json_encode($game_units) . ";\n";
?>

// ── UI Logic Helpers ──
function pilihKategoriUnit(kat) {
  const checkboxes = document.querySelectorAll('.chk-unit');
  checkboxes.forEach(chk => {
    if (kat === 'reset') {
      chk.checked = false;
    } else if (chk.getAttribute('data-kat') === kat) {
      chk.checked = true;
    }
  });
}

function previewFoto(i) {
  if (i.files[0]) {
    document.getElementById('foto-text').textContent = i.files[0].name;
    document.getElementById('foto-icon').textContent = '✅';
    document.getElementById('foto-box').style.borderColor = 'var(--v-violet)';
  }
}

function bukaUnassign(id, judul){
  document.getElementById('ua-judul').textContent = judul;
  document.getElementById('ua-id-game').value = id;
  const units = gameUnits[id] || [];
  const list = document.getElementById('ua-unit-list');
  if(!units.length) {
    list.innerHTML = '<div style="color:var(--v-muted);font-family:var(--font-ui);font-size:.85rem;grid-column:1/-1;">Game ini belum di-assign ke unit manapun.</div>';
  } else {
    list.innerHTML = units.map(u => `
      <label class="unit-check" style="justify-content: flex-start;">
        <input type="checkbox" name="unit_ids[]" value="${u.id}" style="accent-color:#f87171;width:14px;height:14px;">
        <span style="font-family:var(--font-ui);font-size:.85rem;color:#C4B5D4;">${u.nama}</span>
      </label>`).join('');
  }
  document.getElementById('modalUnassign').classList.add('open');
}

// Modal Background Close Handlers
document.getElementById('modalTambah').addEventListener('click', function(e) {
  if(e.target === this) this.classList.remove('open');
});
document.getElementById('modalUnassign').addEventListener('click', function(e) {
  if(e.target === this) this.classList.remove('open');
});
</script>
</body>
</html>