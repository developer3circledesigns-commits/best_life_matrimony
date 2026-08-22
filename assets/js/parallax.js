/* Parallax engine — reproduces HeroSection + IntroSection Framer motion transforms with vanilla JS
   - Hero: videoY 0→80 over scrollY 0→800, contentY 0→-40 over 0→600, opacity 1→0 over 0→400, scale 1→0.96 over 0→500 (with spring-like lerp)
   - Intro: y 30→-30 and opacity 0→1→1→0 based on section scrollProgress
   Respects prefers-reduced-motion.
*/
(function(){
  const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  if (prefersReduced) return;

  const heroBg = document.querySelector('[data-hero-bg]');
  const heroContent = document.querySelector('[data-hero-content]');
  const heroVideo = document.querySelector('[data-hero-video]');
  const introSection = document.querySelector('[data-intro-section]');
  const introInner = document.querySelector('[data-intro-inner]');

  // hero spring-like smoothing: lerp
  let currentVideoY = 0, targetVideoY = 0;
  let currentContentY = 0, targetContentY = 0;
  let currentOpacity = 1, targetOpacity = 1;
  let currentScale = 1, targetScale = 1;

  function clamp(v, min, max){ return Math.max(min, Math.min(max, v)); }
  function mapRange(value, inMin, inMax, outMin, outMax){
    const t = clamp((value - inMin) / (inMax - inMin), 0, 1);
    return outMin + t * (outMax - outMin);
  }
  // ease out for more premium feel
  function easeOutCubic(t){ return 1 - Math.pow(1 - t, 3); }

  let ticking = false;
  let scrollY = window.scrollY;

  function onScrollFrame(){
    ticking = false;
    scrollY = window.scrollY; // fallback; lenis value will also be window.scrollY synced via raf
    // Use Lenis scroll if available for accuracy
    if (window.__lenis && typeof window.__lenis.scroll === 'number') scrollY = window.__lenis.scroll;

    // Hero targets
    targetVideoY = mapRange(scrollY, 0, 800, 0, 80);
    targetContentY = mapRange(scrollY, 0, 600, 0, -40);
    // opacity mapping piecewise linear: [0,400] => 1 to 0
    targetOpacity = clamp(1 - (scrollY / 400), 0, 1);
    // scale 1 -> 0.96
    targetScale = mapRange(scrollY, 0, 500, 1, 0.96);
  }

  function lerp(a,b,t){ return a + (b - a) * t; }

  function render(){
    // spring damping ~0.12 for video, .14 for content
    currentVideoY = lerp(currentVideoY, targetVideoY, 0.09);
    currentContentY = lerp(currentContentY, targetContentY, 0.12);
    currentOpacity = lerp(currentOpacity, targetOpacity, 0.14);
    currentScale = lerp(currentScale, targetScale, 0.12);

    if (heroBg) heroBg.style.transform = 'translate3d(0,'+ currentVideoY.toFixed(2) +'px,0)';
    if (heroContent) {
      heroContent.style.transform = 'translate3d(0,'+ currentContentY.toFixed(2) +'px,0) scale('+ currentScale.toFixed(4) +')';
      heroContent.style.opacity = currentOpacity.toFixed(3);
    }
    // Intro parallax based on scrollProgress of section
    if (introSection && introInner) {
      const rect = introSection.getBoundingClientRect();
      const vpH = window.innerHeight;
      // progress 0 when top at bottom of viewport, 1 when bottom at top
      const progress = clamp(1 - (rect.top + rect.height) / (vpH + rect.height) + 0.5, 0, 1);
      // Actually simpler: offset ["start end","end start"] => scrollProgress 0..1
      // Approx via rect.top
      const start = vpH; // when rect.top = vpH => 0
      const end = -rect.height; // when rect.top = -rect.height => 1
      const p = clamp((vpH - rect.top) / (vpH + rect.height), 0, 1);
      const y = mapRange(p, 0, 1, 30, -30);
      let op = 1;
      if (p < 0.2) op = mapRange(p, 0, 0.2, 0, 1);
      else if (p > 0.8) op = mapRange(p, 0.8, 1, 1, 0);
      introInner.style.transform = 'translate3d(0,'+ y.toFixed(2) +'px,0)';
      introInner.style.opacity = op.toFixed(3);
    }
    requestAnimationFrame(render);
  }

  function requestTick(){
    if (!ticking){ ticking=true; requestAnimationFrame(onScrollFrame); }
  }

  // attach scroll listeners
  if (window.__lenis && window.__lenis.on) window.__lenis.on('scroll', requestTick);
  window.addEventListener('scroll', requestTick, {passive:true});
  window.addEventListener('resize', requestTick, {passive:true});
  // init
  onScrollFrame();
  requestAnimationFrame(render);

  // video autoplay safety (mirror React effect)
  if (heroVideo) {
    heroVideo.play().catch(function(){});
  }
})();
