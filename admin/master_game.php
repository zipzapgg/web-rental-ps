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
        <div style="font-size: .78rem; color: var(--v-muted); margin-bottom: .25rem;">Genre: <?php echo htmlspecialchars($g['genre_game'] ?? 'Action / Adventure'); ?></div>
        <div style="font-size: .78rem; color: var(--v-muted); margin-bottom: .75rem;">Players: <?php echo htmlspecialchars($g['players_game'] ?? '1-2 Players'); ?></div>
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
        <label class="v-label">Genre Game</label>
        <input type="text" name="genre" class="v-input" placeholder="Contoh: Sports / Racing, Action RPG" required>
      </div>
      <div class="form-group">
        <label class="v-label">Jumlah Pemain (Players)</label>
        <input type="text" name="players" class="v-input" placeholder="Contoh: 1-4 Players, 1-2 Players" required>
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
  <div class="modal-box" style="max-width: 520px;">
    <button class="modal-close" onclick="document.getElementById('modalUnassign').classList.remove('open')">✕</button>
    <div class="modal-title" style="margin-bottom: 0.5rem;">🔗 KELOLA UNIT</div>
    <div style="font-family: var(--font-display); font-size: 1.15rem; font-weight: 700; color: #fbbf24; margin-bottom: 1rem;" id="ua-judul"></div>
    
    <div style="background: rgba(239, 68, 68, 0.08); border: 1px solid rgba(239, 68, 68, 0.2); border-radius: 12px; padding: 0.85rem 1rem; margin-bottom: 1.5rem;">
      <p style="color: #fca5a5; font-size: 0.84rem; line-height: 1.5; margin: 0;">
        ⚠️ <strong>Pilih unit untuk mencopot game:</strong> Centang unit di bawah yang ingin dihapus relasi gamenya, kemudian tekan tombol Simpan untuk mengeksekusi.
      </p>
    </div>

    <form method="POST" id="unassignForm">
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="aksi" value="unassign">
      <input type="hidden" name="id_game" id="ua-id-game" value="">
      
      <div id="ua-unit-list" style="display: flex; flex-direction: column; gap: 0.65rem; max-height: 280px; overflow-y: auto; padding: 0.25rem; margin-bottom: 1.75rem;"></div>
      
      <div style="display:flex; gap: 0.75rem; justify-content: flex-end; border-top: 1px solid rgba(255,255,255,0.06); padding-top: 1.25rem;">
        <button type="button" onclick="document.getElementById('modalUnassign').classList.remove('open')" class="btn-sm btn-blue" style="padding: 0.7rem 1.5rem; border-radius: 8px;">Batal</button>
        <button type="submit" class="btn-sm btn-red" style="padding: 0.7rem 1.5rem; border-radius: 8px; font-weight: 700;">💾 Simpan Perubahan</button>
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
    list.innerHTML = '<div style="color:var(--v-muted);font-family:var(--font-ui);font-size:0.88rem;text-align:center;padding:2rem 0;grid-column:1/-1;">Game ini belum di-assign ke unit manapun.</div>';
  } else {
    list.innerHTML = units.map(u => `
      <label class="unassign-unit-item">
        <div class="unassign-unit-left">
          <span class="unassign-unit-icon">🎮</span>
          <span class="unassign-unit-name">${u.nama}</span>
        </div>
        <div class="unassign-checkbox-wrap">
          <input type="checkbox" name="unit_ids[]" value="${u.id}" class="unassign-checkbox-input">
        </div>
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