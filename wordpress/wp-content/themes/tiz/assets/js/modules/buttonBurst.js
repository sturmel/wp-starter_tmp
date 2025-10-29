import { gsap } from 'gsap';

function getStartPoint(evt, el) {
  const rect = el.getBoundingClientRect();
  const cx = rect.left + rect.width / 2;
  const cy = rect.top + rect.height / 2;
  const ex = (evt && typeof evt.clientX === 'number') ? evt.clientX : cx;
  const ey = (evt && typeof evt.clientY === 'number') ? evt.clientY : cy;
  return { x: ex, y: ey };
}

function getBubbleColor(el) {
  const cs = window.getComputedStyle(el);
  // Prefer button bg color, fallback to currentColor (text) or white
  const bg = cs.backgroundColor && cs.backgroundColor !== 'rgba(0, 0, 0, 0)'
    ? cs.backgroundColor
    : cs.color || 'rgba(255,255,255,0.9)';
  return bg;
}

// Helpers used for background sampling and luminance checks
function parseColor(str) {
  if (!str) return null;
  const s = String(str).trim();
  let m = s.match(/^rgba?\(([^)]+)\)$/i);
  if (m) {
    const parts = m[1].split(',').map(v => v.trim());
    if (parts.length >= 3) {
      const r = Math.max(0, Math.min(255, parseFloat(parts[0])));
      const g = Math.max(0, Math.min(255, parseFloat(parts[1])));
      const b = Math.max(0, Math.min(255, parseFloat(parts[2])));
      const a = parts.length >= 4 ? Math.max(0, Math.min(1, parseFloat(parts[3]))) : 1;
      return { r, g, b, a };
    }
  }
  m = s.match(/^#([0-9a-f]{3}|[0-9a-f]{6})$/i);
  if (m) {
    let hex = m[1];
    if (hex.length === 3) hex = hex.split('').map(c => c + c).join('');
    const num = parseInt(hex, 16);
    return { r: (num >> 16) & 255, g: (num >> 8) & 255, b: num & 255, a: 1 };
  }
  return null;
}
function srgbToLinear(c) { c /= 255; return c <= 0.04045 ? c / 12.92 : Math.pow((c + 0.055) / 1.055, 2.4); }
function relativeLuminance(rgb) {
  if (!rgb) return 1;
  const r = srgbToLinear(rgb.r), g = srgbToLinear(rgb.g), b = srgbToLinear(rgb.b);
  return 0.2126 * r + 0.7152 * g + 0.0722 * b;
}
function getOpaqueBgUpTree(el) {
  while (el && el !== document.documentElement) {
    const bg = getComputedStyle(el).backgroundColor;
    const c = parseColor(bg);
    if (c && c.a > 0) return bg;
    el = el.parentElement;
  }
  return getComputedStyle(document.body).backgroundColor || 'rgb(255,255,255)';
}
function getBackgroundColorAtPoint(x, y) {
  let el = document.elementFromPoint(x, y);
  // Avoid sampling our particles
  if (el && el.classList && el.classList.contains('btn-burst-particle')) {
    el = el.previousElementSibling || el.parentElement;
  }
  return getOpaqueBgUpTree(el);
}
// sampleBackgroundRing was only used for debugging; keeping for future needs but unused
function sampleBackgroundRing(x, y, radius = 28, samples = 12) {
  let light = 0, dark = 0, total = 0;
  const TWO_PI = Math.PI * 2;
  for (let i = 0; i < samples; i++) {
    const a = (i / samples) * TWO_PI;
    const sx = x + Math.cos(a) * radius;
    const sy = y + Math.sin(a) * radius;
    if (sx < 0 || sy < 0 || sx > window.innerWidth || sy > window.innerHeight) continue;
    const bg = getBackgroundColorAtPoint(sx, sy);
    const parsed = parseColor(bg);
    const lum = parsed ? relativeLuminance(parsed) : 1;
    const isLight = lum >= 0.8;
    if (isLight) light++; else dark++;
    total++;
  }
  return { light, dark, total };
}

function burstFrom(el, evt) {
  // Throttle per element
  const now = Date.now();
  if (el.__lastBurst && now - el.__lastBurst < 400) return;
  el.__lastBurst = now;

  const origin = getStartPoint(evt, el);
  const color = getBubbleColor(el);

  const bubbles = [];
  const count = 12; // moderate for perf

  for (let i = 0; i < count; i++) {
    const s = Math.floor(6 + Math.random() * 8); // 6-14px
    const node = document.createElement('span');
    node.className = 'btn-burst-particle';

    // Decide per-particle blend based on background along its initial direction
    const angle = Math.random() * Math.PI * 2; // 0..360°
    const dist = 40 + Math.random() * 90; // px
    const dx = Math.cos(angle) * dist;
    const dy = Math.sin(angle) * dist;
    const sampleDist = Math.min(48, dist);
    const sx = origin.x + Math.cos(angle) * sampleDist;
    const sy = origin.y + Math.sin(angle) * sampleDist;
    const sampleBg = getBackgroundColorAtPoint(sx, sy);
    const sampleParsed = parseColor(sampleBg);
    const sampleLum = sampleParsed ? relativeLuminance(sampleParsed) : 1;
    const sampleIsLight = sampleLum >= 0.8;

    const pBlend = sampleIsLight ? 'normal' : 'screen';
    let pColor = color;
    if (sampleIsLight) {
      const pc = parseColor(pColor);
      if (!pc || relativeLuminance(pc) > 0.65) {
        pColor = 'rgba(0,0,0,0.35)';
      }
    }

    Object.assign(node.style, {
      position: 'fixed',
      left: `${origin.x}px`,
      top: `${origin.y}px`,
      width: `${s}px`,
      height: `${s}px`,
      background: pColor,
      borderRadius: '9999px',
      pointerEvents: 'none',
      opacity: '0.9',
      transform: 'translate(-50%, -50%) scale(0.7)',
      willChange: 'transform, opacity',
      zIndex: 9999,
      mixBlendMode: pBlend,
      boxShadow: '0 0 10px rgba(255,255,255,0.4)'
    });
    document.body.appendChild(node);
    bubbles.push(node);

    const dur = 0.55 + Math.random() * 0.35;

    gsap.to(node, {
      x: `+=${dx}`,
      y: `+=${dy}`,
      opacity: 0,
      scale: 0.2 + Math.random() * 0.5,
      ease: 'power2.out',
      duration: dur,
      onComplete: () => {
        node.remove();
      }
    });
  }
}

export function initButtonBurst(root = document) {
  const btns = root.querySelectorAll('.animate-button');
  btns.forEach((btn) => {
    if (btn.__burstInitialized) return;
    btn.__burstInitialized = true;

    const handler = (e) => burstFrom(btn, e);
    btn.addEventListener('mouseenter', handler);
    btn.addEventListener('click', handler);

    // Cleanup hook if needed later
    btn.__burstCleanup = () => {
      btn.removeEventListener('mouseenter', handler);
      btn.removeEventListener('click', handler);
    };
  });
}
