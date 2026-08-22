/* Lenis smooth scroll — single instance, exact RootLayout config */
(function(){
  if (typeof Lenis === 'undefined') {
    // CDN may expose as window.Lenis
    if (typeof window.Lenis !== 'undefined') window.Lenis = window.Lenis;
    else { console.warn('Lenis not loaded'); return; }
  }
  const LenisCtor = window.Lenis || Lenis;
  // Respect reduced motion
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReduced) {
    // still expose stub so parallax code doesn't break
    window.__lenis = { scroll: window.scrollY, on: function(){}, off: function(){} , raf: function(){}, destroy:function(){} };
    return;
  }
  const lenis = new LenisCtor({
    duration: 1.2,
    easing: function(t){ return Math.min(1, 1.001 - Math.pow(2, -10 * t)); },
    orientation: 'vertical',
    gestureOrientation: 'vertical',
    smoothWheel: true,
    touchMultiplier: 2,
    syncTouch: true,
    syncTouchLerp: 0.075,
    lerp: 0.1,
    infinite: false,
  });
  window.__lenis = lenis;
  function raf(time){
    lenis.raf(time);
    requestAnimationFrame(raf);
  }
  requestAnimationFrame(raf);
  // Anchor smooth scrolling via Lenis — intercept hash links
  document.addEventListener('click', function(e){
    const a = e.target.closest('a[href^="#"]');
    if (!a) return;
    const id = a.getAttribute('href');
    if (id.length > 1) {
      const target = document.querySelector(id);
      if (target) { e.preventDefault(); lenis.scrollTo(target, { offset: -64 }); }
    }
  });
  // Expose scroll value for parallax readers that check lenis.scroll
  // Lenis already provides .scroll ; keep synced
})();
