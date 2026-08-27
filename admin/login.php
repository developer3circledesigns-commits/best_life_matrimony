<?php
require_once __DIR__ . '/../includes/db.php';

// ── B: separate admin login surface ────────────────────────────────────────
// Only accounts with is_admin = 1 may authenticate here. The public login.php
// rejects admin emails, so admin credentials are verified on this hardened route.

// IP allowlist (defense-in-depth). Empty list = allow all (local dev friendly).
$allow = $siteConfig['admin_ip_allowlist'] ?? [];
if (!empty($allow) && !admin_ip_allowed($_SERVER['REMOTE_ADDR'] ?? '', $allow)) {
  http_response_code(403);
  echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><title>Forbidden</title></head>'
     . '<body style="font-family:system-ui;padding:40px"><h1>403 Forbidden</h1>'
     . '<p>Admin login is not permitted from your network address.</p></body></html>';
  exit;
}

// Already authenticated as an admin -> straight to the dashboard
if (is_admin()) {
  header('Location: ./index.php');
  exit;
}

// CAPTCHA (self-contained math challenge; no third-party service required)
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
  // Fresh GET: clear any half-finished 2FA state and issue a new CAPTCHA
  unset($_SESSION['admin_auth_uid'], $_SESSION['admin_2fa_pending']);
  $a = random_int(1, 9); $b = random_int(1, 9);
  $_SESSION['admin_captcha'] = ['a' => $a, 'b' => $b, 'answer' => $a + $b];
} elseif (empty($_SESSION['admin_captcha']) || !is_array($_SESSION['admin_captcha'])) {
  $a = random_int(1, 9); $b = random_int(1, 9);
  $_SESSION['admin_captcha'] = ['a' => $a, 'b' => $b, 'answer' => $a + $b];
}

$errors = [];
$step = $_POST['step'] ?? '1';
$emailVal = htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  if (!csrf_verify()) {
    $errors['auth'] = 'Invalid request. Please try again.';
  }
  if (!$errors && !rate_limit_check('admin_login', 5, 300)) {
    $errors['auth'] = 'Too many attempts. Please wait 5 minutes and try again.';
  }

  if (!$errors && $step === '1') {
    $captcha = (int) ($_POST['captcha'] ?? -1);
    if ($captcha !== ($_SESSION['admin_captcha']['answer'] ?? -1)) {
      $errors['captcha'] = 'Incorrect answer, please try again.';
    }
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($email === '' || $password === '') {
      $errors['auth'] = 'Email and password are required.';
    }

    if (!$errors) {
      try {
        $db = getDB();
        $stmt = $db->prepare('SELECT id, full_name, password, is_suspended, is_admin, twofa_secret FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
      } catch (Exception $e) {
        $user = false;
      }
      if (!$user || (int) $user['is_admin'] !== 1) {
        $errors['auth'] = 'Invalid admin credentials.';
      } elseif ((int) $user['is_suspended'] === 1) {
        $errors['auth'] = 'This admin account is suspended.';
      } elseif (!password_verify($password, $user['password'])) {
        rate_limit_increment('admin_login');
        $errors['auth'] = 'Invalid admin credentials.';
      } else {
        // Password OK -> proceed to 2FA (or first-time enrollment)
        $_SESSION['admin_auth_uid'] = (int) $user['id'];
        if (!empty($user['twofa_secret'])) {
          $step = '2';
        } else {
          $_SESSION['admin_2fa_pending'] = twofa_generate_secret();
          $step = '2';
        }
      }
    }
    // Always issue a fresh CAPTCHA for the next attempt
    $a = random_int(1, 9); $b = random_int(1, 9);
    $_SESSION['admin_captcha'] = ['a' => $a, 'b' => $b, 'answer' => $a + $b];
  }

  if (!$errors && $step === '2') {
    $uid = (int) ($_SESSION['admin_auth_uid'] ?? 0);
    if (!$uid) {
      $errors['auth'] = 'Session expired. Please sign in again.';
    }
    $code = trim($_POST['code'] ?? '');
    if ($code === '') {
      $errors['auth'] = 'Enter the 6-digit code.';
    }
    if (!$errors) {
      $db = getDB();
      if (isset($_SESSION['admin_2fa_pending'])) {
        // First-time enrollment: verify against the pending secret, then persist it
        if (!twofa_verify($_SESSION['admin_2fa_pending'], $code, 1)) {
          $errors['auth'] = 'Invalid code. Make sure your authenticator app is synced.';
        } else {
          $db->prepare('UPDATE users SET twofa_secret = ? WHERE id = ?')->execute([$_SESSION['admin_2fa_pending'], $uid]);
          finalize_admin_login($uid);
        }
      } else {
        $stmt = $db->prepare('SELECT twofa_secret FROM users WHERE id = ?');
        $stmt->execute([$uid]);
        $secret = $stmt->fetchColumn();
        if (!twofa_verify((string) $secret, $code, 1)) {
          $errors['auth'] = 'Invalid code.';
        } else {
          finalize_admin_login($uid);
        }
      }
    }
  }
}

// Completes admin authentication and starts a fresh session
function finalize_admin_login(int $uid): void {
  session_regenerate_id(true);
  $_SESSION['user_id'] = $uid;
  rate_limit_reset('admin_login');
  $target = $_SESSION['admin_redirect'] ?? '';
  unset($_SESSION['admin_auth_uid'], $_SESSION['admin_2fa_pending'], $_SESSION['admin_captcha'], $_SESSION['auth_flash'], $_SESSION['admin_redirect']);
  if ($target && strpos($target, 'admin/') === 0) {
    header('Location: ' . site_url($target));
  } else {
    header('Location: ' . site_url('admin/index.php'));
  }
  exit;
}

$pageTitle = 'Admin Login — BestLife Matrimony';
require_once __DIR__ . '/../includes/header.php';
?>
<main class="flex-1 bg-[#f4f2ee] text-[#3a0c15]">
  <div class="mx-auto flex max-w-6xl flex-col items-center justify-center px-4 py-10 sm:px-6 lg:px-8 lg:py-14">
    <!-- Login card -->
    <section class="flex flex-1 items-center justify-center">
      <div class="w-full max-w-md rounded-none border border-[#e8dcc8] bg-white p-8 shadow-xl">
        <h1 class="font-serif text-2xl font-bold text-[#3a0c15]">Admin Login</h1>
        <p class="mt-1 text-sm text-[#6b5a5f]">Restricted area. Admin accounts only.</p>
        <?php if (!empty($_SESSION['auth_flash'])): ?><div class="mt-4 rounded-none bg-[#fdf9f1] border border-[#e8d9b5] px-4 py-3 text-sm text-[#8a4a2f]"><?php echo $_SESSION['auth_flash']; ?></div><?php unset($_SESSION['auth_flash']); ?><?php endif; ?>
        <?php if (!empty($errors['auth'])): ?><div class="mt-4 rounded-none bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700"><?php echo $errors['auth']; ?></div><?php endif; ?>

        <?php if (isset($_SESSION['admin_auth_uid']) && $step === '2'): ?>
          <?php
            $enrolling = isset($_SESSION['admin_2fa_pending']);
            $hint = '';
            if ($siteConfig['debug']) {
              $sec = $_SESSION['admin_2fa_pending'] ?? '';
              if (!$sec) {
                try {
                  $dbh = getDB();
                  $st = $dbh->prepare('SELECT twofa_secret FROM users WHERE id = ?');
                  $st->execute([$_SESSION['admin_auth_uid']]);
                  $sec = (string) $st->fetchColumn();
                } catch (Exception $e) { $sec = ''; }
              }
              if ($sec !== '') $hint = twofa_totp($sec);
            }
          ?>
          <p class="mt-4 text-sm text-[#6b5a5f]">
            <?php echo $enrolling
              ? 'Set up two-factor authentication: add the secret below to your authenticator app, then enter the 6-digit code.'
              : 'Enter your 6-digit authentication code.'; ?>
          </p>
          <?php if ($enrolling): ?>
            <div class="mt-2 rounded-none bg-[#fdf9f1] border border-[#e8d9b5] px-4 py-3 text-sm break-all text-[#3a0c15]">
              Secret: <code class="font-mono"><?php echo htmlspecialchars($_SESSION['admin_2fa_pending']); ?></code>
            </div>
          <?php endif; ?>
          <?php if ($hint !== ''): ?><p class="mt-2 text-xs text-[#8a4a2f]">Dev hint (debug on): current code = <b><?php echo $hint; ?></b></p><?php endif; ?>

          <form method="post" action="./login.php" class="mt-4 grid gap-3">
            <?php csrf_field(); ?>
            <input type="hidden" name="step" value="2">
            <label class="text-sm font-medium text-[#3a0c15]">Authentication code
              <input name="code" inputmode="numeric" autocomplete="one-time-code" class="mt-1 w-full rounded-none border border-[#e8d9b5] bg-[#fdf9f1] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877]" required>
            </label>
            <button type="submit" class="inline-flex h-11 items-center justify-center rounded-none bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-sm font-bold text-[#3a0c15] hover:brightness-110 transition-all">Verify</button>
            <a href="./login.php" class="text-xs text-[#8a4a2f] hover:underline text-center">Cancel</a>
          </form>
        <?php else: ?>
          <form method="post" action="./login.php" class="mt-4 grid gap-3">
            <?php csrf_field(); ?>
            <input type="hidden" name="step" value="1">
            <label class="text-sm font-medium text-[#3a0c15]">Admin email
              <input type="email" name="email" value="<?php echo $emailVal; ?>" autocomplete="username" class="mt-1 w-full rounded-none border border-[#e8d9b5] bg-[#fdf9f1] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877]" required>
            </label>
            <label class="text-sm font-medium text-[#3a0c15]">Password
              <input type="password" name="password" autocomplete="current-password" class="mt-1 w-full rounded-none border border-[#e8d9b5] bg-[#fdf9f1] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877]" required>
            </label>
            <label class="text-sm font-medium text-[#3a0c15]">Security check: what is <?php echo (int) ($_SESSION['admin_captcha']['a'] ?? 0); ?> + <?php echo (int) ($_SESSION['admin_captcha']['b'] ?? 0); ?>?
              <input name="captcha" inputmode="numeric" autocomplete="off" class="mt-1 w-full rounded-none border border-[#e8d9b5] bg-[#fdf9f1] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877]" required>
            </label>
            <?php if (isset($errors['captcha'])): ?><span class="text-xs text-red-600"><?php echo $errors['captcha']; ?></span><?php endif; ?>
            <button type="submit" class="inline-flex h-11 items-center justify-center rounded-none bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-sm font-bold text-[#3a0c15] hover:brightness-110 transition-all">Sign in</button>
          </form>
          <p class="mt-4 text-xs text-[#6b5a5f] text-center">Not an admin? <a href="../login.php" class="text-[#8a4a2f] font-medium hover:underline">User sign in</a></p>
        <?php endif; ?>
      </div>
    </section>
  </div>
</main>
<?php // Close the two wrapper divs opened by includes/header.php, then the document.
// The admin login renders standalone — no site navbar or footer. ?>
</div>
</div>
</body>
</html>
