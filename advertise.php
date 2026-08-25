<?php
$pageTitle = 'Advertise With Us — BestLife Matrimony';
$pageDescription = 'Advertise where trust is earned. Brand-safe, moderated, verified audience.';
$pageHeadExtra = <<<HTML
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  body{font-family:'Inter',sans-serif;background:#fff;color:#111}
  div.bg-\[\#0c0205\]{background:#fff}
  div.text-\[\#fff6e8\]{color:#111}
  .navbar-root{background:rgba(255,255,255,.9) !important;backdrop-filter:blur(10px);border-bottom:1px solid #eee !important}
  footer{background:#fff !important;border-top:1px solid #eee !important}
</style>
HTML;
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="flex-1 bg-white text-[#111]">
  <section class="mx-auto max-w-[900px] px-4 py-12 text-center">
    <h1 class="mt-3 text-[36px] font-bold leading-[1.05] tracking-[-.02em] text-[#111]">Advertise<br>where trust is<br>earned.</h1>
    <p class="mx-auto mt-3 max-w-[560px] text-[15px] leading-6 text-[#666]">Brand-safe, moderated, verified audience. No clutter. No dark patterns. Just families making one of life's most important decisions — and the brands they invite in.</p>
    <div class="mt-5 flex flex-wrap justify-center gap-2">
      <a href="./contact.php" class="inline-flex items-center gap-2 bg-[#111] px-5 py-3 text-sm font-semibold text-white hover:opacity-90">Enquire <i class="bi bi-arrow-right"></i></a>
      <a href="./contact.php" class="inline-flex items-center gap-2 border border-[#ddd] bg-white px-5 py-3 text-sm font-semibold text-[#111] hover:bg-[#fafafa]"><i class="bi bi-file-earmark-text"></i> Brand Guidelines PDF</a>
    </div>
    <p class="mt-3 text-xs text-[#8a7a6a]">Reply in 4 hours • Media kit • Rate card on request</p>
  </section>

  <section class="mx-auto grid max-w-[900px] grid-cols-1 gap-3.5 px-4 pb-6 md:grid-cols-3" aria-label="Trust pillars">
    <div class="border border-[#eee] bg-white px-4 py-6 text-center">
      <i class="bi bi-shield-lock text-[22px] text-[#111]"></i>
      <p class="mt-2 text-sm font-bold text-[#111]">Brand Safe</p>
      <p class="mt-1 text-xs leading-5 text-[#666]">Human moderated, verified profiles only</p>
    </div>
    <div class="border border-[#eee] bg-white px-4 py-6 text-center">
      <i class="bi bi-eye-slash text-[22px] text-[#111]"></i>
      <p class="mt-2 text-sm font-bold text-[#111]">No Tracking Tricks</p>
      <p class="mt-1 text-xs leading-5 text-[#666]">Consent-first, pixel optional</p>
    </div>
    <div class="border border-[#eee] bg-white px-4 py-6 text-center">
      <i class="bi bi-receipt text-[22px] text-[#111]"></i>
      <p class="mt-2 text-sm font-bold text-[#111]">Transparent</p>
      <p class="mt-1 text-xs leading-5 text-[#666]">Clear specs, invoices, reports</p>
    </div>
  </section>

  <section class="mx-auto max-w-[900px] px-4 pb-10 text-center">
    <p class="text-xs text-[#8a7a6a]">Trusted by 40+ partners • Jewellery, venues, hospitality</p>
    <div class="mt-3 flex justify-center gap-4 text-[22px] text-[#111] opacity-50">
      <i class="bi bi-gem" aria-hidden="true"></i>
      <i class="bi bi-buildings" aria-hidden="true"></i>
      <i class="bi bi-camera" aria-hidden="true"></i>
      <i class="bi bi-airplane" aria-hidden="true"></i>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; require_once __DIR__ . '/includes/scripts.php'; ?>
