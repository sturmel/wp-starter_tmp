// Toggle header background and shadow based on scroll position
// At top: transparent; after 2rem (32px by default): bg-black-haze-50 + shadow

export function initHeaderScroll(root = document, { threshold = '2rem' } = {}) {
  const header = root.querySelector('#site-header');
  const mobilePanel = root.querySelector('#mobile-menu-panel');
  const topbar = root.querySelector('#site-topbar') || root.querySelector('#header-topbar');
  // We'll translate the whole header instead of individual inner elements
  if (!header && !mobilePanel && !topbar) return;

  const shadowClass = 'shadow-[1rem_0px_1rem_rgba(35,58,124,0.15)]';

  const toPx = (t) => {
    if (typeof t === 'number') return t;
    if (typeof t === 'string' && t.endsWith('rem')) {
      const n = parseFloat(t);
      const base = parseFloat(getComputedStyle(document.documentElement).fontSize) || 16;
      return n * base;
    }
    return parseFloat(t) || 32; // fallback
  };

  const thresholdPx = toPx(threshold);

  let topbarHeight = 0;
  const measureTopbar = () => {
    topbarHeight = topbar ? topbar.getBoundingClientRect().height : 0;
  };
  measureTopbar();
  window.addEventListener('resize', () => {
    measureTopbar();
    // Reset transform when resizing below md
    if (topbar && window.innerWidth < 768) {
      topbar.style.transform = '';
    }
  });

  let lastState = null;
  let initialBounceScheduled = false;

  function playStarBounce() {
    if (!topbar) return;
    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
    const stars = topbar.querySelectorAll('.star-icon');
    if (!stars.length) return;
    stars.forEach((el) => {
      el.style.transition = 'none';
      el.style.transformOrigin = 'center';
      el.style.opacity = '0';
      el.style.transform = 'translateY(-10px)';
    });
    // Force reflow to apply initial styles
    // eslint-disable-next-line @typescript-eslint/no-unused-expressions
    topbar.offsetHeight;
    stars.forEach((el, i) => {
      const baseDelay = 200; // initial global delay requirement
      const stagger = i * 100; // spacing between stars
      const start = baseDelay + stagger;

      // Sequence: fade + drop, slight overshoot up, settle
      setTimeout(() => {
        el.style.transition = 'opacity 260ms ease-out, transform 260ms cubic-bezier(.25,.9,.35,1.4)';
        el.style.opacity = '1';
        el.style.transform = 'translateY(0)';
      }, start);

      // First rebound upward (small)
      setTimeout(() => {
        el.style.transition = 'transform 180ms cubic-bezier(.55,.15,.45,1)';
        el.style.transform = 'translateY(-4px)';
      }, start + 260);

      // Final settle with second mini bounce to 0
      setTimeout(() => {
        el.style.transition = 'transform 220ms cubic-bezier(.25,.8,.3,1)';
        el.style.transform = 'translateY(0)';
      }, start + 260 + 180);
    });
  }
  const update = () => {
    const scrolled = window.scrollY >= thresholdPx;

    // Header bg/shadow
    if (header) {
      header.classList.toggle('bg-transparent', !scrolled);
      header.classList.toggle('bg-black-haze-50', scrolled);
      if (scrolled) header.classList.add(shadowClass);
      else header.classList.remove(shadowClass);
    }

    // Mobile panel rounding: top => rounded-l-tiz, else => rounded-bl-tiz
    if (mobilePanel) {
      mobilePanel.classList.toggle('rounded-l-tiz', !scrolled);
      mobilePanel.classList.toggle('rounded-bl-tiz', scrolled);
    }

    if (header && topbar && window.innerWidth >= 768) {
      if (scrolled && lastState !== true) {
        header.style.transform = `translateY(-${topbarHeight}px)`;
        lastState = true;
      } else if (!scrolled && lastState !== false) {
        header.style.transform = 'translateY(0)';
        lastState = false;
        // Re-trigger bounce when returning to top (header reappears)
        playStarBounce();
      }
    } else {
      // reset transform for mobile or when no topbar
      if (header) header.style.transform = '';
    }
  };

  // Initial state
  update();
  if (!initialBounceScheduled) {
    initialBounceScheduled = true;
    // Schedule initial bounce only if we start at top (not scrolled) and desktop width
    if (window.scrollY < thresholdPx && window.innerWidth >= 768) {
      setTimeout(() => playStarBounce(), 50); // internal 800ms per star still applies
    }
  }

  // Listen to scroll
  window.addEventListener('scroll', update, { passive: true });
}
