<?php require_once '../config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin Violet PlayStation</title>
 <link rel="stylesheet" href="../assets/css/violet.css?v=<?php echo time(); ?>">

  <script src="../assets/app.js" defer></script>
</head>
<body class="login-body">
  <div class="login-bg"></div>
  <div class="login-grid"></div>

  <div class="login-wrap">
    <?php if(isset($_GET['pesan'])):
      $msg = $_GET['pesan'];
      $txt = $msg === 'belum_login' ? '⚠ Silakan login terlebih dahulu.' : ($msg === 'logout' ? '✓ Berhasil logout.' : ($msg === 'timeout' ? '⏱ Sesi berakhir karena tidak aktif. Silakan login kembali.' : ''));
      if($txt): ?>
      <div class="login-error" style="display:block; background:rgba(182,255,0,.08); border:1px solid rgba(182,255,0,.25); border-radius:8px; padding:.75rem 1rem; font-family:var(--font-ui); font-size:.85rem; letter-spacing:1px; color:var(--v-white); text-align:center; margin-bottom:1rem; box-shadow:0 0 10px rgba(182,255,0,0.1);">
        <?php echo $txt; ?>
      </div>
      <?php endif; endif; ?>

    <div class="login-card">
      <div class="login-logo">
        <img src="../assets/images/logo-violet.jpeg" alt="Violet PlayStation">
        <h2>VIOLET <span class="neon">PLAYSTATION</span></h2>
        <p>Admin Panel</p>
      </div>

      <form action="cek_login.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <div class="form-group">
          <label class="v-label">Username</label>
          <input type="text" name="user" class="v-input" placeholder="admin" autocomplete="username" required>
        </div>
        <div class="form-group">
          <label class="v-label">Password</label>
          <div class="input-wrap">
          <input type="password" name="pass" id="inp-pass" class="v-input" placeholder="••••••••" autocomplete="current-password" required>
          <button type="button" class="btn-eye" onclick="togglePassword('inp-pass',this)" tabindex="-1" aria-label="Tampilkan password">
            <svg width="18" height="18" style="color:var(--v-muted)"><use href="../assets/icons.svg#ico-eye"/></svg>
          </button>
        </div>
        </div>
        <button type="submit" class="btn-violet btn-login">
          <span style="display:inline-flex;align-items:center;gap:.5rem;">
            <svg width="16" height="16"><use href="../assets/icons.svg#ico-lock"/></svg>
            Login
          </span>
        </button>
      </form>
    </div>

    <div class="back-to-site">
      <a href="../index.php">← Kembali ke Halaman Utama</a>
    </div>
  </div>
</body>
</html>