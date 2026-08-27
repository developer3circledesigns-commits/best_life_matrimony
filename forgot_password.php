<?php
require_once __DIR__ . '/includes/db.php';

$errors = [];
$success = false;
$email = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  if (!csrf_verify()) {
    $errors['auth'] = 'Invalid request. Please try again.';
  }

  if (!rate_limit_check('forgot_pw', 3, 600)) {
    $errors['auth'] = 'Too many attempts. Please wait 10 minutes.';
  }

  $email = trim($_POST['email'] ?? '');
  if (!$errors) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors['email'] = 'Valid email required.';
    }
  }

  if (!$errors) {
    try {
      $db = getDB();
      $stmt = $db->prepare('SELECT id, full_name FROM users WHERE email = ?');
      $stmt->execute([$email]);
      $user = $stmt->fetch();
      rate_limit_reset('forgot_pw');

      if ($user) {
        // Invalidate any previous unused tokens, then issue a new one
        $db->prepare('UPDATE password_resets SET used = 1 WHERE user_id = ? AND used = 0')->execute([$user['id']]);

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour
        $db->prepare('INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, ?)')
           ->execute([$user['id'], $tokenHash, $expires]);

        $resetUrl = site_url('password_reset.php?token=' . urlencode($token));
        $html = '<p>Hi ' . htmlspecialchars($user['full_name']) . ',</p>'
              . '<p>We received a request to reset your password. Click the button below to choose a new one:</p>'
              . '<p><a href="' . htmlspecialchars($resetUrl) . '">Reset my password</a></p>'
              . '<p>This link expires in 1 hour. If you didn\'t request this, you can safely ignore this email.</p>';
        send_email($email, 'Reset your BestLife Matrimony password', $html);
      }

      // Always show success to prevent email enumeration
      $success = true;
    } catch (PDOException $e) {
      $errors['db'] = 'Something went wrong. Please try again later.';
    }
  }
}

$pageTitle = 'Forgot Password — BestLife Matrimony';
$pageDescription = 'Reset your BestLife Matrimony password.';
$pageHeadExtra = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="flex-1 bg-[#f4f2ee]">
  <section class="mx-auto w-full max-w-6xl px-4 py-16 sm:px-6 sm:py-24">
    <div class="grid lg:grid-cols-2 gap-8 items-start">
      <div class="reveal rounded-none border border-[#f6e6b4]/20 bg-[#0c0205] p-8 sm:p-12 shadow-2xl">
        <h1 class="font-serif text-4xl font-bold tracking-tight text-[#fff6e8] sm:text-5xl">Reset Password</h1>
        <p class="mt-6 text-lg text-[#f3e6d8]/90">Enter the email address associated with your account and we'll send you a link to reset your password.</p>
        <ul class="mt-6 space-y-2 text-sm text-[#fff6e8]/80">
          <li>• Check your inbox for the reset link</li><li>• Link expires in 1 hour</li><li>• Check spam folder if not found</li>
        </ul>
        <a href="./login.php" class="mt-8 inline-flex h-10 items-center justify-center rounded-full border border-white/20 bg-white/5 px-6 text-sm font-medium text-[#fff6e8] hover:bg-white/10"><i class="bi bi-arrow-left mr-2"></i>Back to Login</a>
      </div>
      <form method="post" action="./forgot_password.php" novalidate class="reveal reveal-delay-1 rounded-none border border-[#f6e6b4]/20 bg-white p-6 sm:p-8 shadow-xl text-[#2b1a1e]">
        <h2 class="font-serif text-xl font-bold">Find your account</h2>
        <?php csrf_field(); ?>
        <?php if (isset($errors['auth'])) echo '<div class="mt-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">'.$errors['auth'].'</div>'; ?>
        <?php if (isset($errors['db'])) echo '<div class="mt-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">'.$errors['db'].'</div>'; ?>
        <?php if ($success): ?>
          <div class="mt-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
            <i class="bi bi-check-circle-fill mr-1"></i>
            If an account exists with <strong><?php echo htmlspecialchars($email); ?></strong>, a password reset link has been sent. Please check your inbox and spam folder.
          </div>
        <?php else: ?>
          <div class="mt-6 grid gap-4">
            <label class="text-sm font-medium">Email address
              <input type="email" name="email" maxlength="191" value="<?php echo htmlspecialchars($email); ?>" placeholder="you@example.com" class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($errors['email'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" required />
              <?php if (isset($errors['email'])) echo '<span class="text-xs text-red-600">'.$errors['email'].'</span>'; ?>
            </label>
            <button type="submit" class="mt-2 inline-flex h-11 items-center justify-center rounded-full bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-sm font-bold text-[#3a0c15] hover:brightness-110 transition-all disabled:opacity-50">Send Reset Link</button>
            <p class="text-xs text-[#5a3a3f] text-center">Remember your password? <a href="./login.php" class="text-[#8a4a2f] font-medium hover:underline">Sign in</a></p>
          </div>
        <?php endif; ?>
      </form>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; require_once __DIR__ . '/includes/scripts.php'; ?>
