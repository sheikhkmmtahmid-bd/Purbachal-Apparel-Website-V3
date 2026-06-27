/* =========================================================
   PAL CMS Admin - admin.js
   ========================================================= */
'use strict';

// ── CSRF helper ──────────────────────────────────────────────────────────────
function getCsrfToken() {
  var m = document.querySelector('meta[name="csrf-token"]');
  return m ? m.getAttribute('content') : '';
}

// ── AJAX helper ─────────────────────────────────────────────────────────────
async function palPost(url, data) {
  try {
    var body;
    var headers = { 'X-CSRF-Token': getCsrfToken() };
    if (data instanceof FormData) {
      body = data;
    } else {
      body = JSON.stringify(data);
      headers['Content-Type'] = 'application/json';
    }
    var resp = await fetch(url, { method: 'POST', headers: headers, body: body });
    return await resp.json();
  } catch (e) {
    return { ok: false, message: e.message };
  }
}

// ── Toast notifications ──────────────────────────────────────────────────────
function showToast(msg, type) {
  type = type || 'success';
  var c = document.getElementById('toast-container');
  if (!c) return;
  var t = document.createElement('div');
  t.className = 'toast ' + type;
  t.textContent = msg;
  c.appendChild(t);
  setTimeout(function() { t.remove(); }, 3500);
}

// ── Sidebar toggle ───────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function() {
  var btn = document.getElementById('sidebarToggle');
  var sb  = document.getElementById('sidebar');
  if (btn && sb) {
    btn.addEventListener('click', function() { sb.classList.toggle('open'); });
    document.addEventListener('click', function(e) {
      if (sb.classList.contains('open') && !sb.contains(e.target) && e.target !== btn) {
        sb.classList.remove('open');
      }
    });
  }

  // ── Image preview on file input ────────────────────────────────────────────
  document.querySelectorAll('input[type="file"]').forEach(function(inp) {
    inp.addEventListener('change', function() {
      if (!this.files || !this.files[0]) return;
      var prev = this.closest('.card-body') && this.closest('.card-body').querySelector('.img-preview');
      if (prev) {
        var reader = new FileReader();
        reader.onload = function(ev) { prev.src = ev.target.result; prev.style.display = 'block'; };
        reader.readAsDataURL(inp.files[0]);
      }
    });
  });

  // ── Confirm destructive forms ─────────────────────────────────────────────
  document.querySelectorAll('form[data-confirm]').forEach(function(form) {
    form.addEventListener('submit', function(e) {
      if (!confirm(this.dataset.confirm)) e.preventDefault();
    });
  });
});