import { gsap } from 'gsap';

// Initialize all marquees (logos or photos) inside a root (defaults to document)
export function initInfiniteScroll(root = document) {
  const marquees = root.querySelectorAll('.logo[data-speed], .photo-marquee[data-speed]');
  marquees.forEach((container) => {
    if (container.__logoInitialized) return; // prevent double init
    container.__logoInitialized = true;

    const ul = container.querySelector('ul');
    if (!ul) return;

    let baseCount = parseInt(ul.getAttribute('data-base-count') || '0', 10);
    if (!baseCount || baseCount <= 0) baseCount = ul.children.length;

    let speed = parseFloat(container.getAttribute('data-speed')) || 20; // seconds per loop of one cycle
    let cycleWidth = 0; // width of one base cycle (first baseCount items)
    let pxPerSec = 0;
    let paused = false;
    let x = 0;

    // Soft pause/resume state (tweened factor 0..1)
    const state = { factor: 1 };
    let speedTween = null;

    const cloneOneCycle = () => {
      for (let i = 0; i < baseCount; i++) {
        const li = ul.children[i].cloneNode(true);
        ul.appendChild(li);
      }
    };

    const ensureAtLeastTwoCycles = () => {
      while (ul.children.length < baseCount * 2) cloneOneCycle();
    };

    const measureCycleWidth = () => {
      if (ul.children.length < baseCount + 1) return 0;
      const first = ul.children[0];
      const afterCycle = ul.children[baseCount];
      const firstLeft = first.offsetLeft;
      const nextLeft = afterCycle.offsetLeft;
      const width = nextLeft - firstLeft;
      return width > 0 ? width : afterCycle.getBoundingClientRect().left - first.getBoundingClientRect().left;
    };

    const ensureCoverage = () => {
      ensureAtLeastTwoCycles();
      cycleWidth = measureCycleWidth();
      if (cycleWidth <= 0) return; // will be recalculated when images load
      const requiredCycles = Math.max(3, Math.ceil((window.innerWidth * 3) / cycleWidth));
      while (ul.children.length < baseCount * requiredCycles) cloneOneCycle();
      cycleWidth = measureCycleWidth();
      pxPerSec = cycleWidth / (speed || 20);
    };

    const recalc = () => {
      x = 0;
      gsap.set(ul, { x });
      ensureCoverage();
    };

    // Initial setup
    recalc();

    // Recalculate when images load (logos/photos)
    const imgs = ul.querySelectorAll('img');
    imgs.forEach(img => {
      if (!img.complete) {
        img.addEventListener('load', recalc, { once: true });
        img.addEventListener('error', recalc, { once: true });
      }
    });

    // Recalculate on window resize (debounced)
    let resizeTimer;
    const onResize = () => {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(recalc, 150);
    };
    window.addEventListener('resize', onResize);

    // Smooth, seamless loop using GSAP ticker on a single <ul>
    let last = performance.now();
    const tick = () => {
      if (cycleWidth === 0) {
        last = performance.now();
        return;
      }
      const now = performance.now();
      const dt = (now - last) / 1000;
      last = now;

      const v = pxPerSec * state.factor; // tweened speed
      x -= v * dt;
      if (x <= -cycleWidth) {
        x += cycleWidth; // wrap by exactly one cycle
      }
      gsap.set(ul, { x });
    };

    gsap.ticker.add(tick);

    // Pause on hover (soft, 500ms)
    const hoverTarget = container.closest('section') || container;
    const setPaused = (p) => {
      if (speedTween) speedTween.kill();
      const target = p ? 0 : 1;
      speedTween = gsap.to(state, { factor: target, duration: 1, ease: 'power2.out' });
    };
    const onEnter = () => { setPaused(true); };
    const onLeave = () => { setPaused(false); };
    hoverTarget.addEventListener('mouseenter', onEnter);
    hoverTarget.addEventListener('mouseleave', onLeave);

    // Update speed dynamically if data-speed changes later
    const observer = new MutationObserver(() => {
      const newSpeed = parseFloat(container.getAttribute('data-speed')) || speed;
      if (newSpeed !== speed) {
        speed = newSpeed;
        pxPerSec = cycleWidth / (speed || 20);
      }
    });
    observer.observe(container, { attributes: true, attributeFilter: ['data-speed'] });

    // Store cleanup if needed later
    container.__logoCleanup = () => {
      window.removeEventListener('resize', onResize);
      hoverTarget.removeEventListener('mouseenter', onEnter);
      hoverTarget.removeEventListener('mouseleave', onLeave);
      observer.disconnect();
      if (speedTween) speedTween.kill();
    };
  });
}
