<?php require_once 'config/koneksi.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Form Sewa — Violet Playstation</title>
  <link rel="stylesheet" href="assets/css/violet.css">
  <style>
    body{background:var(--v-black);}
    .form-bg{position:fixed;inset:0;z-index:-1;background:radial-gradient(ellipse 50% 60% at 10% 20%,rgba(123,47,190,.15) 0%,transparent 60%),var(--v-black);}
    .form-bg-grid{position:fixed;inset:0;z-index:-1;background-image:linear-gradient(rgba(123,47,190,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(123,47,190,.04) 1px,transparent 1px);background-size:60px 60px;}
    .form-header{text-align:center;padding:4rem 1.5rem 2.5rem;}
    .form-header img{height:80px;filter:drop-shadow(0 0 16px rgba(168,85,247,.6));margin-bottom:1.5rem;animation:floatY 4s ease-in-out infinite;}
    .form-header h1{font-family:var(--font-display);font-size:clamp(2rem,6vw,3.5rem);font-weight:800;letter-spacing:4px;text-transform:uppercase;line-height:1;}
    .form-header p{color:var(--v-muted);font-size:1rem;margin-top:.75rem;letter-spacing:1px;}
    .form-card{background:rgba(18,18,31,.8);border:1px solid var(--v-border);border-radius:20px;padding:2.5rem;backdrop-filter:blur(10px);max-width:720px;margin:0 auto;width:100%;}
    .form-section-label{font-family:var(--font-display);font-size:1.1rem;font-weight:700;letter-spacing:3px;text-transform:uppercase;color:var(--v-lavender);margin-bottom:1.25rem;padding-bottom:.75rem;border-bottom:1px solid var(--v-border);display:flex;align-items:center;gap:.75rem;}
    .form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;}
    .form-group{margin-bottom:1.25rem;}
    .file-upload-box{position:relative;border:2px dashed var(--v-border);border-radius:10px;background:rgba(255,255,255,.02);padding:1.5rem;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;}
    .file-upload-box:hover{border-color:var(--v-violet);background:rgba(168,85,247,.05);}
    .file-upload-box input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%;}
    .file-upload-box .upload-icon{font-size:2rem;margin-bottom:.5rem;}
    .file-upload-box .upload-text{font-family:var(--font-ui);font-size:1rem;color:var(--v-muted);letter-spacing:1px;text-transform:uppercase;}
    .file-upload-box .upload-hint{font-size:.75rem;color:#4B3F6B;margin-top:.25rem;}
    .syarat-box{background:rgba(123,47,190,.06);border:1px dashed rgba(168,85,247,.3);border-radius:10px;padding:1.25rem 1.5rem;margin-top:1.5rem;}
    .syarat-box .syarat-title{font-family:var(--font-ui);font-size:.8rem;letter-spacing:2px;text-transform:uppercase;color:var(--v-violet);margin-bottom:.75rem;}
    .syarat-box ul{list-style:none;padding:0;}
    .syarat-box li{font-size:.85rem;color:var(--v-muted);padding:.3rem 0;padding-left:1.25rem;position:relative;}
    .syarat-box li::before{content:'›';position:absolute;left:0;color:var(--v-violet);}
    .info-pickup{background:rgba(96,165,250,.08);border:1px solid rgba(96,165,250,.25);border-radius:10px;padding:1rem 1.25rem;margin-bottom:2rem;display:flex;gap:.75rem;align-items:flex-start;}
    .info-pickup .icon{font-size:1.4rem;flex-shrink:0;}
    .info-pickup p{font-size:.85rem;color:#93c5fd;line-height:1.6;}
    .info-pickup strong{color:#60a5fa;display:block;font-family:var(--font-ui);letter-spacing:1px;text-transform:uppercase;font-size:.8rem;margin-bottom:.25rem;}
    .btn-submit{width:100%;padding:1rem;font-size:1.1rem;letter-spacing:3px;border-radius:10px;margin-top:2rem;}
    .back-link{display:inline-flex;align-items:center;gap:.5rem;font-family:var(--font-ui);font-size:.85rem;letter-spacing:1.5px;text-transform:uppercase;color:var(--v-muted);text-decoration:none;transition:color .2s;}
    .back-link:hover{color:var(--v-lavender);}
    .container{max-width:1200px;margin:0 auto;padding:0 1.5rem;}
    .form-container{max-width:760px;margin:0 auto;padding:0 1.5rem 5rem;}
    @media(max-width:540px){.form-grid-2{grid-template-columns:1fr;}.form-card{padding:1.5rem;}}
  </style>
</head>
<body>
<div class="form-bg"></div><div class="form-bg-grid"></div>
<nav class="v-navbar">
  <div class="container" style="display:flex;justify-content:space-between;align-items:center;">
    <a href="index.php" class="brand"><img src="assets/images/logo-violet.jpeg" alt="Violet PlayStation">VIOLET <span class="neon" style="margin-left:.3rem;">PLAYSTATION</span></a>
    <div style="display:flex;align-items:center;gap:1rem;"><a href="https://www.instagram.com/violetplaystation/" target="_blank" class="ig-btn" title="Instagram"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/></svg></a><a href="index.php" class="back-link" style="margin:0;">← Kembali</a></div>
  </div>
</nav>
<div class="form-header">
  <img src="assets/images/logo-violet.jpeg" alt="Logo">
  <h1>FORM <span class="neon">PENGAJUAN</span><br>SEWA</h1>
  <p>Unit diambil langsung di toko · Bawa KTP & STNK asli</p>
</div>
<div class="form-container">
  <div class="info-pickup">
    <div class="icon">🏪</div>
    <div><strong>Ambil di Toko</strong>
    <p>Unit PS harus diambil langsung ke toko kami di Jagakarsa. Setelah pengajuan disetujui, kamu akan dihubungi via WhatsApp untuk konfirmasi waktu pengambilan.</p></div>
  </div>
  <div class="form-card">
    <form action="proses_sewa.php" method="POST" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
      <div class="form-section-label"><span>👤</span> Data Diri</div>
      <div class="form-grid-2">
        <div class="form-group"><label class="v-label">Nama Lengkap (Sesuai KTP)</label><input type="text" name="nama" class="v-input" placeholder="John Doe" required></div>
        <div class="form-group"><label class="v-label">Nomor WhatsApp</label><input type="tel" name="wa" class="v-input" placeholder="08xxxxxxxxxx" required></div>
      </div>
      <div class="form-group"><label class="v-label">Alamat Lengkap</label><textarea name="alamat" class="v-input" rows="2" required style="resize:vertical;"></textarea></div>
      <div class="form-section-label" style="margin-top:2rem;"><span>🎮</span> Pilih Unit & Durasi</div>
      <div class="form-grid-2">
        <div class="form-group"><label class="v-label">Unit PS</label>
          <select name="id_unit" class="v-input" required><option value="">-- Pilih Unit --</option>
          <?php
          $stmt=$koneksi->prepare("SELECT * FROM units WHERE tipe_layanan='Sewa Luar' AND status='Tersedia'");
          $stmt->execute(); $units=$stmt->get_result();
          while($u=$units->fetch_assoc()) echo "<option value='".(int)$u['id_unit']."'>".htmlspecialchars($u['nama_unit'])." (".$u['kategori'].")</option>";
          $stmt->close();
          ?></select>
        </div>
        <div class="form-group"><label class="v-label">Durasi Sewa</label>
          <select name="durasi" class="v-input" required><option value="1 Hari">1 Hari</option><option value="2 Hari">2 Hari</option><option value="3 Hari">3 Hari</option></select>
        </div>
      </div>
      <div class="form-section-label" style="margin-top:2rem;"><span>📄</span> Upload Dokumen</div>
      <div class="form-grid-2">
        <div class="form-group"><label class="v-label">Foto KTP Asli</label>
          <div class="file-upload-box" id="ktp-box"><input type="file" name="ktp" accept="image/*" required onchange="previewFile(this,'ktp-box','ktp-text')"><div class="upload-icon">🪪</div><div class="upload-text" id="ktp-text">Klik untuk upload</div><div class="upload-hint">JPG, PNG · Max 5MB</div></div>
        </div>
        <div class="form-group"><label class="v-label">Foto STNK Asli</label>
          <div class="file-upload-box" id="stnk-box"><input type="file" name="stnk" accept="image/*" required onchange="previewFile(this,'stnk-box','stnk-text')"><div class="upload-icon">🚗</div><div class="upload-text" id="stnk-text">Klik untuk upload</div><div class="upload-hint">JPG, PNG · Max 5MB</div></div>
        </div>
      </div>
      <div class="syarat-box">
        <div class="syarat-title">⚠ Syarat & Ketentuan</div>
        <ul>
          <li>Unit diambil langsung ke toko kami di Jagakarsa</li>
          <li>KTP & STNK asli wajib dibawa saat pengambilan sebagai jaminan</li>
          <li>Unit dikembalikan tepat waktu sesuai durasi yang dipilih</li>
          <li>Kerusakan akibat kelalaian menjadi tanggung jawab penyewa</li>
          <li>Data dokumen kamu dijaga keamanannya dan tidak disebarluaskan</li>
        </ul>
      </div>
      <button type="submit" name="kirim" class="btn-violet btn-submit"><span>🎮 Ajukan Sewa Sekarang</span></button>
    </form>
  </div>
</div>
<script>
function previewFile(input,boxId,textId){const f=input.files[0];if(f){document.getElementById(textId).textContent=f.name;document.getElementById(boxId).style.borderColor='var(--v-violet)';document.getElementById(boxId).style.background='rgba(168,85,247,.08)';}}
</script>
</body></html>