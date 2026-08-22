<?php
$pageTitle = 'BestLife Matrimony — Find Someone Who Makes Life Better.';
$pageDescription = 'Where meaningful connections become lifelong relationships. Genuine profiles, meaningful preferences, and trusted connections.';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="flex-1 bg-transparent">
  <div class="relative min-h-screen bg-[#0c0205] text-[#fff6e8] selection:bg-[#dcb04a] selection:text-[#3a0c15]">
    <?php include __DIR__ . '/sections/hero.php'; ?>
    <?php include __DIR__ . '/sections/intro.php'; ?>
    <?php include __DIR__ . '/sections/why.php'; ?>
    <?php include __DIR__ . '/sections/featured.php'; ?>
    <?php include __DIR__ . '/sections/families.php'; ?>
    <?php include __DIR__ . '/sections/stats.php'; ?>
    <?php include __DIR__ . '/sections/faq.php'; ?>
    <?php include __DIR__ . '/sections/cta.php'; ?>
  </div>
</main>
<?php
require_once __DIR__ . '/includes/footer.php';
require_once __DIR__ . '/includes/scripts.php';
?>
