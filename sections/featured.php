<?php // FeaturedMatchesSection.php — reproduces FeaturedMatchesSection.tsx with PHP loop for profiles ?>
<section class="relative py-24 px-4 sm:px-6 lg:px-8 bg-[#fdf9f1] border-y border-[#e8d9b5]">
  <div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="reveal text-center max-w-3xl mx-auto mb-16">
      <div class="inline-flex items-center gap-2 rounded-full border border-[#e8d9b5] bg-white px-4 py-1 text-xs font-semibold uppercase tracking-widest text-[#800020] mb-3 shadow-sm">
        <span>✦</span><span>Curated Showcase</span>
      </div>
      <h2 class="font-serif text-3xl sm:text-5xl font-bold tracking-tight text-[#2b1a1e] leading-tight">
        Maybe Your Search <span class="bg-gradient-to-r from-[#800020] via-[#9a3350] to-[#c9a227] bg-clip-text text-transparent italic">Ends Here.</span>
      </h2>
      <p class="mt-4 text-base sm:text-lg text-[#5a3a3f] leading-relaxed">A few profiles. A few possibilities. One meaningful connection.</p>
    </div>

    <!-- 4 Profile Cards Grid -->
    <?php
    $profiles = [
      ['id'=>'BLM-1082','name'=>'Dr. Ananya S.','age'=>27,'height'=>"5' 5\"",'location'=>'Chennai, Tamil Nadu','education'=>'MBBS, MD (General Medicine)','profession'=>'Consultant Physician','religion'=>'Hindu / Iyer'],
      ['id'=>'BLM-1094','name'=>'Karthik R.','age'=>29,'height'=>"5' 11\"",'location'=>'Bengaluru, Karnataka','education'=>'B.Tech (IIT Madras), MS','profession'=>'Senior Product Manager','religion'=>'Hindu / Mudaliar'],
      ['id'=>'BLM-1120','name'=>'Pooja V.','age'=>26,'height'=>"5' 4\"",'location'=>'Coimbatore, Tamil Nadu','education'=>'Chartered Accountant (CA)','profession'=>'Finance Specialist','religion'=>'Hindu / Chettiar'],
      ['id'=>'BLM-1145','name'=>'Dr. Siddharth M.','age'=>31,'height'=>"6' 0\"",'location'=>'Hyderabad, Telangana','education'=>'MS (Ortho), Fellowship (UK)','profession'=>'Orthopaedic Surgeon','religion'=>'Hindu / Brahmin'],
    ];
    ?>
    <div class="stagger grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php foreach ($profiles as $p): ?>
        <div class="relative flex flex-col justify-between overflow-hidden rounded-2xl border border-white/15 bg-black/45 p-6 backdrop-blur-xl card-hover">
          <div>
            <div class="flex items-center justify-between mb-4">
              <span class="text-[11px] font-mono font-semibold tracking-wider text-[#f6e6b4]/70 bg-white/5 px-2.5 py-1 rounded-md border border-white/10"><?php echo htmlspecialchars($p['id']); ?></span>
              <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-emerald-400 bg-emerald-950/40 px-2.5 py-0.5 rounded-full border border-emerald-500/30">
                <svg class="h-3 w-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m9 11 3 3L22 4"/></svg> Verified
              </span>
            </div>
            <div class="mb-5 flex h-20 w-20 mx-auto items-center justify-center rounded-full border-2 border-[#f6e6b4]/40 bg-gradient-to-br from-[#dcb04a]/20 to-black text-2xl font-serif font-bold text-[#f6e6b4]"><?php echo htmlspecialchars(mb_substr($p['name'],0,1)); ?></div>
            <div class="text-center mb-5">
              <h3 class="font-serif text-xl font-bold text-[#fff6e8]"><?php echo htmlspecialchars($p['name']); ?></h3>
              <p class="text-xs text-[#f4e3c9]/80 mt-1"><?php echo $p['age']; ?> Yrs • <?php echo htmlspecialchars($p['height']); ?> • <?php echo htmlspecialchars($p['religion']); ?></p>
            </div>
            <div class="space-y-2.5 text-xs text-[#fff6e8]/80 border-t border-white/10 pt-4">
              <div class="flex items-center gap-2"><svg class="h-3.5 w-3.5 text-[#e3c877] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><span class="truncate"><?php echo htmlspecialchars($p['location']); ?></span></div>
              <div class="flex items-center gap-2"><svg class="h-3.5 w-3.5 text-[#e3c877] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 10v6"/><path d="M22 10 13 5.5a2 2 0 0 0-2 0L2 10l9 5.5a2 2 0 0 0 2 0L22 10Z"/><path d="M6 12.5V16a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2v-3.5"/></svg><span class="truncate"><?php echo htmlspecialchars($p['education']); ?></span></div>
              <div class="flex items-center gap-2"><svg class="h-3.5 w-3.5 text-[#e3c877] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg><span class="truncate"><?php echo htmlspecialchars($p['profession']); ?></span></div>
            </div>
          </div>
          <div class="mt-6 pt-4 border-t border-white/10">
            <a href="./matches.php" class="inline-flex w-full items-center justify-center rounded-xl border border-[#f6e6b4]/30 bg-white/5 px-4 py-2.5 text-xs font-semibold text-[#fff6e8] hover:bg-[#e3c877] hover:text-[#3a0c15] hover:border-[#e3c877] transition-all">View Profile</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="reveal mt-12 text-center">
      <a href="./matches.php" class="inline-flex items-center gap-2 text-base font-semibold text-[#800020] hover:text-[#9a3350] transition-colors group">
        <span>Explore More Profiles</span>
        <svg class="h-4 w-4 group-hover:translate-x-1 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
      </a>
    </div>
  </div>
</section>
