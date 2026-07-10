<?php
require_once '../config/koneksi.php';
require_admin('login.php');

$id_unit = intval($_GET['id'] ?? 0);
if(!$id_unit){ header("Location: index.php"); exit(); }

$s = $koneksi->prepare("SELECT * FROM units WHERE id_unit=?");
$s->bind_param("i",$id_unit); $s->execute();
$unit = $s->get_result()->fetch_assoc(); $s->close();
if(!$unit){ header("Location: index.php"); exit(); }

$msg = '';
if($_SERVER['REQUEST_METHOD']==='POST'){
    csrf_check();
    $nama  = trim($_POST['nama_unit'] ?? '');
    $kat   = in_array($_POST['kategori'],['PS4','PS5','Nintendo']) ? $_POST['kategori'] : null;
    $tipe  = in_array($_POST['tipe_layanan'],['Main di Tempat','Sewa Luar']) ? $_POST['tipe_layanan'] : null;
    if(!$nama||!$kat||!$tipe){
        $msg = ['type'=>'error','text'=>'Semua field wajib diisi.'];
    } else {
        $s = $koneksi->prepare("UPDATE units SET nama_unit=?,kategori=?,tipe_layanan=? WHERE id_unit=?");
        $s->bind_param("sssi",$nama,$kat,$tipe,$id_unit); $s->execute(); $s->close();
        header("Location: index.php?msg=edit_ok"); exit();
    }
}
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Edit Unit Violet PlayStation</title>
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
  <div style="display:flex;align-items:center;gap:.5rem;font-family:var(--font-ui);font-size:.78rem;letter-spacing:1px;text-transform:uppercase;color:var(--v-muted);margin-bottom:1.5rem;">
    <a href="index.php" style="color:var(--v-muted);text-decoration:none;transition:color .2s;" onmouseover="this.style.color='var(--v-lavender)'" onmouseout="this.style.color='var(--v-muted)'">Dashboard</a>
    <span style="opacity:.4;">›</span>
    <span style="color:var(--v-lavender);">Edit Unit</span>
  </div>
  <div style="font-family:var(--font-display);font-size:2rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;margin-bottom:2rem;">EDIT <span class="neon">UNIT</span></div>
  <?php if($msg && $msg['type']==='error'): ?><div class="alert-error"><?php echo $msg['text']; ?></div><?php endif; ?>
  <div class="form-card">
    <form method="POST">
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
      <div class="form-group">
        <label class="v-label">Nama Unit</label>
        <input type="text" name="nama_unit" class="v-input" value="<?php echo htmlspecialchars($unit['nama_unit']); ?>" required>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;">
        <div class="form-group">
          <label class="v-label">Kategori</label>
          <select name="kategori" class="v-input" required>
            <?php foreach(['PS4','PS5','Nintendo'] as $k): ?>
            <option value="<?php echo $k; ?>" <?php echo $unit['kategori']===$k?'selected':''; ?>><?php echo $k; ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="v-label">Tipe Layanan</label>
          <select name="tipe_layanan" class="v-input" required>
            <option value="Main di Tempat" <?php echo $unit['tipe_layanan']==='Main di Tempat'?'selected':''; ?>>Main di Tempat</option>
            <option value="Sewa Luar" <?php echo $unit['tipe_layanan']==='Sewa Luar'?'selected':''; ?>>Sewa Bawa Pulang</option>
          </select>
        </div>
      </div>
      <button type="submit" class="btn-violet" style="width:100%;padding:.85rem;letter-spacing:2px;border-radius:8px;margin-top:.5rem;"><span>💾 Simpan Perubahan</span></button>
    </form>
  </div>
</main>
</body></html>