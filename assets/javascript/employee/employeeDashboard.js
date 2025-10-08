import '../../styles/employee/employeeDashboard.css';

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
  setTimeout(() => el.remove(), 2200);
}

function esc(s) { return (s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;'); }

async function fetchJSON(url, opts = {}) {
  const res = await fetch(url, { headers: { 'X-Requested-With': 'fetch' }, ...opts });
  const data = await res.json().catch(() => ({}));
  if (!res.ok || data.ok === false) throw new Error(data.message || 'Erreur');
  return data;
}

function renderReviews(container, rows) {
  if (!rows.length) { container.innerHTML = '<p class="muted">Aucun avis en attente.</p>'; return; }

  const ul = document.createElement('ul');
  ul.className = 'reviews';

  rows.forEach(r => {
    const li = document.createElement('li');
    li.className = 'review';
    li.dataset.id = r.id;
    const who = [];
    if (r.author) who.push(`De ${esc(r.author.username)} <small class="muted">(${esc(r.author.email)})</small>`);
    if (r.driver) who.push(`Pour ${esc(r.driver.username)} <small class="muted">(${esc(r.driver.email)})</small>`);
    if (r.trip)   who.push(`Trajet #${r.trip.id} ${esc(r.trip.fromCity)} → ${esc(r.trip.toCity)}`);

    li.innerHTML = `
      <div class="review__head">
        <div>
          <div class="who">${who.join(' • ')}</div>
          <div class="muted">${esc(r.createdAt)} ${r.rating ? `• ★ ${r.rating}/5` : ''}</div>
        </div>
      </div>
      <p class="review__content">${r.comment ? esc(r.comment) : '—'}</p>
      <div class="tdr-modal__actions" style="gap:8px;flex-wrap:wrap">
        <button class="btn" data-approve data-token="${esc(r.tokens.approve)}">Valider</button>
        <button class="btn btn--ghost" data-reject data-token="${esc(r.tokens.reject)}">Refuser</button>
      </div>
    `;
    ul.appendChild(li);
  });

  container.innerHTML = '';
  container.appendChild(ul);

  ul.querySelectorAll('[data-approve]').forEach(btn => {
    btn.addEventListener('click', async () => {
      const li = btn.closest('li.review'); const id = li.dataset.id;
      btn.disabled = true;
      try {
        await fetchJSON(`/employe/api/reviews/${id}/approve`, {
          method: 'POST', body: new URLSearchParams({ _token: btn.dataset.token })
        });
        toast('Avis approuvé', 'success'); li.remove();
        if (!ul.querySelector('li.review')) container.innerHTML = '<p class="muted">Aucun avis en attente.</p>';
      } catch (e) { toast(e.message, 'error'); } finally { btn.disabled = false; }
    });
  });

  ul.querySelectorAll('[data-reject]').forEach(btn => {
    btn.addEventListener('click', async () => {
      const li = btn.closest('li.review'); const id = li.dataset.id;
      btn.disabled = true;
      try {
        await fetchJSON(`/employe/api/reviews/${id}/reject`, {
          method: 'POST', body: new URLSearchParams({ _token: btn.dataset.token })
        });
        toast('Avis refusé', 'success'); li.remove();
        if (!ul.querySelector('li.review')) container.innerHTML = '<p class="muted">Aucun avis en attente.</p>';
      } catch (e) { toast(e.message, 'error'); } finally { btn.disabled = false; }
    });
  });
}

function renderReports(container, rows) {
  if (!rows.length) { container.innerHTML = '<p class="muted">Aucun incident ouvert.</p>'; return; }

  const ul = document.createElement('ul');
  ul.className = 'reviews';

  rows.forEach(r => {
    const li = document.createElement('li');
    li.className = 'review';
    li.dataset.id = r.id;
    const who = [];
    if (r.reporter) who.push(`Par ${esc(r.reporter.username)} <small class="muted">(${esc(r.reporter.email)})</small>`);
    if (r.driver)   who.push(`Chauffeur ${esc(r.driver.username)} <small class="muted">(${esc(r.driver.email)})</small>`);
    if (r.trip)     who.push(`Trajet #${r.trip.id} ${esc(r.trip.fromCity)} → ${esc(r.trip.toCity)}`);

    li.innerHTML = `
      <div class="review__head">
        <div>
          <div class="who">${who.join(' • ')}</div>
          <div class="muted">${esc(r.createdAt)} • Statut: ${esc(r.status)}</div>
        </div>
      </div>
      <p class="review__content"><strong>Raison:</strong> ${r.reason ? esc(r.reason) : '—'}</p>
      <div class="muted">
        ${r.trip ? `Départ: ${esc(r.trip.departureAt ?? '—')} • Arrivée: ${esc(r.trip.arrivalAt ?? '—')}` : ''}
      </div>
      <div class="tdr-modal__actions" style="gap:8px;flex-wrap:wrap;margin-top:8px">
        <button class="btn" data-resolve data-token="${esc(r.tokens.resolve)}">Marquer comme résolu</button>
        <button class="btn btn--ghost" data-reject data-token="${esc(r.tokens.reject)}">Rejeter</button>
      </div>
    `;
    ul.appendChild(li);
  });

  container.innerHTML = '';
  container.appendChild(ul);

  ul.querySelectorAll('[data-resolve]').forEach(btn => {
    btn.addEventListener('click', async () => {
      const li = btn.closest('li.review'); const id = li.dataset.id;
      btn.disabled = true;
      try {
        await fetchJSON(`/employe/api/reports/${id}/resolve`, {
          method: 'POST', body: new URLSearchParams({ _token: btn.dataset.token })
        });
        toast('Incident résolu', 'success'); li.remove();
        if (!ul.querySelector('li.review')) container.innerHTML = '<p class="muted">Aucun incident ouvert.</p>';
      } catch (e) { toast(e.message, 'error'); } finally { btn.disabled = false; }
    });
  });

  ul.querySelectorAll('[data-reject]').forEach(btn => {
    btn.addEventListener('click', async () => {
      const li = btn.closest('li.review'); const id = li.dataset.id;
      btn.disabled = true;
      try {
        await fetchJSON(`/employe/api/reports/${id}/reject`, {
          method: 'POST', body: new URLSearchParams({ _token: btn.dataset.token })
        });
        toast('Incident rejeté', 'success'); li.remove();
        if (!ul.querySelector('li.review')) container.innerHTML = '<p class="muted">Aucun incident ouvert.</p>';
      } catch (e) { toast(e.message, 'error'); } finally { btn.disabled = false; }
    });
  });
}

async function loadAll() {
  const boxReviews = document.getElementById('employee-reviews');
  const boxReports = document.getElementById('employee-reports');
  try {
    const [rev, rep] = await Promise.all([
      fetchJSON('/employe/api/reviews?status=pending'),
      fetchJSON('/employe/api/reports?status=open'),
    ]);
    renderReviews(boxReviews, rev.data || []);
    renderReports(boxReports, rep.data || []);
  } catch (e) {
    toast(e.message, 'error');
    boxReviews.innerHTML = '<p class="muted">Erreur de chargement.</p>';
    boxReports.innerHTML = '<p class="muted">Erreur de chargement.</p>';
  }
}

document.addEventListener('DOMContentLoaded', loadAll);
