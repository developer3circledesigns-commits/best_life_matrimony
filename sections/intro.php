<?php // IntroSection.php — reproduces IntroSection.tsx with parallax and reveal staggered animations ?>
<section data-intro-section class="relative z-20 py-20 sm:py-28 px-4 sm:px-6 lg:px-8 overflow-hidden section-overlap border-t border-[#f6e6b4]/25 bg-[#0c0205]">
  <div data-intro-inner class="relative max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-14 items-center reveal-scale">
    <!-- Left copy -->
    <div class="lg:col-span-7">
      <div class="reveal reveal-delay-1">
        <span class="inline-flex items-center gap-2 rounded-full border border-[#f6e6b4]/20 bg-white/[0.06] px-4 py-1.5 text-[11px] font-bold uppercase tracking-[0.18em] text-[#e3c877] backdrop-blur-md">Brand Promise</span>
      </div>

      <h2 class="reveal reveal-delay-2 mt-5 font-serif text-3xl sm:text-4xl lg:text-[42px] font-bold leading-[1.1] tracking-tight text-[#fff6e8]">
        Your Best Life Could Begin With
        <span class="bg-gradient-to-r from-[#f6e6b4] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic"> One Connection.</span>
      </h2>

      <div class="reveal reveal-delay-3 mt-6 space-y-4 text-[15px] leading-[1.85] text-[#fff6e8]/80">
        <p>Finding the right life partner is one of life's most important decisions. At BestLife Matrimony, we make the journey simpler, more personal and more meaningful.</p>
        <p>Discover profiles based on your preferences, connect with compatible individuals and take the first step towards building a beautiful future together.</p>
        <p class="font-medium text-[#f6e6b4]">Because the right match isn't just about finding someone. It's about finding someone who fits your life.</p>
      </div>

      <div class="reveal reveal-delay-4 mt-8">
        <a href="./about.php" class="inline-flex h-12 items-center justify-center gap-2 rounded-full border border-[#f6e6b4]/40 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-[15px] font-bold text-[#3a0c15] shadow-xl shadow-[#dcb04a]/20 hover:scale-[1.02] hover:brightness-110 transition-all">
          Know More About Us
          <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
        </a>
      </div>
    </div>

    <!-- Right features list -->
    <div class="lg:col-span-5">
      <div class="reveal-right relative rounded-[2rem] border border-white/10 bg-gradient-to-br from-white/[0.08] to-white/[0.03] p-6 sm:p-7 backdrop-blur-xl">
        <p class="text-xs font-bold uppercase tracking-widest text-[#e3c877]/80 mb-4">Why families trust us</p>
        <ul class="space-y-4">
          <?php
          $reasonsIntro = [
            ['t' => 'Genuine, verified profiles', 'd' => 'Photo & ID checked before you see them.'],
            ['t' => 'Preference-first search', 'd' => 'Age, city, education, profession & values.'],
            ['t' => 'Privacy with dignity', 'd' => 'You control what is visible and when.'],
          ];
          foreach ($reasonsIntro as $idx => $r): ?>
            <li class="flex gap-3 rounded-2xl border border-white/10 bg-black/25 px-4 py-4 card-hover">
              <span class="mt-1 h-2 w-2 shrink-0 rounded-full bg-[#e3c877]"></span>
              <div>
                <p class="text-sm font-semibold text-[#fff6e8]"><?php echo htmlspecialchars($r['t']); ?></p>
                <p class="text-xs leading-relaxed text-[#fff6e8]/70"><?php echo htmlspecialchars($r['d']); ?></p>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
        <div class="mt-6 rounded-xl bg-[#f6e6b4] px-4 py-3 text-center">
          <p class="font-serif text-sm font-bold text-[#3a0c15]">Trusted Profiles • Meaningful Matches • A Better Way to Begin</p>
        </div>
      </div>
    </div>
  </div>
</section>
