<?php
require_once '../config/koneksi.php';
require_admin('login.php');

$id_unit = intval($_GET['id'] ?? 0);
if (!$id_unit) { header("Location: index.php"); exit(); }

$s = $koneksi->prepare("SELECT * FROM units WHERE id_unit=?");
$s->bind_param("i", $id_unit); $s->execute();
$unit = $s->get_result()->fetch_assoc(); $s->close();
if (!$unit) { header("Location: index.php"); exit(); }

$msg = $_GET['msg'] ?? '';
$kat  = $unit['kategori'];
$bc   = $kat==='PS5'?'v-badge-ps5':($kat==='Nintendo'?'v-badge-nin':'v-badge-ps4');
$games_all = $koneksi->query("SELECT * FROM games ORDER BY judul_game ASC");

$assigned = [];
$r = $koneksi->prepare("SELECT id_game FROM unit_games WHERE id_unit=?");
$r->bind_param("i", $id_unit); $r->execute();
$res = $r->get_result();
while($row=$res->fetch_assoc()) $assigned[]=$row['id_game'];
$r->close();
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Game Unit <?php echo htmlspecialchars($unit['nama_unit']); ?> — Violet PlayStation</title>
<link rel="stylesheet" href="../assets/css/violet.css">
<style>
body{display:flex;min-height:100vh;}
.main-content{margin-left:240px;flex:1;padding:2.5rem;background:var(--v-black);}
.games-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:1rem;margin-top:1.5rem;}
.game-item{background:var(--v-card);border:1px solid var(--v-border);border-radius:10px;overflow:hidden;transition:border-color .2s,box-shadow .2s;}
.game-item.assigned{border-color:rgba(16,185,129,.4);box-shadow:0 0 12px rgba(16,185,129,.15);}
.game-item img{width:100%;height:130px;object-fit:cover;display:block;}
.game-item-body{padding:.6rem .75rem;}
.game-item-title{font-family:var(--font-ui);font-size:.82rem;font-weight:600;color:#C4B5D4;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-bottom:.5rem;}
.btn-toggle{display:block;width:100%;font-family:var(--font-ui);font-size:.72rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:.35rem;border-radius:6px;text-align:center;text-decoration:none;transition:all .2s;border:none;cursor:pointer;}
.btn-add{background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);}
.btn-add:hover{background:rgba(16,185,129,.3);}
.btn-remove{background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);}
.btn-remove:hover{background:rgba(239,68,68,.3);}
.unit-info{background:var(--v-card);border:1px solid var(--v-border);border-radius:14px;padding:1.5rem 2rem;margin-bottom:2rem;display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;}
.alert-msg{border-radius:8px;padding:.75rem 1rem;font-family:var(--font-ui);font-size:.85rem;letter-spacing:1px;margin-bottom:1.5rem;}
.alert-success{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#34d399;}
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
<aside class="sidebar">
  <div class="sidebar-brand"><img src="../assets/images/logo-violet.jpeg" alt="Logo"><h2>VIOLET <span class="neon">PLAYSTATION</span></h2><p>Admin Panel</p></div>
  <div class="nav-section">Menu</div>
  <a href="index.php" class="nav-item active">🏠 Dashboard</a>
  <a href="data_sewa.php" class="nav-item">📋 Data Sewa</a>
  <div class="nav-section">Admin Only</div>
  <a href="master_game.php" class="nav-item">🎮 Master Game</a>
  <a href="kelola_akun.php" class="nav-item">👥 Kelola Akun</a>
  <div class="sidebar-bottom">
    <div class="user-chip">Login sebagai<strong><?php echo htmlspecialchars($_SESSION['nama']??$_SESSION['user']); ?></strong>
    <span class="role-badge role-<?php echo $_SESSION['role']; ?>"><?php echo ucfirst($_SESSION['role']); ?></span></div>
    <a href="logout.php" class="btn-violet" style="display:block;text-align:center;text-decoration:none;padding:.6rem;font-size:.8rem;letter-spacing:2px;" onclick="return confirm('Yakin ingin keluar?')"><span>Logout</span></a>
  </div>
</aside>
<main class="main-content">
  <a href="index.php" style="font-family:var(--font-ui);font-size:.8rem;letter-spacing:1.5px;text-transform:uppercase;color:var(--v-muted);text-decoration:none;display:inline-block;margin-bottom:1.5rem;">← Kembali</a>

  <?php if($msg==='ok'): ?><div class="alert-msg alert-success">✓ Game berhasil diperbarui.</div><?php endif; ?>

  <div class="unit-info">
    <div>
      <div style="font-family:var(--font-display);font-size:1.5rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;color:var(--v-lavender);"><?php echo htmlspecialchars($unit['nama_unit']); ?></div>
      <div style="margin-top:.5rem;display:flex;gap:.5rem;align-items:center;">
        <span class="v-badge <?php echo $bc; ?>"><?php echo $kat; ?></span>
        <span style="font-family:var(--font-ui);font-size:.8rem;color:var(--v-muted);"><?php echo $unit['tipe_layanan']; ?></span>
        <span style="font-family:var(--font-ui);font-size:.78rem;color:#34d399;"><?php echo count($assigned); ?> game terpilih</span>
      </div>
    </div>
  </div>

  <div style="font-family:var(--font-display);font-size:1.1rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-lavender);">Assign Game</div>
  <div style="font-family:var(--font-ui);font-size:.8rem;color:var(--v-muted);margin-top:.25rem;">Klik Tambah/Hapus untuk update game di unit ini</div>

  <div class="games-grid">
    <?php while($g=$games_all->fetch_assoc()):
      $is_assigned = in_array($g['id_game'], $assigned);
      $gkat=$g['kategori_game']??''; $gbc=$gkat==='PS5'?'v-badge-ps5':($gkat==='Nintendo'?'v-badge-nin':'v-badge-ps4');
      $token = csrf_get_token();
    ?>
    <div class="game-item <?php echo $is_assigned?'assigned':''; ?>">
      <img src="../uploads/games/<?php echo htmlspecialchars($g['foto_game']); ?>" alt="<?php echo htmlspecialchars($g['judul_game']); ?>">
      <div class="game-item-body">
        <?php if($gkat): ?><span class="v-badge <?php echo $gbc; ?>" style="font-size:.62rem;padding:.08rem .35rem;margin-bottom:.3rem;display:inline-block;"><?php echo $gkat; ?></span><?php endif; ?>
        <div class="game-item-title"><?php echo htmlspecialchars($g['judul_game']); ?></div>
        <a href="proses_isi_unit.php?act=<?php echo $is_assigned?'hapus':'tambah'; ?>&unit=<?php echo $id_unit; ?>&game=<?php echo $g['id_game']; ?>&_token=<?php echo $token; ?>"
           class="btn-toggle <?php echo $is_assigned?'btn-remove':'btn-add'; ?>">
          <?php echo $is_assigned?'✕ Hapus':'+ Tambah'; ?>
        </a>
      </div>
    </div>
    <?php endwhile; ?>
  </div>
</main>
<script>
function toggleSidebar(){document.querySelector('.sidebar').classList.toggle('mobile-open');document.getElementById('sidebarOverlay').classList.toggle('open');}
function closeSidebar(){document.querySelector('.sidebar').classList.remove('mobile-open');document.getElementById('sidebarOverlay').classList.remove('open');}
</script>
</body></html>