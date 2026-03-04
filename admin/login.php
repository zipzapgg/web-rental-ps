<?php require_once '../config/koneksi.php'; ?>
<?php require_once '../config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Admin — Violet PS</title>
  <link rel="stylesheet" href="../assets/css/violet.css">
  <style>
    html, body {
      height: 100%;
      display: flex; align-items: center; justify-content: center;
      background: var(--v-black);
    }
    .login-bg {
      position: fixed; inset: 0; z-index: 0;
      background:
        radial-gradient(ellipse 60% 60% at 50% 50%, rgba(123,47,190,.18) 0%, transparent 70%),
        var(--v-black);
    }
    .login-grid {
      position: fixed; inset: 0; z-index: 0;
      background-image:
        linear-gradient(rgba(123,47,190,.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(123,47,190,.05) 1px, transparent 1px);
      background-size: 50px 50px;
    }
    .login-wrap {
      position: relative; z-index: 1;
      width: 100%; max-width: 420px;
      padding: 1.5rem;
      animation: fadeUp .6s ease both;
    }
    .login-card {
      background: rgba(18,18,31,.9);
      border: 1px solid var(--v-border);
      border-radius: 20px;
      padding: 3rem 2.5rem;
      backdrop-filter: blur(12px);
      box-shadow: 0 0 60px rgba(123,47,190,.2);
    }
    .login-logo {
      text-align: center;
      margin-bottom: 2.5rem;
    }
    .login-logo img {
      height: 80px;
      filter: drop-shadow(0 0 16px rgba(168,85,247,.6));
      animation: floatY 4s ease-in-out infinite;
    }
    .login-logo h2 {
      font-family: var(--font-display);
      font-size: 1.8rem; font-weight: 800;
      letter-spacing: 4px; text-transform: uppercase;
      margin-top: 1rem;
    }
    .login-logo p {
      font-family: var(--font-ui);
      font-size: .8rem; letter-spacing: 2px;
      text-transform: uppercase; color: var(--v-muted);
      margin-top: .25rem;
    }
    .form-group { margin-bottom: 1.25rem; }
    .btn-login {
      width: 100%;
      padding: .9rem;
      font-size: 1rem;
      letter-spacing: 3px;
      border-radius: 10px;
      margin-top: .5rem;
    }

    /* Error message */
    .login-error {
      background: rgba(239,68,68,.1);
      border: 1px solid rgba(239,68,68,.3);
      border-radius: 8px;
      padding: .75rem 1rem;
      font-family: var(--font-ui);
      font-size: .85rem; letter-spacing: 1px;
      color: #f87171; text-align: center;
      margin-bottom: 1.25rem;
      display: none;
    }

    .back-to-site {
      text-align: center;
      margin-top: 1.5rem;
    }
    .back-to-site a {
      font-family: var(--font-ui);
      font-size: .8rem; letter-spacing: 1.5px;
      text-transform: uppercase; color: var(--v-muted);
      text-decoration: none; transition: color .2s;
    }
    .back-to-site a:hover { color: var(--v-lavender); }
  </style>
</head>
<body>
  <div class="login-bg"></div>
  <div class="login-grid"></div>

  <div class="login-wrap">
    <?php if(isset($_GET['pesan'])):
      $msg = $_GET['pesan'];
      $txt = $msg === 'belum_login' ? '⚠ Silakan login terlebih dahulu.' : ($msg === 'logout' ? '✓ Berhasil logout.' : '');
      if($txt): ?>
      <div style="background:rgba(168,85,247,.1); border:1px solid rgba(168,85,247,.3); border-radius:8px; padding:.75rem 1rem; font-family:var(--font-ui); font-size:.85rem; letter-spacing:1px; color:var(--v-lavender); text-align:center; margin-bottom:1rem;">
        <?php echo $txt; ?>
      </div>
      <?php endif; endif; ?>

    <div class="login-card">
      <div class="login-logo">
        <img src="../assets/images/logo-violet.jpeg" alt="Violet PS">
        <h2>VIOLET <span class="neon">PS</span></h2>
        <p>Admin Panel</p>
      </div>

      <form action="cek_login.php" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
        <div class="form-group">
          <label class="v-label">Username</label>
          <input type="text" name="user" class="v-input" placeholder="admin" autocomplete="username" required>
        </div>
        <div class="form-group">
          <label class="v-label">Password</label>
          <input type="password" name="pass" class="v-input" placeholder="••••••••" autocomplete="current-password" required>
        </div>
        <button type="submit" class="btn-violet btn-login"><span>🔐 Login</span></button>
      </form>
    </div>

    <div class="back-to-site">
      <a href="../index.php">← Kembali ke Halaman Utama</a>
    </div>
  </div>
</body>
</html>