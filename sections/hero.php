<?php // HeroSection.php — reproduces HeroSection.tsx including parallax layers and staggered entrance ?>
<section class="hero-section relative min-h-screen h-screen w-full flex flex-col items-center justify-center text-center px-4 sm:px-6 lg:px-8 pt-24 pb-20 overflow-hidden bg-[#3a0c15]" aria-label="Hero">
  <!-- Background Video — contained within hero only, with parallax translateY -->
  <div data-hero-bg class="hero-bg absolute inset-0 z-0 overflow-hidden pointer-events-none">
    <video
      data-hero-video
      src="./assets/videos/bride-groom-cinematic-hq.mp4"
      autoplay
      loop
      muted
      playsinline
      preload="auto"
      disablePictureInPicture
      class="h-full w-full object-cover select-none"
    ></video>
    <div class="absolute inset-0 bg-[#3a0c15]/20 pointer-events-none"></div>
  </div>

  <!-- Hero Content — replicates Framer motion initial/animate + parallax opacity/scale/y -->
  <div data-hero-content class="hero-content relative z-10 max-w-4xl mx-auto flex flex-col items-center">
    <!-- Main Title -->
    <h1 class="hero-entrance font-serif text-3xl sm:text-4xl md:text-5xl font-bold tracking-tight text-[#fff6e8] leading-[1.08]">
      Find Someone Who <span class="text-white italic">Makes Life Better.</span>
    </h1>

    <!-- Action Buttons -->
    <div class="hero-entrance-4 mt-9 flex flex-col sm:flex-row items-center justify-center gap-4 w-full sm:w-auto">
      <a href="<?php echo register_cta_href(); ?>" class="inline-flex h-12 sm:h-[52px] w-full sm:w-auto items-center justify-center gap-2 rounded-full border border-[#f6e6b4] bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-base font-bold text-[#3a0c15] transition-all hover:scale-[1.03] hover:brightness-110 active:scale-95 shadow-none">
        <?php echo register_cta_label(); ?>
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
      </a>
      <a href="./matches.php" class="inline-flex h-12 sm:h-[52px] w-full sm:w-auto items-center justify-center rounded-full border border-[#f6e6b4]/60 bg-[#3a0c15]/80 px-8 text-base font-semibold text-[#fff6e8] backdrop-blur-sm transition-all hover:bg-[#5a1322] hover:border-[#f6e6b4] active:scale-95">
        Explore Matches
      </a>
    </div>

    <!-- Trust Indicators -->
    <div class="hero-entrance-5 mt-14 inline-flex flex-wrap items-center justify-center gap-6 rounded-2xl border border-[#f6e6b4]/40 bg-[#3a0c15]/80 px-6 py-3.5 backdrop-blur-sm text-xs sm:text-sm text-[#f6e6b4]" role="list" aria-label="Platform trust indicators">
      <div class="flex items-center gap-2" role="listitem">
        <svg class="h-4 w-4 text-[#e3c877]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
        <span>Trusted Profiles</span>
      </div>
      <span class="text-[#e3c877]/60" aria-hidden="true">•</span>
      <div class="flex items-center gap-2" role="listitem">
        <svg class="h-4 w-4 text-[#e3c877]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2.08C10.5 3.5 9.26 3 7.5 3A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7 7-7Z"/><path d="M12 11v5"/></svg>
        <span>Meaningful Matches</span>
      </div>
      <span class="text-[#e3c877]/60" aria-hidden="true">•</span>
      <div class="flex items-center gap-2" role="listitem">
        <span class="h-4 w-4 text-[#e3c877] flex items-center justify-center" aria-hidden="true">✦</span>
        <span>A Better Way to Begin</span>
      </div>
    </div>
  </div>
</section>
<script>
(function(){
  var v=document.querySelector('[data-hero-video]');
  if(!v) return;
  v.loop = true;
  v.muted = true;
  v.setAttribute('loop','');
  v.setAttribute('autoplay','');
  v.setAttribute('playsinline','');
  function tryPlay(){
    var p=v.play();
    if(p&&p.catch) p.catch(function(){});
  }
  v.addEventListener('ended', function(){
    try{ v.currentTime=0; }catch(e){}
    tryPlay();
  });
  v.addEventListener('canplay', tryPlay, {once:true});
  document.addEventListener('visibilitychange', function(){
    if(!document.hidden) tryPlay();
  });
  // iOS / autoplay policy fallback — play on first interaction
  var once=function(){ tryPlay(); document.removeEventListener('click',once); document.removeEventListener('touchstart',once); };
  document.addEventListener('click', once, {once:true});
  document.addEventListener('touchstart', once, {once:true});
  tryPlay();
})();
</script>
