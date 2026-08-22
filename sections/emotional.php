<?php // Emotional Section — Full Width Horizontal Pinned Carousel — real Indian couple images ?>
<section id="emotional" class="relative hidden lg:block w-full overflow-hidden border-t border-white/10" style="background: #0c0205;">
  <div id="pin-emo" class="relative h-screen w-screen overflow-hidden">
    <div id="track-emo" class="absolute inset-0 flex will-change-transform" style="will-change:transform">
      <!-- Slide 1 — Two People Two Stories -->
      <div class="shrink-0 w-screen h-full flex">
        <div class="w-1/2 h-full relative overflow-hidden bg-[#1a0a0f]">
          <img src="<?php echo asset('images/parallax/couple-varmala.jpg'); ?>" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1625772299848-391b6a87d7b3?w=1200&q=80&auto=format&fit=crop'" alt="Real Indian couple — varmala ceremony — BestLife Matrimony" class="w-full h-full object-cover" loading="eager" decoding="async">
          <div class="absolute inset-0 bg-[#3a0c15]/15 pointer-events-none"></div>
        </div>
        <div class="w-1/2 h-full flex flex-col justify-center px-14 bg-white">
          <h2 class="font-serif text-[42px] font-bold leading-[0.9] tracking-tight text-[#3a0c15]">Two People. Two Stories.<br><span class="italic text-[#8a4a2f]">One Beautiful Beginning.</span></h2>
          <p class="mt-4 text-sm leading-7 text-[#3a0c15]/70 max-w-md">Every person has a story. Every family has dreams. And sometimes, two stories come together to create a new chapter.</p>
          <p class="mt-6 text-xs tracking-[0.16em] uppercase text-[#3a0c15]/40">Scroll →</p>
        </div>
      </div>

      <!-- Slide 2 — Help take first step -->
      <div class="shrink-0 w-screen h-full flex">
        <div class="w-1/2 h-full flex flex-col justify-center px-14 bg-[#fdf6e8] border-r border-[#3a0c15]/5">
          <p class="text-[11px] tracking-[0.16em] uppercase text-[#3a0c15]/50 font-bold">BestLife Matrimony</p>
          <p class="mt-4 font-serif italic text-2xl leading-relaxed text-[#3a0c15]">BestLife Matrimony is here to help you take that first step.</p>
          <p class="mt-4 text-sm leading-7 text-[#3a0c15]/60">Your courage meets our guidance — a platform built on trust, respect and genuine connections.</p>
        </div>
        <div class="w-1/2 h-full relative overflow-hidden bg-[#1a0a0f]">
          <img src="<?php echo asset('images/parallax/couple-walk.jpg'); ?>" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1591604466107-ec97de577aff?w=1200&q=80&auto=format&fit=crop'" alt="Real Indian couple walking together — BestLife Matrimony" class="w-full h-full object-cover" loading="lazy" decoding="async">
        </div>
      </div>

      <!-- Slide 3 — Journey easier + CTA -->
      <div class="shrink-0 w-screen h-full flex">
        <div class="w-1/2 h-full relative overflow-hidden bg-[#1a0a0f]">
          <img src="<?php echo asset('images/second.jpg'); ?>" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=1200&q=80&auto=format&fit=crop'" alt="Real Indian couple portrait — BestLife Matrimony" class="w-full h-full object-cover" loading="lazy" decoding="async">
          <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent pointer-events-none"></div>
        </div>
        <div class="w-1/2 h-full flex flex-col justify-center px-14 bg-[#3a0c15] text-white">
          <p class="text-sm leading-7 text-white/80 max-w-md">Whether you're beginning your search or helping someone you love find their life partner, we're here to make the journey easier.</p>
          <a href="./register.php" class="mt-8 inline-flex h-11 px-7 rounded-none bg-white text-[#3a0c15] font-bold text-sm items-center gap-2 hover:bg-[#f6e6b4] transition will-change-transform" style="will-change:transform; border-radius:0">Register Now →</a>
          <p class="mt-3 text-xs tracking-[0.16em] uppercase text-white/40">Trusted • Private • Family-first</p>
        </div>
      </div>
    </div>
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-3 rounded-full bg-black/40 border border-white/10 px-4 py-2 backdrop-blur"><div class="h-1 w-24 bg-white/10 rounded-full overflow-hidden"><div id="progress-emo" class="h-full bg-white w-0 will-change-transform" style="will-change:width"></div></div><span class="text-[10px] tracking-[0.16em] uppercase text-white/60">3 Slides</span><div class="flex gap-1.5 ml-2"><span class="w-1.5 h-1.5 rounded-full bg-white"></span><span class="w-1.5 h-1.5 rounded-full bg-white/30"></span><span class="w-1.5 h-1.5 rounded-full bg-white/30"></span></div></div>
  </div>
</section>

<!-- Mobile fallback — stacked full width with real images -->
<div class="lg:hidden w-full border-t border-white/10">
  <div class="flex overflow-x-auto snap-x snap-mandatory w-full" style="scrollbar-width:none; -ms-overflow-style:none">
    <div class="snap-center shrink-0 w-screen">
      <div class="h-64 overflow-hidden bg-[#1a0a0f]"><img src="<?php echo asset('images/parallax/couple-varmala.jpg'); ?>" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1625772299848-391b6a87d7b3?w=800&q=80&auto=format&fit=crop'" alt="Real Indian couple varmala" class="w-full h-full object-cover" loading="lazy"></div>
      <div class="bg-white p-6"><h2 class="font-serif text-2xl font-bold leading-[0.9] text-[#3a0c15]">Two People. Two Stories.<br><span class="italic text-[#8a4a2f]">One Beautiful Beginning.</span></h2><p class="mt-3 text-sm leading-6 text-[#3a0c15]/60">Every person has a story. Every family has dreams. And sometimes, two stories come together to create a new chapter.</p></div>
    </div>
    <div class="snap-center shrink-0 w-screen">
      <div class="h-64 overflow-hidden bg-[#1a0a0f]"><img src="<?php echo asset('images/parallax/couple-walk.jpg'); ?>" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1591604466107-ec97de577aff?w=800&q=80&auto=format&fit=crop'" alt="Real Indian couple walk" class="w-full h-full object-cover" loading="lazy"></div>
      <div class="bg-[#fdf6e8] p-6"><p class="font-serif italic text-lg leading-relaxed text-[#3a0c15]">BestLife Matrimony is here to help you take that first step.</p></div>
    </div>
    <div class="snap-center shrink-0 w-screen">
      <div class="h-64 overflow-hidden bg-[#1a0a0f]"><img src="<?php echo asset('images/second.jpg'); ?>" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=800&q=80&auto=format&fit=crop'" alt="Real Indian couple" class="w-full h-full object-cover" loading="lazy"></div>
      <div class="bg-[#3a0c15] p-6"><p class="text-sm leading-6 text-white/80">Whether you're beginning your search or helping someone you love find their life partner, we're here to make the journey easier.</p><a href="./register.php" class="mt-4 inline-flex h-10 px-6 rounded-none bg-white text-[#3a0c15] text-sm font-bold items-center gap-2" style="border-radius:0">Register Now →</a></div>
    </div>
  </div>
</div>

<!-- GSAP horizontal pinned — transform only, Lenis synced -->
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
<script>
(function(){
  if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if(typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
  gsap.registerPlugin(ScrollTrigger);
  function initEmo(){
    var lenis = window.__lenis;
    if(lenis && lenis.on) lenis.on('scroll', ScrollTrigger.update);
    if(!window.matchMedia('(min-width:1024px)').matches) return;
    var track = document.getElementById('track-emo');
    var pin = document.getElementById('pin-emo');
    if(!track || !pin) return;
    var getScroll = function(){ return -(track.scrollWidth - window.innerWidth); };
    gsap.to(track, { x: getScroll, ease:"none", scrollTrigger:{ trigger: pin, pin:true, scrub:1, start:"top top", end:function(){ return "+=" + (track.scrollWidth - window.innerWidth); }, invalidateOnRefresh:true }});
    gsap.to("#progress-emo", { width:"100%", ease:"none", scrollTrigger:{ trigger: pin, start:"top top", end:function(){ return "+=" + (track.scrollWidth - window.innerWidth); }, scrub:0.6 }});
    window.addEventListener('load', function(){ ScrollTrigger.refresh(); });
  }
  if(document.readyState === 'loading'){ document.addEventListener('DOMContentLoaded', initEmo); } else { setTimeout(initEmo, 180); }
})();
</script>
