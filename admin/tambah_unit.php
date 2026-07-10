<?php
require_once '../config/koneksi.php';
require_admin('login.php');
$games = $koneksi->query("SELECT * FROM games ORDER BY kategori_game, judul_game ASC");
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Tambah Unit Violet PlayStation</title>
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
<?php $active_page = 'dashboard'; include __DIR__.'/sidebar.php'; ?>

<main class="main-content">
  <div style="display:flex;align-items:center;gap:1rem;margin-bottom:2rem;">
    <div style="display:flex;align-items:center;gap:.5rem;font-family:var(--font-ui);font-size:.78rem;letter-spacing:1px;text-transform:uppercase;color:var(--v-muted);">
      <a href="index.php" style="color:var(--v-muted);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='var(--v-lavender)'" onmouseout="this.style.color='var(--v-muted)'">Dashboard</a>
      <span style="opacity:.4;">›</span>
      <span style="color:var(--v-lavender);">Tambah Unit</span>
    </div>
  </div>
  <div class="page-title">TAMBAH <span class="neon">UNIT</span></div>

  <div class="form-card">
    <form action="proses_tambah_unit.php" method="POST">
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

      <div class="form-section-label">Info Unit</div>
      <div class="form-group">
        <label class="v-label">Nama Unit</label>
        <input type="text" name="nama_unit" class="v-input" placeholder="Contoh: PS5 - TV 04" required>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
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
          <label class="v-label">Tipe Layanan</label>
          <select name="tipe_layanan" class="v-input" required>
            <option value="Main di Tempat">Main di Tempat</option>
            <option value="Sewa Luar">Sewa Bawa Pulang</option>
          </select>
        </div>
      </div>

      <div class="form-section-label" style="margin-top:1rem;">Assign Game ke Unit Ini</div>
      <p style="font-size:.83rem;color:var(--v-muted);margin-bottom:.75rem;">Pilih game dari master game yang ingin ditambahkan ke unit ini. Bisa diubah lagi nanti.</p>

      <?php if($games->num_rows > 0): ?>
      <button type="button" class="select-all-btn" onclick="toggleAll()">☑ Pilih Semua</button>
      <div class="games-check-grid" id="games-grid">
        <?php while($g=$games->fetch_assoc()): $kat=$g['kategori_game']??''; ?>
        <div class="game-check-item">
          <input type="checkbox" name="game_ids[]" value="<?php echo $g['id_game']; ?>" id="g<?php echo $g['id_game']; ?>">
          <label for="g<?php echo $g['id_game']; ?>">
            <?php echo htmlspecialchars($g['judul_game']); ?>
            <?php if($kat): ?><span class="game-kat d-block"><?php echo $kat; ?></span><?php endif; ?>
          </label>
        </div>
        <?php endwhile; ?>
      </div>
      <?php else: ?>
      <div style="background:rgba(168,85,247,.06);border:1px dashed rgba(168,85,247,.3);border-radius:10px;padding:1.5rem;text-align:center;color:var(--v-muted);font-family:var(--font-ui);font-size:.85rem;letter-spacing:1px;">
        Belum ada game di master game. <a href="master_game.php" style="color:var(--v-violet);">Tambah game dulu →</a>
      </div>
      <?php endif; ?>

      <button type="submit" class="btn-violet" style="width:100%;padding:.9rem;font-size:1rem;letter-spacing:2px;border-radius:10px;margin-top:2rem;"><span>💾 Simpan Unit</span></button>
    </form>
  </div>
</main>

<script>
let allSelected = false;
function toggleAll() {
  allSelected = !allSelected;
  document.querySelectorAll('#games-grid input[type=checkbox]').forEach(cb => cb.checked = allSelected);
  document.querySelector('.select-all-btn').textContent = allSelected ? '☐ Batal Pilih Semua' : '☑ Pilih Semua';
}
</script>
</body></html>