<?php // How It Works — Timeline Sticky (02) — lag-free ?>
<section id="how-it-works" class="relative border-t border-white/10 py-16 lg:py-20 overflow-hidden" style="background: linear-gradient(145deg, #3a0c15 0%, #4a1322 20%, #5e1e2e 35%, #6e2a2a 52%, #8a4a2f 68%, #a67d3a 82%, #c9a86a 90%, #e3c877 96%, #f6e6b4 100%);">
  <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(800px 520px at 15% 20%, rgba(255,246,232,0.06) 0%, transparent 70%), radial-gradient(900px 600px at 85% 85%, rgba(58,12,21,0.28) 0%, transparent 68%);"></div>
  <div class="max-w-6xl mx-auto px-6">
    <div class="text-center max-w-2xl mx-auto mb-12">
      <span class="inline-flex rounded-none border border-[#e3c877]/20 bg-white/5 px-4 py-1.5 text-[11px] tracking-[0.16em] uppercase text-[#e3c877] font-bold" style="border-radius:0">How It Works</span>
      <h2 class="mt-4 font-serif text-[32px] sm:text-[42px] font-bold leading-[0.9] tracking-tight text-[#fff6e8]">
        Finding Your Match Can Be <span class="bg-gradient-to-r from-[#f6e6b4] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic">Simple.</span>
      </h2>
    </div>

    <div class="grid lg:grid-cols-12 gap-10 items-start">
      <div class="hidden lg:block lg:col-span-3">
        <div class="sticky top-24">
          <p class="text-[11px] tracking-[0.16em] uppercase text-white/30">Steps 01 — 04</p>
          <div class="mt-4 h-1 w-full bg-white/10 rounded-none overflow-hidden" style="border-radius:0"><div id="progress-how" class="h-full bg-[#e3c877] w-0 will-change-transform" style="will-change:width"></div></div>
          <p class="mt-4 font-serif italic text-[#f6e6b4] leading-relaxed">Your journey starts with a profile.</p>
          <p class="mt-2 text-xs leading-6 text-white/40">Follow the simple steps to find meaningful connections.</p>
        </div>
      </div>
      <div class="lg:col-span-9 space-y-5">
        <div id="step-how-1" class="flex gap-5 rounded-none border border-white/10 bg-white/[0.04] p-6 backdrop-blur will-change-transform" style="will-change:transform; border-radius:0">
          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-none bg-[#e3c877] text-[#3a0c15] font-bold text-sm" style="border-radius:0">01</span>
          <div>
            <h3 class="font-bold text-[#fff6e8]">Register</h3>
            <p class="mt-1 text-sm leading-6 text-white/60">Create your BestLife Matrimony profile with your basic information and preferences.</p>
          </div>
        </div>
        <div id="step-how-2" class="flex gap-5 rounded-none border border-white/10 bg-white/[0.04] p-6 backdrop-blur will-change-transform" style="will-change:transform; border-radius:0">
          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-none bg-white text-[#3a0c15] font-bold text-sm" style="border-radius:0">02</span>
          <div>
            <h3 class="font-bold text-[#fff6e8]">Discover</h3>
            <p class="mt-1 text-sm leading-6 text-white/60">Browse profiles and discover people who match your expectations.</p>
          </div>
        </div>
        <div id="step-how-3" class="flex gap-5 rounded-none border border-white/10 bg-white/[0.04] p-6 backdrop-blur will-change-transform" style="will-change:transform; border-radius:0">
          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-none bg-[#e3c877]/20 border border-[#e3c877]/30 text-[#e3c877] font-bold text-sm" style="border-radius:0">03</span>
          <div>
            <h3 class="font-bold text-[#fff6e8]">Connect</h3>
            <p class="mt-1 text-sm leading-6 text-white/60">Show interest and start a conversation with someone you feel connected with.</p>
          </div>
        </div>
        <div id="step-how-4" class="flex gap-5 rounded-none border border-white/10 bg-white/[0.04] p-6 backdrop-blur will-change-transform" style="will-change:transform; border-radius:0">
          <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-none bg-[#e3c877] text-[#3a0c15] font-bold text-sm" style="border-radius:0">04</span>
          <div>
            <h3 class="font-bold text-[#fff6e8]">Take It Forward</h3>
            <p class="mt-1 text-sm leading-6 text-white/60">Build trust, involve your families when the time is right and take your relationship towards a beautiful future.</p>
          </div>
        </div>
        <div class="pt-6 text-center lg:text-left">
          <p class="font-serif italic text-[#f6e6b4]">Your future starts with a connection.</p>
          <a href="<?php echo register_cta_href(); ?>" class="mt-4 inline-flex h-11 px-6 rounded-none bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] text-[#3a0c15] font-bold text-sm items-center gap-2 hover:brightness-110 transition will-change-transform" style="will-change:transform; border-radius:0">
            <?php echo register_cta_label('My Profile', 'Create Your Profile'); ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- GSAP + Lenis sync — lag-free -->
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
<script>
(function(){
  if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if(typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
  gsap.registerPlugin(ScrollTrigger);
  function initHow(){
    var lenis = window.__lenis;
    if(lenis && lenis.on) lenis.on('scroll', ScrollTrigger.update);
    gsap.to("#progress-how", { width:"100%", ease:"none", scrollTrigger:{ trigger:"#how-it-works", start:"top center", end:"bottom 60%", scrub:0.8 }});
    gsap.to("#step-how-1", { y:-12, ease:"none", scrollTrigger:{ trigger:"#how-it-works", start:"top bottom", end:"bottom top", scrub:1 }});
    gsap.to("#step-how-2", { y:-22, ease:"none", scrollTrigger:{ trigger:"#how-it-works", start:"top bottom", end:"bottom top", scrub:1.1 }});
    gsap.to("#step-how-3", { y:-16, ease:"none", scrollTrigger:{trigger:"#how-it-works", start:"top bottom", end:"bottom top", scrub:0.95 }});
    gsap.to("#step-how-4", { y:-28, ease:"none", scrollTrigger:{trigger:"#how-it-works", start:"top bottom", end:"bottom top", scrub:1.15 }});
    window.addEventListener('load', function(){ ScrollTrigger.refresh(); });
  }
  if(document.readyState === 'loading'){ document.addEventListener('DOMContentLoaded', initHow); } else { setTimeout(initHow, 150); }
})();
</script>
