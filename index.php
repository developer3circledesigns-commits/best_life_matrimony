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
    <?php include __DIR__ . '/sections/profile-matches.php'; ?>
    <?php include __DIR__ . '/sections/how-it-works.php'; ?>
    <?php include __DIR__ . '/sections/why-bestlife.php'; ?>
    <?php include __DIR__ . '/sections/featured-matches.php'; ?>
    <?php include __DIR__ . '/sections/emotional.php'; ?>
    <?php include __DIR__ . '/sections/for-families.php'; ?>
    <?php include __DIR__ . '/sections/advertise.php'; ?>
    <?php include __DIR__ . '/sections/marquee.php'; ?>
    <?php include __DIR__ . '/sections/faq.php'; ?>
    <?php include __DIR__ . '/sections/marquee2.php'; ?>
    <?php include __DIR__ . '/sections/final-cta.php'; ?>
  </div>
</main>
<?php
require_once __DIR__ . '/includes/footer.php';
require_once __DIR__ . '/includes/scripts.php';
?>
