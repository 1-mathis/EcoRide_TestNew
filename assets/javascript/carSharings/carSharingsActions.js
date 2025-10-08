document.addEventListener('DOMContentLoaded', () => {
  const fetchJson = async (url, form) => {
    const fd = new FormData(form);
    const res = await fetch(url, { method: 'POST', body: fd });
    return { ok: res.ok, status: res.status, json: await res.json().catch(() => ({})) };
  };

  const handleStart = async (form) => {
    const tripId = form.dataset.trip;
    const { ok, json } = await fetchJson(`/covoiturages/${tripId}/start`, form);
    alert(json.message || (ok ? 'Trajet démarré' : 'Erreur démarrage'));
    if (ok) location.reload();
  };

  const handleFinish = async (form) => {
    const tripId = form.dataset.trip;
    const { ok, json } = await fetchJson(`/covoiturages/${tripId}/finish`, form);
    alert(json.message || (ok ? 'Trajet terminé' : 'Erreur fin de trajet'));
    if (ok) location.reload();
  };

  document.querySelectorAll('form.js-start-trip').forEach(f => {
    f.addEventListener('submit', (e) => { e.preventDefault(); handleStart(f); });
  });
  document.querySelectorAll('form.js-finish-trip').forEach(f => {
    f.addEventListener('submit', (e) => { e.preventDefault(); handleFinish(f); });
  });
});
