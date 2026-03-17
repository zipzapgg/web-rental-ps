<?php
require_once 'config/koneksi.php';
require_once 'config/promo.php';

// Ambil range libur mendatang untuk JS
$libur_ranges = get_libur_ranges($koneksi);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <title>Form Sewa — Violet PlayStation</title>
  <meta name="description" content="Ajukan sewa PS4, PS5, Nintendo Switch & Playbox di Violet PlayStation Jagakarsa.">
  <meta property="og:title" content="Form Sewa — Violet PlayStation">
  <meta property="og:type" content="website">
  <meta name="theme-color" content="#7B2FBE">
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
    .syarat-box li{font-size:.85rem;color:var(--v-muted);padding:.3rem 0 .3rem 1.25rem;position:relative;}
    .syarat-box li::before{content:'›';position:absolute;left:0;color:var(--v-violet);}
    .info-box{border-radius:10px;padding:1rem 1.25rem;margin-bottom:1.5rem;display:flex;gap:.75rem;align-items:flex-start;}
    .info-box.blue{background:rgba(96,165,250,.08);border:1px solid rgba(96,165,250,.25);}
    .info-box.blue p{font-size:.85rem;color:#93c5fd;line-height:1.6;}
    .info-box.blue strong{color:#60a5fa;display:block;font-family:var(--font-ui);letter-spacing:1px;text-transform:uppercase;font-size:.8rem;margin-bottom:.25rem;}
    .info-box.green{background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.25);}
    .info-box.green p{font-size:.85rem;color:#6ee7b7;line-height:1.6;}
    .info-box.yellow{background:rgba(251,191,36,.08);border:1px solid rgba(251,191,36,.25);}
    .info-box.yellow p{font-size:.85rem;color:#fbbf24;line-height:1.6;}

    /* Playbox checkbox */
    .playbox-toggle{display:flex;align-items:center;gap:1rem;background:rgba(16,185,129,.06);border:1px solid rgba(16,185,129,.2);border-radius:12px;padding:1rem 1.25rem;cursor:pointer;transition:all .2s;margin-bottom:1.25rem;}
    .playbox-toggle:hover{border-color:rgba(16,185,129,.4);background:rgba(16,185,129,.1);}
    .playbox-toggle.active{border-color:#34d399;background:rgba(16,185,129,.15);}
    .playbox-toggle input[type=checkbox]{accent-color:#34d399;width:18px;height:18px;cursor:pointer;flex-shrink:0;}
    .playbox-toggle-label{flex:1;}
    .playbox-toggle-label strong{font-family:var(--font-ui);font-size:.95rem;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#34d399;display:block;}
    .playbox-toggle-label span{font-size:.82rem;color:var(--v-muted);margin-top:.15rem;display:block;}

    /* Kalkulasi harga */
    .harga-preview{background:rgba(168,85,247,.08);border:1px solid rgba(168,85,247,.25);border-radius:14px;padding:1.5rem;margin-top:1.5rem;display:none;}
    .harga-preview.show{display:block;animation:fadeUp .3s ease both;}
    .harga-preview h4{font-family:var(--font-display);font-size:1rem;font-weight:700;letter-spacing:2px;text-transform:uppercase;color:var(--v-lavender);margin-bottom:1rem;}
    .harga-row{display:flex;justify-content:space-between;align-items:center;font-family:var(--font-ui);font-size:.9rem;padding:.5rem 0;border-bottom:1px solid rgba(255,255,255,.05);}
    .harga-row:last-child{border-bottom:none;}
    .harga-row .lbl{color:var(--v-muted);}
    .harga-row .val{color:var(--v-white);font-weight:600;}
    .harga-total{display:flex;justify-content:space-between;align-items:center;font-family:var(--font-display);font-size:1.4rem;font-weight:800;letter-spacing:1px;padding-top:.75rem;margin-top:.25rem;border-top:2px solid rgba(168,85,247,.3);}
    .harga-total .total-lbl{color:var(--v-lavender);}
    .harga-total .total-val{color:var(--v-lavender);text-shadow:0 0 12px rgba(168,85,247,.5);}
    .bayar-info{background:rgba(251,191,36,.06);border:1px solid rgba(251,191,36,.2);border-radius:10px;padding:.85rem 1.1rem;margin-top:1rem;display:flex;gap:.75rem;align-items:flex-start;}
    .bayar-info p{font-size:.82rem;color:#fbbf24;line-height:1.6;}

    .btn-submit{width:100%;padding:1rem;font-size:1.1rem;letter-spacing:3px;border-radius:10px;margin-top:1.5rem;}
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
    <a href="index.php" class="back-link">← Kembali</a>
  </div>
</nav>

<div class="form-header">
  <img src="assets/images/logo-violet.jpeg" alt="Logo">
  <h1>FORM <span class="neon">PENGAJUAN</span><br>SEWA</h1>
  <p>Unit diambil langsung di toko · Bawa KTP & STNK asli</p>
</div>

<div class="form-container">
  <div class="info-box blue" style="margin-bottom:1.5rem;">
    <div>
      <strong>Ambil di Toko</strong>
      <p>Unit PS harus diambil langsung ke toko kami di Jagakarsa. Setelah pengajuan disetujui, kamu akan dihubungi via WhatsApp untuk konfirmasi waktu pengambilan.</p>
    </div>
  </div>

  <div class="form-card">
    <form action="proses_sewa.php" method="POST" enctype="multipart/form-data" id="sewaForm">
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
      <input type="hidden" name="harga" id="input_harga" value="0">

      <div class="form-section-label"><span>👤</span> Data Diri</div>
      <div class="form-grid-2">
        <div class="form-group"><label class="v-label">Nama Lengkap (Sesuai KTP)</label><input type="text" name="nama" class="v-input" autocomplete="name" placeholder="John Doe" required maxlength="100" oninput="updateCounter(this,'cnt-nama',100)"><div id="cnt-nama" style="font-family:var(--font-ui);font-size:.72rem;color:var(--v-muted);text-align:right;margin-top:.2rem;">0/100</div></div>
        <div class="form-group">
          <label class="v-label">Nomor WhatsApp (Aktif)</label>
          <input type="tel" name="wa" class="v-input" autocomplete="tel" inputmode="numeric" placeholder="08xxxxxxxxxx" required>
          <div style="display:flex;gap:.5rem;align-items:flex-start;background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.18);border-radius:8px;padding:.6rem .85rem;margin-top:.45rem;">
            <span style="font-size:1rem;flex-shrink:0;">⚠️</span>
            <p style="font-size:.78rem;color:#f87171;font-family:var(--font-body);line-height:1.6;margin:0;">
              <strong style="font-family:var(--font-ui);letter-spacing:.5px;">Wajib nomor pribadi.</strong>
              Nomor kamu akan dicek di <strong>GetContact</strong> — minimal <strong>50 tag</strong> dari orang lain sebagai verifikasi identitas. Nomor baru atau nomor tidak dikenal tidak dapat memproses sewa.
            </p>
          </div>
        </div>
      </div>
      <div class="form-group"><label class="v-label">Alamat Lengkap</label><textarea name="alamat" class="v-input" autocomplete="street-address" rows="2" required style="resize:vertical;" maxlength="300" oninput="updateCounter(this,'cnt-alamat',300)"></textarea><div id="cnt-alamat" style="font-family:var(--font-ui);font-size:.72rem;color:var(--v-muted);text-align:right;margin-top:.2rem;">0/300</div></div>

      <div class="form-section-label" style="margin-top:2rem;"><span>🎮</span> Pilih Unit & Durasi</div>
      <div class="form-grid-2">
        <div class="form-group"><label class="v-label">Unit PS</label>
          <select name="id_unit" id="sel_unit" class="v-input" required onchange="hitungHarga()">
            <option value="">-- Pilih Unit --</option>
            <?php
            $stmt=$koneksi->prepare("SELECT * FROM units WHERE (tipe_layanan='Sewa Luar' OR (tipe_layanan='Main di Tempat' AND kategori='PS5')) AND status='Tersedia' ORDER BY kategori,nama_unit");
            $stmt->execute(); $units=$stmt->get_result();
            while($u=$units->fetch_assoc()){
              $label = htmlspecialchars($u['nama_unit']).' ('.$u['kategori'].')';
              if($u['tipe_layanan']==='Main di Tempat') $label .= ' — WA dulu';
              $sel = (isset($_GET['unit']) && intval($_GET['unit'])===$u['id_unit']) ? ' selected' : '';
            echo "<option value='".(int)$u['id_unit']."' data-kategori='".htmlspecialchars($u['kategori'])."'$sel>$label</option>";
            }
            $stmt->close();
            ?>
          </select>
        </div>
        <div class="form-group"><label class="v-label">Durasi Sewa</label>
          <select name="durasi" id="sel_durasi" class="v-input" required onchange="hitungHarga()">
            <option value="1">1 Hari</option>
            <option value="2">2 Hari</option>
            <option value="3">3 Hari</option>
          </select>
          <div id="durasi_promo_hint" style="font-size:.78rem;color:#fbbf24;font-family:var(--font-ui);margin-top:.35rem;display:none;"></div>
        </div>
      </div>
      <div class="form-group">
        <label class="v-label">Rencana Tanggal Ambil</label>
        <input type="date" name="tgl_ambil" id="tgl_ambil_input" class="v-input" required min="<?php echo date('Y-m-d'); ?>" style="padding:.75rem 1rem;" onchange="hitungHarga()">
        <div style="font-size:.78rem;color:var(--v-muted);font-family:var(--font-ui);margin-top:.35rem;">📅 Booking minimal H-1. Konfirmasi final via WhatsApp.</div>
        <div id="promo-info" style="display:none;margin-top:.6rem;"></div>
      </div>

      <!-- Playbox checkbox — hanya PS4 -->
      <div id="playbox_wrap" style="display:none;">
        <label class="playbox-toggle" id="playbox_label" for="chk_playbox">
          <input type="checkbox" name="pakai_playbox" id="chk_playbox" value="1" onchange="togglePlaybox(this)">
          <div class="playbox-toggle-label">
            <strong>🎒 Tambah Playbox (+Rp 30.000/hari)</strong>
            <span>Monitor + speaker built-in, plug & play. Wajib 2 orang motor saat ambil.</span>
          </div>
        </label>
        <div style="font-size:.78rem;color:var(--v-muted);font-family:var(--font-ui);margin-top:-.75rem;margin-bottom:1.25rem;padding:0 .25rem;">⚠ Playbox hanya tersedia untuk unit PS4</div>
      </div>

      <!-- Kalkulasi Harga -->
      <div class="harga-preview" id="harga_preview">
        <h4>💰 Estimasi Biaya</h4>
        <div class="harga-row"><span class="lbl">Sewa Unit</span><span class="val" id="row_unit">—</span></div>
        <div class="harga-row" id="row_playbox_wrap" style="display:none;"><span class="lbl">Playbox</span><span class="val" id="row_playbox">—</span></div>
        <div class="harga-row"><span class="lbl">Durasi</span><span class="val" id="row_durasi">—</span></div>
        <div class="harga-total"><span class="total-lbl">TOTAL</span><span class="total-val" id="row_total">—</span></div>
        <div class="bayar-info">
          <span>💳</span>
          <p><strong style="color:#fbbf24;font-family:var(--font-ui);letter-spacing:1px;text-transform:uppercase;font-size:.78rem;display:block;margin-bottom:.2rem;">Pembayaran di Lokasi</strong>
          Pembayaran dilakukan langsung saat kamu mengambil unit di toko, setelah pengajuan disetujui admin. Nominal di atas adalah estimasi — konfirmasi final via WhatsApp.</p>
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
          <li>Nomor WA wajib <strong>nomor pribadi</strong> dengan minimal <strong>50 tag GetContact</strong> — nomor baru/tidak dikenal tidak dapat menyewa</li>
          <li>KTP & STNK asli wajib dibawa saat pengambilan sebagai jaminan</li>
          <li>Unit dikembalikan tepat waktu sesuai durasi yang dipilih</li>
          <li>Kerusakan akibat kelalaian menjadi tanggung jawab penyewa</li>
          <li>Pembayaran dilakukan di lokasi saat pengambilan unit</li>
        </ul>
      </div>

      <button type="submit" name="kirim" id="btn-submit" class="btn-violet btn-submit"><span id="btn-submit-text">🎮 Ajukan Sewa Sekarang</span></button>
    </form>
  </div>
</div>

<script>
function updateCounter(el, counterId, max){
  const len = el.value.length;
  const counter = document.getElementById(counterId);
  if(!counter) return;
  counter.textContent = len + '/' + max;
  counter.style.color = len > max * 0.9 ? '#f87171' : 'var(--v-muted)';
}

const hargaPS4=100000, hargaPS5=195000, hargaNin=100000, hargaPlaybox=30000;
const hariLibur=<?php echo json_encode($libur_ranges); ?>;

function isPromoWeekday(tgl){
  if(!tgl) return false;
  const d = new Date(tgl+'T00:00:00');
  const hari = d.getDay(); // 0=Min,6=Sab
  if(hari===0||hari===5||hari===6) return false;
  return !getLiburKet(tgl);
}

function getLiburKet(tgl){
  if(!tgl || !hariLibur || !Array.isArray(hariLibur)) return null;
  for(const r of hariLibur){
    if(tgl >= r.tgl_mulai && tgl <= r.tgl_selesai) return r.keterangan;
  }
  return null;
}

function updatePromoInfo(){
  const tgl = document.querySelector('input[name="tgl_ambil"]')?.value;
  const box = document.getElementById('promo-info');
  if(!box) return;
  if(!tgl){ box.style.display='none'; return; }
  const promo = isPromoWeekday(tgl);
  const d     = new Date(tgl);
  const hari  = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][d.getDay()];
  const libur = getLiburKet(tgl);
  if(promo){
    box.innerHTML='<div style="background:rgba(251,191,36,.1);border:1px solid rgba(251,191,36,.3);border-radius:8px;padding:.6rem .9rem;font-family:var(--font-ui);font-size:.8rem;color:#fbbf24;">🎉 '+hari+' — <strong>Promo Weekday berlaku!</strong> Sewa 2 hari gratis 1, sewa 3 hari gratis 2.</div>';
  } else if(libur){
    box.innerHTML='<div style="background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.25);border-radius:8px;padding:.6rem .9rem;font-family:var(--font-ui);font-size:.8rem;color:#f87171;">🚫 '+hari+' ('+libur+') — Promo tidak berlaku pada hari libur panjang.</div>';
  } else {
    box.innerHTML='<div style="background:rgba(255,255,255,.04);border:1px solid var(--v-border);border-radius:8px;padding:.6rem .9rem;font-family:var(--font-ui);font-size:.8rem;color:var(--v-muted);">'+hari+' — Bukan weekday, promo tidak berlaku.</div>';
  }
  box.style.display='block';
  hitungHarga();
}
const hariLibur=<?php echo json_encode($libur_ranges); ?>; // array of {tgl_mulai,tgl_selesai,keterangan}

function isPromoWeekday(tglStr){
  if(!tglStr) return false;
  const d=new Date(tglStr+'T00:00:00');
  const hari=d.getDay(); // 0=Min,1=Sen,...,4=Kam,5=Jum,6=Sab
  if(hari===0||hari>=5) return false;
  // Cek apakah masuk range libur
  for(const r of hariLibur){
    if(tglStr >= r.tgl_mulai && tglStr <= r.tgl_selesai) return false;
  }
  return true;
}



function getHargaUnit(){
  const sel=document.getElementById('sel_unit');
  const opt=sel.options[sel.selectedIndex];
  if(!opt||!opt.value) return 0;
  const kat=opt.dataset.kategori||'';
  if(kat==='PS5') return hargaPS5;
  if(kat==='Nintendo') return hargaNin;
  return hargaPS4;
}

function hitungHarga(){
  const durasi  = parseInt(document.getElementById('sel_durasi').value)||1;
  const unitVal = document.getElementById('sel_unit').value;
  const tgl     = document.getElementById('tgl_ambil_input')?.value||'';
  const preview = document.getElementById('harga_preview');

  const sel = document.getElementById('sel_unit');
  const kat = (sel.options[sel.selectedIndex]?.dataset?.kategori||'');
  const pbWrap = document.getElementById('playbox_wrap');
  const chk    = document.getElementById('chk_playbox');
  if(kat==='PS4'){ pbWrap.style.display='block'; }
  else { pbWrap.style.display='none'; chk.checked=false; document.getElementById('playbox_label')?.classList.remove('active'); }

  const pakai = chk.checked;
  if(!unitVal){ preview.classList.remove('show'); return; }

  const hUnit  = getHargaUnit();
  const hPb    = pakai ? hargaPlaybox : 0;
  const hSehari= hUnit + hPb;
  const promo     = isPromoWeekday(tgl) && durasi >= 2;
  // Promo: bayar N hari → dapat 2N-1 hari. Harga = N × harga/hari
  const hariDapat = promo ? (2*durasi - 1) : durasi;
  const total     = hSehari * durasi; // selalu bayar sejumlah durasi yang dipilih

  document.getElementById('row_unit').textContent  = fmt(hUnit)+'/hari';
  document.getElementById('row_durasi').textContent = durasi+' hari dibayar';
  document.getElementById('row_playbox_wrap').style.display = pakai?'flex':'none';
  if(pakai) document.getElementById('row_playbox').textContent = fmt(hPb)+'/hari';

  // Promo row
  let promoEl = document.getElementById('row_promo_wrap');
  if(!promoEl){
    promoEl = document.createElement('div');
    promoEl.id = 'row_promo_wrap';
    promoEl.className = 'harga-row';
    promoEl.innerHTML = '<span class="lbl">🎁 Promo Weekday</span><span id="row_promo" style="color:#fbbf24;font-weight:700;"></span>';
    document.getElementById('row_total').closest('.harga-total').before(promoEl);
  }
  // Durasi dapat row
  let dapatEl = document.getElementById('row_dapat_wrap');
  if(!dapatEl){
    dapatEl = document.createElement('div');
    dapatEl.id = 'row_dapat_wrap';
    dapatEl.className = 'harga-row';
    dapatEl.innerHTML = '<span class="lbl" style="color:#34d399;">✓ Total hari didapat</span><span id="row_dapat" style="color:#34d399;font-weight:800;font-size:1rem;"></span>';
    document.getElementById('row_total').closest('.harga-total').before(dapatEl);
  }
  if(promo){
    promoEl.style.display='flex';
    document.getElementById('row_promo').textContent = 'Bayar '+durasi+' hari, dapat '+hariDapat+' hari!';
    dapatEl.style.display='flex';
    document.getElementById('row_dapat').textContent = hariDapat+' Hari';
  } else {
    promoEl.style.display='none';
    dapatEl.style.display='none';
  }

  // Info libur
  let liburEl = document.getElementById('promo_libur_info');
  const liburKet = getLiburKet(tgl);
  if(liburKet){
    if(!liburEl){
      liburEl = document.createElement('div');
      liburEl.id = 'promo_libur_info';
      liburEl.style.cssText='background:rgba(239,68,68,.08);border:1px solid rgba(239,68,68,.2);border-radius:8px;padding:.65rem 1rem;margin-top:.75rem;font-size:.8rem;color:#f87171;font-family:var(--font-ui);';
      document.getElementById('harga_preview').appendChild(liburEl);
    }
    liburEl.textContent = '⚠ Tanggal ini masuk periode libur ('+liburKet+') — promo weekday tidak berlaku.';
    liburEl.style.display='block';
  } else if(liburEl){ liburEl.style.display='none'; }

  document.getElementById('row_total').textContent = fmt(total);
  document.getElementById('input_harga').value = total;
  // Hint di bawah select durasi
  const hint = document.getElementById('durasi_promo_hint');
  if(hint){
    if(promo){ hint.style.display='block'; hint.textContent='🎁 Weekday: bayar '+durasi+' hari → dapat '+hariDapat+' hari'; }
    else { hint.style.display='none'; }
  }
  preview.classList.add('show');
}

function togglePlaybox(cb){
  cb.closest('.playbox-toggle').classList.toggle('active',cb.checked);
  hitungHarga();
}

function fmt(n){return 'Rp '+n.toLocaleString('id-ID');}

function previewFile(input,boxId,textId){
  const f=input.files[0];
  if(f){
    document.getElementById(textId).textContent=f.name;
    document.getElementById(boxId).style.borderColor='var(--v-violet)';
    document.getElementById(boxId).style.background='rgba(168,85,247,.08)';
  }
}

document.getElementById('sel_unit').addEventListener('change',hitungHarga);
document.getElementById('sel_durasi').addEventListener('change',hitungHarga);
document.querySelector('input[name="tgl_ambil"]')?.addEventListener('change', updatePromoInfo);
// Auto hitung jika unit sudah pre-selected dari URL
if(document.getElementById('sel_unit').value){ hitungHarga(); }

document.getElementById('sewaForm').addEventListener('submit', function(){
  const btn = document.getElementById('btn-submit');
  const txt = document.getElementById('btn-submit-text');
  if(btn){ btn.disabled=true; btn.style.opacity='.6'; }
  if(txt){ txt.textContent='⏳ Memproses...'; }
});
</script>
</body></html>