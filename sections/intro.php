<?php // IntroSection.php — Atelier Awwwards inspired layout with asymmetric reveal ?>
<section id="intro" class="relative overflow-hidden border-t border-white/[0.06]" style="background: linear-gradient(145deg, #4a0a1a 0%, #6b1020 15%, #8b1428 30%, #a01830 45%, #8a4a2f 60%, #a67d3a 75%, #c9a86a 85%, #e3c877 95%, #f6e6b4 100%);">
  <!-- hairline grid -->
  <div class="absolute inset-0 pointer-events-none opacity-[0.04]" style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 120px 120px;"></div>
  <!-- large outline number -->
  <div id="outline-num" class="absolute top-8 right-6 lg:right-10 font-display text-[140px] lg:text-[220px] leading-none text-transparent will-change-transform" style="-webkit-text-stroke:1px rgba(255,246,232,0.15); will-change:transform">01</div>

  <div class="relative max-w-[1600px] mx-auto px-6 lg:px-10 py-16 lg:py-28 grid lg:grid-cols-12 gap-8 lg:gap-6 items-start">
    <!-- LEFT — editorial kicker -->
    <div class="lg:col-span-1 hidden lg:block pt-2">
      <p class="text-[11px] tracking-[0.18em] uppercase text-white/30 [writing-mode:vertical-lr]">Brand Promise — 2026</p>
      <div class="mt-6 h-20 w-px bg-gradient-to-b from-[#e3c877]/50 to-transparent ml-1.5"></div>
    </div>

    <!-- CENTER — image with reveal -->
    <div class="lg:col-span-5 order-2 lg:order-1">
      <div class="relative overflow-hidden rounded-[2px] border border-white/10">
        <div class="relative aspect-[4/5] overflow-hidden bg-[#1a0a0f]">
          <img id="intro-img" src="./assets/images/intro.jpg" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1519741497674-611481863552?w=900&q=80&auto=format&fit=crop'" alt="Couple portrait" class="absolute inset-0 h-full w-full object-cover will-change-transform" loading="eager" decoding="async" style="will-change:transform">
          <!-- <div id="img-overlay" class="absolute inset-0 bg-[#4a0a1a] origin-top will-change-transform" style="will-change:transform"></div> -->
          <div class="absolute bottom-0 inset-x-0 h-40 bg-gradient-to-t from-black/70 to-transparent"></div>
          <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between text-[11px] tracking-[0.14em] uppercase">
            <span class="bg-white text-[#0c0205] px-3 py-1.5 font-bold">Est. Chennai</span>
            <span class="bg-black/40 backdrop-blur border border-white/15 text-white px-3 py-1.5">Trusted • Verified</span>
          </div>
        </div>
      </div>
      <!-- horizontal strip under image -->
      <div class="mt-3 overflow-hidden border-y border-white/10 py-2">
        <div id="strip-hz" class="flex gap-6 whitespace-nowrap text-[11px] tracking-[0.18em] uppercase text-white/35 will-change-transform">Meaningful Matches — Privacy with Dignity — Families First — Genuine Profiles — Meaningful Matches — Privacy with Dignity —</div>
      </div>
    </div>

    <!-- RIGHT — text -->
    <div class="lg:col-span-6 order-1 lg:order-2 lg:pl-10">
      <div class="flex items-center gap-3 text-[11px] tracking-[0.18em] uppercase font-bold text-[#e3c877]">
        <span class="h-px w-8 bg-[#e3c877]/50"></span> Introduction
        <span class="text-white/20 font-normal">/ 01</span>
      </div>
      <h2 class="mt-5 font-serif font-light leading-[0.9] tracking-[-0.03em] text-[38px] sm:text-[54px] lg:text-[62px]">
        <span class="block overflow-hidden"><span class="reveal-mask inline-block">Your Best Life</span></span>
        <span class="block overflow-hidden"><span class="reveal-mask inline-block">Could Begin With</span></span>
        <span class="block overflow-hidden"><span class="reveal-mask inline-block font-display italic font-normal text-[#e3c877]">One Connection.</span></span>
      </h2>
      <div id="hairline" class="mt-6 h-px w-0 bg-[#e3c877]/40 will-change-transform" style="will-change:width"></div>

      <div class="mt-7 grid sm:grid-cols-2 gap-6 text-[14.5px] leading-[1.85] text-white/65">
        <p class="reveal-p">Finding the right life partner is one of life's most important decisions. At BestLife Matrimony, we make the journey simpler, more personal and more meaningful.</p>
        <p class="reveal-p">Discover profiles based on your preferences, connect with compatible individuals and take the first step towards building a beautiful future together.</p>
      </div>
      <div class="reveal-p mt-6 border-l-2 border-[#e3c877]/30 pl-5">
        <p class="font-serif italic text-[18px] leading-relaxed text-[#f6e6b4]">Because the right match isn't just about finding someone. It's about finding someone who fits your life.</p>
      </div>
      <div class="reveal-p mt-8 flex flex-wrap gap-3">
        <a href="./about.php" class="inline-flex h-[46px] items-center gap-2 bg-[#fff6e8] text-[#0c0205] px-7 text-sm font-bold tracking-wide hover:bg-white transition">Know More About Us <span>→</span></a>
      </div>
    </div>
  </div>
</section>

<script>
(function(){
  if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if(typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
  gsap.registerPlugin(ScrollTrigger);
  
  var lenis = window.__lenis;
  if(lenis && lenis.on) lenis.on('scroll', ScrollTrigger.update);
  
  function initIntro(){
    if(!window.matchMedia('(min-width:1024px)').matches) return;
    
    gsap.to("#img-overlay", {scaleY:0, duration:1.2, ease:"expo.inOut", scrollTrigger:{trigger:"#intro", start:"top 75%"}});
    gsap.to("#intro-img", {yPercent:10, ease:"none", scrollTrigger:{trigger:"#intro", start:"top bottom", end:"bottom top", scrub:1.2}});
    gsap.to("#outline-num", {y:-80, ease:"none", scrollTrigger:{trigger:"#intro", start:"top bottom", end:"bottom top", scrub:1}});
    gsap.to("#strip-hz", {xPercent:-20, ease:"none", scrollTrigger:{trigger:"#intro", start:"top bottom", end:"bottom top", scrub:1}});
    gsap.from(".reveal-mask", {yPercent:105, duration:1.1, stagger:0.12, ease:"expo.out", delay:0.4, scrollTrigger:{trigger:"#intro", start:"top 75%"}});
    gsap.to("#hairline", {width:"160px", duration:1, ease:"power3.out", scrollTrigger:{trigger:"#intro", start:"top 70%"}});
    gsap.from(".reveal-p", {y:18, opacity:0, stagger:0.08, duration:0.8, ease:"power3.out", scrollTrigger:{trigger:"#intro", start:"top 65%"}});
    
    window.addEventListener('load', function(){ ScrollTrigger.refresh(); });
  }
  
  if(document.readyState === 'loading'){ 
    document.addEventListener('DOMContentLoaded', initIntro); 
  } else { 
    setTimeout(initIntro, 180); 
  }
})();
</script>
