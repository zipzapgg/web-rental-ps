<aside class="sidebar">
  <div class="sidebar-brand">
    <img src="../assets/images/logo-violet.jpeg" alt="Logo">
    <h2>VIOLET <span class="neon">PLAYSTATION</span></h2>
    <p>Admin Panel</p>
  </div>
  <div class="nav-section">Menu</div>
  <a href="index.php" class="nav-item<?php echo ($active_page??'')=='dashboard'?' active':''; ?>"><svg width="16" height="16"><use href="#ico-home"/></svg> Dashboard</a>
  <a href="data_sewa.php" class="nav-item<?php echo ($active_page??'')=='sewa'?' active':''; ?>" style="justify-content:space-between;">
    <span style="display:inline-flex;align-items:center;gap:.5rem;"><svg width="16" height="16"><use href="#ico-clipboard"/></svg> Data Sewa</span>
    <?php
    if(!isset($total_pending)){
        $r = $koneksi->query("SELECT COUNT(*) as c FROM pengajuan WHERE status_pengajuan='Pending'");
        $total_pending = $r ? $r->fetch_assoc()['c'] : 0;
    }
    if($total_pending > 0): ?><span class="nav-badge"><?php echo $total_pending; ?></span><?php endif; ?>
  </a>
  <a href="laporan.php" class="nav-item<?php echo ($active_page??'')=='laporan'?' active':''; ?>"><svg width="16" height="16"><use href="#ico-chart"/></svg> Laporan</a>
  <?php if(is_admin()): ?>
  <div class="nav-section">Admin Only</div>
  <a href="master_game.php" class="nav-item<?php echo ($active_page??'')=='game'?' active':''; ?>"><svg width="16" height="16"><use href="#ico-gamepad"/></svg> Master Game</a>
  <a href="hari_libur.php" class="nav-item<?php echo ($active_page??'')=='libur'?' active':''; ?>"><svg width="16" height="16"><use href="#ico-calendar"/></svg> Hari Libur</a>
  <a href="kelola_akun.php" class="nav-item<?php echo ($active_page??'')=='akun'?' active':''; ?>"><svg width="16" height="16"><use href="#ico-users"/></svg> Kelola Akun</a>
  <?php endif; ?>
  <div class="sidebar-bottom">
    <div class="user-chip">Login sebagai
      <strong><?php echo htmlspecialchars($_SESSION["nama"]??$_SESSION["user"]); ?></strong>
      <span class="role-badge role-<?php echo $_SESSION["role"]; ?>"><?php echo ucfirst($_SESSION["role"]); ?></span>
    </div>
    <a href="logout.php" class="btn-violet" style="display:flex;align-items:center;justify-content:center;gap:.5rem;text-decoration:none;padding:.6rem;font-size:.8rem;letter-spacing:2px;" onclick="return confirm('Yakin ingin keluar?')" >
      <svg width="14" height="14"><use href="#ico-logout"/></svg>
      <span>Logout</span>
    </a>
  </div>
</aside>