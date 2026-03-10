<?php
require_once '../config/koneksi.php';
require_admin('login.php');
$games = $koneksi->query("SELECT * FROM games ORDER BY kategori_game, judul_game ASC");
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Tambah Unit — Violet PlayStation</title>
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
.page-title{font-family:var(--font-display);font-size:2rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;margin-bottom:2rem;}
.form-card{background:var(--v-card);border:1px solid var(--v-border);border-radius:16px;padding:2.5rem;max-width:700px;}
.form-group{margin-bottom:1.25rem;}
.form-section-label{font-family:var(--font-display);font-size:1rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-lavender);margin-bottom:1rem;padding-bottom:.6rem;border-bottom:1px solid var(--v-border);}
.games-check-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:.75rem;max-height:320px;overflow-y:auto;padding:.5rem;background:rgba(255,255,255,.02);border:1px solid var(--v-border);border-radius:10px;}
.game-check-item{display:flex;align-items:center;gap:.6rem;padding:.5rem .6rem;border-radius:8px;transition:background .2s;cursor:pointer;}
.game-check-item:hover{background:rgba(168,85,247,.08);}
.game-check-item input[type=checkbox]{accent-color:var(--v-violet);width:15px;height:15px;cursor:pointer;flex-shrink:0;}
.game-check-item label{font-family:var(--font-ui);font-size:.85rem;color:#C4B5D4;cursor:pointer;line-height:1.3;}
.game-check-item .game-kat{font-size:.7rem;color:var(--v-muted);}
.select-all-btn{font-family:var(--font-ui);font-size:.75rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:var(--v-violet);background:none;border:1px solid rgba(168,85,247,.3);border-radius:6px;padding:.3rem .75rem;cursor:pointer;transition:all .2s;margin-bottom:.75rem;}
.select-all-btn:hover{background:rgba(168,85,247,.1);}
@media(max-width:768px){.sidebar{display:none;}.main-content{margin-left:0;}}
</style>
</head>
<body>
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
  <a href="index.php" class="nav-item active"><span class="icon">🏠</span> Dashboard</a>
  <a href="data_sewa.php" class="nav-item"><span class="icon">📋</span> Data Sewa</a>
  <div class="nav-section">Admin Only</div>
  <a href="master_game.php" class="nav-item"><span class="icon">🎮</span> Master Game</a>
  <a href="kelola_akun.php" class="nav-item"><span class="icon">👥</span> Kelola Akun</a>
  <div class="sidebar-bottom">
    <div class="user-chip">Login sebagai<strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong></div>
    <a href="logout.php" class="btn-violet" style="display:block;text-align:center;text-decoration:none;padding:.6rem;font-size:.8rem;letter-spacing:2px;" onclick="return confirm('Yakin ingin keluar?')"><span>Logout</span></a>
  </div>
</aside>

<main class="main-content">
  <div style="display:flex;align-items:center;gap:1rem;margin-bottom:2rem;">
    <a href="index.php" style="font-family:var(--font-ui);font-size:.8rem;letter-spacing:1.5px;text-transform:uppercase;color:var(--v-muted);text-decoration:none;">← Kembali</a>
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