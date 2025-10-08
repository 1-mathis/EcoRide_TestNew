import '../../styles/user/user.css';

function showToast(message, type = 'success', timeout = 2500) {
  const root = document.getElementById('toast-root') || document.body;
  const toast = document.createElement('div');
  toast.className = `toast toast--${type}`;
  toast.role = 'status';
  toast.textContent = message;

  root.appendChild(toast);
  requestAnimationFrame(() => toast.classList.add('is-visible'));

  setTimeout(() => {
    toast.classList.remove('is-visible');
    setTimeout(() => toast.remove(), 300);
  }, timeout);
}

document.addEventListener('submit', async (e) => {
  const form = e.target;
  if (!(form instanceof HTMLFormElement)) return;
  if (!form.classList.contains('js-del-pref')) return;

  e.preventDefault();

  const url   = form.dataset.url || form.action;
  const token = form.dataset.token || (form.querySelector('input[name="_token"]')?.value ?? '');
  const btn   = form.querySelector('button[type="submit"], button');
  const li    = form.closest('li');

  const originalText = btn ? btn.textContent : '';

  try {
    if (btn) {
      btn.disabled = true;
      btn.textContent = 'Suppression…';
      btn.setAttribute('aria-busy', 'true');
    }

    const res = await fetch(url, {
      method: 'POST',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: new URLSearchParams({ _token: token }),
    });

    let data = {};
    try { data = await res.json(); } catch {  }

    if (!res.ok || !data.ok) {
      throw new Error(data.message || 'Erreur lors de la suppression.');
    }

    if (li) li.remove();
    showToast(data.message || 'Préférence supprimée.', 'success');
  } catch (err) {
    showToast(err.message || 'Suppression impossible.', 'error', 3500);
    if (btn) {
      btn.disabled = false;
      btn.textContent = originalText;
      btn.removeAttribute('aria-busy');
    }
  }
});
