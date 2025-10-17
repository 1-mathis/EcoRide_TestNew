import '../../../styles/components/menu/menu.css';

(function () {
  if (window.__tdrMenuBound) return;
  window.__tdrMenuBound = true;

  const nav = document.querySelector('.tdr-menu');
  const burger = document.querySelector('.tdr-burger');
  const menu = document.querySelector('.tdr-menu__links');
  const overlay = document.querySelector('.tdr-menu__overlay');

  if (!nav || !burger || !menu || !overlay) return;

  let lastFocused = null;
  const focusableSel = 'a[href], button:not([disabled])';
  const focusTrap = (e) => {
    const items = menu.querySelectorAll(focusableSel);
    if (!items.length) return;
    const first = items[0];
    const last = items[items.length - 1];
    if (e.key === 'Tab' && !e.shiftKey && document.activeElement === last) {
      e.preventDefault(); first.focus();
    } else if (e.key === 'Tab' && e.shiftKey && document.activeElement === first) {
      e.preventDefault(); last.focus();
    }
  };

  function openMenu() {
    lastFocused = document.activeElement;
    burger.setAttribute('aria-expanded', 'true');
    burger.setAttribute('aria-label', 'Fermer le menu');
    menu.classList.add('is-open');
    overlay.hidden = false;
    document.body.style.overflow = 'hidden';
    const first = menu.querySelector(focusableSel);
    if (first) first.focus();
    document.addEventListener('keydown', onKeyDown);
    document.addEventListener('keydown', focusTrap);
  }

  function closeMenu() {
    burger.setAttribute('aria-expanded', 'false');
    burger.setAttribute('aria-label', 'Ouvrir le menu');
    menu.classList.remove('is-open');
    overlay.hidden = true;
    document.body.style.overflow = '';
    document.removeEventListener('keydown', onKeyDown);
    document.removeEventListener('keydown', focusTrap);
    if (lastFocused && document.contains(lastFocused)) lastFocused.focus();
  }

  function onKeyDown(e) {
    if (e.key === 'Escape') closeMenu();
  }

  burger.addEventListener('click', () => {
    const expanded = burger.getAttribute('aria-expanded') === 'true';
    expanded ? closeMenu() : openMenu();
  });

  overlay.addEventListener('click', closeMenu);

  menu.addEventListener('click', (e) => {
    const a = e.target.closest('a');
    if (!a) return;
    if (getComputedStyle(burger).display !== 'none') closeMenu();
  });

  const mql = window.matchMedia('(min-width: 860px)');
  mql.addEventListener('change', () => {
    if (mql.matches) closeMenu();
  });

  const onScroll = () => {
    const y = window.scrollY || document.documentElement.scrollTop;
    nav.classList.toggle('is-scrolled', y > 4);
  };
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });

  try {
    const here = window.location.pathname;
    const links = menu.querySelectorAll('a[href^="/"]');
    let hasActive = false;
    links.forEach(a => { if (a.classList.contains('active')) hasActive = true; });
    if (!hasActive) {
      links.forEach(a => {
        const href = a.getAttribute('href');
        if (href !== '/' && here.startsWith(href)) a.classList.add('active');
      });
    }
  } catch (_) {}
})();
