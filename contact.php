<?php
$pageTitle = 'Contact — BestLife Matrimony';
$pageDescription = 'Get in touch with the BestLife Matrimony team.';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="flex-1 bg-transparent">
  <section class="mx-auto w-full max-w-6xl px-4 py-16 sm:px-6 sm:py-24">
    <div class="grid lg:grid-cols-2 gap-8">
      <div class="reveal max-w-3xl rounded-3xl border border-[#f6e6b4]/20 bg-black/35 p-8 sm:p-12 backdrop-blur-md shadow-2xl">
        <h1 class="font-serif text-4xl font-bold tracking-tight text-[#fff6e8] sm:text-5xl">Contact us</h1>
        <p class="mt-6 text-lg text-[#f3e6d8]/90">Get in touch with the BestLife Matrimony team. We usually respond within 24 hours.</p>
        <ul class="mt-6 space-y-3 text-sm text-[#fff6e8]/80">
          <li class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-[#e3c877]"></span> +91 98765 43210</li>
          <li class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-[#e3c877]"></span> info@bestlifematrimony.com</li>
          <li class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-[#e3c877]"></span> Chennai, Tamil Nadu, India</li>
        </ul>
        <a href="./index.php" class="mt-8 inline-flex h-11 items-center justify-center rounded-full border border-[#f6e6b4]/50 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 font-semibold text-[#3a0c15] shadow-lg hover:scale-105 transition-transform">Back to Home</a>
      </div>
      <!-- Contact Form — preserves fields/labels/validation parity -->
      <form class="reveal reveal-delay-1 rounded-3xl border border-[#f6e6b4]/20 bg-white p-6 sm:p-8 shadow-xl text-[#2b1a1e]" method="post" action="./contact.php" novalidate>
        <h2 class="font-serif text-xl font-bold">Send us a message</h2>
        <div class="mt-6 grid gap-4">
          <label class="text-sm font-medium">Full Name
            <input type="text" name="name" required placeholder="Your full name" class="mt-1 w-full rounded-xl border border-[#e8d9b5] bg-[#fdf9f1] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877]" />
          </label>
          <label class="text-sm font-medium">Email
            <input type="email" name="email" required placeholder="you@example.com" class="mt-1 w-full rounded-xl border border-[#e8d9b5] bg-[#fdf9f1] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877]" />
          </label>
          <label class="text-sm font-medium">Message
            <textarea name="message" rows="4" required placeholder="How can we help?" class="mt-1 w-full rounded-xl border border-[#e8d9b5] bg-[#fdf9f1] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877]"></textarea>
          </label>
          <?php if (($_SERVER['REQUEST_METHOD'] ?? 'GET')==='POST' && !empty($_POST['name'])): ?>
            <div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">Thank you, <?php echo htmlspecialchars($_POST['name']); ?>! Your message has been received (demo). We'll get back to you soon.</div>
          <?php endif; ?>
          <button type="submit" class="inline-flex h-11 items-center justify-center rounded-full bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-sm font-bold text-[#3a0c15] hover:brightness-110 transition-all">Send Message</button>
        </div>
      </form>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; require_once __DIR__ . '/includes/scripts.php'; ?>
