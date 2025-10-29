// Masonry Gallery module: JS-driven layout + emoji bubbles

function initMasonry(root = document) {
  const MIN = 250; // px
  const MAX = 600; // px
  const GAP = 16; // constant gap between items (px)

  const grids = root.querySelectorAll('.masonry-grid');

  grids.forEach((grid) => {
    Object.assign(grid.style, {
      position: 'relative',
      display: 'block',
      padding: '0',
      margin: '0',
      listStyle: 'none',
    });

    const items = Array.from(grid.querySelectorAll('[data-masonry-item]'));

    items.forEach((it) => {
      Object.assign(it.style, { position: 'absolute', margin: '0' });
      const content = it.querySelector('.masonry-content');
      if (content) content.style.height = '';
    });

    const getColumnCount = () => {
      const w = grid.clientWidth;
      if (w >= 1280) return 4; // xl
      if (w >= 1024) return 3; // lg
      if (w >= 640) return 2; // sm
      return 1; // base
    };

    const getItemHeight = (it, columnWidth) => {
      const img = it.querySelector('img');
      if (!img || !img.naturalWidth || !img.naturalHeight) return MIN;
      const desired = (img.naturalHeight / img.naturalWidth) * columnWidth;
      return Math.max(MIN, Math.min(MAX, Math.round(desired)));
    };

    const layout = () => {
      const colCount = getColumnCount();
      const gridWidth = grid.clientWidth;
      if (gridWidth <= 0 || colCount <= 0) return;

      const colWidth = Math.floor((gridWidth - GAP * (colCount - 1)) / colCount);

      // First pass: assign to columns (shortest-first) and record base/final heights
      const colHeights = new Array(colCount).fill(0);
      const cols = Array.from({ length: colCount }, () => []);

      items.forEach((it) => {
        const baseH = getItemHeight(it, colWidth);
        let col = 0;
        for (let i = 1; i < colCount; i++) if (colHeights[i] < colHeights[col]) col = i;
        cols[col].push({ el: it, content: it.querySelector('.masonry-content'), base: baseH, final: baseH });
        colHeights[col] += baseH + GAP;
      });

      const used = cols.map((arr) => arr.length > 0);
      if (!used.some(Boolean)) { grid.style.height = '0px'; return; }

      const bottoms = colHeights.map((h, i) => (used[i] ? h - GAP : 0));
      const upCaps = cols.map((arr, i) => (used[i] ? arr.reduce((acc, r) => acc + Math.max(0, MAX - r.base), 0) : 0));
      const downCaps = cols.map((arr, i) => (used[i] ? arr.reduce((acc, r) => acc + Math.max(0, r.base - MIN), 0) : 0));

      const maxBottom = Math.max(...bottoms.filter((_, i) => used[i]));
      const lowerBound = Math.max(...bottoms.map((b, i) => (used[i] ? b - downCaps[i] : 0)));
      const upperBound = Math.min(...bottoms.map((b, i) => (used[i] ? b + upCaps[i] : Infinity)));

      let target = maxBottom;
      if (upperBound < target) target = upperBound; // need to shrink some columns
      if (target < lowerBound) target = lowerBound; // need to grow some columns

      // Adjust columns from bottom upwards to reach target within [MIN, MAX]
      for (let i = 0; i < colCount; i++) {
        if (!used[i]) continue;
        const col = cols[i];
        const current = bottoms[i];
        if (current < target) {
          let need = target - current;
          for (let k = col.length - 1; k >= 0 && need > 0; k--) {
            const r = col[k];
            const cap = Math.max(0, MAX - r.final);
            if (cap <= 0) continue;
            const add = Math.min(cap, need);
            r.final += add;
            need -= add;
          }
        } else if (current > target) {
          let need = current - target;
          for (let k = col.length - 1; k >= 0 && need > 0; k--) {
            const r = col[k];
            const cap = Math.max(0, r.final - MIN);
            if (cap <= 0) continue;
            const sub = Math.min(cap, need);
            r.final -= sub;
            need -= sub;
          }
        }
      }

      // Second pass: render positions with final heights
      let containerHeight = 0;
      for (let i = 0; i < colCount; i++) {
        let y = 0;
        const x = i * (colWidth + GAP);
        const col = cols[i];
        for (let k = 0; k < col.length; k++) {
          const r = col[k];
          if (r.content) r.content.style.height = r.final + 'px';
          r.el.style.width = colWidth + 'px';
          r.el.style.left = x + 'px';
          r.el.style.top = y + 'px';
          y += r.final + GAP;
        }
        if (col.length) containerHeight = Math.max(containerHeight, y - GAP);
      }

      grid.style.height = (containerHeight > 0 ? containerHeight : 0) + 'px';
    };

    // Relayout when images load
    const imgs = grid.querySelectorAll('img');
    imgs.forEach((img) => {
      if (img.complete && img.naturalWidth) return; // already has size
      img.addEventListener('load', layout, { once: true });
    });

    if ('ResizeObserver' in window) {
      const ro = new ResizeObserver(layout);
      ro.observe(grid);
    } else {
      window.addEventListener('resize', layout);
      window.addEventListener('orientationchange', layout);
    }

    layout();
  });
}

function initEmojiBubbles(root = document) {
  const sections = root.querySelectorAll('section[data-emojis]');
  const parseEmojiAttr = (val) => {
    if (!val) return [];
    try { return JSON.parse(val); } catch (e) {
      try { return JSON.parse(val.replace(/&quot;/g, '"')); } catch (_) { return []; }
    }
  };

  sections.forEach((sec) => {
    const urls = parseEmojiAttr(sec.getAttribute('data-emojis'));
    if (!urls || !urls.length) return;
    const defaultCount = parseInt(sec.getAttribute('data-emoji-count') || '6', 10);
    const cards = sec.querySelectorAll('.masonry-content');

    const pick = () => urls[Math.floor(Math.random() * urls.length)];

    const spawnOne = (layer) => {
      const rect = layer.getBoundingClientRect();
      const size = Math.round(16 + Math.random() * 16);
      const x = Math.max(size / 2, Math.min(rect.width - size / 2, Math.random() * rect.width));
      const yStart = rect.height + size;

      const node = document.createElement('img');
      node.src = pick();
      node.alt = '';
      Object.assign(node.style, {
        position: 'absolute', left: x + 'px', top: yStart + 'px', width: size + 'px', height: size + 'px',
        transform: 'translate(-50%, 0) translate(0px, 0px)', willChange: 'transform, opacity', pointerEvents: 'none', opacity: '0.65',
        filter: 'drop-shadow(0 2px 2px rgba(0,0,0,0.15))',
      });
      node.className = 'emoji-bubble';
      layer.appendChild(node);

      const speed = 90 + Math.random() * 90;
      const amp = 6 + Math.random() * 8;
      const freq = 0.3 + Math.random() * 0.4;
      const rot = (Math.random() - 0.5) * 12;
      let dy = 0;
      let t0 = performance.now();
      let time = 0;

      const step = (t) => {
        const dt = Math.min(0.032, (t - t0) / 1000);
        t0 = t;
        time += dt;
        dy -= speed * dt;
        const drift = Math.sin(time * 2 * Math.PI * freq) * amp;
        node.style.transform = `translate(-50%, 0) translate(${drift}px, ${dy}px) rotate(${rot}deg)`;
        if (yStart + dy <= -size) {
          node.style.transition = 'opacity 240ms ease-out';
          node.style.opacity = '0';
          setTimeout(() => node.remove(), 260);
          return;
        }
        requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
    };

    cards.forEach((card) => {
      if (card.__emojiBubblesInit) return;
      card.__emojiBubblesInit = true;
      const layer = card.querySelector('.emoji-layer');
      if (!layer) return;

      const start = () => {
        if (layer.__bubbleTimer) return;
        const rate = Math.max(80, Math.round(2000 / Math.max(1, defaultCount)));
        spawnOne(layer);
        layer.__bubbleTimer = setInterval(() => { spawnOne(layer); }, rate);
      };
      const stop = () => {
        if (layer.__bubbleTimer) { clearInterval(layer.__bubbleTimer); layer.__bubbleTimer = null; }
      };

      card.addEventListener('mouseenter', start);
      card.addEventListener('mouseleave', stop);
    });
  });
}

export function initMasonryGallery(root = document) {
  initMasonry(root);
  initEmojiBubbles(root);
}

export { initMasonry, initEmojiBubbles };

export default initMasonryGallery;
