<?php
require_once __DIR__ . '/config.php';
$pageTitle = $pageTitle ?? $siteConfig['name'] . ' — Find Someone Who Makes Life Better.';
$pageDescription = $pageDescription ?? $siteConfig['description'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="description" content="<?php echo htmlspecialchars($pageDescription); ?>" />
  <meta name="csrf-token" content="<?php echo htmlspecialchars(csrf_token()); ?>" />
  <meta name="current-user-id" content="<?php echo (int) ($_SESSION['user_id'] ?? 0); ?>" />
  <title><?php echo htmlspecialchars($pageTitle); ?></title>
  <link rel="icon" type="image/png" href="<?php echo asset('images/favicon.png'); ?>" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,600;1,700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet" />
  <!-- Tailwind CDN for utility parity (development only — see $siteConfig['tailwind_cdn']) -->
  <?php if (!empty($siteConfig['tailwind_cdn'])): ?>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: { gurgundy: '#8b0000' },
          fontFamily: {
            sans: ['Plus Jakarta Sans','ui-sans-serif','system-ui'],
            serif: ['Cormorant Garamond','Georgia','serif'],
            display: ['Instrument Serif','Georgia','serif'],
          }
        }
      }
    }
  </script>
  <?php endif; ?>
  <style>.font-display{font-family:'Instrument Serif',Georgia,serif}</style>
  <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>" />
  <?php echo isset($pageHeadExtra) ? $pageHeadExtra : ''; ?>
</head>
<body class="bg-[#0c0205] text-[#fff6e8] antialiased selection:bg-[#dcb04a] selection:text-[#3a0c15]">
  <div class="relative min-h-screen bg-[#0c0205] text-[#fff6e8]">
    <div class="relative z-10 flex min-h-screen flex-col bg-transparent">
