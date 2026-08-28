<?php // For Families — Real Book — 6 pages — assets/images 1-6.jpg — pinned until done ?>
<section id="for-families" class="relative w-screen overflow-hidden border-t border-white/10" style="background: linear-gradient(145deg, #3a0c15 0%, #4a1322 20%, #5e1e2e 35%, #6e2a2a 52%, #8a4a2f 68%, #a67d3a 82%, #c9a86a 90%, #e3c877 96%, #f6e6b4 100%);">
  <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(800px 400px at 50% 12%, rgba(255,246,232,0.07) 0%, transparent 70%);"></div>

  <div id="pin-fam" class="relative hidden lg:flex w-screen h-screen items-center justify-center">
    <div class="absolute top-8 left-0 right-0 flex items-center justify-center z-30">
      <h2 class="font-serif text-5xl font-bold text-white tracking-wide">Together We Choose</h2>
    </div>
    <div id="book-fam" class="relative w-[92vw] max-w-6xl h-[72vh] flex book-shadow" style="perspective:2800px; perspective-origin:50% 50%; will-change:transform">
      <style>
        .book-shadow{box-shadow:0 30px 70px rgba(0,0,0,0.45),0 10px 20px rgba(0,0,0,0.25),0 0 0 1px rgba(0,0,0,0.08)}
        .page{backface-visibility:hidden; transform-style:preserve-3d}
        .page-back{transform:rotateY(180deg)}
        .spine{ background: linear-gradient(90deg, #2a0a12 0%, #4a1322 20%, #5e1e2e 50%, #3a0c15 100%); box-shadow: inset 2px 0 6px rgba(0,0,0,0.4), inset -1px 0 2px rgba(255,255,255,0.1); }
        .page-edge{ background: repeating-linear-gradient(90deg, #fdf6e8 0px, #fdf6e8 2px, #e8ddd0 2px, #e8ddd0 3px); }
      </style>
      <div class="absolute -inset-3 bg-[#2a0a12] rounded-[4px] pointer-events-none" style="box-shadow: inset 0 1px 0 rgba(255,255,255,0.06), 0 20px 50px rgba(0,0,0,0.4);"></div>
      <div class="absolute -inset-3 rounded-[4px] border border-white/5 pointer-events-none"></div>
      <div class="absolute -bottom-8 left-6 right-6 h-8 bg-black/30 blur-[18px] rounded-full pointer-events-none"></div>

      <div class="relative flex w-full h-full bg-white overflow-hidden" style="border-radius:2px">
        <!-- LEFT — 6 images from assets/images/1.jpg - 6.jpg -->
        <div class="w-1/2 h-full relative overflow-hidden bg-[#1a0a0f]" style="border-radius:2px 0 0 2px">
          <div class="left-img-fam absolute inset-0"><img src="<?php echo asset('images/1.jpg'); ?>" alt="Family image 1" class="w-full h-full object-cover" loading="eager"><div class="absolute inset-0" style="background: linear-gradient(90deg, transparent 70%, rgba(0,0,0,0.18) 100%);"></div></div>
          <div class="left-img-fam absolute inset-0"><img src="<?php echo asset('images/2.jpg'); ?>" alt="Family image 2" class="w-full h-full object-cover" loading="lazy"><div class="absolute inset-0" style="background: linear-gradient(90deg, transparent 70%, rgba(0,0,0,0.18) 100%);"></div></div>
          <div class="left-img-fam absolute inset-0"><img src="<?php echo asset('images/3.jpg'); ?>" alt="Family image 3" class="w-full h-full object-cover" loading="lazy"><div class="absolute inset-0" style="background: linear-gradient(90deg, transparent 70%, rgba(0,0,0,0.18) 100%);"></div></div>
          <div class="left-img-fam absolute inset-0"><img src="<?php echo asset('images/4.jpg'); ?>" alt="Family image 4" class="w-full h-full object-cover" loading="lazy"><div class="absolute inset-0" style="background: linear-gradient(90deg, transparent 70%, rgba(0,0,0,0.18) 100%);"></div></div>
          <div class="left-img-fam absolute inset-0"><img src="<?php echo asset('images/5.jpg'); ?>" alt="Family image 5" class="w-full h-full object-cover" loading="lazy"><div class="absolute inset-0" style="background: linear-gradient(90deg, transparent 70%, rgba(0,0,0,0.18) 100%);"></div></div>
          <div class="left-img-fam absolute inset-0"><img src="<?php echo asset('images/6.jpg'); ?>" alt="Family image 6" class="w-full h-full object-cover" loading="lazy"><div class="absolute inset-0" style="background: linear-gradient(90deg, transparent 70%, rgba(0,0,0,0.18) 100%);"></div></div>
          <div class="absolute right-0 top-0 bottom-0 w-12 pointer-events-none z-10" style="background: linear-gradient(90deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.12) 100%);"></div>
          <div class="absolute left-0 top-1 bottom-1 w-[6px] page-edge opacity-60 pointer-events-none"></div>
          </div>

        <div class="spine w-[22px] shrink-0 relative z-20 flex items-center justify-center">
          <div class="absolute left-0 top-0 bottom-0 w-[8px] bg-gradient-to-r from-black/30 to-transparent"></div>
          <div class="absolute right-0 top-0 bottom-0 w-[8px] bg-gradient-to-l from-black/20 to-transparent"></div>
        </div>

        <!-- RIGHT — 6 pages all white bg -->
        <div class="w-1/2 h-full relative bg-white overflow-hidden" style="perspective:2000px; border-radius:0 2px 2px 0">
          <!-- Base page 06 bottom -->
          <div class="absolute inset-0 p-8 lg:p-10 flex flex-col justify-center bg-white">
            <div class="absolute top-3 right-3 lg:top-4 lg:right-4 z-10 pointer-events-none"><img src="<?php echo asset('images/logo.png'); ?>" alt="BestLife Matrimony" class="h-16 lg:h-20 w-auto object-contain opacity-90" loading="lazy" onerror="this.style.display='none'"></div>
            <p class="font-serif italic text-2xl leading-relaxed text-[#3a0c15]">Search Together. Choose Together. Begin Together.</p>
            <p class="mt-4 text-sm leading-7 text-[#3a0c15]/70">Your family's love, your choice — together from the first page to forever. BestLife Matrimony is here to make every step clearer, kinder and closer to the right match.</p>
            <p class="mt-3 text-sm leading-7 text-[#3a0c15]/60">Because when families and individuals walk together, the right beginning feels natural.</p>
            </div>
          <!-- Flip 5 — page 05 -->
          <div id="flip-fam-5" class="page absolute inset-0 will-change-transform" style="will-change:transform; transform-origin:left center; transform-style:preserve-3d">
            <div class="absolute inset-0 p-8 lg:p-10 flex flex-col justify-center bg-white" style="backface-visibility:hidden">
              <div class="absolute top-3 right-3 lg:top-4 lg:right-4 z-10 pointer-events-none"><img src="<?php echo asset('images/logo.png'); ?>" alt="BestLife Matrimony" class="h-16 lg:h-20 w-auto object-contain opacity-90" loading="lazy" onerror="this.style.display='none'"></div>
              <p class="text-sm leading-7 text-[#3a0c15]/70">BestLife Matrimony makes it easier for families to explore suitable profiles while keeping the individual's preferences and expectations at the heart — where respect meets choice.</p>
              <p class="mt-4 text-sm leading-7 text-[#3a0c15]/70">Search by age, education, location and values — together, yet always prioritising the individual's voice. Your family can guide, while your heart decides.</p>
            </div>
            <div class="page-back absolute inset-0 bg-white" style="backface-visibility:hidden"></div>
          </div>
          <!-- Flip 4 — page 04 -->
          <div id="flip-fam-4" class="page absolute inset-0 will-change-transform" style="will-change:transform; transform-origin:left center; transform-style:preserve-3d">
            <div class="absolute inset-0 p-8 lg:p-10 flex flex-col justify-center bg-white" style="backface-visibility:hidden">
              <div class="absolute top-3 right-3 lg:top-4 lg:right-4 z-10 pointer-events-none"><img src="<?php echo asset('images/logo.png'); ?>" alt="BestLife Matrimony" class="h-16 lg:h-20 w-auto object-contain opacity-90" loading="lazy" onerror="this.style.display='none'"></div>
              <p class="text-sm leading-7 text-[#3a0c15]/70">Parents, siblings and family members often play an important role in finding the right life partner — with love, wisdom and care.</p>
              <p class="mt-4 text-sm leading-7 text-[#3a0c15]/70">We help families discover compatible matches with privacy, verification and genuine intent — so every introduction feels trusted and every conversation starts with confidence.</p>
            </div>
            <div class="page-back absolute inset-0 bg-white" style="backface-visibility:hidden"></div>
          </div>
          <!-- Flip 3 — page 03 -->
          <div id="flip-fam-3" class="page absolute inset-0 will-change-transform" style="will-change:transform; transform-origin:left center; transform-style:preserve-3d">
            <div class="absolute inset-0 p-8 lg:p-10 flex flex-col justify-center bg-white" style="backface-visibility:hidden">
              <div class="absolute top-3 right-3 lg:top-4 lg:right-4 z-10 pointer-events-none"><img src="<?php echo asset('images/logo.png'); ?>" alt="BestLife Matrimony" class="h-16 lg:h-20 w-auto object-contain opacity-90" loading="lazy" onerror="this.style.display='none'"></div>
              <p class="text-sm leading-7 text-[#3a0c15]/70">Sometimes, the search isn't just yours. It begins with care, continues with conversation, and grows with trust.</p>
              <p class="mt-4 text-sm leading-7 text-[#3a0c15]/70">The involvement of loved ones brings strength — a second pair of eyes, a lifetime of experience.</p>
              <p class="mt-3 text-sm leading-7 text-[#3a0c15]/60">We make that shared journey simple, respectful and joyful.</p>
            </div>
            <div class="page-back absolute inset-0 bg-white" style="backface-visibility:hidden"></div>
          </div>
          <!-- Flip 2 — page 02 -->
          <div id="flip-fam-2" class="page absolute inset-0 will-change-transform" style="will-change:transform; transform-origin:left center; transform-style:preserve-3d">
            <div class="absolute inset-0 p-8 lg:p-10 flex flex-col justify-center bg-white" style="backface-visibility:hidden">
              <div class="absolute top-3 right-3 lg:top-4 lg:right-4 z-10 pointer-events-none"><img src="<?php echo asset('images/logo.png'); ?>" alt="BestLife Matrimony" class="h-16 lg:h-20 w-auto object-contain opacity-90" loading="lazy" onerror="this.style.display='none'"></div>
              <p class="font-serif italic text-xl leading-relaxed text-[#3a0c15]">For every family, a story. For every individual, a dream.</p>
              <p class="mt-4 text-sm leading-7 text-[#3a0c15]/70">Every home holds traditions, values and hopes for the future. We listen to both — the family's wisdom and the individual's heart.</p>
              <p class="mt-3 text-sm leading-7 text-[#3a0c15]/60">Together, we find someone who respects your roots and shares your tomorrow.</p>
            </div>
            <div class="page-back absolute inset-0 bg-white" style="backface-visibility:hidden"></div>
          </div>
          <!-- Flip 1 — page 01 top -->
          <div id="flip-fam-1" class="page absolute inset-0 will-change-transform" style="will-change:transform; transform-origin:left center; transform-style:preserve-3d; z-index:7">
            <div class="absolute inset-0 p-8 lg:p-10 flex flex-col justify-center bg-white" style="backface-visibility:hidden">
              <div class="absolute top-3 right-3 lg:top-4 lg:right-4 z-10 pointer-events-none"><img src="<?php echo asset('images/logo.png'); ?>" alt="BestLife Matrimony" class="h-16 lg:h-20 w-auto object-contain opacity-90" loading="lazy" onerror="this.style.display='none'"></div>
              <span class="inline-flex w-fit border border-[#3a0c15]/10 bg-white px-3 py-1 text-[11px] tracking-[0.16em] uppercase text-[#3a0c15] font-bold" style="border-radius:0">For Families</span>
              <h2 class="mt-4 font-serif text-[32px] font-bold leading-[0.9] text-[#3a0c15]">Looking for a Life Partner<br><span class="italic text-[#8a4a2f]">for Someone You Love?</span></h2>
              <p class="mt-4 text-sm leading-7 text-[#3a0c15]/70">When a family searches together, every step is filled with care, hope and shared dreams. BestLife Matrimony honours that bond.</p>
              <p class="mt-3 text-sm leading-7 text-[#3a0c15]/60">Because finding a partner is not just an individual choice — it is a family blessing, guided by love and trust.</p>
              </div>
            <div class="page-back absolute inset-0 bg-white" style="backface-visibility:hidden"></div>
          </div>

          <div class="absolute right-0 top-1 bottom-1 w-[6px] page-edge opacity-60 pointer-events-none"></div>
          <div class="absolute left-0 top-0 bottom-0 w-12 pointer-events-none z-10" style="background: linear-gradient(90deg, rgba(0,0,0,0.14) 0%, transparent 70%);"></div>
        </div>
        <!-- CLOSED COVER — Together We Choose — full width closed, text on right half not cut by spine -->
        <div id="flip-cover" class="page absolute inset-0 will-change-transform" style="will-change:transform; transform-origin:left center; transform-style:preserve-3d; z-index:12">
          <div class="absolute inset-0 overflow-hidden" style="backface-visibility:hidden; border-radius:2px">
            <img src="<?php echo asset('images/1.jpg'); ?>" alt="Together We Choose cover — real family" class="w-full h-full object-cover" loading="eager">
            <div class="absolute inset-0 bg-[#3a0c15]/55"></div>

            <div class="absolute inset-0 pointer-events-none" style="box-shadow: inset 0 0 0 1px rgba(246,230,180,0.15);"></div>
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-black/10 pointer-events-none"></div>
          </div>
          <div class="page-back absolute inset-0 bg-[#fdf6e8] flex items-center justify-center" style="backface-visibility:hidden; border-radius:2px 0 0 2px; border-right:1px solid rgba(0,0,0,0.06)">
            <p class="text-[11px] tracking-[0.2em] uppercase text-[#3a0c15]/20">Together We Choose</p>
          </div>
        </div>
      </div>
    </div>
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-3 bg-black/40 border border-white/10 px-4 py-2 backdrop-blur rounded-full">
      <div class="h-1 w-24 bg-white/10 rounded-full overflow-hidden"><div id="progress-fam" class="h-full bg-white w-0 will-change-transform" style="will-change:width"></div></div>
      <span class="text-[10px] tracking-[0.16em] uppercase text-white/0"></span>
    </div>
  </div>

  <!-- Mobile heading -->
  <div class="lg:hidden w-full pt-8 pb-4 text-center">
    <h2 class="font-serif text-3xl font-bold text-white tracking-wide">Together We Choose</h2>
  </div>

  <!-- Mobile swipe — 6 images -->
  <div class="lg:hidden w-full">
    <div class="flex overflow-x-auto snap-x snap-mandatory w-full" style="scrollbar-width:none">
      <div class="snap-center shrink-0 w-screen p-4"><div class="bg-white overflow-hidden border border-white/15"><div class="h-56 overflow-hidden"><img src="<?php echo asset('images/1.jpg'); ?>" class="w-full h-full object-cover" alt="Family 1" loading="lazy"></div><div class="p-6"><h2 class="font-serif text-xl font-bold text-[#3a0c15]">Looking for a Life Partner for Someone You Love?</h2></div></div></div>
      <div class="snap-center shrink-0 w-screen p-4"><div class="bg-white overflow-hidden border border-white/15"><div class="h-56 overflow-hidden"><img src="<?php echo asset('images/2.jpg'); ?>" class="w-full h-full object-cover" alt="Family 2" loading="lazy"></div><div class="p-6"><p class="text-sm leading-7 text-[#3a0c15]/70">Sometimes, the search isn't just yours.</p></div></div></div>
      <div class="snap-center shrink-0 w-screen p-4"><div class="bg-white overflow-hidden border border-white/15"><div class="h-56 overflow-hidden"><img src="<?php echo asset('images/3.jpg'); ?>" class="w-full h-full object-cover" alt="Family 3" loading="lazy"></div><div class="p-6"><p class="text-sm leading-7 text-[#3a0c15]/70">Parents, siblings and family members often play an important role.</p></div></div></div>
      <div class="snap-center shrink-0 w-screen p-4"><div class="bg-white overflow-hidden border border-white/15"><div class="h-56 overflow-hidden"><img src="<?php echo asset('images/4.jpg'); ?>" class="w-full h-full object-cover" alt="Family 4" loading="lazy"></div><div class="p-6"><p class="text-sm leading-7 text-[#3a0c15]/70">BestLife Matrimony makes it easier for families to explore suitable profiles.</p></div></div></div>
      <div class="snap-center shrink-0 w-screen p-4"><div class="bg-white overflow-hidden border border-white/15"><div class="h-56 overflow-hidden"><img src="<?php echo asset('images/5.jpg'); ?>" class="w-full h-full object-cover" alt="Family 5" loading="lazy"></div><div class="p-6"><p class="font-serif italic text-[#3a0c15]">At the heart of the journey — individual dreams, family support.</p></div></div></div>
      <div class="snap-center shrink-0 w-screen p-4"><div class="bg-white overflow-hidden border border-white/15"><div class="h-56 overflow-hidden"><img src="<?php echo asset('images/6.jpg'); ?>" class="w-full h-full object-cover" alt="Family 6" loading="lazy"></div><div class="p-6"><p class="font-serif italic text-xl leading-relaxed text-[#3a0c15]">Search Together. Choose Together. Begin Together.</p><p class="mt-3 text-sm leading-7 text-[#3a0c15]/60">Your family's love, your choice — together from the first page to forever.</p></div></div></div>
    </div>
  </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
<script>
(function(){
  if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if(typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
  gsap.registerPlugin(ScrollTrigger);
  function initFam(){
    var lenis = window.__lenis;
    if(lenis && lenis.on) lenis.on('scroll', ScrollTrigger.update);
    if(!window.matchMedia('(min-width:1024px)').matches) return;
    var leftImgs = document.querySelectorAll(".left-img-fam");
    gsap.set(leftImgs,{autoAlpha:0});
    // Explicit zIndex + backface handling — prevents previous page ghosting on right side
    gsap.set("#flip-cover",{rotateY:0, autoAlpha:1, zIndex:20});
    gsap.set("#flip-fam-1",{rotateY:0, autoAlpha:1, zIndex:12});
    gsap.set("#flip-fam-2",{rotateY:-180, autoAlpha:0, zIndex:11});
    gsap.set("#flip-fam-3",{rotateY:-180, autoAlpha:0, zIndex:10});
    gsap.set("#flip-fam-4",{rotateY:-180, autoAlpha:0, zIndex:9});
    gsap.set("#flip-fam-5",{rotateY:-180, autoAlpha:0, zIndex:8});
    gsap.set("#progress-fam",{width:"0%"});
    var tl = gsap.timeline({scrollTrigger:{trigger:"#for-families", pin:"#pin-fam", pinSpacing:true, anticipatePin:1, start:"top top", end:"+=900%", scrub:1.8, invalidateOnRefresh:true}});
    gsap.to("#book-fam",{rotateY:0.8, rotateX:0.3, ease:"none", scrollTrigger:{trigger:"#for-families", start:"top top", end:"+=900%", scrub:1.8}});
    // 0 — cover opens (book closed → open) + left image 1 appears — hide cover after it lands on left so it never reappears on right
    tl.to("#flip-cover",{rotateY:-180, duration:1.0, ease:"sine.inOut"},0)
      .set("#flip-cover",{autoAlpha:0, zIndex:1},1.0)
      .to(leftImgs[0],{autoAlpha:1, duration:0.6, ease:"sine.inOut"},0.4)
    // 1 — page 1 turn → hide page1 after it lands left (prevents ghost on right), reveal page2
      .to("#flip-fam-1",{rotateY:-180, duration:1.0, ease:"sine.inOut"},1.4)
      .set("#flip-fam-1",{autoAlpha:0},2.4)
      .to(leftImgs[0],{autoAlpha:0, duration:0.6, ease:"sine.inOut"},1.4)
      .to(leftImgs[1],{autoAlpha:1, duration:0.6, ease:"sine.inOut"},1.7)
      .set("#flip-fam-2",{autoAlpha:1},2.35)
      .to("#flip-fam-2",{rotateY:0, duration:0.9, ease:"sine.inOut"},2.4)
    // 2 — page 2 turn → hide after landing
      .to("#flip-fam-2",{rotateY:-180, duration:1.0, ease:"sine.inOut"},3.6)
      .set("#flip-fam-2",{autoAlpha:0},4.6)
      .to(leftImgs[1],{autoAlpha:0, duration:0.6, ease:"sine.inOut"},3.6)
      .to(leftImgs[2],{autoAlpha:1, duration:0.6, ease:"sine.inOut"},3.9)
      .set("#flip-fam-3",{autoAlpha:1},4.55)
      .to("#flip-fam-3",{rotateY:0, duration:0.9, ease:"sine.inOut"},4.6)
    // 3 — page 3 turn
      .to("#flip-fam-3",{rotateY:-180, duration:1.0, ease:"sine.inOut"},5.8)
      .set("#flip-fam-3",{autoAlpha:0},6.8)
      .to(leftImgs[2],{autoAlpha:0, duration:0.6, ease:"sine.inOut"},5.8)
      .to(leftImgs[3],{autoAlpha:1, duration:0.6, ease:"sine.inOut"},6.1)
      .set("#flip-fam-4",{autoAlpha:1},6.75)
      .to("#flip-fam-4",{rotateY:0, duration:0.9, ease:"sine.inOut"},6.8)
    // 4 — page 4 turn
      .to("#flip-fam-4",{rotateY:-180, duration:1.0, ease:"sine.inOut"},8.0)
      .set("#flip-fam-4",{autoAlpha:0},9.0)
      .to(leftImgs[3],{autoAlpha:0, duration:0.6, ease:"sine.inOut"},8.0)
      .to(leftImgs[4],{autoAlpha:1, duration:0.6, ease:"sine.inOut"},8.3)
      .set("#flip-fam-5",{autoAlpha:1},8.95)
      .to("#flip-fam-5",{rotateY:0, duration:0.9, ease:"sine.inOut"},9.0)
    // 5 — page 5 turn → reveal base page 06 (no hide needed for base)
      .to("#flip-fam-5",{rotateY:-180, duration:1.0, ease:"sine.inOut"},10.2)
      .set("#flip-fam-5",{autoAlpha:0},11.2)
      .to(leftImgs[4],{autoAlpha:0, duration:0.6, ease:"sine.inOut"},10.2)
      .to(leftImgs[5],{autoAlpha:1, duration:0.6, ease:"sine.inOut"},10.5);
    gsap.to("#progress-fam",{width:"100%", ease:"none", scrollTrigger:{trigger:"#for-families", start:"top top", end:"+=900%", scrub:1.2}});
    window.addEventListener('load', function(){ ScrollTrigger.refresh(); });
  }
  if(document.readyState === 'loading'){ document.addEventListener('DOMContentLoaded', initFam); } else { setTimeout(initFam, 180); }
})();
</script>
