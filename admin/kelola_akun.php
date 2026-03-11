<?php
require_once '../config/koneksi.php';
require_admin('login.php');

$msg = '';

// Tambah akun
if (isset($_POST['aksi']) && $_POST['aksi'] === 'tambah') {
    csrf_check();
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $nama     = trim($_POST['nama_lengkap']);
    $role     = in_array($_POST['role'], ['admin','karyawan']) ? $_POST['role'] : 'karyawan';

    if (strlen($password) < 8 || !preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $msg = ['type'=>'error', 'text'=>'Password minimal 8 karakter.'];
    } else {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $koneksi->prepare("INSERT INTO admin (username, password, role, nama_lengkap) VALUES (?,?,?,?)");
        $stmt->bind_param("ssss", $username, $hash, $role, $nama);
        if ($stmt->execute()) {
            $msg = ['type'=>'success', 'text'=>"Akun '$username' berhasil dibuat."];
        } else {
            $msg = ['type'=>'error', 'text'=>'Username sudah digunakan.'];
        }
        $stmt->close();
    }
}

// Hapus akun
if (isset($_GET['hapus'])) {
    csrf_get_check();
    $id = intval($_GET['hapus']);
    // Tidak boleh hapus diri sendiri
    if ($id === intval($_SESSION['id_admin'])) {
        $msg = ['type'=>'error', 'text'=>'Tidak bisa menghapus akun sendiri.'];
    } else {
        $stmt = $koneksi->prepare("DELETE FROM admin WHERE id_admin = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $msg = ['type'=>'success', 'text'=>'Akun berhasil dihapus.'];
    }
}

$akuns = $koneksi->query("SELECT id_admin, username, nama_lengkap, role, created_at FROM admin ORDER BY role ASC, created_at ASC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Akun — Violet PlayStation</title>
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
    .two-col{display:grid;grid-template-columns:1fr 1.5fr;gap:2rem;align-items:start;}
    .form-card{background:var(--v-card);border:1px solid var(--v-border);border-radius:16px;padding:2rem;}
    .form-card h3{font-family:var(--font-display);font-size:1.1rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-lavender);margin-bottom:1.5rem;}
    .form-group{margin-bottom:1.1rem;}
    .table-card{background:var(--v-card);border:1px solid var(--v-border);border-radius:16px;overflow:hidden;}
    .table-card-header{padding:1.25rem 1.5rem;border-bottom:1px solid var(--v-border);}
    .table-card-header h3{font-family:var(--font-display);font-size:1.1rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-lavender);}
    .table-wrap{overflow-x:auto;}
    .role-admin{background:rgba(168,85,247,.2);color:var(--v-lavender);border:1px solid rgba(168,85,247,.3);}
    .role-karyawan{background:rgba(96,165,250,.15);color:#60a5fa;border:1px solid rgba(96,165,250,.3);}
    .btn-sm{font-family:var(--font-ui);font-size:.75rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:.35rem .9rem;border-radius:6px;text-decoration:none;display:inline-block;transition:opacity .2s;}
    .btn-sm:hover{opacity:.8;}
    .btn-red{background:rgba(239,68,68,.2);color:#f87171;border:1px solid rgba(239,68,68,.3);}
    .alert-msg{border-radius:8px;padding:.75rem 1rem;font-family:var(--font-ui);font-size:.85rem;letter-spacing:1px;margin-bottom:1.5rem;}
    .alert-success{background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#34d399;}
    .alert-error{background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#f87171;}
    @media(max-width:768px){.sidebar{display:none;}.main-content{margin-left:0;}.two-col{grid-template-columns:1fr;}}
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
  <div class="sidebar-brand">
    <img src="../assets/images/logo-violet.jpeg" alt="Logo">
    <h2>VIOLET <span class="neon">PLAYSTATION</span></h2>
    <p>Admin Panel</p>
  </div>
  <div class="nav-section">Menu</div>
  <a href="index.php" class="nav-item"><span class="icon">🏠</span> Dashboard</a>
  <a href="data_sewa.php" class="nav-item"><span class="icon">📋</span> Data Sewa</a>
  <div class="nav-section">Admin Only</div>
  <a href="master_game.php" class="nav-item"><span class="icon">🎮</span> Master Game</a>
  <a href="kelola_akun.php" class="nav-item active"><span class="icon">👥</span> Kelola Akun</a>
  <div class="sidebar-bottom">
    <div class="user-chip">Login sebagai<strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong></div>
    <a href="logout.php" class="btn-violet" style="display:block;text-align:center;text-decoration:none;padding:.6rem;font-size:.8rem;letter-spacing:2px;" onclick="return confirm('Yakin ingin keluar?')"><span>Logout</span></a>
  </div>
</aside>

<main class="main-content">
  <div class="page-title">KELOLA <span class="neon">AKUN</span></div>

  <?php if($msg): ?>
  <div class="alert-msg alert-<?php echo $msg['type']; ?>"><?php echo $msg['text']; ?></div>
  <?php endif; ?>

  <div class="two-col">
    <!-- Form tambah -->
    <div class="form-card">
      <h3>➕ Tambah Akun</h3>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="aksi" value="tambah">
        <div class="form-group">
          <label class="v-label">Nama Lengkap</label>
          <input type="text" name="nama_lengkap" class="v-input" required>
        </div>
        <div class="form-group">
          <label class="v-label">Username</label>
          <input type="text" name="username" class="v-input" required>
        </div>
        <div class="form-group">
          <label class="v-label">Password (min. 6 karakter)</label>
          <input type="password" name="password" class="v-input" minlength="6" required>
        </div>
        <div class="form-group">
          <label class="v-label">Role</label>
          <select name="role" class="v-input">
            <option value="karyawan">Karyawan</option>
            <option value="admin">Admin</option>
          </select>
        </div>
        <button type="submit" class="btn-violet" style="width:100%;padding:.75rem;letter-spacing:2px;border-radius:8px;margin-top:.5rem;"><span>Simpan Akun</span></button>
      </form>
    </div>

    <!-- Daftar akun -->
    <div class="table-card">
      <div class="table-card-header"><h3>Daftar Akun</h3></div>
      <div class="table-wrap">
        <table class="v-table">
          <thead><tr><th>Nama</th><th>Username</th><th>Role</th><th>Aksi</th></tr></thead>
          <tbody>
          <?php while($a = $akuns->fetch_assoc()): ?>
          <tr>
            <td style="color:var(--v-white);"><?php echo htmlspecialchars($a['nama_lengkap'] ?: '-'); ?></td>
            <td style="font-family:var(--font-ui);"><?php echo htmlspecialchars($a['username']); ?></td>
            <td><span class="v-badge role-<?php echo $a['role']; ?>"><?php echo ucfirst($a['role']); ?></span></td>
            <td>
              <?php if($a['id_admin'] != $_SESSION['id_admin']): ?>
              <a href="kelola_akun.php?hapus=<?php echo $a['id_admin']; ?>&_token=<?php echo csrf_get_token(); ?>" class="btn-sm btn-red" onclick="return confirm('Hapus akun <?php echo htmlspecialchars($a['username']); ?>?')">Hapus</a>
              <?php else: ?>
              <span style="font-family:var(--font-ui);font-size:.75rem;color:var(--v-muted);">— Anda —</span>
              <?php endif; ?>
            </td>
          </tr>
          <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>
</body>
</html>