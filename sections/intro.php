<?php // IntroSection.php — Full-page flex layout with asymmetric reveal ?>
<section id="intro" class="relative overflow-hidden border-t border-white/[0.06] min-h-screen flex flex-col" style="background: linear-gradient(145deg, #4a0a1a 0%, #6b1020 15%, #8b1428 30%, #a01830 45%, #8a4a2f 60%, #a67d3a 75%, #c9a86a 85%, #e3c877 95%, #f6e6b4 100%); margin-top: 0;">
  <!-- hairline grid -->
  <div class="absolute inset-0 pointer-events-none opacity-[0.04]" style="background-image: linear-gradient(to right, white 1px, transparent 1px), linear-gradient(to bottom, white 1px, transparent 1px); background-size: 120px 120px;"></div>
  <!-- large outline number -->
  <div id="outline-num" class="absolute top-4 right-4 sm:top-6 sm:right-6 lg:top-8 lg:right-10 font-display text-[80px] sm:text-[120px] lg:text-[220px] leading-none text-transparent will-change-transform" style="-webkit-text-stroke:1px rgba(255,246,232,0.15); will-change:transform">01</div>

  <div class="relative flex-1 max-w-[1600px] mx-auto w-full px-4 sm:px-6 lg:px-10 py-12 sm:py-16 lg:py-24 flex flex-col lg:flex-row gap-6 lg:gap-6 items-stretch">
    <!-- LEFT — editorial kicker -->
    <div class="hidden lg:flex shrink-0 basis-[60px] flex-col pt-2">
      <p class="text-[11px] tracking-[0.18em] uppercase text-white/30 [writing-mode:vertical-lr]">Brand Promise — 2026</p>
      <div class="mt-6 h-20 w-px bg-gradient-to-b from-[#e3c877]/50 to-transparent ml-1.5"></div>
    </div>

    <!-- CENTER — image with reveal -->
    <div class="flex-1 lg:flex-[5] order-2 lg:order-1 flex flex-col min-w-0 justify-center items-center">
      <div class="relative overflow-hidden rounded-[2px] border border-white/10 w-full shrink-0 self-center">
        <div class="relative overflow-hidden bg-[#1a0a0f] w-full flex items-center justify-center">
          <img id="intro-img" src="./assets/images/intro.jpg" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1519741497674-611481863552?w=900&q=80&auto=format&fit=contain'" alt="Couple portrait" class="block w-full h-auto object-contain will-change-transform" loading="eager" decoding="async" style="will-change:transform">
          <div class="absolute bottom-0 inset-x-0 h-20 sm:h-28 lg:h-32 bg-gradient-to-t from-black/70 to-transparent"></div>
          <div class="absolute bottom-3 left-3 right-3 sm:bottom-4 sm:left-4 sm:right-4 flex items-center justify-between text-[9px] sm:text-[10px] lg:text-[11px] tracking-[0.14em] uppercase">
            <span class="bg-white text-[#0c0205] px-2 py-1 sm:px-3 sm:py-1.5 font-bold">Est. Chennai</span>
            <span class="bg-black/40 backdrop-blur border border-white/15 text-white px-2 py-1 sm:px-3 sm:py-1.5">Trusted • Verified</span>
          </div>
        </div>
      </div>
      <!-- horizontal strip under image -->
      <div class="mt-2 sm:mt-3 overflow-hidden border-y border-white/10 py-1.5 sm:py-2 flex">
        <div id="strip-hz" class="flex gap-4 sm:gap-6 whitespace-nowrap text-[9px] sm:text-[10px] lg:text-[11px] tracking-[0.18em] uppercase text-white/35 will-change-transform">Meaningful Matches — Privacy with Dignity — Families First — Genuine Profiles — Meaningful Matches — Privacy with Dignity —</div>
      </div>
    </div>

    <!-- RIGHT — text -->
    <div class="flex-[6] order-1 lg:order-2 lg:pl-8 xl:pl-10 flex flex-col justify-center min-w-0 items-center text-center lg:items-start lg:text-left">
      <h2 class="mt-0 lg:mt-4 font-serif font-light leading-[0.9] tracking-[-0.03em] text-[26px] sm:text-[36px] md:text-[44px] lg:text-[62px]">
        <span class="block overflow-hidden"><span class="reveal-mask inline-block">Your Best Life</span></span>
        <span class="block overflow-hidden"><span class="reveal-mask inline-block">Could Begin With</span></span>
        <span class="block overflow-hidden"><span class="reveal-mask inline-block font-display italic font-normal text-[#e3c877]">One Connection.</span></span>
      </h2>
      <div id="hairline" class="mt-4 sm:mt-6 h-px w-0 bg-[#e3c877]/40 will-change-transform" style="will-change:width"></div>

      <div class="mt-5 sm:mt-7 flex flex-col sm:flex-row gap-4 sm:gap-6 text-[13px] sm:text-[14px] lg:text-[14.5px] leading-[1.8] sm:leading-[1.85] text-white/65 items-center lg:items-start">
        <p class="reveal-p flex-1">Finding the right life partner is one of life's most important decisions. At BestLife Matrimony, we make the journey simpler, more personal and more meaningful.</p>
        <p class="reveal-p flex-1">Discover profiles based on your preferences, connect with compatible individuals and take the first step towards building a beautiful future together.</p>
      </div>
      <div class="reveal-p mt-5 sm:mt-6 border-l-2 lg:border-l-2 border-[#e3c877]/30 pl-4 sm:pl-5 lg:pl-5 text-center lg:text-left">
        <p class="font-serif italic text-[15px] sm:text-[16px] lg:text-[18px] leading-relaxed text-[#f6e6b4]">Because the right match isn't just about finding someone. It's about finding someone who fits your life.</p>
      </div>
      <div class="reveal-p mt-6 sm:mt-8 flex flex-wrap gap-3 justify-center lg:justify-start">
        <a href="./about.php" class="inline-flex h-[40px] sm:h-[42px] lg:h-[46px] items-center gap-2 bg-[#fff6e8] text-[#0c0205] px-5 sm:px-6 lg:px-7 text-xs sm:text-sm font-bold tracking-wide hover:bg-white transition">Know More About Us <span>→</span></a>
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
