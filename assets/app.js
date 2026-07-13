/* ── Violet PS — Global JS Utilities ── */

/* Toast notification */
function showToast(msg, type='info', duration=3500){
  let wrap = document.getElementById('toast-wrap');
  if(!wrap){
    wrap = document.createElement('div');
    wrap.id = 'toast-wrap';
    wrap.className = 'toast-wrap';
    document.body.appendChild(wrap);
  }
  const icons = {success:'✓', error:'✕', warn:'⚠', info:'ℹ'};
  const t = document.createElement('div');
  t.className = `toast ${type}`;
  t.innerHTML = `<span>${icons[type]||'ℹ'}</span><span style="flex:1">${msg}</span><button class="toast-close" onclick="this.closest('.toast').remove()">✕</button>`;
  wrap.appendChild(t);
  setTimeout(()=>{ t.style.animation='toastOut .3s ease both'; setTimeout(()=>t.remove(), 300); }, duration);
}

/* Password show/hide toggle
 * PERBAIKAN: Hapus hardcode path '/violet-ps/' — gunakan path relatif dari href existing
 */
function togglePassword(inputId, btn){
  const input = document.getElementById(inputId);
  if(!input) return;
  const isText = input.type === 'text';
  input.type = isText ? 'password' : 'text';

  // Cari elemen use di dalam btn
  const eyeUse = btn.querySelector('use');
  if(eyeUse){
    // Ambil href existing, ganti hanya bagian fragment (#ico-xxx)
    const existingHref = eyeUse.getAttribute('href') || eyeUse.getAttribute('xlink:href') || '';
    const basePath = existingHref.includes('#') ? existingHref.split('#')[0] : existingHref;
    const newFragment = isText ? 'ico-eye' : 'ico-eye-off';
    const newHref = basePath ? basePath + '#' + newFragment : '#' + newFragment;
    eyeUse.setAttribute('href', newHref);
  }
}

/* Image preview for file inputs */
function previewImage(input, previewId){
  const f = input.files[0];
  const wrap = document.getElementById(previewId);
  if(!f || !wrap) return;
  const img = wrap.querySelector('img') || document.createElement('img');
  img.src = URL.createObjectURL(f);
  if(!wrap.querySelector('img')) wrap.appendChild(img);
  wrap.classList.add('show');
  // Update upload box styling
  const box = input.closest('.file-upload-box');
  if(box){ box.style.borderColor='var(--v-violet)'; box.style.background='rgba(157, 86, 255,.05)'; }
  const txt = box?.querySelector('.upload-text');
  if(txt) txt.textContent = f.name;
}

function removePreview(previewId, inputSelector){
  const wrap = document.getElementById(previewId);
  if(wrap){ wrap.classList.remove('show'); const img=wrap.querySelector('img'); if(img) img.src=''; }
  const input = document.querySelector(inputSelector);
  if(input){ input.value=''; }
  // Reset box
  const box = input?.closest('.file-upload-box');
  if(box){ box.style.borderColor=''; box.style.background=''; }
}

/* Confirm dialog */
function confirmAction(msg, callback){
  if(window.confirm(msg)) callback();
}

/* Format Rupiah */
function fmtRupiah(n){ return 'Rp ' + parseInt(n).toLocaleString('id-ID'); }
/* =========================================================
   GLOBAL ADMIN SIDEBAR TOGGLE
   ========================================================= */
function toggleSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebar) sidebar.classList.toggle('mobile-open');
    if (overlay) overlay.classList.toggle('open');
    
    // Kunci scroll layar saat menu terbuka
    document.body.style.overflow = sidebar && sidebar.classList.contains('mobile-open') ? 'hidden' : '';
}

function closeSidebar() {
    const sidebar = document.querySelector('.sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    
    if (sidebar) sidebar.classList.remove('mobile-open');
    if (overlay) overlay.classList.remove('open');
    
    // Buka kunci scroll
    document.body.style.overflow = '';
}

/* ── Interactive Cursor Glow Spotlight ── */
let glowFrame;
document.addEventListener('mousemove', e => {
  if (glowFrame) cancelAnimationFrame(glowFrame);
  glowFrame = requestAnimationFrame(() => {
    const glow = document.getElementById('cursor-glow');
    if (glow) {
      glow.style.left = e.clientX + 'px';
      glow.style.top = e.clientY + 'px';
    }
  });
}, { passive: true });

/* ── Universal 3D Tilt Effect (Hero Logo) ── */
document.querySelectorAll('.tilt-3d').forEach(el => {
  const shine = el.querySelector('.hero-logo-shine');
  const img   = el.querySelector('.hero-logo-img-solo');
  let rafId = null;
  let currentRX = 0, currentRY = 0;
  let targetRX = 0, targetRY = 0;
  let isHovering = false;

  function lerp(a, b, t) { return a + (b - a) * t; }

  function tick() {
    currentRX = lerp(currentRX, targetRX, 0.12);
    currentRY = lerp(currentRY, targetRY, 0.12);

    el.style.transform = `perspective(600px) rotateX(${currentRX}deg) rotateY(${currentRY}deg) scale3d(1.04, 1.04, 1.04)`;

    if (isHovering || Math.abs(currentRX) > 0.05 || Math.abs(currentRY) > 0.05) {
      rafId = requestAnimationFrame(tick);
    } else {
      currentRX = 0; currentRY = 0;
      el.style.transform = '';
      rafId = null;
    }
  }

  el.addEventListener('mousemove', e => {
    const rect = el.getBoundingClientRect();
    const x = e.clientX - rect.left - rect.width / 2;
    const y = e.clientY - rect.top - rect.height / 2;

    // Max tilt 22deg
    targetRX = -(y / (rect.height / 2)) * 22;
    targetRY =  (x / (rect.width  / 2)) * 22;

    // Shine overlay (hanya jika ada)
    if (shine) {
      const px = ((x / rect.width)  + 0.5) * 100;
      const py = ((y / rect.height) + 0.5) * 100;
      shine.style.background = `radial-gradient(circle at ${px}% ${py}%, rgba(255,255,255,0.18) 0%, rgba(255,255,255,0.06) 30%, transparent 65%)`;
    }

    // Efek parallax ringan pada gambar solo
    if (img) {
      const px = (x / rect.width)  * 12;
      const py = (y / rect.height) * 12;
      img.style.transform = `translate(${px}px, ${py}px) scale(1.03)`;
    }

    if (!rafId) { isHovering = true; rafId = requestAnimationFrame(tick); }
  });

  el.addEventListener('mouseleave', () => {
    isHovering = false;
    targetRX = 0;
    targetRY = 0;
    if (shine) shine.style.background = '';
    if (img)   img.style.transform = '';
    if (!rafId) rafId = requestAnimationFrame(tick);
  });
});

// ── Auto-read URL params to show premium toasts ──
document.addEventListener('DOMContentLoaded', () => {
  const urlParams = new URLSearchParams(window.location.search);
  const msg = urlParams.get('msg');
  const pesan = urlParams.get('pesan');
  const denda = urlParams.get('denda');
  const errorText = urlParams.get('error_text');

  const messages = {
    // msg params (Admin & user actions)
    'edit_ok': { text: '✓ Perubahan berhasil disimpan.', type: 'success' },
    'maint_gagal': { text: '✕ Keterangan maintenance wajib diisi!', type: 'error' },
    'hapus_ok': { text: '✓ Unit berhasil dihapus.', type: 'success' },
    'qe_ok': { text: '⚡ Quick Entry Berhasil! Unit langsung masuk ke Live Monitoring.', type: 'success' },
    'terima': { text: '✓ Pengajuan disetujui.', type: 'success' },
    'tolak': { text: '✕ Pengajuan ditolak. Unit dikembalikan.', type: 'warn' },
    'selesai': { text: '✓ Transaksi selesai. Unit tersedia kembali.' + (denda ? ` (Denda: Rp ${parseInt(denda).toLocaleString('id-ID')})` : ''), type: 'success' },
    'perpanjang': { text: '✓ Sewa berhasil diperpanjang.', type: 'success' },
    'unassign_ok': { text: '✓ Game berhasil dihapus dari unit.', type: 'success' },
    'tambah': { text: '✓ Periode libur berhasil ditambahkan.', type: 'success' },
    'hapus': { text: '✓ Periode libur berhasil dihapus.', type: 'success' },
    'error': { text: errorText ? decodeURIComponent(errorText) : '✕ Data tidak valid atau terjadi kesalahan.', type: 'error' },
    'qe_not_avail': { text: '✕ Unit sudah tidak tersedia atau sedang disewa orang lain!', type: 'error' },
    'qe_error': { text: errorText ? decodeURIComponent(errorText) : '✕ Terjadi kesalahan pada Quick Entry.', type: 'error' },
    'tambah_game_ok': { text: '✓ Game berhasil ditambahkan!', type: 'success' },
    'tambah_unit_ok': { text: errorText ? decodeURIComponent(errorText) : '✓ Unit berhasil ditambahkan!', type: 'success' },

    // pesan params (Auth/Login/General alerts)
    'timeout': { text: '⏱ Sesi berakhir karena tidak aktif. Silakan login kembali.', type: 'warn' },
    'belum_login': { text: '⚠ Silakan login terlebih dahulu.', type: 'warn' },
    'akses_ditolak': { text: '✕ Akses ditolak.', type: 'error' },
    'attempts_limit': { text: '✕ Terlalu banyak percobaan login. Coba lagi 15 menit kemudian.', type: 'error' },
    'wrong': { text: '✕ Username atau password salah.', type: 'error' }
  };

  const key = msg || pesan;
  if (key && messages[key]) {
    const item = messages[key];
    showToast(item.text, item.type, 5000);
    
    // Clean up URL parameters to prevent re-showing toast on refresh
    const newUrl = window.location.pathname + (window.location.hash || '');
    window.history.replaceState({}, document.title, newUrl);
  }
});