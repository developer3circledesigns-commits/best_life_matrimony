<?php // FinalCtaSection.php — reproduces FinalCtaSection.tsx ?>
<section class="relative py-28 px-4 sm:px-6 lg:px-8 bg-[#800020]">
  <div class="max-w-7xl mx-auto">
    <div class="reveal-scale relative overflow-hidden rounded-3xl border border-[#f6e6b4]/40 bg-gradient-to-b from-black/85 via-black/70 to-black/90 p-8 sm:p-16 text-center backdrop-blur-2xl">
      <h2 class="font-serif text-3xl sm:text-5xl md:text-6xl font-bold tracking-tight text-[#fff6e8] leading-tight mb-6">Ready to Meet <span class="bg-gradient-to-r from-[#f6e6b4] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic">Someone Special?</span></h2>
      <p class="text-base sm:text-lg text-[#f4e3c9]/90 max-w-2xl mx-auto mb-10 leading-relaxed">Create your BestLife Matrimony profile and take the first step towards a meaningful relationship and a joyful future.</p>
      <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
        <a href="<?php echo register_cta_href(); ?>" class="inline-flex h-13 w-full sm:w-auto items-center justify-center gap-2 rounded-full border border-[#f6e6b4]/60 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-10 text-base font-bold text-[#3a0c15] shadow-[0_16px_36px_-12px_rgba(220,176,74,0.85)] hover:scale-105 transition-all">
          <?php echo register_cta_label(); ?> <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
        <a href="./matches.php" class="inline-flex h-13 w-full sm:w-auto items-center justify-center rounded-full border border-white/30 bg-white/10 px-9 text-base font-semibold text-[#fff6e8] backdrop-blur-md hover:bg-white/20 hover:border-white/50 transition-all">Browse Matches</a>
      </div>
      <div class="mt-12 pt-8 border-t border-white/10 text-xs sm:text-sm font-serif font-bold uppercase tracking-widest text-[#e3c877]/80">BestLife Matrimony — Where Connections Become Beginnings.</div>
    </div>
  </div>
</section>
