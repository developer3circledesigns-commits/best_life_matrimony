<?php // Profile Matches Section — Matches Sample 05 Marquee Stagger — lag-free ?>
<section id="profile-matches" class="relative min-h-screen flex flex-col justify-center py-16 lg:py-20 overflow-hidden will-change-transform" style="background: linear-gradient(135deg, #3a0c15 0%, #4a1322 18%, #5e1e2e 32%, #6e2a2a 48%, #8a4a2f 64%, #a67d3a 78%, #c9a86a 88%, #e3c877 94%, #f6e6b4 100%); min-height:100vh; min-height:100svh; margin-top:-40px; border-radius:0; box-shadow:0 -20px 60px rgba(0,0,0,0.35); z-index:10; will-change:transform;">
  <!-- vignette to keep text legible on gold wash -->  <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(900px 600px at 30% 15%, rgba(58,12,21,0.45) 0%, rgba(58,12,21,0.18) 42%, transparent 72%);"></div>
  <!-- Marquee background — horizontal parallax -->
  <div class="absolute inset-0 flex flex-col justify-center gap-4 opacity-[0.05] pointer-events-none select-none overflow-hidden" aria-hidden="true">
    <div id="marquee-pm" class="flex gap-8 whitespace-nowrap text-[10vw] font-serif font-bold will-change-transform" style="will-change:transform"><span>Perfect Match • </span><span>Perfect Match • </span><span>Perfect Match • </span><span>Perfect Match • </span></div>
    <div id="marquee-pm-2" class="flex gap-8 whitespace-nowrap text-[8vw] font-serif italic will-change-transform" style="will-change:transform"><span>Age • Education • Family • Preferences • </span><span>Age • Education • Family • Preferences • </span><span>Age • Education • Family • Preferences • </span></div>
  </div>

  <div class="relative max-w-6xl mx-auto px-6">
    <div class="max-w-3xl mx-auto text-center">
      <span class="inline-flex rounded-full border border-[#e3c877]/20 bg-white/5 px-4 py-1.5 text-[11px] tracking-[0.16em] uppercase text-[#e3c877] font-bold">Profile Matches</span>
      <h2 class="mt-4 font-serif text-[34px] sm:text-[44px] font-bold leading-[0.9] tracking-tight text-[#fff6e8] text-center">
        Meet Profiles That Could Be<br><span class="bg-gradient-to-r from-[#f6e6b4] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic">Your Perfect Match.</span>
      </h2>
      <p class="mt-3 text-[15px] leading-7 text-white/60 max-w-2xl mx-auto text-center">Your ideal partner may be closer than you think. Explore our growing community of individuals looking for a meaningful relationship and a lifelong partner.</p>
    </div>

    <div id="grid-pm" class="mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
      <div class="stagger-pm rounded-none border border-white/10 bg-white/[0.04] p-6 backdrop-blur will-change-transform" style="will-change:transform,opacity; border-radius:0">
        <p class="text-[11px] tracking-[0.16em] uppercase text-[#e3c877] font-bold">01 — Age & Location</p>
        <h3 class="mt-3 font-bold text-[#fff6e8] text-[15px] leading-tight">Find Matches Based On Age & Location</h3>
        <p class="mt-2 text-sm leading-6 text-white/60">Discover profiles that match your preferred age group and location.</p>
      </div>
      <div class="stagger-pm rounded-none border border-white/10 bg-white/[0.04] p-6 backdrop-blur will-change-transform" style="will-change:transform,opacity; border-radius:0">
        <p class="text-[11px] tracking-[0.16em] uppercase text-[#e3c877] font-bold">02 — Education</p>
        <h3 class="mt-3 font-bold text-[#fff6e8] text-[15px] leading-tight">Education & Profession</h3>
        <p class="mt-2 text-sm leading-6 text-white/60">Find someone whose educational and professional background aligns with your expectations.</p>
      </div>
      <div class="stagger-pm rounded-none border border-white/10 bg-white/[0.04] p-6 backdrop-blur will-change-transform" style="will-change:transform,opacity; border-radius:0">
        <p class="text-[11px] tracking-[0.16em] uppercase text-[#e3c877] font-bold">03 — Family</p>
        <h3 class="mt-3 font-bold text-[#fff6e8] text-[15px] leading-tight">Family & Lifestyle</h3>
        <p class="mt-2 text-sm leading-6 text-white/60">Connect with people who share compatible family values, interests and lifestyles.</p>
      </div>
      <div class="stagger-pm rounded-none border border-white/10 bg-white/[0.04] p-6 backdrop-blur will-change-transform" style="will-change:transform,opacity; border-radius:0">
        <p class="text-[11px] tracking-[0.16em] uppercase text-[#e3c877] font-bold">04 — Preferences</p>
        <h3 class="mt-3 font-bold text-[#fff6e8] text-[15px] leading-tight">Personal Preferences</h3>
        <p class="mt-2 text-sm leading-6 text-white/60">Your preferences matter. Discover profiles based on the qualities that are important to you.</p>
      </div>
    </div>
    <div class="mt-8 flex justify-center">
      <a href="./matches.php" class="inline-flex h-12 px-7 rounded-none bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] text-[#3a0c15] font-bold text-sm items-center gap-2 hover:brightness-110 transition will-change-transform" style="will-change:transform; border-radius:0">
        View Profile Matches
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
      </a>
    </div>
    <p class="mt-3 text-center text-[11px] tracking-[0.16em] uppercase text-white/30">Preference-first • Verified • Private</p>
  </div>
</section>

<!-- GSAP + ScrollTrigger + Lenis sync — lag-free, uses global Lenis -->
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
<script>
(function(){
  if(window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if(typeof gsap === 'undefined' || typeof ScrollTrigger === 'undefined') return;
  gsap.registerPlugin(ScrollTrigger);

  function initPM(){
    var lenis = window.__lenis;
    if(lenis && lenis.on){
      lenis.on('scroll', ScrollTrigger.update);
    }

    // Apple Stack — pin intro, profile-matches slides over it
    ScrollTrigger.create({
      trigger: "#intro",
      start: "top top",
      end: "+=85%",
      pin: true,
      pinSpacing: false,
      anticipatePin: 1
    });
    gsap.to("#intro-inner", {
      y: -60,
      opacity: 0,
      scale: 0.97,
      ease: "none",
      scrollTrigger: { trigger: "#intro", start: "top top", end: "+=85%", scrub: 0.9 }
    });
    gsap.fromTo("#profile-matches",
      { y: 80, scale: 0.96 },
      { y: 0, scale: 1, ease: "none", scrollTrigger: { trigger: "#profile-matches", start: "top bottom", end: "top top", scrub: 0.9 } }
    );

    // Horizontal marquee parallax — only transform
    gsap.to("#marquee-pm", {
      xPercent: -18,
      ease: "none",
      scrollTrigger: { trigger: "#profile-matches", start: "top bottom", end: "bottom top", scrub: 1.2 }
    });
    gsap.to("#marquee-pm-2", {
      xPercent: 12,
      ease: "none",
      scrollTrigger: { trigger: "#profile-matches", start: "top bottom", end: "bottom top", scrub: 1 }
    });

    // Cards stagger — opacity + y only, once
    gsap.from(".stagger-pm", {
      y: 28,
      opacity: 0,
      stagger: 0.12,
      duration: 0.8,
      ease: "power3.out",
      scrollTrigger: { trigger: "#grid-pm", start: "top 85%", once: true }
    });

    window.addEventListener('load', function(){ ScrollTrigger.refresh(); });
  }

  if(document.readyState === 'loading'){
    document.addEventListener('DOMContentLoaded', initPM);
  } else {
    // delay to ensure intro GSAP registered first
    setTimeout(initPM, 120);
  }
})();
</script>
