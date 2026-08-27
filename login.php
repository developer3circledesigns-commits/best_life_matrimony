<?php
require_once __DIR__ . '/includes/db.php';

// Already logged in? Don't show the login form again — go to the profile.
if (is_logged_in()) {
  header('Location: ./profile.php');
  exit;
}

/* ── Handle login BEFORE any output ─────────────── */
$errors = [];
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  // CSRF check
  if (!csrf_verify()) {
    $errors['auth'] = 'Invalid request. Please try again.';
  }

  // Rate limit check
  if (!$errors && !rate_limit_check('login', 5, 300)) {
    $errors['auth'] = 'Too many login attempts. Please wait 5 minutes and try again.';
  }

  $email = trim($_POST['email'] ?? '');
  $password = $_POST['password'] ?? '';
  if (!$errors) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email required.';
    if ($password === '') $errors['password'] = 'Password is required.';
  }

  if (!$errors) {
    try {
      $db = getDB();
      $stmt = $db->prepare('SELECT id, full_name, password, is_suspended, is_admin FROM users WHERE email = ?');
      $stmt->execute([$email]);
      $user = $stmt->fetch();
      if ($user && (int) ($user['is_suspended'] ?? 0) === 1) {
        $errors['auth'] = 'Your account has been suspended. Please contact support.';
      } elseif ($user && (int) ($user['is_admin'] ?? 0) === 1) {
        // B: admin accounts must use the separate, hardened admin login surface
        $errors['auth'] = 'Admin accounts must sign in from the admin login page.';
      } elseif ($user && password_verify($password, $user['password'])) {
        // Session regeneration to prevent fixation
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        rate_limit_reset('login');
        // First-admin bootstrap only: auto-promote via config list solely when no
        // admin exists yet, so a misconfigured list can't silently escalate users.
        $adminCount = (int) $db->query('SELECT COUNT(*) FROM users WHERE is_admin = 1')->fetchColumn();
        if (!(int) ($user['is_admin'] ?? 0) && $adminCount === 0 && in_array(strtolower($email), array_map('strtolower', $siteConfig['admin_emails'] ?? []), true)) {
          $db->prepare('UPDATE users SET is_admin = 1 WHERE id = ?')->execute([$user['id']]);
          log_activity((int) $user['id'], 'admin_bootstrap', 'user', (int) $user['id'], 'Auto-promoted to admin via config (first-admin bootstrap)');
        }
        log_activity((int) $user['id'], 'login', 'user', (int) $user['id'], 'Signed in');

        // Remember me
        if (!empty($_POST['remember'])) {
          remember_me_set($user['id']);
        }

        // Role-based destination; honour a safe internal ?redirect= (set by require_admin)
        $_SESSION['auth_flash'] = null;
        $dest = is_admin() ? './admin/index.php' : './profile.php';
        $redirect = $_GET['redirect'] ?? '';
        if ($redirect !== '' && strpos($redirect, '://') === false && strpos($redirect, '//') === false) {
          if (!(strpos($redirect, 'admin/') === 0 && !is_admin())) {
            $dest = $redirect;
          }
        }
        header('Location: ' . $dest);
        exit;
      } else {
        rate_limit_increment('login');
        $errors['auth'] = 'Invalid email or password.';
      }
    } catch (PDOException $e) {
      $errors['db'] = 'Something went wrong. Please try again later.';
    }
  }
}

/* ── Now render the page (output starts here) ──── */
$pageTitle = 'Login — BestLife Matrimony';
$pageDescription = 'Sign in to your BestLife Matrimony account and continue your journey.';
$pageHeadExtra = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="flex-1 bg-[#f4f2ee]">
  <section class="mx-auto w-full max-w-6xl px-4 py-16 sm:px-6 sm:py-24">
    <div class="grid lg:grid-cols-2 gap-8 items-start">
      <div class="reveal rounded-none border border-[#f6e6b4]/20 bg-[#6b1020] p-8 sm:p-12 shadow-2xl">
        <h1 class="font-serif text-4xl font-bold tracking-tight text-[#fff6e8] sm:text-5xl">Welcome Back</h1>
        <p class="mt-6 text-lg text-[#f3e6d8]/90">Sign in to BestLife Matrimony and pick up right where you left off. Your matches are waiting.</p>
        <ul class="mt-6 space-y-2 text-sm text-[#fff6e8]/80">
          <li>• Secure, encrypted login</li><li>• Quick access to your matches</li><li>• Reset anytime via email</li>
        </ul>
        <a href="./index.php" class="mt-8 inline-flex h-10 items-center justify-center rounded-full border border-white/20 bg-white/5 px-6 text-sm font-medium text-[#fff6e8] hover:bg-white/10">Back to Home</a>
      </div>
      <form method="post" action="./login.php" novalidate class="reveal reveal-delay-1 rounded-none border border-[#f6e6b4]/20 bg-white p-6 sm:p-8 shadow-xl text-[#2b1a1e]">
        <h2 class="font-serif text-xl font-bold">Sign in to your account</h2>
        <?php csrf_field(); ?>
        <?php if (!empty($_SESSION['auth_flash'])): ?><div class="mt-4 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800"><?php echo htmlspecialchars($_SESSION['auth_flash']); ?><?php $_SESSION['auth_flash'] = null; ?></div><?php endif; ?>
        <?php if (isset($errors['auth'])) echo '<div class="mt-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">'.$errors['auth'].'</div>'; ?>
        <?php if (isset($errors['db'])) echo '<div class="mt-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">'.$errors['db'].'</div>'; ?>
        <div class="mt-6 grid gap-4">
          <label class="text-sm font-medium">Email
            <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" placeholder="you@example.com" class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($errors['email'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" required />
            <?php if (isset($errors['email'])) echo '<span class="text-xs text-red-600">'.$errors['email'].'</span>'; ?>
          </label>
          <label class="text-sm font-medium">Password
            <div class="relative mt-1">
              <input type="password" name="password" placeholder="••••••••" class="w-full rounded-xl border px-4 py-3 pr-11 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($errors['password'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" required />
              <button type="button" class="toggle-password absolute inset-y-0 right-0 flex w-11 items-center justify-center text-[#8a4a2f] hover:text-[#5a3a3f]" aria-label="Show password"><i class="bi bi-eye-slash"></i></button>
            </div>
            <?php if (isset($errors['password'])) echo '<span class="text-xs text-red-600">'.$errors['password'].'</span>'; ?>
          </label>
          <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2 text-[#5a3a3f]"><input type="checkbox" name="remember" value="1" class="rounded border-[#e8d9b5] focus:ring-[#e3c877]" /> Remember me</label>
            <a href="./forgot_password.php" class="text-[#8a4a2f] hover:underline">Forgot password?</a>
          </div>
          <button type="submit" class="mt-2 inline-flex h-11 items-center justify-center rounded-full bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-sm font-bold text-[#3a0c15] hover:brightness-110 transition-all disabled:opacity-50">Sign In</button>
          <p class="text-xs text-[#5a3a3f] text-center">Don't have an account? <a href="./register.php" class="text-[#8a4a2f] font-medium hover:underline">Create one</a></p>
        </div>
      </form>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; require_once __DIR__ . '/includes/scripts.php'; ?>
