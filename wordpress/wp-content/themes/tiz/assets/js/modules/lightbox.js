export function initLightbox(root = document) {
  const overlay = root.querySelector('[data-lightbox-overlay]');
  const imgEl = overlay ? overlay.querySelector('[data-lightbox-image]') : null;
  if (!overlay || !imgEl) return;

  const open = (src, alt = '') => {
    imgEl.style.opacity = '0';
    imgEl.src = src;
    imgEl.alt = alt;
    overlay.classList.remove('invisible');
    overlay.style.opacity = '0';
    requestAnimationFrame(() => {
      overlay.style.opacity = '1';
      overlay.classList.remove('opacity-0');
      overlay.classList.remove('invisible');
      imgEl.onload = () => {
        imgEl.style.opacity = '1';
      };
    });
  };

  const close = () => {
    overlay.style.opacity = '0';
    setTimeout(() => {
      overlay.classList.add('invisible');
      if (imgEl) imgEl.src = '';
    }, 250);
  };

  root.addEventListener('click', (e) => {
    const trigger = e.target.closest('[data-lightbox-trigger]');
    if (trigger) {
      e.preventDefault();
      const img = trigger.querySelector('img');
      const src = img ? (img.currentSrc || img.src) : trigger.getAttribute('href');
      const alt = img ? (img.alt || '') : '';
      if (src) open(src, alt);
    }
    if (e.target.closest('[data-lightbox-close]') || e.target === overlay) {
      e.preventDefault();
      close();
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });
}
