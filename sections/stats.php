<?php // StatsSection.php — reproduces StatsSection.tsx ?>
<section class="relative py-24 px-4 sm:px-6 lg:px-8 bg-[#800020]">
  <div class="max-w-7xl mx-auto">
    <div class="reveal text-center max-w-3xl mx-auto mb-16">
      <p class="text-xs font-bold uppercase tracking-widest text-[#e3c877] mb-3">Real Impact & Growth</p>
      <h2 class="font-serif text-3xl sm:text-5xl font-bold tracking-tight text-[#fff6e8] leading-tight">A Community Built Around <span class="bg-gradient-to-r from-[#f6e6b4] via-[#e3c877] to-[#dcb04a] bg-clip-text text-transparent italic">New Beginnings.</span></h2>
      <p class="mt-4 text-sm sm:text-base text-[#fff6e8]/75 leading-relaxed">Transparent metrics reflecting our dedicated community of individuals and families.</p>
    </div>
    <?php
    $stats = [
      ['icon'=>'users','value'=>'50,000+','label'=>'Registered Profiles','detail'=>'Verified matrimonial aspirants'],
      ['icon'=>'check','value'=>'12,500+','label'=>'Active Members','detail'=>'Engaged weekly in discovery'],
      ['icon'=>'plus','value'=>'1,200+','label'=>'Profiles Added Weekly','detail'=>'Fresh compatible connections'],
      ['icon'=>'globe','value'=>'45+','label'=>'Cities & Locations','detail'=>'Pan-India & Global NRI networks'],
    ];
    ?>
    <div class="stagger grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php foreach ($stats as $s): ?>
        <div class="relative overflow-hidden rounded-2xl border border-white/15 bg-black/45 p-8 text-center backdrop-blur-xl card-hover">
          <div class="flex h-12 w-12 mx-auto items-center justify-center rounded-xl border border-[#f6e6b4]/30 bg-white/5 text-[#e3c877] mb-4">
            <?php if ($s['icon']=='users'): ?><svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <?php elseif ($s['icon']=='check'): ?><svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m16 11 2 2 4-4"/></svg>
            <?php elseif ($s['icon']=='plus'): ?><svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
            <?php else: ?><svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M2 12h20"/><path d="M12 2a15 15 0 0 1 0 20a15 15 0 0 1 0-20"/></svg>
            <?php endif; ?>
          </div>
          <div class="font-serif text-3xl sm:text-4xl font-extrabold text-[#f6e6b4] tracking-tight mb-2"><?php echo htmlspecialchars($s['value']); ?></div>
          <div class="text-sm font-bold text-[#fff6e8] mb-1"><?php echo htmlspecialchars($s['label']); ?></div>
          <div class="text-xs text-[#fff6e8]/60"><?php echo htmlspecialchars($s['detail']); ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
