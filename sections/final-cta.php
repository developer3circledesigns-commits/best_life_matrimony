<?php // Final CTA Banner — after marquee2 ?>
<section id="final-cta" class="relative py-16 lg:py-20 overflow-hidden border-t border-white/10" style="background: linear-gradient(145deg, #3a0c15 0%, #4a1322 20%, #5e1e2e 35%, #6e2a2a 52%, #8a4a2f 68%, #a67d3a 82%, #c9a86a 90%, #e3c877 96%, #f6e6b4 100%);">
  <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(800px 400px at 50% 10%, rgba(255,246,232,0.07) 0%, transparent 70%), radial-gradient(600px 300px at 90% 90%, rgba(58,12,21,0.25) 0%, transparent 65%);"></div>
  <div id="cta-orb" class="absolute left-1/2 -translate-x-1/2 top-6 w-[600px] h-[300px] rounded-full bg-white/[0.04] blur-[60px] pointer-events-none will-change-transform"></div>

  <div class="relative max-w-4xl mx-auto px-6 text-center">
    <h2 class="font-serif text-[34px] sm:text-[44px] font-bold leading-[0.9] text-white">Ready to Meet<br><span class="italic text-[#f6e6b4]">Someone Special?</span></h2>
    <p class="mt-4 text-sm sm:text-base leading-7 text-white/70 max-w-2xl mx-auto">Create your BestLife Matrimony profile and take the first step towards a meaningful relationship.</p>

    <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
      <a href="<?php echo register_cta_href(); ?>" class="inline-flex h-12 px-8 rounded-none bg-white text-[#3a0c15] font-bold text-sm items-center gap-2 hover:bg-[#fff6e8] transition will-change-transform" style="will-change:transform; border-radius:0"><?php echo register_cta_label('My Profile', 'Register Now'); ?> →</a>
      <a href="./matches.php" class="inline-flex h-12 px-8 rounded-none bg-transparent border border-white/30 text-white font-bold text-sm items-center gap-2 hover:bg-white/10 hover:border-white/50 transition will-change-transform" style="will-change:transform; border-radius:0">Browse Matches →</a>
    </div>

    <p class="mt-8 font-serif italic text-sm tracking-wide text-white/60">BestLife Matrimony — Where Connections Become Beginnings.</p>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
<script>
(function(){
  // ensure buttons visible by default — fallback if GSAP fails
  function ensureVisible(){
    document.querySelectorAll("#final-cta a").forEach(function(a){
      if(getComputedStyle(a).opacity === "0"){
        a.style.opacity = "1";
        a.style.transform = "none";
      }
    });
  }
  setTimeout(ensureVisible, 900);
  if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if(typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined'){
    ensureVisible();
    return;
  }
  gsap.registerPlugin(ScrollTrigger);
  function initCta(){
    var lenis = window.__lenis;
    if(lenis && lenis.on) lenis.on('scroll', ScrollTrigger.update);
    // make sure buttons start visible then animate — from will set to 0 then to 1 when triggered
    gsap.set("#final-cta a",{opacity:1, y:0});
    gsap.from("#final-cta h2",{y:20, opacity:0, duration:0.7, ease:"power3.out", scrollTrigger:{trigger:"#final-cta", start:"top 85%", once:true}});
    gsap.from("#final-cta p",{y:16, opacity:0, duration:0.6, ease:"power3.out", delay:0.08, scrollTrigger:{trigger:"#final-cta", start:"top 85%", once:true}});
    gsap.from("#final-cta a",{y:16, opacity:0, stagger:0.12, duration:0.6, ease:"power3.out", scrollTrigger:{trigger:"#final-cta", start:"top 90%", once:true, onEnter:function(){ ensureVisible(); }}});
    gsap.to("#cta-orb",{y:-20, ease:"none", scrollTrigger:{trigger:"#final-cta", start:"top bottom", end:"bottom top", scrub:1}});
    // fallback: if ScrollTrigger never fires (already in view), force visible after 1.2s
    setTimeout(ensureVisible, 1200);
    window.addEventListener('load', function(){ ScrollTrigger.refresh(); ensureVisible(); });
  }
  if(document.readyState === 'loading'){ document.addEventListener('DOMContentLoaded', initCta); } else { setTimeout(initCta, 120); }
  // immediate ensure for fast paint
  ensureVisible();
})();
</script>
