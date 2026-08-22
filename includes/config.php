<?php
// BestLife Matrimony - Site Config
$siteConfig = [
  'name' => 'BestLife Matrimony',
  'description' => 'BestLife Matrimony - Find Someone Who Makes Life Better. A modern matrimony platform built with trust, respect and privacy.',
  'url' => 'https://yourdomain.com',
  'navItems' => [
    ['label' => 'Home', 'href' => '/', 'php' => 'index.php'],
    ['label' => 'Profile Matches', 'href' => '/matches', 'php' => 'matches.php'],
    ['label' => 'Advertise with us', 'href' => '/advertise', 'php' => 'advertise.php'],
    ['label' => 'Contact', 'href' => '/contact', 'php' => 'contact.php'],
  ],
  'year' => '2026',
];

// Helper: is active route
function isActiveRoute($current, $href) {
  $cur = rtrim($current, '/');
  $hrefTrim = rtrim($href, '/');
  if ($hrefTrim === '') $hrefTrim = '/';
  if ($cur === '') $cur = '/';
  return $cur === $hrefTrim;
}

// Helper: resolve asset path from root
function asset($path) {
  return './assets/' . ltrim($path, '/');
}

// Current request path for nav active state (fallback for CLI)
$currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
if ($currentPath === null) $currentPath = '/';
// Map .php files to pretty paths for menu highlight when accessed directly
$phpToPretty = [
  '/index.php' => '/',
  '/about.php' => '/about',
  '/matches.php' => '/matches',
  '/advertise.php' => '/advertise',
  '/contact.php' => '/contact',
  '/register.php' => '/register',
];
if (isset($phpToPretty[$currentPath])) {
  $currentPath = $phpToPretty[$currentPath];
}
?>
