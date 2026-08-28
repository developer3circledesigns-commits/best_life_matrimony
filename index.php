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
// WhatsApp — logged-in only, with sender info in prefilled message
require_once __DIR__ . '/includes/db.php';
if (is_logged_in()):
  $cu = current_user();
  $waName = $cu['full_name'] ?? ($_SESSION['user_name'] ?? 'Member');
  $waEmail = $cu['email'] ?? '';
  $waPhone = $cu['phone'] ?? '';
  $waId = (string)($cu['id'] ?? $_SESSION['user_id'] ?? '');
  $waDisplayId = $waId !== '' ? 'BLM-' . date('Y', strtotime($cu['created_at'] ?? 'now')) . '-' . str_pad($waId, 5, '0', STR_PAD_LEFT) . ' (ID: ' . $waId . ')' : 'N/A';
  $waLooking = $cu['looking_for'] ?? 'N/A';
  $waMemberSince = !empty($cu['created_at']) ? date('M Y', strtotime($cu['created_at'])) : 'N/A';
  $waPage = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'bestlifematrimony.com') . ($_SERVER['REQUEST_URI'] ?? '/');
  $waRaw = "Hi BestLife Matrimony,\n\nI need assistance regarding my matrimony profile.\n\n"
         . "Sender Details:\n"
         . "• Name: " . $waName . "\n"
         . "• Email: " . $waEmail . "\n"
         . "• Phone: " . $waPhone . "\n"
         . "• User ID: " . $waDisplayId . "\n"
         . "• Looking for: " . $waLooking . "\n"
         . "• Member Since: " . $waMemberSince . "\n"
         . "• Page: " . $waPage . "\n\n"
         . "Request: Please assist me with my profile/matches. Thank you!";
  $waUrl = 'https://wa.me/917200005622?text=' . rawurlencode($waRaw);
?>
<a href="<?php echo htmlspecialchars($waUrl); ?>" target="_blank" rel="noopener noreferrer" class="whatsapp-float" aria-label="Chat on WhatsApp with 7200005622 (<?php echo htmlspecialchars($waName); ?>)">
  <svg viewBox="0 0 24 24" width="28" height="28" fill="white" aria-hidden="true"><path d="M19.05 4.91A9.82 9.82 0 0 0 12.03 2C6.55 2 2.07 6.48 2.07 11.96c0 1.75.46 3.45 1.32 4.96L2 22l5.23-1.37a9.96 9.96 0 0 0 4.8 1.22h.01c5.48 0 9.96-4.48 9.96-9.96 0-2.66-1.04-5.16-2.95-7.04Zm-7.02 15.2h-.01a8.18 8.18 0 0 1-4.17-1.14l-.3-.18-3.1.81.83-3.02-.2-.31A8.15 8.15 0 0 1 3.84 11.96c0-4.5 3.65-8.15 8.15-8.15 2.18 0 4.23.85 5.77 2.39a8.11 8.11 0 0 1 2.38 5.77c0 4.5-3.66 8.15-8.15 8.15Zm6.92-5.9c-.38-.19-2.24-1.11-2.59-1.23-.35-.13-.6-.19-.85.19-.25.38-.98 1.23-1.2 1.48-.22.25-.45.28-.83.09-.38-.19-1.6-.59-3.04-1.88-1.12-1-1.88-2.23-2.1-2.61-.22-.38-.02-.58.16-.77.17-.17.38-.44.56-.66.19-.22.25-.38.38-.63.13-.25.06-.47-.03-.66-.09-.19-.85-2.05-1.17-2.81-.31-.74-.62-.64-.85-.65l-.73-.01c-.25 0-.66.09-1 .47-.35.38-1.33 1.3-1.33 3.17s1.36 3.68 1.55 3.93c.19.25 2.68 4.1 6.69 5.75.93.4 1.66.64 2.23.82.94.3 1.79.26 2.47.16.75-.11 2.24-.92 2.56-1.81.32-.89.32-1.65.22-1.81-.09-.16-.34-.25-.72-.44Z"/></svg>
</a>
<?php endif; ?>
<style>
.whatsapp-float{position:fixed;right:20px;bottom:20px;width:56px;height:56px;background:#25D366;border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(0,0,0,.18),0 2px 6px rgba(0,0,0,.12);z-index:9999;transition:transform .2s,box-shadow .2s;text-decoration:none}
.whatsapp-float:hover{transform:scale(1.08);box-shadow:0 6px 16px rgba(0,0,0,.22)}
.whatsapp-float:active{transform:scale(.96)}
@media(max-width:480px){.whatsapp-float{right:16px;bottom:16px;width:52px;height:52px}}
</style>

<?php
require_once __DIR__ . '/includes/footer.php';
require_once __DIR__ . '/includes/scripts.php';
?>
