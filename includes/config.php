<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Load environment variables from .env file if it exists
// Container env (docker-compose) takes precedence — only set from .env if not already set
if (file_exists(__DIR__ . '/../.env')) {
  $lines = file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
  foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '' || strpos($line, '#') === 0) continue;
    $parts = explode('=', $line, 2);
    if (count($parts) === 2) {
      $name = trim($parts[0]);
      $value = trim($parts[1]);
      // Strip outer quotes if present
      if (preg_match('/^([\'"])(.*)\1$/', $value, $matches)) {
        $value = $matches[2];
      }
      if (getenv($name) === false || getenv($name) === '') {
        putenv("$name=$value");
        $_ENV[$name] = $value;
        $_SERVER[$name] = $value;
      }
    }
  }
}

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
  // F4: Tailwind Play CDN is for development only. In production, set this to
  // false and include a compiled build (e.g. assets/css/tailwind.css) in header.php.
  'tailwind_cdn' => true,
  // F2: When true (development), PHP shows raw errors/warnings so developers can
  // debug. When false (production), errors are logged to logs/error.log and users
  // see a friendly generic message instead of raw stack traces. Set APP_ENV=production
  // on the server to enable the friendly handler.
  'debug' => getenv('APP_ENV') !== 'production',
  // P4: Emails in this list are auto-promoted to admin on login (first-admin bootstrap).
  // e.g. ['admin@bestlifematrimony.com']
    'admin_emails' => [],
    // B: separate admin login surface — restrict by source IP (e.g. ['127.0.0.1','203.0.113.5/24']);
    // empty array = allow all networks (suitable for local dev)
    'admin_ip_allowlist' => [],
  ];

// ── Security Helpers ──────────────────────────────

// CSRF token: generate
function csrf_token() {
  if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
  }
  return $_SESSION['csrf_token'];
}

// CSRF token: hidden input
function csrf_field() {
  echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token()) . '">';
}

// CSRF token: validate
function csrf_verify() {
  $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
  return hash_equals(csrf_token(), $token);
}

// Rate limiter: check attempts (stored in session)
// $key: identifier, $max: max attempts, $window: seconds
function rate_limit_check(string $key, int $max = 5, int $window = 300): bool {
  $now = time();
  $rateKey = 'rate_' . $key;
  if (!isset($_SESSION[$rateKey])) {
    $_SESSION[$rateKey] = ['count' => 0, 'first' => $now];
  }
  $rl = &$_SESSION[$rateKey];
  // Reset window if expired
  if (($now - $rl['first']) > $window) {
    $rl = ['count' => 0, 'first' => $now];
  }
  return $rl['count'] < $max;
}

// Rate limiter: increment on failure
function rate_limit_increment(string $key): void {
  $rateKey = 'rate_' . $key;
  if (!isset($_SESSION[$rateKey])) {
    $_SESSION[$rateKey] = ['count' => 0, 'first' => time()];
  }
  $_SESSION[$rateKey]['count']++;
}

// Rate limiter: reset on success
function rate_limit_reset(string $key): void {
  unset($_SESSION['rate_' . $key]);
}

// Remember me: issue a DB-backed token + cookie (30 days)
function remember_me_set(int $userId): void {
  $token = bin2hex(random_bytes(32));
  $hashedToken = hash('sha256', $token);
  $expiry = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60));
  try {
    if (!function_exists('getDB')) {
      if (file_exists(__DIR__ . '/db.php')) @require_once __DIR__ . '/db.php';
      if (!function_exists('getDB')) throw new Exception('DB unavailable');
    }
    $db = getDB();
    $db->prepare('DELETE FROM remember_tokens WHERE user_id = ?')->execute([$userId]);
    $db->prepare('INSERT INTO remember_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)')
       ->execute([$userId, $hashedToken, $expiry]);
  } catch (Throwable $e) { /* ignore — still set cookie */ }
  setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
}

// Remember me: validate cookie against the DB; rotate token on success
function remember_me_validate(): ?int {
  if (!empty($_SESSION['user_id'])) return null; // already logged in
  $token = $_COOKIE['remember_token'] ?? '';
  if (!$token) return null;
  $hashedToken = hash('sha256', $token);
  try {
    if (!function_exists('getDB')) {
      if (file_exists(__DIR__ . '/db.php')) @require_once __DIR__ . '/db.php';
      if (!function_exists('getDB')) return null;
    }
    $db = getDB();
    $stmt = $db->prepare('SELECT user_id FROM remember_tokens WHERE token_hash = ? AND expires_at > NOW()');
    $stmt->execute([$hashedToken]);
    $row = $stmt->fetch();
    if (!$row) return null;
    // Rotate to a fresh token so the cookie keeps working and old tokens can't be reused
    $newToken = bin2hex(random_bytes(32));
    $newHash = hash('sha256', $newToken);
    $newExpiry = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60));
    $db->prepare('UPDATE remember_tokens SET token_hash = ?, expires_at = ? WHERE user_id = ?')
       ->execute([$newHash, $newExpiry, $row['user_id']]);
    setcookie('remember_token', $newToken, time() + (30 * 24 * 60 * 60), '/', '', false, true);
    return (int) $row['user_id'];
  } catch (Throwable $e) {
    return null;
  }
}

// Remember me: delete the DB token + expire the cookie
function remember_me_clear(): void {
  $token = $_COOKIE['remember_token'] ?? '';
  if ($token) {
    try {
      if (!function_exists('getDB')) {
        if (file_exists(__DIR__ . '/db.php')) @require_once __DIR__ . '/db.php';
      }
      if (function_exists('getDB')) {
        $db = getDB();
        $db->prepare('DELETE FROM remember_tokens WHERE token_hash = ?')->execute([hash('sha256', $token)]);
      }
    } catch (Throwable $e) { /* ignore — still clear cookie */ }
  }
  // Expire cookie even if DB is unavailable
  if (!headers_sent()) {
    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
  } else {
    @setcookie('remember_token', '', time() - 3600, '/', '', false, true);
  }
  $_COOKIE['remember_token'] = '';
}

// ── Admin login surface helpers (separate /admin/login.php) ──

// IP allowlist check for the admin login surface (supports exact IPs and CIDRs)
function admin_ip_allowed(string $ip, array $list): bool {
  foreach ($list as $entry) {
    $entry = trim((string) $entry);
    if ($entry === '' || $entry === $ip) continue;
    if (strpos($entry, '/') !== false && ip_in_cidr($ip, $entry)) return true;
  }
  return false;
}
function ip_in_cidr(string $ip, string $cidr): bool {
  [$subnet, $bits] = array_pad(explode('/', $cidr, 2), 2, '32');
  $ipLong = ip2long($ip);
  $subLong = ip2long($subnet);
  if ($ipLong === false || $subLong === false) return false;
  $bits = (int) $bits;
  $mask = $bits <= 0 ? 0 : (-1 << (32 - $bits));
  return ($ipLong & $mask) === ($subLong & $mask);
}

// TOTP (RFC 6238) — pure PHP, no external dependency
function twofa_generate_secret(): string {
  $data = random_bytes(20);
  $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
  $bits = '';
  foreach (str_split($data) as $b) $bits .= str_pad(decbin(ord($b)), 8, '0', STR_PAD_LEFT);
  $out = '';
  for ($i = 0; $i + 5 <= strlen($bits); $i += 5) $out .= $chars[bindec(substr($bits, $i, 5))];
  return $out;
}
function twofa_base32_decode(string $secret): string {
  $secret = strtoupper(preg_replace('/[^A-Z2-7]/', '', $secret));
  $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
  $bits = '';
  for ($i = 0; $i < strlen($secret); $i++) {
    $idx = strpos($chars, $secret[$i]);
    if ($idx === false) continue;
    $bits .= str_pad(decbin($idx), 5, '0', STR_PAD_LEFT);
  }
  $out = '';
  for ($i = 0; $i + 8 <= strlen($bits); $i += 8) $out .= chr(bindec(substr($bits, $i, 8)));
  return $out;
}
function twofa_totp(string $secret, ?int $time = null, int $step = 30, int $digits = 6): string {
  $time = $time ?? time();
  $counter = intdiv($time, $step);
  $bin = '';
  for ($i = 7; $i >= 0; $i--) $bin .= chr(($counter >> (8 * $i)) & 0xFF);
  $key = twofa_base32_decode($secret);
  $hash = hash_hmac('sha1', $bin, $key, true);
  $offset = ord($hash[19]) & 0x0F;
  $code = (unpack('N', substr($hash, $offset, 4))[1] & 0x7FFFFFFF) % (10 ** $digits);
  return str_pad((string) $code, $digits, '0', STR_PAD_LEFT);
}
function twofa_verify(string $secret, string $code, int $window = 1): bool {
  $code = preg_replace('/\s/', '', $code);
  if (!ctype_digit($code)) return false;
  $now = time();
  for ($w = -$window; $w <= $window; $w++) {
    if (hash_equals(twofa_totp($secret, $now + $w * 30), $code)) return true;
  }
  return false;
}

// ── Existing Helpers ──────────────────────────────

// Helper: is active route
function isActiveRoute($current, $href) {
  $cur = rtrim($current, '/');
  $hrefTrim = rtrim($href, '/');
  if ($hrefTrim === '') $hrefTrim = '/';
  if ($cur === '') $cur = '/';
  return $cur === $hrefTrim;
}

// Whether the current visitor is authenticated
function is_logged_in(): bool {
  return !empty($_SESSION['user_id']);
}

// Destination for primary sign-up CTAs: send logged-in members to their
// own profile instead of back to the registration page.
function register_cta_href(): string {
  return is_logged_in() ? './profile.php' : './register.php';
}

// Label for primary sign-up CTAs, adaptive to auth state.
function register_cta_label(string $loggedIn = 'My Profile', string $guest = 'Register Now'): string {
  return is_logged_in() ? $loggedIn : $guest;
}

// Destination for "View Profile"-style demo links: guests should sign up to
// unlock the member area, logged-in members go straight to the matches page.
function member_cta_href(): string {
  return is_logged_in() ? './matches.php' : './register.php';
}

// Helper: resolve asset path from root
function asset($path) {
  return '/assets/' . ltrim($path, '/');
}

// Helper: normalize a stored photo path to a root-relative URL
function photo_url($path) {
  if (!$path) return '';
  $p = ltrim($path, './');
  return '/' . ltrim($p, '/');
}

// Helper: normalize stored photo path for filesystem operations
function photo_fs_path($path, $baseDir) {
  if (!$path) return '';
  $p = ltrim($path, './');
  $safe = $baseDir . '/' . ltrim($p, '/');
  $safe = str_replace('\\', '/', $safe);
  $safe = preg_replace('#/+#', '/', $safe);
  return $safe;
}

// Determine the site base URL (best effort)
function site_url($path = '') {
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
  return $scheme . '://' . $host . '/' . ltrim($path, '/');
}

// Send a transactional email. Returns bool. mail() is used with graceful fallback.
function send_email(string $to, string $subject, string $htmlBody, string $textBody = '', string $replyTo = ''): bool {
  $fromName = 'BestLife Matrimony';
  $fromEmail = 'noreply@bestlifematrimony.com';
  $headers = "From: $fromName <$fromEmail>\r\n";
  if ($replyTo !== '') {
    $headers .= "Reply-To: $replyTo\r\n";
  }
  $headers .= "MIME-Version: 1.0\r\n";
  $headers .= 'Content-Type: text/html; charset=UTF-8' . "\r\n";
  try {
    $sent = @mail($to, $subject, $htmlBody, $headers);
    return $sent;
  } catch (Exception $e) {
    return false;
  }
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
  '/profile.php' => '/profile',
  '/login.php' => '/login',
];
if (isset($phpToPretty[$currentPath])) {
  $currentPath = $phpToPretty[$currentPath];
}

// (Remember-me auto-login moved to includes/db.php so getDB() is available at runtime.)


// ── F2: Centralised error handling ────────────────
// Development (debug) shows raw errors; production logs them and shows a
// friendly generic message. This keeps behaviour consistent across pages and
// prevents raw stack traces leaking to end users.
$debugMode = isset($siteConfig['debug']) ? (bool)$siteConfig['debug'] : true;
ini_set('display_errors', $debugMode ? '1' : '0');
error_reporting($debugMode ? E_ALL : (E_ALL & ~E_DEPRECATED));

if (!$debugMode) {
  $errorLogFile = __DIR__ . '/../logs/error.log';
  if (!is_dir(dirname($errorLogFile))) {
    @mkdir(dirname($errorLogFile), 0755, true);
  }
  ini_set('log_errors', '1');
  ini_set('error_log', $errorLogFile);

  // Friendly handler for uncaught exceptions: log it, then show a generic page
  // (or JSON error for API endpoints) instead of the raw trace.
  set_exception_handler(function (Throwable $e) {
    @error_log('Uncaught ' . get_class($e) . ': ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (is_api_request()) {
      @http_response_code(500);
      @header('Content-Type: application/json');
      echo json_encode(['ok' => false, 'error' => 'An unexpected error occurred.']);
    } else {
      http_response_code(500);
      echo '<!doctype html><html><head><meta charset="utf-8"><title>Something went wrong</title></head>'
         . '<body style="font-family:sans-serif;text-align:center;padding:4rem;color:#333;">'
         . '<h1>Something went wrong</h1><p>We\'re sorry — an unexpected error occurred. Please try again later.</p>'
         . '<p><a href="./">Go to home</a></p></body></html>';
    }
    exit;
  });
}

// Standard JSON response helpers for API endpoints (F2)
function json_response(array $data, int $code = 200): void {
  http_response_code($code);
  if (!headers_sent()) header('Content-Type: application/json');
  echo json_encode($data);
  exit;
}

// A graceful API failure — always returns a structured {ok:false, ...} body.
function api_fail(int $code, string $message, array $extra = []): void {
  json_response(array_merge(['ok' => false, 'error' => $message], $extra), $code);
}

// Best-effort heuristic: are we handling a JSON/API request?
function is_api_request(): bool {
  $path = strtolower($_SERVER['REQUEST_URI'] ?? '');
  $agent = strtolower($_SERVER['HTTP_ACCEPT'] ?? '');
  if (strpos($path, '_api.php') !== false) return true;
  if (strpos($agent, 'application/json') !== false) return true;
  return false;
}
?>
