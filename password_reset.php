<?php
require_once __DIR__ . '/includes/db.php';

$token = $_GET['token'] ?? ($_POST['token'] ?? '');
$errors = [];
$tokenValid = false;
$resetDone = false;
$resetEmail = '';

if ($token === '') {
  $errors['general'] = 'This password reset link is invalid or incomplete.';
} else {
  $tokenHash = hash('sha256', $token);
  try {
    $db = getDB();
    $stmt = $db->prepare(
      'SELECT pr.user_id, pr.expires_at, u.email FROM password_resets pr
       JOIN users u ON u.id = pr.user_id
       WHERE pr.token_hash = ? AND pr.used = 0 LIMIT 1'
    );
    $stmt->execute([$tokenHash]);
    $row = $stmt->fetch();
    if (!$row || strtotime($row['expires_at']) < time()) {
      $errors['general'] = 'This password reset link is invalid or has expired. Please request a new one.';
    } else {
      $tokenValid = true;
      $resetEmail = $row['email'];
      $resetUserId = (int) $row['user_id'];
    }
  } catch (PDOException $e) {
    $errors['general'] = 'Something went wrong. Please try again.';
  }
}

if ($tokenValid && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  if (!csrf_verify()) {
    $errors['password'] = 'Invalid request. Please try again.';
  } else {
    $p1 = $_POST['password'] ?? '';
    $p2 = $_POST['password_confirm'] ?? '';
    if (strlen($p1) < 8) $errors['password'] = 'Password must be at least 8 characters.';
    if (strlen($p1) > 255) $errors['password'] = 'Password must be 255 characters or fewer.';
    if ($p1 !== $p2) $errors['password_confirm'] = 'Passwords do not match.';

    if (!$errors) {
      try {
        $hash = password_hash($p1, PASSWORD_DEFAULT);
        $db->prepare('UPDATE users SET password = ? WHERE id = ?')->execute([$hash, $resetUserId]);
        $db->prepare('UPDATE password_resets SET used = 1 WHERE token_hash = ?')->execute([$tokenHash]);
        rate_limit_reset('reset_' . $resetUserId);
        $resetDone = true;
      } catch (PDOException $e) {
        $errors['password'] = 'Something went wrong. Please try again.';
      }
    }
  }
}

$pageTitle = 'Reset Password — BestLife Matrimony';
$pageDescription = 'Choose a new password for your BestLife Matrimony account.';
$pageHeadExtra = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="flex-1 bg-[#f4f2ee]">
  <section class="mx-auto w-full max-w-6xl px-4 py-16 sm:px-6 sm:py-24">
    <div class="grid lg:grid-cols-2 gap-8 items-start">
      <div class="reveal rounded-none border border-[#f6e6b4]/20 bg-[#0c0205] p-8 sm:p-12 shadow-2xl">
        <h1 class="font-serif text-4xl font-bold tracking-tight text-[#fff6e8] sm:text-5xl">Set New Password</h1>
        <p class="mt-6 text-lg text-[#f3e6d8]/90">Choose a strong, new password for your account. You'll be able to sign in with it right away.</p>
        <a href="./login.php" class="mt-8 inline-flex h-10 items-center justify-center rounded-full border border-white/20 bg-white/5 px-6 text-sm font-medium text-[#fff6e8] hover:bg-white/10"><i class="bi bi-arrow-left mr-2"></i>Back to Login</a>
      </div>
      <div class="reveal reveal-delay-1 rounded-none border border-[#f6e6b4]/20 bg-white p-6 sm:p-8 shadow-xl text-[#2b1a1e]">
        <?php if ($resetDone): ?>
          <h2 class="font-serif text-xl font-bold">Password Updated</h2>
          <div class="mt-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">
            <i class="bi bi-check-circle-fill mr-1"></i>
            Your password has been reset successfully. You can now <a href="./login.php" class="font-bold text-emerald-900 underline">sign in</a> with your new password.
          </div>
        <?php elseif (!empty($errors['general'])): ?>
          <h2 class="font-serif text-xl font-bold">Reset Link Invalid</h2>
          <div class="mt-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700"><?php echo htmlspecialchars($errors['general']); ?></div>
          <a href="./forgot_password.php" class="mt-4 inline-flex h-10 items-center justify-center rounded-full bg-[#6b1020] px-6 text-sm font-bold text-white">Request a new link</a>
        <?php elseif ($tokenValid): ?>
          <h2 class="font-serif text-xl font-bold">Choose a new password</h2>
          <?php if ($resetEmail): ?>
            <p class="mt-2 text-sm text-[#5a3a3f]">For <?php echo htmlspecialchars($resetEmail); ?></p>
          <?php endif; ?>
          <?php if (isset($errors['password'])) echo '<div class="mt-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">'.$errors['password'].'</div>'; ?>
          <form method="post" action="./password_reset.php" novalidate class="mt-6 grid gap-4">
            <?php csrf_field(); ?>
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <label class="text-sm font-medium">New Password
              <div class="relative mt-1">
                <input type="password" name="password" maxlength="255" placeholder="At least 8 characters" class="w-full rounded-xl border px-4 py-3 pr-11 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] border-[#e8d9b5] bg-[#fdf9f1]" required />
                <button type="button" class="toggle-password absolute inset-y-0 right-0 flex w-11 items-center justify-center text-[#8a4a2f]" aria-label="Show password"><i class="bi bi-eye-slash"></i></button>
              </div>
            </label>
            <label class="text-sm font-medium">Confirm New Password
              <div class="relative mt-1">
                <input type="password" name="password_confirm" maxlength="255" placeholder="Re-enter new password" class="w-full rounded-xl border px-4 py-3 pr-11 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] border-[#e8d9b5] bg-[#fdf9f1]" required />
                <button type="button" class="toggle-password absolute inset-y-0 right-0 flex w-11 items-center justify-center text-[#8a4a2f]" aria-label="Show password"><i class="bi bi-eye-slash"></i></button>
              </div>
              <?php if (isset($errors['password_confirm'])) echo '<span class="text-xs text-red-600">'.$errors['password_confirm'].'</span>'; ?>
            </label>
            <button type="submit" class="mt-2 inline-flex h-11 items-center justify-center rounded-full bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-sm font-bold text-[#3a0c15] hover:brightness-110 transition-all">Update Password</button>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; require_once __DIR__ . '/includes/scripts.php'; ?>
