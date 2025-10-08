import Chart from "chart.js/auto";
import '../../styles/admin/adminDashboard.css';

async function fetchJSON(url) {
  const r = await fetch(url, { headers: { "X-Requested-With": "fetch" } });
  if (!r.ok) throw new Error(`HTTP ${r.status}`);
  return r.json();
}

async function boot() {
  const chartTripsEl   = document.getElementById('chartTrips');
  const chartCreditsEl = document.getElementById('chartCredits');
  if (!chartTripsEl || !chartCreditsEl) return;

  const q = '';
  const [trips, credits] = await Promise.all([
    fetchJSON(`/admin/api/stats/carsharings-per-day${q}`),
    fetchJSON(`/admin/api/stats/platform-credits-per-day${q}`),
  ]);

  new Chart(chartTripsEl.getContext('2d'), {
    type: 'line',
    data: {
      labels: trips.labels,
      datasets: [{ label: 'Covoiturages', data: trips.data, tension: .25 }]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });

  new Chart(chartCreditsEl.getContext('2d'), {
    type: 'bar',
    data: {
      labels: credits.labels,
      datasets: [{ label: 'Crédits gagnés', data: credits.data }]
    },
    options: { responsive: true, maintainAspectRatio: false }
  });
}

document.addEventListener('DOMContentLoaded', boot);
