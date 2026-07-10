<?php
require_once '../config/koneksi.php';
require_admin('login.php');

$msg = '';

if(isset($_POST['aksi']) && $_POST['aksi']==='ganti_password'){
    csrf_check();
    $pw_lama  = $_POST['pw_lama']  ?? '';
    $pw_baru  = trim($_POST['pw_baru']  ?? '');
    $pw_ulang = trim($_POST['pw_ulang'] ?? '');

    $s = $koneksi->prepare("SELECT password FROM admin WHERE id_admin=?");
    $s->bind_param("i",$_SESSION['id_admin']); $s->execute();
    $row = $s->get_result()->fetch_assoc(); $s->close();

    if(!password_verify($pw_lama,$row['password'])){
        $msg = ['type'=>'error','text'=>'Password lama salah.'];
    } elseif($pw_baru !== $pw_ulang){
        $msg = ['type'=>'error','text'=>'Konfirmasi password tidak cocok.'];
    } elseif(strlen($pw_baru)<8 || !preg_match('/[A-Za-z]/',$pw_baru) || !preg_match('/[0-9]/',$pw_baru)){
        $msg = ['type'=>'error','text'=>'Password baru minimal 8 karakter, harus ada huruf dan angka.'];
    } else {
        $hash = password_hash($pw_baru, PASSWORD_BCRYPT);
        $s = $koneksi->prepare("UPDATE admin SET password=? WHERE id_admin=?");
        $s->bind_param("si",$hash,$_SESSION['id_admin']); $s->execute(); $s->close();
        $msg = ['type'=>'success','text'=>'Password berhasil diubah.'];
    }
}

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
  <title>Kelola Akun Violet PlayStation</title>
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
<?php $active_page = 'akun'; include __DIR__.'/sidebar.php'; ?>

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
          <label class="v-label">Password (min. 8 karakter, harus ada huruf & angka)</label>
          <div class="input-wrap">
          <input type="password" name="password" id="inp-pw-baru" class="v-input" minlength="8" required>
          <button type="button" class="btn-eye" onclick="togglePassword('inp-pw-baru',this)" tabindex="-1">
            <svg width="16" height="16"><use href="../assets/icons.svg#ico-eye"/></svg>
          </button>
        </div>
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

    <!-- Ganti password sendiri -->
    <div class="form-card" style="margin-top:1.5rem;">
      <h3>🔑 Ganti Password Saya</h3>
      <form method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="aksi" value="ganti_password">
        <div class="form-group"><label class="v-label">Password Lama</label><div class="input-wrap"><input type="password" name="pw_lama" id="inp-pw-lama" class="v-input" required><button type="button" class="btn-eye" onclick="togglePassword('inp-pw-lama',this)" tabindex="-1"><svg width="16" height="16"><use href="../assets/icons.svg#ico-eye"/></svg></button></div></div>
        <div class="form-group"><label class="v-label">Password Baru (min. 8 karakter)</label><div class="input-wrap"><input type="password" name="pw_baru" id="inp-pw-baru2" class="v-input" required><button type="button" class="btn-eye" onclick="togglePassword('inp-pw-baru2',this)" tabindex="-1"><svg width="16" height="16"><use href="../assets/icons.svg#ico-eye"/></svg></button></div></div>
        <div class="form-group"><label class="v-label">Konfirmasi Password Baru</label><div class="input-wrap"><input type="password" name="pw_ulang" id="inp-pw-ulang" class="v-input" required><button type="button" class="btn-eye" onclick="togglePassword('inp-pw-ulang',this)" tabindex="-1"><svg width="16" height="16"><use href="../assets/icons.svg#ico-eye"/></svg></button></div></div>
        <button type="submit" class="btn-violet" style="width:100%;padding:.75rem;letter-spacing:2px;border-radius:8px;"><span>Simpan Password</span></button>
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
              <span style="font-family:var(--font-ui);font-size:.75rem;color:var(--v-muted);">— Anda </span>
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