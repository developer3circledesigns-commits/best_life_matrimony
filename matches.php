<?php
$pageTitle = 'Profile Matches — BestLife Matrimony';
$pageDescription = 'Explore compatible profiles curated just for you on BestLife Matrimony.';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="flex-1 bg-transparent">
  <section class="mx-auto w-full max-w-6xl px-4 py-16 sm:px-6 sm:py-24">
    <div class="reveal max-w-3xl rounded-3xl border border-[#f6e6b4]/20 bg-black/35 p-8 sm:p-12 backdrop-blur-md shadow-2xl">
      <h1 class="font-serif text-4xl font-bold tracking-tight text-[#fff6e8] sm:text-5xl">Profile Matches</h1>
      <p class="mt-6 text-lg text-[#f3e6d8]/90">Explore compatible profiles curated just for you.</p>
      <div class="mt-8 grid sm:grid-cols-2 gap-4 text-sm text-[#fff6e8]/80">
        <p>Advanced filters: age, location, education, profession, family values.</p>
        <p>Verified profiles with photo & ID checks.</p>
      </div>
      <a href="./index.php" class="mt-8 inline-flex h-11 items-center justify-center rounded-full border border-[#f6e6b4]/50 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 font-semibold text-[#3a0c15] shadow-lg hover:scale-105 transition-transform">Back to Home</a>
    </div>
    <!-- Reuse featured profiles showcase for visual parity -->
    <?php include __DIR__ . '/sections/featured.php'; ?>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; require_once __DIR__ . '/includes/scripts.php'; ?>
