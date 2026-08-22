<?php // IntroSection.php — hero intro with staggered entrance and pinned reveal ?>
<section id="intro" class="relative min-h-screen h-screen flex items-center overflow-hidden border-t border-white/10 py-16 lg:py-0" style="background: linear-gradient(145deg, #3a0c15 0%, #4a1322 20%, #5e1e2e 35%, #6e2a2a 52%, #8a4a2f 68%, #a67d3a 82%, #c9a86a 90%, #e3c877 96%, #f6e6b4 100%);">
  <div class="absolute inset-0 pointer-events-none" style="background: radial-gradient(800px 400px at 50% 12%, rgba(255,246,232,0.07) 0%, transparent 70%);"></div>
  <div id="intro-inner" class="relative max-w-6xl w-full mx-auto px-6 sm:px-6 lg:px-8" style="will-change:transform,opacity">
    <div class="grid lg:grid-cols-2 gap-12 items-center">
      <div>
        <span class="inline-flex rounded-none border border-white/20 bg-white/10 px-3 py-1.5 text-[11px] tracking-[0.16em] uppercase text-white font-bold" style="border-radius:0">BestLife Matrimony</span>
        <h2 class="font-serif text-5xl sm:text-6xl md:text-7xl font-bold tracking-tight text-[#fff6e8] leading-[1.08] mt-4">Find Someone Who<br><span class="bg-gradient-to-r from-[#fbf1d3] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic">Makes Life Better.</span></h2>
        <p class="mt-6 text-sm leading-6 text-white/80">Whether you're beginning your search or helping someone you love find their life partner, we're here to make the journey easier.</p>
        <a href="./register.php" class="mt-8 inline-flex h-10 px-6 rounded-none bg-white text-[#3a0c15] text-sm font-bold items-center gap-2" style="border-radius:0">Register Now →</a>
      </div>
      <div class="snap-center shrink-0 w-full lg:w-auto">
        <div class="h-[50vh] lg:h-[70vh] overflow-hidden bg-[#1a0a0f]"><img src="<?php echo asset('images/intro.jpg'); ?>" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=1200&q=80&auto=format&fit=crop'" alt="Real Indian couple" class="w-full h-full object-contain" loading="lazy"></div>
      </div>
    </div>
  </div>
</section>

<!-- GSAP plugins — loaded once in assets/js/plugins.js -->
<script src="assets/js/plugins.js"></script>
