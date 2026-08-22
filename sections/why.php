<?php // WhyBestLifeSection.php — reproduces WhyBestLifeSection.tsx ?>
<section class="relative py-24 px-4 sm:px-6 lg:px-8 bg-[#800020]">
  <div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="reveal text-center max-w-3xl mx-auto mb-16">
      <p class="text-xs font-bold uppercase tracking-widest text-[#e3c877] mb-3">The BestLife Advantage</p>
      <h2 class="font-serif text-3xl sm:text-5xl font-bold tracking-tight text-[#fff6e8] leading-tight">
        More Than Profiles. We Focus On
        <span class="bg-gradient-to-r from-[#f6e6b4] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic"> Compatibility.</span>
      </h2>
      <p class="mt-4 text-base sm:text-lg text-[#fff6e8]/80 leading-relaxed">Choosing a life partner is not about finding the most profiles. It's about finding the right profile.</p>
    </div>

    <!-- 6 Reasons Grid -->
    <?php
    $reasons = [
      ['icon'=>'heart','title'=>'Genuine Connections','desc'=>'A platform purpose-built for people looking for serious, meaningful, and lifelong relationships.'],
      ['icon'=>'sliders','title'=>'Preference-Based Discovery','desc'=>'Find profiles according to specific values, qualities, and lifestyle expectations that matter to you.'],
      ['icon'=>'sparkles','title'=>'Simple & Easy to Use','desc'=>'A clean, modern matrimonial interface without clutter, spam, or unnecessary complexities.'],
      ['icon'=>'shield','title'=>'Privacy & Respect','desc'=>'Your personal contact details and matrimonial journey are safeguarded with strict privacy controls.'],
      ['icon'=>'users','title'=>'For Individuals & Families','desc'=>'A collaborative platform where candidates and families can participate together in the search.'],
      ['icon'=>'link','title'=>'Built Around Relationships','desc'=>'We believe matrimonial platforms should help spark real connections—not just hoard profile collections.'],
    ];
    // Icon SVGs map (lucide)
    $iconSvgs = [
      'heart' => '<path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2.08C10.5 3.5 9.26 3 7.5 3A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7 7-7Z"/>',
      'sliders' => '<line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/>',
      'sparkles' => '<path d="M11 We" />', // placeholder will use star
      'shield' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/>',
      'users' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
      'link' => '<path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>',
    ];
    ?>
    <div class="stagger grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6" role="list">
      <?php foreach ($reasons as $i => $item): ?>
        <div class="relative rounded-2xl border border-white/15 bg-black/40 p-7 backdrop-blur-xl card-hover" role="listitem">
          <div class="flex h-12 w-12 items-center justify-center rounded-xl border border-[#f6e6b4]/30 bg-gradient-to-br from-[#dcb04a]/20 to-black/30 text-[#e3c877] mb-5" aria-hidden="true">
            <?php if ($item['icon']==='heart'): ?>
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2.08C10.5 3.5 9.26 3 7.5 3A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7 7-7Z"/></svg>
            <?php elseif ($item['icon']==='sliders'): ?>
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="21" x2="4" y2="14"/><line x1="4" y1="10" x2="4" y2="3"/><line x1="12" y1="21" x2="12" y2="12"/><line x1="12" y1="8" x2="12" y2="3"/><line x1="20" y1="21" x2="20" y2="16"/><line x1="20" y1="12" x2="20" y2="3"/><line x1="1" y1="14" x2="7" y2="14"/><line x1="9" y1="8" x2="15" y2="8"/><line x1="17" y1="16" x2="23" y2="16"/></svg>
            <?php elseif ($item['icon']==='sparkles'): ?>
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l1.9 5.1L19 10l-5.1 1.9L12 17l-1.9-5.1L5 10l5.1-1.9L12 3Z"/><path d="M19 14l1 2 2 1-2 1-1 2-1-2-2-1 2-1 1-2Z"/><path d="M5 14l1 2 2 1-2 1-1 2-1-2-2-1 2-1 1-2Z"/></svg>
            <?php elseif ($item['icon']==='shield'): ?>
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
            <?php elseif ($item['icon']==='users'): ?>
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <?php else: ?>
              <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
            <?php endif; ?>
          </div>
          <h3 class="font-serif text-xl font-bold text-[#fff6e8] mb-2"><?php echo htmlspecialchars($item['title']); ?></h3>
          <p class="text-sm text-[#fff6e8]/75 leading-relaxed"><?php echo htmlspecialchars($item['desc']); ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
