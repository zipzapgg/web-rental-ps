<?php
require_once '../config/koneksi.php';
require_login('login.php');
$is_admin = is_admin();
?>
<!DOCTYPE html><html lang="id">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Data Sewa — Violet PS</title>
<link rel="stylesheet" href="../assets/css/violet.css">
<style>
body{display:flex;min-height:100vh;}
.sidebar{width:240px;flex-shrink:0;background:var(--v-dark);border-right:1px solid var(--v-border);display:flex;flex-direction:column;padding:1.5rem 0;position:fixed;top:0;left:0;bottom:0;z-index:50;}
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
.role-badge{display:inline-block;font-family:var(--font-ui);font-size:.7rem;letter-spacing:1.5px;text-transform:uppercase;padding:.15rem .5rem;border-radius:4px;margin-top:.25rem;}
.role-admin{background:rgba(168,85,247,.2);color:var(--v-lavender);border:1px solid rgba(168,85,247,.3);}
.role-karyawan{background:rgba(96,165,250,.15);color:#60a5fa;border:1px solid rgba(96,165,250,.3);}
.main-content{margin-left:240px;flex:1;padding:2.5rem;background:var(--v-black);}
.page-title{font-family:var(--font-display);font-size:2rem;font-weight:800;letter-spacing:3px;text-transform:uppercase;margin-bottom:2rem;}
.table-card{background:var(--v-card);border:1px solid var(--v-border);border-radius:16px;overflow:hidden;}
.table-card-header{padding:1.25rem 1.5rem;border-bottom:1px solid var(--v-border);}
.table-card-header h3{font-family:var(--font-display);font-size:1.1rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-lavender);}
.table-wrap{overflow-x:auto;}
.s-pending{background:rgba(251,191,36,.15);color:#fbbf24;border:1px solid rgba(251,191,36,.3);}
.s-selesai{background:rgba(16,185,129,.15);color:#34d399;border:1px solid rgba(16,185,129,.3);}
.s-ditolak{background:rgba(239,68,68,.15);color:#f87171;border:1px solid rgba(239,68,68,.3);}
.btn-sm{font-family:var(--font-ui);font-size:.75rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;padding:.35rem .9rem;border-radius:6px;text-decoration:none;display:inline-block;transition:opacity .2s;margin-right:.3rem;}
.btn-sm:hover{opacity:.8;}
.btn-blue{background:rgba(96,165,250,.2);color:#60a5fa;border:1px solid rgba(96,165,250,.3);}
.btn-green{background:rgba(16,185,129,.2);color:#34d399;border:1px solid rgba(16,185,129,.3);}
.btn-purple{background:rgba(168,85,247,.2);color:var(--v-lavender);border:1px solid rgba(168,85,247,.3);}
@media(max-width:768px){.sidebar{display:none;}.main-content{margin-left:0;}}
</style></head><body>
<aside class="sidebar">
  <div class="sidebar-brand"><img src="../assets/images/logo-violet.jpeg" alt="Logo"><h2>VIOLET <span class="neon">PS</span></h2><p>Admin Panel</p></div>
  <div class="nav-section">Menu</div>
  <a href="index.php" class="nav-item"><span class="icon">🏠</span> Dashboard</a>
  <a href="data_sewa.php" class="nav-item active"><span class="icon">📋</span> Data Sewa</a>
  <?php if($is_admin): ?>
  <div class="nav-section">Admin Only</div>
  <a href="master_game.php" class="nav-item"><span class="icon">🎮</span> Master Game</a>
  <a href="kelola_akun.php" class="nav-item"><span class="icon">👥</span> Kelola Akun</a>
  <?php endif; ?>
  <div class="sidebar-bottom">
    <div class="user-chip">Login sebagai<strong><?php echo htmlspecialchars($_SESSION['nama'] ?: $_SESSION['user']); ?></strong>
    <span class="role-badge role-<?php echo $_SESSION['role']; ?>"><?php echo ucfirst($_SESSION['role']); ?></span></div>
    <a href="logout.php" class="btn-violet" style="display:block;text-align:center;text-decoration:none;padding:.6rem;font-size:.8rem;letter-spacing:2px;" onclick="return confirm('Yakin ingin keluar?')"><span>Logout</span></a>
  </div>
</aside>
<main class="main-content">
  <div class="page-title">DATA <span class="neon">SEWA</span></div>
  <div class="table-card">
    <div class="table-card-header"><h3>Pengajuan Sewa</h3></div>
    <div class="table-wrap">
      <table class="v-table">
        <thead><tr><th>Tanggal</th><th>Nama</th><th>No. WA</th><th>Unit</th><th>Durasi</th><th>Dokumen</th><th>Status</th><th>Aksi</th></tr></thead>
        <tbody>
        <?php
        $sql = "SELECT pengajuan.*, units.nama_unit FROM pengajuan JOIN units ON pengajuan.id_unit = units.id_unit ORDER BY tgl_pengajuan DESC";
        $q = $koneksi->query($sql);
        while($d = $q->fetch_assoc()):
          $st=$d['status_pengajuan'];
          $sc=$st==='Pending'?'s-pending':($st==='Selesai'?'s-selesai':'s-ditolak');
        ?>
        <tr>
          <td style="font-size:.82rem;color:var(--v-muted);"><?php echo date('d/m/Y H:i', strtotime($d['tgl_pengajuan'])); ?></td>
          <td><strong style="color:var(--v-white);"><?php echo htmlspecialchars($d['nama_penyewa']); ?></strong><br><span style="font-size:.8rem;color:var(--v-muted);"><?php echo htmlspecialchars($d['alamat']); ?></span></td>
          <td style="font-size:.85rem;"><?php echo htmlspecialchars($d['no_wa']); ?></td>
          <td style="font-size:.85rem;color:var(--v-muted);"><?php echo htmlspecialchars($d['nama_unit']); ?></td>
          <td><span style="font-family:var(--font-ui);font-size:.85rem;"><?php echo htmlspecialchars($d['durasi']??'-'); ?></span></td>
          <td>
            <a href="lihat_berkas.php?file=<?php echo urlencode($d['foto_ktp']); ?>" class="btn-sm btn-purple" target="_blank">KTP</a>
            <a href="lihat_berkas.php?file=<?php echo urlencode($d['foto_stnk']); ?>" class="btn-sm btn-purple" target="_blank">STNK</a>
          </td>
          <td><span class="v-badge <?php echo $sc; ?>"><?php echo $st; ?></span></td>
          <td>
            <?php if($st==='Pending'): ?>
            <a href="proses_konfirmasi.php?id=<?php echo $d['id_pengajuan']; ?>&unit=<?php echo $d['id_unit']; ?>" class="btn-sm btn-green" onclick="return confirm('Selesaikan transaksi ini?')">Selesaikan</a>
            <?php else: ?>
            <span style="font-family:var(--font-ui);font-size:.75rem;color:var(--v-muted);">—</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>
</main>
</body></html>