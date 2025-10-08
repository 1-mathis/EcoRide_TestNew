function toast(msg, type = 'info') {
  const el = document.createElement('div');
  el.textContent = msg;
  el.className = `toast ${type}`;
  Object.assign(el.style, {
    position: 'fixed', right: '16px', top: '16px', padding: '10px 12px',
    background: type === 'success' ? '#16a34a' : (type === 'error' ? '#dc2626' : '#334155'),
    color: '#fff', borderRadius: '8px', zIndex: 9999
  });
  document.body.appendChild(el);
  setTimeout(() => el.remove(), 2500);
}

function handleConfirmForms() {
  document.querySelectorAll('form.js-pc-confirm').forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = form.querySelector('button[type="submit"]');
      btn.disabled = true;

      const action = form.getAttribute('action');
      const fd = new FormData(form);

      try {
        const res = await fetch(action, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'fetch' } });
        const data = await res.json();
        if (!res.ok || data.ok === false) throw new Error(data.message || 'Erreur inconnue');

        toast(data.message || 'Confirmation enregistrée', 'success');

        const li = form.closest('li.review');
        if (li) {
          const statusEl = li.querySelector('.muted');
          if (statusEl) statusEl.textContent = 'Statut: CONFIRMED';
          const actions = li.querySelector('.tdr-modal__actions');
          if (actions) actions.innerHTML = '<p class="muted">Déjà confirmé.</p>';
        }
      } catch (err) {
        toast(err.message, 'error');
      } finally {
        btn.disabled = false;
      }
    });
  });
}

function handleReportModal() {
  const modal = document.getElementById('report-modal');
  if (!modal) return;

  const openBtns = document.querySelectorAll('[data-open-report]');
  const closeBtns = modal.querySelectorAll('[data-close-report]');
  const inputPc = modal.querySelector('#pc_id');
  const inputCsrf = modal.querySelector('#report_csrf');
  const form = modal.querySelector('#report-form');

  openBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const pcId = btn.getAttribute('data-open-report');
      const token = btn.getAttribute('data-report-token');
      inputPc.value = pcId;
      inputCsrf.value = token;
      modal.removeAttribute('hidden');
      modal.setAttribute('aria-hidden', 'false');
    });
  });

  closeBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      modal.setAttribute('hidden', 'true');
      modal.setAttribute('aria-hidden', 'true');
      form.reset();
    });
  });

  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const pcId = inputPc.value;
    const token = inputCsrf.value;
    const comment = form.querySelector('textarea[name="comment"]').value.trim();

    if (!comment) {
      toast('Merci de préciser un commentaire', 'error');
      return;
    }

    try {
      const res = await fetch(`/confirmations/${pcId}/report`, {
        method: 'POST',
        headers: { 'X-Requested-With': 'fetch' },
        body: new URLSearchParams({ _token: token, comment })
      });
      const data = await res.json();
      if (!res.ok || data.ok === false) throw new Error(data.message || 'Erreur inconnue');

      toast(data.message || 'Signalement enregistré', 'success');

      const li = document.getElementById(`pc-row-${pcId}`);
      if (li) {
        const statusEl = li.querySelector('.muted');
        if (statusEl) statusEl.textContent = 'Statut: REPORTED';
        const actions = li.querySelector('.tdr-modal__actions');
        if (actions) actions.innerHTML = '<p class="muted">Déjà signalé.</p>';
      }

      modal.setAttribute('hidden', 'true');
      modal.setAttribute('aria-hidden', 'true');
      form.reset();
    } catch (err) {
      toast(err.message, 'error');
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  handleConfirmForms();
  handleReportModal();
});
