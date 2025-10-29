import { gsap } from 'gsap';
import '../css/styles.css';
import { initInfiniteScroll } from './modules/infiniteScroll';
import { initButtonBurst } from './modules/buttonBurst';
import { initScrollAnimations } from './modules/scrollAnimations';
import { initCounterUp } from './modules/counterUp';
import { initLightbox } from './modules/lightbox';
import { initMasonryGallery } from './modules/masonryGallery';
import { initMobileMenu } from './modules/mobileMenu';
import { initHeaderScroll } from './modules/headerScroll';
import { initContentToggle } from './modules/contentToggle';

addEventListener('DOMContentLoaded', function() {
  console.log('🔧 Webpack entry file loaded');
  initScrollAnimations();
  // Initialize infinite scroll marquees
  initInfiniteScroll(document);

  // Initialize button burst hover/click effect
  initButtonBurst(document);

  // Initialize counter up animations
  initCounterUp(document);

  // Initialize lightbox for photos
  initLightbox(document);

  // Initialize masonry gallery sizing and emoji bubbles
  initMasonryGallery(document);

  // Initialize GSAP-powered mobile menu transitions
  initMobileMenu(document);

  // Initialize content toggle (Lire plus)
  initContentToggle(document);


  // Toggle header background/shadow on scroll
  initHeaderScroll(document, { threshold: 32 });
});
