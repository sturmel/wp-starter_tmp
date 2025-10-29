import { gsap } from 'gsap';

/**
 * GSAP "Lire plus" toggle (height + opacity + icon rotation).
 * Markup contract:
 *  - button[data-content-toggle="ID"]
 *  - #ID-extended (panel, has class 'hidden' when collapsed)
 *  - #ID-toggle-text (holds label, has data-more-text / data-less-text)
 *  - #ID-icon (arrow svg rotated 0/180)
 */
export function initContentToggle(context = document) {
  context.querySelectorAll('[data-content-toggle]').forEach(btn => {
    if (btn.__initToggle) return; // guard
    const id    = btn.getAttribute('data-content-toggle');
    const panel = context.querySelector(`#${id}-extended`);
    const txt   = context.querySelector(`#${id}-toggle-text`);
    const icon  = context.querySelector(`#${id}-icon`);
    if (!panel || !txt || !icon) return;
    btn.__initToggle = true;

    const collapsed = panel.classList.contains('hidden');
    gsap.set(panel, collapsed ? {height: 0, opacity: 0, overflow: 'hidden'} : {height: 'auto', opacity: 1});
    let open = !collapsed;

    btn.addEventListener('click', () => {
      if (open) {
        // Collapse
        const h = panel.getBoundingClientRect().height; // current full height
        gsap.timeline()
          .set(panel, {overflow: 'hidden'})
          .to(panel, {height: h, duration: 0}) // lock start
          .to(panel, {height: 0, opacity: 0, duration: 0.45, ease: 'power2.inOut'})
          .add(() => { panel.classList.add('hidden'); panel.style.height = '0px'; });
        rotate(icon, 0);
        swap(txt, txt.getAttribute('data-more-text'));
        open = false;
      } else {
        // Expand
        panel.classList.remove('hidden');
        const target = panel.scrollHeight; // natural height
        gsap.timeline()
          .set(panel, {height: 0, opacity: 0, overflow: 'hidden'})
          .to(panel, {height: target, duration: 0.45, ease: 'power2.out'})
          .to(panel, {opacity: 1, duration: 0.3, ease: 'power2.out'}, 0.05)
          .add(() => { panel.style.height = 'auto'; panel.style.overflow = 'visible'; });
        rotate(icon, 180);
        swap(txt, txt.getAttribute('data-less-text'));
        open = true;
      }
    });
  });
}

function swap(el, newText) {
  if (!newText) return;
  gsap.to(el, {
    opacity: 0,
    duration: 0.15,
    onComplete: () => {
      el.textContent = newText;
      gsap.to(el, {opacity: 1, duration: 0.2});
    }
  });
}

function rotate(icon, deg) {
  gsap.to(icon, {rotation: deg, duration: 0.3, ease: 'power2.out'});
}

// Legacy helper (still supported)
window.toggleExtendedContent = id => {
  const b = document.querySelector(`[data-content-toggle="${id}"]`);
  if (b) b.click();
};

