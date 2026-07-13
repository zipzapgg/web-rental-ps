<?php
require_once 'config/koneksi.php';
require_once 'config/promo.php';

$form_error = '';
if (!empty($_SESSION['form_error'])) {
    $form_error = $_SESSION['form_error'];
    unset($_SESSION['form_error']);
}

$libur_ranges = get_libur_ranges($koneksi);
$pb_aktif = [];
$res_pb = $koneksi->query("SELECT tgl_ambil, durasi FROM pengajuan WHERE pakai_playbox=1 AND status_pengajuan IN ('Pending', 'Disetujui')");
while ($r = $res_pb->fetch_assoc()) {
    preg_match('/(\d+)/', $r['durasi'], $m);
    $hari = intval($m[1] ?? 1);
    $pb_aktif[] = [
        'start' => $r['tgl_ambil'],
        'end'   => date('Y-m-d', strtotime($r['tgl_ambil'] . " + $hari days"))
    ];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <title>Form Sewa Violet PlayStation</title>
  <meta name="description" content="Ajukan sewa PS4, PS5, Nintendo Switch &amp; Playbox di Violet PlayStation Jagakarsa.">
  <meta name="theme-color" content="#8B5CF6">
  <link rel="stylesheet" href="assets/css/violet.css">
  <script src="assets/app.js" defer></script>
</head>
<body>
<?php include_once "config/svg_sprite.php"; ?>
<div class="form-bg"></div><div class="form-bg-grid"></div>
<nav class="v-navbar">
  <div class="container nav-container">
    <a href="index.php" class="brand"><img src="assets/images/logo-violet.jpeg" alt="Violet PlayStation"></a>
    <a href="index.php" class="back-link">← Kembali</a>
  </div>
</nav>

<div class="form-header">
  <img src="assets/images/logo-violet.jpeg" alt="Logo">
  <h1>FORM <span class="neon">PENGAJUAN</span><br>SEWA</h1>
  <p>Unit diambil langsung di toko · Bawa KTP &amp; STNK asli</p>
</div>

<div class="form-container">

  <?php if ($form_error): ?>
  <div class="flash-error" role="alert">
    <span style="font-size:1.2rem;flex-shrink:0;">⚠️</span>
    <p><strong style="color:#f87171;display:block;margin-bottom:.2rem;">Pengajuan gagal</strong><?php echo htmlspecialchars($form_error); ?></p>
  </div>
  <?php endif; ?>

  <div class="info-box">
    <strong>Ambil di Toko</strong>
    <p>Unit PS harus diambil langsung ke toko kami di Jagakarsa. Setelah pengajuan disetujui, kamu akan dihubungi via WhatsApp.</p>
  </div>

  <div class="form-card">
    <!-- Step progress indicator -->
    <div class="step-indicator">
      <div class="step-dot active" id="dot-1"><span>1</span><label>Data Diri</label></div>
      <div class="step-line" id="line-1"></div>
      <div class="step-dot" id="dot-2"><span>2</span><label>Unit &amp; Waktu</label></div>
      <div class="step-line" id="line-2"></div>
      <div class="step-dot" id="dot-3"><span>3</span><label>Upload</label></div>
    </div>

    <form action="proses_sewa.php" method="POST" enctype="multipart/form-data" id="sewaForm" novalidate>
      <input type="hidden" name="kirim" value="1">
      <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">

      <!-- STEP 1: DATA DIRI -->
      <div class="form-step-wrapper active" id="step-wrapper-1">
        <div class="form-section-label">
          <svg width="18" height="18" aria-hidden="true"><use href="#ico-user"/></svg> Data Diri
        </div>
        <div class="form-grid-2">
          <div class="form-group">
            <label class="v-label" for="inp-nama">Nama Lengkap (Sesuai KTP)</label>
            <input type="text" name="nama" id="inp-nama" class="v-input" autocomplete="name" placeholder="John Doe" required maxlength="100" oninput="updateCounter(this,'cnt-nama',100)">
            <div id="cnt-nama" class="input-counter" aria-live="polite">0/100</div>
          </div>
          <div class="form-group">
            <label class="v-label" for="inp-wa">Nomor WhatsApp (Aktif)</label>
            <input type="tel" name="wa" id="inp-wa" class="v-input" autocomplete="tel" inputmode="numeric" placeholder="08xxxxxxxxxx" required>
            <div class="warn-box">
              <span aria-hidden="true">⚠️</span>
              <p><strong>Wajib nomor pribadi.</strong> Nomor kamu akan dicek di <strong>GetContact</strong> minimal <strong>50 tag</strong> dari orang lain.</p>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label class="v-label" for="inp-alamat">Alamat Lengkap</label>
          <textarea name="alamat" id="inp-alamat" class="v-input" autocomplete="street-address" rows="2" required style="resize:vertical;" maxlength="300" oninput="updateCounter(this,'cnt-alamat',300)"></textarea>
          <div id="cnt-alamat" class="input-counter" aria-live="polite">0/300</div>
        </div>
        <div class="step-actions">
          <div style="flex:1;"></div>
          <button type="button" class="btn-violet" onclick="changeStep(2)">
            <span>Lanjut →</span>
          </button>
        </div>
      </div>

      <!-- STEP 2: UNIT & WAKTU -->
      <div class="form-step-wrapper" id="step-wrapper-2" style="display:none;">
        <div class="form-section-label">
          <svg width="18" height="18" aria-hidden="true"><use href="#ico-gamepad"/></svg> Pilih Unit &amp; Durasi
        </div>
        <div class="form-grid-2">
          <div class="form-group">
            <label class="v-label" for="sel_unit">Unit PS</label>
            <select name="id_unit" id="sel_unit" class="v-input" required onchange="hitungHarga()">
              <option value="">-- Pilih Unit --</option>
              <?php
              $stmt = $koneksi->prepare(
                  "SELECT * FROM units
                   WHERE (tipe_layanan='Sewa Luar' OR (tipe_layanan='Main di Tempat' AND kategori='PS5'))
                   AND status='Tersedia' ORDER BY kategori, nama_unit"
              );
              $stmt->execute();
              $units = $stmt->get_result();
              while ($u = $units->fetch_assoc()) {
                  $label = htmlspecialchars($u['nama_unit']) . ' (' . $u['kategori'] . ')';
                  if ($u['tipe_layanan'] === 'Main di Tempat') $label .= ' WA dulu';
                  $sel = (isset($_GET['unit']) && intval($_GET['unit']) === $u['id_unit']) ? ' selected' : '';
                  echo "<option value='" . (int)$u['id_unit'] . "' data-kategori='" . htmlspecialchars($u['kategori']) . "'$sel>$label</option>";
              }
              $stmt->close();
              ?>
            </select>
          </div>
          <div class="form-group">
            <label class="v-label" for="sel_durasi">Durasi Sewa</label>
            <select name="durasi" id="sel_durasi" class="v-input" required onchange="hitungHarga()">
              <?php for ($d = 1; $d <= MAX_DURASI_HARI; $d++): ?>
              <option value="<?php echo $d; ?>"><?php echo $d; ?> Hari</option>
              <?php endfor; ?>
            </select>
          </div>
        </div>

        <!-- Tanggal Ambil -->
        <div class="form-group">
          <label class="v-label" for="tgl_ambil_input">Rencana Tanggal Ambil</label>
          <input type="date" name="tgl_ambil" id="tgl_ambil_input" class="v-input" required
                 min="<?php echo date('Y-m-d'); ?>"
                 style="padding:.75rem 1rem;"
                 onchange="hitungHarga()">
          <div class="hint-text">📅 Booking minimal H-1. Konfirmasi final via WhatsApp.</div>
          <div id="promo-status" class="promo-status"></div>
        </div>

        <!-- Playbox checkbox hanya PS4 -->
        <div id="playbox_wrap" style="display:none; grid-column: 1 / -1;">
          <label class="playbox-toggle" id="playbox_label" for="chk_playbox" style="margin-bottom: 0.25rem;">
            <input type="checkbox" name="pakai_playbox" id="chk_playbox" value="1" onchange="togglePlaybox(this)">
            <div class="playbox-toggle-label">
              <strong id="pb-status-text">🎒 Tambah Playbox (+Rp <?php echo number_format(HARGA_PLAYBOX, 0, ',', '.'); ?>/hari)</strong>
              <span>Monitor + speaker built-in, plug &amp; play. Wajib 2 orang jika naik motor saat ambil.</span>
            </div>
          </label>
          <div class="hint-text" style="padding-left:.25rem;margin-bottom:1.25rem;">⚠ Playbox hanya tersedia untuk unit PS4</div>
        </div>

        <!-- Kalkulasi Harga -->
        <div class="harga-preview" id="harga_preview" aria-live="polite">
          <h4>💰 Estimasi Biaya</h4>
          <div class="harga-row"><span class="lbl">Sewa Unit</span><span class="val" id="row_unit">—</span></div>
          <div class="harga-row" id="row_playbox_wrap" style="display:none;"><span class="lbl">Playbox</span><span class="val" id="row_playbox">—</span></div>
          <div class="harga-row"><span class="lbl">Durasi dibayar</span><span class="val" id="row_durasi">—</span></div>
          <div class="harga-total"><span class="total-lbl">TOTAL</span><span class="total-val" id="row_total">—</span></div>
          <div class="bayar-info">
            <span aria-hidden="true">💳</span>
            <p><strong>Pembayaran di Lokasi</strong>Estimasi di atas konfirmasi final via WhatsApp.</p>
          </div>
        </div>

        <div class="step-actions">
          <button type="button" class="btn-ghost" onclick="changeStep(1)">
            <span>← Kembali</span>
          </button>
          <button type="button" class="btn-violet" onclick="changeStep(3)">
            <span>Lanjut →</span>
          </button>
        </div>
      </div>

      <!-- STEP 3: UPLOAD DOKUMEN -->
      <div class="form-step-wrapper" id="step-wrapper-3" style="display:none;">
        <div class="form-section-label">
          <svg width="18" height="18" aria-hidden="true"><use href="#ico-file"/></svg> Upload Dokumen
        </div>
        <div class="form-grid-2">
          <div class="form-group">
            <label class="v-label" for="inp-ktp">Foto KTP Asli</label>
            <div class="file-upload-box" id="ktp-box">
              <input type="file" name="ktp" id="inp-ktp" accept="image/*" required onchange="previewBox(this,'ktp-box','ktp-prev')" aria-label="Upload foto KTP">
              <div id="ktp-placeholder" style="display:flex;flex-direction:column;align-items:center;gap:.5rem;pointer-events:none;">
                <svg width="32" height="32" style="color:var(--v-muted)" aria-hidden="true"><use href="#ico-idcard"/></svg>
                <div class="upload-text">Klik untuk upload KTP</div>
                <div class="upload-hint">JPG, PNG · Max 5MB</div>
              </div>
              <div id="ktp-prev" style="display:none;width:100%;height:100%;position:absolute;inset:0;pointer-events:none;">
                <img id="ktp-img" src="" alt="Preview KTP" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">
                <button type="button" onclick="clearBox('ktp-box','ktp-prev','ktp-placeholder','#inp-ktp')" aria-label="Hapus foto KTP" style="position:absolute;top:.5rem;right:.5rem;background:rgba(0,0,0,.7);border:none;border-radius:6px;color:#fff;width:28px;height:28px;cursor:pointer;font-size:.9rem;display:flex;align-items:center;justify-content:center;pointer-events:all;z-index:3;">✕</button>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label class="v-label" for="inp-stnk">Foto STNK Asli</label>
            <div class="file-upload-box" id="stnk-box">
              <input type="file" name="stnk" id="inp-stnk" accept="image/*" required onchange="previewBox(this,'stnk-box','stnk-prev')" aria-label="Upload foto STNK">
              <div id="stnk-placeholder" style="display:flex;flex-direction:column;align-items:center;gap:.5rem;pointer-events:none;">
                <svg width="32" height="32" style="color:var(--v-muted)" aria-hidden="true"><use href="#ico-motor"/></svg>
                <div class="upload-text">Klik untuk upload STNK</div>
                <div class="upload-hint">JPG, PNG · Max 5MB</div>
              </div>
              <div id="stnk-prev" style="display:none;width:100%;height:100%;position:absolute;inset:0;pointer-events:none;">
                <img id="stnk-img" src="" alt="Preview STNK" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">
                <button type="button" onclick="clearBox('stnk-box','stnk-prev','stnk-placeholder','#inp-stnk')" aria-label="Hapus foto STNK" style="position:absolute;top:.5rem;right:.5rem;background:rgba(0,0,0,.7);border:none;border-radius:6px;color:#fff;width:28px;height:28px;cursor:pointer;font-size:.9rem;display:flex;align-items:center;justify-content:center;pointer-events:all;z-index:3;">✕</button>
              </div>
            </div>
          </div>
        </div>

        <div class="form-syarat-box">
          <div class="syarat-title">⚠ Syarat &amp; Ketentuan</div>
          <ul>
            <li>Unit diambil langsung ke toko kami di Jagakarsa</li>
            <li>Nomor WA wajib <strong>nomor pribadi</strong> dengan minimal <strong>50 tag GetContact</strong></li>
            <li>KTP &amp; STNK asli wajib dibawa saat pengambilan sebagai jaminan</li>
            <li>Unit dikembalikan tepat waktu sesuai durasi yang dipilih</li>
            <li>Kerusakan akibat kelalaian menjadi tanggung jawab penyewa</li>
            <li>Pembayaran dilakukan di lokasi saat pengambilan unit</li>
          </ul>
        </div>

        <div class="step-actions">
          <button type="button" class="btn-ghost" onclick="changeStep(2)">
            <span>← Kembali</span>
          </button>
          <button type="submit" name="kirim" id="btn-submit" class="btn-violet btn-submit">
            <span id="btn-submit-text">🎮 Ajukan Sewa Sekarang</span>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
// ── Konstanta & Data dari PHP ─────────────────────────────────────────────
const HARGA_PS4       = <?php echo HARGA_PS4; ?>;
const HARGA_PS5       = <?php echo HARGA_PS5; ?>;
const HARGA_NINTENDO  = <?php echo HARGA_NINTENDO; ?>;
const HARGA_PS4_LIBUR = <?php echo defined('HARGA_PS4_LIBUR') ? HARGA_PS4_LIBUR : 135000; ?>;
const HARGA_PS5_LIBUR = <?php echo defined('HARGA_PS5_LIBUR') ? HARGA_PS5_LIBUR : 230000; ?>;
const HARGA_NIN_LIBUR = <?php echo defined('HARGA_NINTENDO_LIBUR') ? HARGA_NINTENDO_LIBUR : 135000; ?>;
const HARGA_PLAYBOX   = <?php echo HARGA_PLAYBOX; ?>;
const MAX_DURASI      = <?php echo MAX_DURASI_HARI; ?>;
const TOTAL_PLAYBOX   = <?php echo defined('TOTAL_PLAYBOX') ? TOTAL_PLAYBOX : 1; ?>;
const activePlaybox   = <?php echo json_encode($pb_aktif ?? []); ?>;
const hariLibur       = <?php echo json_encode($libur_ranges ?? []); ?>;

// ── Helper Functions ──────────────────────────────────────────────────────
function parseLocalDate(tglStr) {
  if (!tglStr) return null;
  const parts = tglStr.split('-');
  if (parts.length !== 3) return null;
  // Radix 10 wajib agar 08/09 tidak terbaca sebagai oktal di browser lawas
  return new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
}

function getLiburKet(tgl) {
  if (!tgl || !Array.isArray(hariLibur)) return null;
  for (const r of hariLibur) {
    if (tgl >= r.tgl_mulai && tgl <= r.tgl_selesai) return r.keterangan;
  }
  return null;
}

function isPromoWeekday(tgl) {
  if (!tgl) return false;
  const d = parseLocalDate(tgl);
  if (!d) return false;
  const hari = d.getDay(); // 0=Min, 1=Sen, 2=Sel, 3=Rab, 4=Kam, 5=Jum, 6=Sab
  if (hari < 1 || hari > 4) return false;
  return getLiburKet(tgl) === null; // Promo hangus jika hari libur manual
}

function getNamaHari(tgl) {
  const d = parseLocalDate(tgl);
  if (!d) return '';
  return ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'][d.getDay()];
}

function getHargaUnit(kat, isLibur) {
  const k = kat.toUpperCase();
  if (isLibur) {
      if (k === 'PS5') return HARGA_PS5_LIBUR;
      if (k === 'NINTENDO') return HARGA_NIN_LIBUR;
      return HARGA_PS4_LIBUR;
  } else {
      if (k === 'PS5') return HARGA_PS5;
      if (k === 'NINTENDO') return HARGA_NINTENDO;
      return HARGA_PS4;
  }
}

function isPlayboxAvailable(tgl_start, durasi_hari) {
    if (!tgl_start) return true;
    const d_start = parseLocalDate(tgl_start);
    const d_end   = parseLocalDate(tgl_start);
    d_end.setDate(d_end.getDate() + durasi_hari);
    
    let overlapCount = 0;
    activePlaybox.forEach(pb => {
        const pb_start = parseLocalDate(pb.start);
        const pb_end   = parseLocalDate(pb.end);
        if (d_start < pb_end && d_end > pb_start) overlapCount++;
    });
    return overlapCount < TOTAL_PLAYBOX;
}

function fmt(n) { return 'Rp ' + n.toLocaleString('id-ID'); }

function updateCounter(el, counterId, max) {
  const len     = el.value.length;
  const counter = document.getElementById(counterId);
  if (!counter) return;
  counter.textContent = len + '/' + max;
  counter.style.color = len > max * 0.9 ? '#f87171' : 'var(--v-muted)';
}

function ensureRow(id, html) {
  if (!document.getElementById(id)) {
    const el = document.createElement('div');
    el.id = id;
    el.className = 'harga-row';
    el.innerHTML = html;
    el.style.display = 'none';
    const totalRow = document.getElementById('row_total');
    if(totalRow) totalRow.closest('.harga-total').before(el);
  }
}

// ── Core Logic: Hitung Harga & UI Toggle ──────────────────────────────────
function hitungHarga() {
  const durasi   = parseInt(document.getElementById('sel_durasi').value) || 1;
  const unitVal  = document.getElementById('sel_unit').value;
  const tgl      = document.getElementById('tgl_ambil_input')?.value || '';
  const preview  = document.getElementById('harga_preview');
  const sel      = document.getElementById('sel_unit');
  const kat      = sel.options[sel.selectedIndex]?.dataset?.kategori || '';
  const pbWrap   = document.getElementById('playbox_wrap');
  const chk      = document.getElementById('chk_playbox');
  const lblPb    = document.getElementById('playbox_label');
  const txtPb    = document.getElementById('pb-status-text');

  const isLiburStatus = getLiburKet(tgl) !== null;

  // 1. Logika Tampilan Playbox (Khusus PS4)
  if (kat === 'PS4') {
      pbWrap.style.display = 'block';
      if (!isPlayboxAvailable(tgl, durasi)) {
          chk.disabled = true; chk.checked = false;
          lblPb.classList.remove('active');
          lblPb.style.opacity = '0.5'; lblPb.style.cursor = 'not-allowed';
          txtPb.innerHTML = '<span style="color:#f87171;">❌ Playbox Sedang Dipinjam Pada Tanggal Ini</span>';
      } else {
          chk.disabled = false;
          lblPb.style.opacity = '1'; lblPb.style.cursor = 'pointer';
          txtPb.innerHTML = '🎒 Tambah Playbox (+Rp ' + HARGA_PLAYBOX.toLocaleString('id-ID') + '/hari)';
      }
  } else {
      pbWrap.style.display = 'none'; 
      chk.checked = false;
      if (lblPb) lblPb.classList.remove('active');
  }

  if (!unitVal) { 
      preview.classList.remove('show'); 
      updatePromoStatus(tgl, durasi); 
      return; 
  }

  // 2. Kalkulasi Harga
  const pakai   = chk.checked;
  const hUnit   = getHargaUnit(kat, isLiburStatus);
  const hPb     = pakai ? HARGA_PLAYBOX : 0;
  const hSehari = hUnit + hPb;

  const isPromo         = !isLiburStatus && isPromoWeekday(tgl);
  const promoApplicable = isPromo && durasi >= 2;
  const hariDapat       = promoApplicable ? (2 * durasi - 1) : durasi;
  const total           = hSehari * durasi;

  // 3. Render ke UI
  document.getElementById('row_unit').textContent   = fmt(hUnit) + '/hari';
  document.getElementById('row_durasi').textContent = durasi + ' hari';
  
  ensureRow('row_playbox_wrap', '<span class="lbl">Playbox</span><span class="val" id="row_playbox">—</span>');
  document.getElementById('row_playbox_wrap').style.display = pakai ? 'flex' : 'none';
  if (pakai) document.getElementById('row_playbox').textContent = fmt(hPb) + '/hari';

  ensureRow('row_promo_wrap', '<span class="lbl">🎁 Promo Weekday</span><span id="row_promo" style="color:#fbbf24;font-weight:700;"></span>');
  ensureRow('row_dapat_wrap', '<span class="lbl" style="color:#34d399;">✓ Total hari didapat</span><span id="row_dapat" style="color:#34d399;font-weight:800;font-size:1rem;"></span>');

  const promoRow  = document.getElementById('row_promo_wrap');
  const dapatRow  = document.getElementById('row_dapat_wrap');

  if (promoApplicable) {
    promoRow.style.display = 'flex';
    document.getElementById('row_promo').textContent = 'Bayar ' + durasi + ' hari, dapat ' + hariDapat + ' hari!';
    dapatRow.style.display = 'flex';
    document.getElementById('row_dapat').textContent = hariDapat + ' Hari';
  } else {
    promoRow.style.display = 'none';
    dapatRow.style.display = 'none';
  }

  document.getElementById('row_total').textContent = fmt(total);
  preview.classList.add('show');
  
  updatePromoStatus(tgl, durasi);
}

// ── Indikator Status Promo UI ──────────────────────────────────────────────
function updatePromoStatus(tgl, durasi) {
  const statusEl = document.getElementById('promo-status');
  if (!statusEl) return;

  if (!tgl) {
    statusEl.className = 'promo-status';
    statusEl.style.display = 'none';
    return;
  }

  const isPromo   = isPromoWeekday(tgl);
  const liburKet  = getLiburKet(tgl);
  const namaHari  = getNamaHari(tgl);

  statusEl.style.display = 'flex';

  if (liburKet) {
    statusEl.className = 'promo-status inactive';
    statusEl.innerHTML = `⛔ <span>Tanggal ini masuk periode libur <strong>${liburKet}</strong> promo tidak berlaku, harga normal.</span>`;
  } else if (isPromo && durasi >= 2) {
    const hariDapat = 2 * durasi - 1;
    statusEl.className = 'promo-status active';
    statusEl.innerHTML = `🎁 <span><strong>Promo weekday aktif!</strong> ${namaHari} bukan hari libur. Bayar ${durasi} hari → dapat ${hariDapat} hari.</span>`;
  } else if (isPromo && durasi < 2) {
    statusEl.className = 'promo-status active';
    statusEl.innerHTML = `✅ <span><strong>${namaHari}</strong> adalah hari weekday. Tambah durasi jadi 2+ hari untuk dapat promo bonus.</span>`;
  } else {
    statusEl.className = 'promo-status inactive';
    statusEl.innerHTML = `📅 <span><strong>${namaHari}</strong> bukan hari promo. Promo berlaku Senin–Kamis.</span>`;
  }
}

// ── UI Interactions ───────────────────────────────────────────────────────
function togglePlaybox(cb) {
  const lbl = cb.closest('.playbox-toggle');
  if (lbl) lbl.classList.toggle('active', cb.checked);
  hitungHarga();
}

function previewBox(input, boxId, prevId) {
  const f = input.files[0];
  if (!f) return;
  const box  = document.getElementById(boxId);
  const prev = document.getElementById(prevId);
  const ph   = document.getElementById(boxId.replace('-box', '-placeholder'));
  const img  = prev ? prev.querySelector('img') : null;
  if (!box || !prev || !img) return;
  img.src = URL.createObjectURL(f);
  box.classList.add('has-file');
  box.style.padding = '0';
  box.style.borderColor = 'var(--v-violet)';
  box.style.borderStyle = 'solid';
  if (ph) ph.style.display = 'none';
  prev.style.display = 'block';
}

function clearBox(boxId, prevId, phId, inputSel) {
  const box  = document.getElementById(boxId);
  const prev = document.getElementById(prevId);
  const ph   = document.getElementById(phId);
  const inp  = document.querySelector(inputSel);
  if (prev) { const img = prev.querySelector('img'); if (img) img.src = ''; prev.style.display = 'none'; }
  if (ph)   ph.style.display = 'flex';
  if (box)  { box.classList.remove('has-file'); box.style.padding = '1.5rem'; box.style.borderColor = ''; box.style.borderStyle = ''; }
  if (inp)  inp.value = '';
}

// ── Multi-Step Form Logic ──────────────────────────────────────────────────
let currentStep = 1;

function validateStep(step) {
  if (step === 1) {
    const nama = document.getElementById('inp-nama').value.trim();
    const wa   = document.getElementById('inp-wa').value.trim();
    const almt = document.getElementById('inp-alamat').value.trim();
    if (!nama || !wa || !almt) {
      showToast('Harap isi semua kolom data diri terlebih dahulu.', 'warn');
      return false;
    }
    if (wa.length < 10) {
      showToast('Nomor WhatsApp minimal 10 digit.', 'warn');
      return false;
    }
    return true;
  }
  if (step === 2) {
    const unit = document.getElementById('sel_unit').value;
    const dur  = document.getElementById('sel_durasi').value;
    const tgl  = document.getElementById('tgl_ambil_input').value;
    if (!unit || !dur || !tgl) {
      showToast('Harap pilih unit, durasi, dan tanggal ambil terlebih dahulu.', 'warn');
      return false;
    }
    return true;
  }
  return true;
}

function changeStep(toStep) {
  if (toStep > currentStep) {
    if (!validateStep(currentStep)) return;
  }

  document.querySelectorAll('.form-step-wrapper').forEach(el => {
    el.style.display = 'none';
    el.classList.remove('active');
  });

  const targetWrapper = document.getElementById('step-wrapper-' + toStep);
  if (targetWrapper) {
    targetWrapper.style.display = 'block';
    setTimeout(() => targetWrapper.classList.add('active'), 20);
  }

  // Update progress indicators
  document.querySelectorAll('.step-dot').forEach((dot, idx) => {
    const stepNum = idx + 1;
    if (stepNum < toStep) {
      dot.classList.add('completed');
      dot.classList.remove('active');
    } else if (stepNum === toStep) {
      dot.classList.add('active');
      dot.classList.remove('completed');
    } else {
      dot.classList.remove('active', 'completed');
    }
  });

  document.querySelectorAll('.step-line').forEach((line, idx) => {
    const lineNum = idx + 1;
    if (lineNum < toStep) {
      line.classList.add('completed');
    } else {
      line.classList.remove('completed');
    }
  });

  currentStep = toStep;

  // Scroll smoothly to form card
  const card = document.querySelector('.form-card');
  if (card) card.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

// ── Event Listeners ───────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
    const selUnit = document.getElementById('sel_unit');
    const selDurasi = document.getElementById('sel_durasi');
    const tglInput = document.getElementById('tgl_ambil_input');
    const form = document.getElementById('sewaForm');

    if (selUnit) selUnit.addEventListener('change', hitungHarga);
    if (selDurasi) selDurasi.addEventListener('change', hitungHarga);
    if (tglInput) tglInput.addEventListener('change', hitungHarga);

    if (selUnit && selUnit.value) hitungHarga();

    if (selUnit && selUnit.options.length <= 1) {
      selUnit.style.borderColor = 'rgba(239,68,68,.4)';
      const warn = document.createElement('div');
      warn.style.cssText = 'background:rgba(239,68,68,.06);border:1px solid rgba(239,68,68,.18);border-radius:8px;padding:.6rem .9rem;margin-top:.4rem;font-size:.8rem;color:#f87171;font-family:var(--font-ui);';
      warn.textContent = '⚠ Semua unit saat ini sedang disewa. Hubungi kami via WhatsApp.';
      selUnit.parentNode.appendChild(warn);
    }

    if (form) {
        form.addEventListener('submit', function() {
            const btn = document.getElementById('btn-submit');
            const txt = document.getElementById('btn-submit-text');
            if (btn) { btn.style.pointerEvents = 'none'; btn.style.opacity = '.6'; }
            if (txt) txt.textContent = '⏳ Memproses...';
        });
    }
});
</script>
</body>
</html>