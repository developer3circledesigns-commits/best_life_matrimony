<?php // FaqSection.php — reproduces FaqSection.tsx with vanilla JS accordion (React state -> DOM classes) ?>
<section class="relative py-24 px-4 sm:px-6 lg:px-8 bg-[#fdf9f1] border-y border-[#e8d9b5]">
  <div class="max-w-5xl mx-auto">
    <div class="reveal text-center max-w-3xl mx-auto mb-16">
      <div class="inline-flex items-center gap-2 rounded-full border border-[#e8d9b5] bg-white px-4 py-1 text-xs font-semibold uppercase tracking-widest text-[#800020] mb-3 shadow-sm"><span>?</span><span>Got Questions?</span></div>
      <h2 class="font-serif text-3xl sm:text-5xl font-bold tracking-tight text-[#2b1a1e] leading-tight">Frequently Asked <span class="bg-gradient-to-r from-[#800020] via-[#9a3350] to-[#c9a227] bg-clip-text text-transparent italic">Questions.</span></h2>
      <p class="mt-4 text-sm sm:text-base text-[#5a3a3f] leading-relaxed">Everything you need to know about registering, searching, and connecting.</p>
    </div>

    <?php
    $faqs = [
      ['q'=>'How do I register on BestLife Matrimony?','a'=>"Click on 'Register Now' at the top of the page. Fill in your basic personal details, upload authentic photographs, and specify your partner preferences and lifestyle expectations to complete your profile in under 3 minutes."],
      ['q'=>'Can I search for profiles based on my preferences?','a'=>'Yes! You can explore profiles based on granular search criteria such as age group, native location, current city, education degrees, profession, family background, horoscope details, and personal lifestyle choices.'],
      ['q'=>'Is BestLife Matrimony only for individuals?','a'=>'No. Parents, siblings, and guardians can actively create profiles, manage accounts, and participate in the matrimonial search and profile shortlisting process alongside the candidate.'],
      ['q'=>'Can I edit my profile after registration?','a'=>'Yes. Once registered and logged in, you can update your photos, career details, contact numbers, and partner preference settings anytime from your dashboard.'],
      ['q'=>'How can I contact a profile?','a'=>"Once logged in, you can send an 'Express Interest' request or unlock direct contact numbers according to the platform's verified communication and safety rules."],
    ];
    ?>
    <div class="stagger space-y-4" role="region" aria-label="Frequently asked questions">
      <?php foreach ($faqs as $idx => $faq): $isOpen = $idx===0; ?>
        <div class="faq-item overflow-hidden rounded-2xl border bg-white shadow-sm transition-all duration-300 <?php echo $isOpen ? 'is-open border-[#e8d9b5] shadow-md' : 'border-[#f0e2c0] hover:border-[#e8d9b5] hover:shadow-md'; ?>">
          <button type="button" class="faq-trigger flex w-full items-center justify-between p-6 text-left transition-colors" aria-expanded="<?php echo $isOpen ? 'true':'false'; ?>" aria-controls="faq-answer-<?php echo $idx; ?>" id="faq-question-<?php echo $idx; ?>">
            <span class="font-serif text-lg sm:text-xl font-semibold text-[#2b1a1e] pr-4"><?php echo htmlspecialchars($faq['q']); ?></span>
            <span class="faq-icon flex h-8 w-8 shrink-0 items-center justify-center rounded-full border <?php echo $isOpen ? 'bg-[#800020] border-[#800020] text-white' : 'border-[#e8d9b5] bg-[#fdf9f1] text-[#800020]'; ?>" aria-hidden="true">
              <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
            </span>
          </button>
          <div id="faq-answer-<?php echo $idx; ?>" role="region" aria-labelledby="faq-question-<?php echo $idx; ?>" class="faq-answer">
            <div class="faq-answer-inner">
              <div class="px-6 pb-6 pt-3 text-sm sm:text-base text-[#5a3a3f] leading-relaxed border-t border-[#f0e2c0]"><?php echo htmlspecialchars($faq['a']); ?></div>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
