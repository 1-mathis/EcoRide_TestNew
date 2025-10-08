import '../../styles/carSharings/carSharingsHistory.css';

function post(url, data) {
  return fetch(url, {
    method: 'POST',
    headers: { 'X-Requested-With': 'XMLHttpRequest' },
    body: data
  }).then(r => r.json());
}

function flash(kind, msg) {
  const el = document.createElement('div');
  el.className = `flash flash--${kind}`;
  el.textContent = msg;
  document.body.prepend(el);
  setTimeout(() => el.remove(), 4000);
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('form.js-cancel-driver').forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const tripId = form.dataset.trip;
      const token = form.querySelector('input[name="_token"]')?.value || '';
      const data = new FormData();
      data.set('_token', token);

      const res = await post(`/covoiturages/${tripId}/annuler`, data);
      if (res.ok) {
        flash('success', res.message || 'Trajet annulé.');
        form.querySelector('button')?.setAttribute('disabled', 'true');
        const pillStatus = document.querySelector('.facts li strong:contains("Statut")');
      } else {
        flash('error', res.message || 'Erreur');
      }
    });
  });

  document.querySelectorAll('form.js-leave-trip').forEach(form => {
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const tripId = form.dataset.trip;
      const token = form.querySelector('input[name="_token"]')?.value || '';
      const data = new FormData();
      data.set('_token', token);

      const res = await post(`/covoiturages/${tripId}/quitter`, data);
      if (res.ok) {
        flash('success', res.message || 'Tu as quitté le trajet.');
        form.querySelector('button')?.setAttribute('disabled', 'true');
      } else {
        flash('error', res.message || 'Erreur');
      }
    });
  });
});
