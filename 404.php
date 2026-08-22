<?php
http_response_code(404);
$pageTitle = '404 — Page Not Found — BestLife Matrimony';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="flex-1 bg-transparent">
  <section class="mx-auto flex w-full max-w-6xl flex-col items-center justify-center px-4 py-24 text-center sm:px-6">
    <div class="reveal rounded-3xl border border-[#f6e6b4]/20 bg-black/35 p-8 sm:p-12 backdrop-blur-md shadow-2xl">
      <p class="font-serif text-7xl font-bold tracking-tight text-[#e3c877]">404</p>
      <h1 class="mt-4 text-2xl font-semibold text-[#fff6e8]">Page not found</h1>
      <p class="mt-2 text-[#f3e6d8]/80">The page you are looking for doesn't exist or has been moved.</p>
      <a href="./index.php" class="mt-8 inline-flex h-11 items-center justify-center gap-2 rounded-full border border-[#f6e6b4]/50 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 font-semibold text-[#3a0c15] shadow-lg">
        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
        Back to Home
      </a>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; require_once __DIR__ . '/includes/scripts.php'; ?>
