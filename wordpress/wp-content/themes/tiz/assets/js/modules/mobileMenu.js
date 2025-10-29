import { gsap } from 'gsap';

export function initMobileMenu(root = document) {
  const viewsWrap = root.querySelector('.mobile-menu-views');
  if (!viewsWrap) return;

  const rootView = viewsWrap.querySelector('[data-mobile-root]');
  const submenus = Array.from(viewsWrap.querySelectorAll('[data-submenu]'));

  // Initial states: visible view participates in layout; hidden views are stacked but invisible
  gsap.set(rootView, { position: 'relative', autoAlpha: 1, filter: 'blur(0px)', pointerEvents: 'auto' });
  submenus.forEach((el) => {
    gsap.set(el, { position: 'absolute', top: 0, left: 0, right: 0, autoAlpha: 0, filter: 'blur(8px)', pointerEvents: 'none' });
  });
  // Keep container from spilling
  gsap.set(viewsWrap, { overflow: 'hidden' });

  // Helper: animate height of the parent container (.mobile-menu-views) to fit target element (keeps numeric height)
  function animateViewsHeightTo(targetEl, duration = 0.5) {
    if (!viewsWrap || !targetEl) return;
    const currentRelative = [rootView, ...submenus].find((el) => el && getComputedStyle(el).position === 'relative');
    let startH = viewsWrap.offsetHeight;
    const startExtra = currentRelative === rootView ? 16 : 0;
    if (startH < 1) startH = currentRelative ? currentRelative.scrollHeight + startExtra : 0;
    const extra = targetEl === rootView ? 16 : 0;
    const targetH = (targetEl.scrollHeight || 0) + extra;
    if (!targetH) return;
    if (Math.abs(startH - targetH) < 1) {
      gsap.set(viewsWrap, { height: targetH }); // enforce numeric height anyway
      return;
    }
    gsap.set(viewsWrap, { height: startH, willChange: 'height' });
    return gsap.to(viewsWrap, {
      height: targetH,
      duration,
      ease: 'power2.inOut',
      onComplete: () => gsap.set(viewsWrap, { height: targetH, willChange: '' })
    });
  }

  function openSubmenu(id) {
    const sub = viewsWrap.querySelector(`[data-submenu="${id}"]`);
    if (!sub || !rootView) return;

    // Prepare positions for correct measuring
    sub.classList.remove('hidden');
    gsap.set(sub, { position: 'relative', visibility: 'hidden' });
    gsap.set(rootView, { position: 'absolute', top: 0, left: 0, right: 0 });

    // Force reflow for consistent measuring
    // eslint-disable-next-line no-unused-expressions
    sub.offsetHeight;

    // Animate container height to submenu height (500ms), keep numeric height
    animateViewsHeightTo(sub, 0.5);

    const tl = gsap.timeline({ defaults: { duration: 0.4, ease: 'power2.out' } });
    tl.set([rootView, sub], { willChange: 'opacity, filter' })
      .to(rootView, { autoAlpha: 0, filter: 'blur(8px)', pointerEvents: 'none' }, 0)
      .fromTo(sub, { autoAlpha: 0, filter: 'blur(8px)', visibility: 'hidden' }, { autoAlpha: 1, filter: 'blur(0px)', visibility: 'visible', pointerEvents: 'auto' }, 0)
      .set([rootView, sub], { willChange: '' });
  }

  function backToRoot(id) {
    const sub = viewsWrap.querySelector(`[data-submenu="${id}"]`);
    if (!sub || !rootView) return;

    // Prepare positions
    gsap.set(rootView, { position: 'relative', visibility: 'hidden' });
    gsap.set(sub, { position: 'absolute', top: 0, left: 0, right: 0, visibility: 'visible' });

    // Force a reflow for consistent measuring
    // eslint-disable-next-line no-unused-expressions
    rootView.offsetHeight;

    // Animate container height to root height (500ms), keep numeric height (+16px)
    animateViewsHeightTo(rootView, 0.5);

    const tl = gsap.timeline({ defaults: { duration: 0.4, ease: 'power2.out' } });
    tl.set([rootView, sub], { willChange: 'opacity, filter' })
      .to(sub, { autoAlpha: 0, filter: 'blur(8px)', pointerEvents: 'none' }, 0)
      .fromTo(rootView, { autoAlpha: 0, filter: 'blur(8px)', visibility: 'hidden' }, { autoAlpha: 1, filter: 'blur(0px)', visibility: 'visible', pointerEvents: 'auto' }, 0)
      .add(() => { sub.classList.add('hidden'); })
      .set([rootView, sub], { willChange: '' });
  }

  // Event delegation for internal view transitions
  root.addEventListener('click', (e) => {
    const openBtn = e.target.closest('[data-open-submenu]');
    if (openBtn) {
      const id = openBtn.getAttribute('data-open-submenu');
      openSubmenu(id);
      return;
    }
    const backBtn = e.target.closest('[data-back]');
    if (backBtn) {
      const id = backBtn.getAttribute('data-back');
      backToRoot(id);
    }
  });

  // Panel open/close controls
  const hamburger = root.querySelector('[data-mobile-menu-open]');
  const panel = root.querySelector('[data-mobile-menu-panel]');
  const overlay = root.querySelector('[data-mobile-menu-overlay]');
  let isOpen = false;

  if (panel && hamburger) {
    // Initial hidden states for panel and overlay
    gsap.set(panel, { y: -16, autoAlpha: 0, filter: 'blur(8px)', pointerEvents: 'none' });
    if (overlay) gsap.set(overlay, { autoAlpha: 0, pointerEvents: 'none' });

    const docBody = document.body;

    function resetToRootView() {
      // Ensure root is visible and submenus are hidden when closing
      gsap.set(rootView, { position: 'relative', autoAlpha: 1, filter: 'blur(0px)', pointerEvents: 'auto', visibility: 'visible' });
      submenus.forEach((el) => {
        el.classList.add('hidden');
        gsap.set(el, { position: 'absolute', top: 0, left: 0, right: 0, autoAlpha: 0, filter: 'blur(8px)', pointerEvents: 'none', visibility: 'hidden' });
      });
      // Keep numeric height to current root height (+16px)
      if (viewsWrap && rootView) gsap.set(viewsWrap, { height: (rootView.scrollHeight || 0) + 16 });
    }

    function openPanel() {
      if (isOpen) return;
      isOpen = true;
      if (overlay) overlay.classList.remove('hidden');
      panel.classList.remove('hidden');
      panel.setAttribute('aria-hidden', 'false');
      hamburger.setAttribute('aria-expanded', 'true');
      hamburger.classList.add('open');
      docBody.classList.add('overflow-hidden');

      const tl = gsap.timeline({ defaults: { duration: 0.35, ease: 'power2.out' } });
      tl.set(panel, { willChange: 'transform, opacity, filter' })
        .fromTo(panel, { x: '100%', autoAlpha: 0, filter: 'blur(16px)' }, { x: 0, autoAlpha: 1, filter: 'blur(0px)', pointerEvents: 'auto' }, 0)
        .set(panel, { willChange: '' });
      if (overlay) tl.to(overlay, { autoAlpha: 1, duration: 0.5 }, 0);

      // Set container to current root numeric height on open (+16px)
      if (rootView) {
        // eslint-disable-next-line no-unused-expressions
        rootView.offsetHeight;
        gsap.set(viewsWrap, { height: (rootView.scrollHeight || 0) + 16 });
      }
    }

    function closePanel(immediate = false) {
      if (!isOpen && !immediate) return;
      isOpen = false;

      const completeHide = () => {
        panel.classList.add('hidden');
        if (overlay) overlay.classList.add('hidden');
        panel.setAttribute('aria-hidden', 'true');
        hamburger.setAttribute('aria-expanded', 'false');
        hamburger.classList.remove('open');
        docBody.classList.remove('overflow-hidden');
        resetToRootView();
      };

      if (immediate) {
        gsap.set(panel, { x: '100%', autoAlpha: 0, filter: 'blur(16px)', pointerEvents: 'none' });
        if (overlay) gsap.set(overlay, { autoAlpha: 0, pointerEvents: 'none' });
        completeHide();
        return;
      }

      const tl = gsap.timeline({ defaults: { duration: 0.5, ease: 'power2.in' } });
      tl.set(panel, { willChange: 'transform, opacity, filter' })
        .to(panel, { x: '100%', autoAlpha: 0, filter: 'blur(16px)', pointerEvents: 'none' }, 0)
        .add(completeHide)
        .set(panel, { willChange: '' });
      if (overlay) tl.to(overlay, { autoAlpha: 0, duration: 0.25 }, 0);
    }

    // Events
    hamburger.addEventListener('click', () => {
      if (isOpen) closePanel(); else openPanel();
    });

    if (overlay) overlay.addEventListener('click', () => closePanel());

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') closePanel();
    });

    // Auto-close on desktop
    const mql = window.matchMedia('(min-width: 1280px)');
    const handleMql = (e) => { if (e.matches) closePanel(true); };
    if (typeof mql.addEventListener === 'function') {
      mql.addEventListener('change', handleMql);
    } else {
      let lastMatch = mql.matches;
      const onResize = () => {
        if (mql.matches !== lastMatch) {
          lastMatch = mql.matches;
          handleMql({ matches: lastMatch });
        }
      };
      window.addEventListener('resize', onResize);
    }
  }
}
