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

/* Password show/hide toggle */
function togglePassword(inputId, btn){
  const input = document.getElementById(inputId);
  if(!input) return;
  const isText = input.type === 'text';
  input.type = isText ? 'password' : 'text';
  const eyeUse = btn.querySelector('use');
  if(eyeUse) eyeUse.setAttribute('href', isText ? '/violet-ps/assets/icons.svg#ico-eye' : '/violet-ps/assets/icons.svg#ico-eye-off');
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
  if(box){ box.style.borderColor='var(--v-violet)'; box.style.background='rgba(168,85,247,.05)'; }
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

/* Confirm dialog yang lebih proper */
function confirmAction(msg, callback){
  if(window.confirm(msg)) callback();
}

/* Format Rupiah */
function fmtRupiah(n){ return 'Rp ' + parseInt(n).toLocaleString('id-ID'); }