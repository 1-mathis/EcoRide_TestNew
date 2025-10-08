import '../../styles/carSharings/carSharings.css';

document.addEventListener('DOMContentLoaded', () => {
  const modal   = document.getElementById('join-confirm-modal');
  const form    = document.getElementById('join-form');
  const openEl  = document.querySelector('[data-open-confirm]');

  if (!modal) return;

  const closeEls = modal.querySelectorAll('[data-close-confirm]');
  const submitEl = modal.querySelector('[data-submit-confirm]');

  const open = () => {
    modal.removeAttribute('hidden');
    modal.setAttribute('aria-hidden', 'false');
    submitEl?.focus();
    document.body.classList.add('modal-open');
  };

  const close = () => {
    modal.setAttribute('hidden', '');
    modal.setAttribute('aria-hidden', 'true');
    openEl?.focus();
    document.body.classList.remove('modal-open');
  };

  openEl?.addEventListener('click', (e) => {
    e.preventDefault();
    open();
  });

  closeEls.forEach(btn => btn.addEventListener('click', (e) => {
    if (btn.tagName !== 'A') {
      e.preventDefault();
      close();
    }
  }));

  submitEl?.addEventListener('click', (e) => {
    if (submitEl.getAttribute('type') !== 'submit' && form) {
      e.preventDefault();
      form.submit();
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.hasAttribute('hidden')) close();
  });

  try {
    const url = new URL(window.location.href);
    if (url.searchParams.get('confirm') === '1') {
      open();
      url.searchParams.delete('confirm');
      window.history.replaceState({}, '', url.toString());
    }
  } catch {  }
});
