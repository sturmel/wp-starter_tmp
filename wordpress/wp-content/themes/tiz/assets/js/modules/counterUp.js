export function initCounterUp(root = document) {
  const counters = root.querySelectorAll('[data-counter]');
  if (!counters.length) return;

  const rafMap = new WeakMap();

  const setZero = (el, decimals = 0) => {
    el.textContent = decimals > 0 ? (0).toFixed(decimals) : '0';
  };

  function burst(el) {
    // create a small celebratory burst near the number
    const rect = el.getBoundingClientRect();
    const origin = { x: rect.left + rect.width / 2, y: rect.top + rect.height / 2 };
    const count = 8;
    const frag = document.createDocumentFragment();
    const nodes = [];
    for (let i = 0; i < count; i++) {
      const s = Math.floor(4 + Math.random() * 6);
      const node = document.createElement('span');
      node.className = 'counter-burst';
      Object.assign(node.style, {
        position: 'fixed',
        left: `${origin.x}px`,
        top: `${origin.y}px`,
        width: `${s}px`,
        height: `${s}px`,
        background: 'rgba(255,255,255,0.9)',
        borderRadius: '9999px',
        pointerEvents: 'none',
        opacity: '0.95',
        transform: 'translate(-50%, -50%) scale(0.6)',
        willChange: 'transform, opacity',
        zIndex: 9999,
        mixBlendMode: 'screen',
        boxShadow: '0 0 8px rgba(255,255,255,0.5)'
      });
      frag.appendChild(node);
      nodes.push(node);
    }
    document.body.appendChild(frag);

    nodes.forEach((node) => {
      const angle = Math.random() * Math.PI * 2;
      const dist = 24 + Math.random() * 40;
      const dx = Math.cos(angle) * dist;
      const dy = Math.sin(angle) * dist;
      const dur = 400 + Math.random() * 250;

      const start = performance.now();
      const anim = (now) => {
        const t = Math.min(1, (now - start) / dur);
        const eased = 1 - Math.pow(1 - t, 2);
        node.style.transform = `translate(calc(-50% + ${dx * eased}px), calc(-50% + ${dy * eased}px)) scale(${0.6 + 0.2 * (1 - t)})`;
        node.style.opacity = String(0.95 * (1 - t));
        if (t < 1) requestAnimationFrame(anim);
        else node.remove();
      };
      requestAnimationFrame(anim);
    });
  }

  const io = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        const el = entry.target;
        if (entry.isIntersecting) {
          if (el.dataset.counted === '1') return;
          el.dataset.counted = '1';

          const to = parseFloat(el.dataset.target || '0');
          const finalText = el.dataset.final || `${to}`;
          const hasDecimal = /\./.test(finalText);
          const decimals = el.dataset.decimals
            ? parseInt(el.dataset.decimals, 10)
            : hasDecimal
            ? (finalText.split('.')[1] || '').length
            : 0;

          // Resolve timing from item or group defaults
          const group = el.closest('[data-counter-group]');
          const idx = group ? Array.from(group.querySelectorAll('[data-counter]')).indexOf(el) : 0;
          const groupStagger = group && group.dataset.stagger ? parseInt(group.dataset.stagger, 10) : 120;
          const groupDuration = group && group.dataset.durationDefault ? parseInt(group.dataset.durationDefault, 10) : 1600;
          const duration = el.dataset.duration ? parseInt(el.dataset.duration, 10) : groupDuration;
          const delay = el.dataset.delay ? parseInt(el.dataset.delay, 10) : idx * groupStagger;

          const easeOutCubic = (t) => 1 - Math.pow(1 - t, 3);

          const startTime = performance.now() + delay;
          const from = 0;

          if (rafMap.has(el)) {
            cancelAnimationFrame(rafMap.get(el));
            rafMap.delete(el);
          }

          function tick(now) {
            if (now < startTime) {
              const id = requestAnimationFrame(tick);
              rafMap.set(el, id);
              return;
            }
            const elapsed = now - startTime;
            const t = Math.min(1, elapsed / duration);
            const eased = easeOutCubic(t);
            const current = from + (to - from) * eased;
            el.textContent = decimals > 0 ? current.toFixed(decimals) : Math.round(current).toString();
            if (t < 1) {
              const id = requestAnimationFrame(tick);
              rafMap.set(el, id);
            } else {
              el.textContent = finalText; // Ensure final formatting
              burst(el);
              if (rafMap.has(el)) rafMap.delete(el);
            }
          }

          // Initialize display
          setZero(el, decimals);
          const id = requestAnimationFrame(tick);
          rafMap.set(el, id);
        } else {
          // Leaving viewport: reset to 0 so it can replay next time
          if (rafMap.has(entry.target)) {
            cancelAnimationFrame(rafMap.get(entry.target));
            rafMap.delete(entry.target);
          }
          const decimals = entry.target.dataset.decimals ? parseInt(entry.target.dataset.decimals, 10) : 0;
          setZero(entry.target, decimals);
          entry.target.dataset.counted = '0';
        }
      });
    },
    { threshold: 0.3 }
  );

  counters.forEach((el) => {
    // do not auto-assign delay here; we use group stagger
    el.dataset.counted = el.dataset.counted || '0';
    io.observe(el);
  });
}
