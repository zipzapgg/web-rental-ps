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