<?php
require_once __DIR__ . '/includes/db.php';

$userId = $_SESSION['user_id'] ?? null;
$token = $_GET['token'] ?? '';
$status = '';
$verifiedEmail = '';

if ($token !== '') {
  $tokenHash = hash('sha256', $token);
  try {
    $db = getDB();
    $stmt = $db->prepare(
      'SELECT ev.user_id, ev.expires_at, u.email, u.email_verified FROM email_verifications ev
       JOIN users u ON u.id = ev.user_id
       WHERE ev.token_hash = ? AND ev.used = 0 LIMIT 1'
    );
    $stmt->execute([$tokenHash]);
    $row = $stmt->fetch();
    if (!$row || strtotime($row['expires_at']) < time()) {
      $status = 'invalid';
    } elseif ((int) $row['email_verified'] === 1) {
      $db->prepare('UPDATE email_verifications SET used = 1 WHERE token_hash = ?')->execute([$tokenHash]);
      $status = 'already';
      $verifiedEmail = $row['email'];
    } else {
      $db->prepare('UPDATE users SET email_verified = 1 WHERE id = ?')->execute([$row['user_id']]);
      $db->prepare('UPDATE email_verifications SET used = 1 WHERE token_hash = ?')->execute([$tokenHash]);
      // Sign the user in if not already
      if (!$userId) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $row['user_id'];
        $userId = (int) $row['user_id'];
      }
      $status = 'verified';
      $verifiedEmail = $row['email'];
    }
  } catch (Exception $e) {
    $status = 'error';
  }
}

// Handle resend request
$resendMessage = '';
if (($userId || isset($_GET['resend'])) && ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  if (!csrf_verify()) {
    $resendMessage = 'Invalid request. Please try again.';
  } elseif (!$userId) {
    $resendMessage = 'Please log in to resend a verification email.';
  } elseif (!rate_limit_check('resend_verify_' . $userId, 3, 300)) {
    $resendMessage = 'Too many requests. Please wait a few minutes.';
  } else {
    try {
      $db = getDB();
      $s = $db->prepare('SELECT email, email_verified FROM users WHERE id = ?');
      $s->execute([$userId]);
      $u = $s->fetch();
      if ($u && (int) $u['email_verified'] === 1) {
        $resendMessage = 'Your email is already verified.';
      } elseif ($u) {
        issue_email_verification($userId, $u['email']);
        rate_limit_increment('resend_verify_' . $userId);
        $resendMessage = 'A new verification link has been sent to your email.';
      } else {
        $resendMessage = 'Account not found.';
      }
    } catch (Exception $e) {
      $resendMessage = 'Something went wrong. Please try again.';
    }
  }
}

$pageTitle = 'Verify Email — BestLife Matrimony';
$pageDescription = 'Confirm your BestLife Matrimony account email address.';
$pageHeadExtra = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="flex-1 bg-[#f4f2ee]">
  <section class="mx-auto w-full max-w-xl px-4 py-16 sm:px-6 sm:py-24">
    <div class="rounded-none border border-[#f6e6b4]/20 bg-white p-6 sm:p-10 shadow-xl text-[#2b1a1e] text-center">
      <?php if ($status === 'verified'): ?>
        <i class="bi bi-check-circle-fill text-5xl text-emerald-600"></i>
        <h1 class="mt-4 font-serif text-3xl font-bold">Email Verified</h1>
        <p class="mt-3 text-[#5a3a3f]">Thank you! <?php echo htmlspecialchars($verifiedEmail ?: 'Your email address'); ?> has been verified successfully. Your account is now fully active.</p>
        <a href="./profile.php" class="mt-6 inline-flex h-11 items-center justify-center rounded-full bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-sm font-bold text-[#3a0c15]">Go to My Profile</a>
      <?php elseif ($status === 'already'): ?>
        <i class="bi bi-patch-check text-5xl text-emerald-600"></i>
        <h1 class="mt-4 font-serif text-3xl font-bold">Already Verified</h1>
        <p class="mt-3 text-[#5a3a3f]"><?php echo htmlspecialchars($verifiedEmail ?: 'This email'); ?> has already been verified.</p>
      <?php elseif ($status === 'invalid'): ?>
        <i class="bi bi-x-circle text-5xl text-red-600"></i>
        <h1 class="mt-4 font-serif text-3xl font-bold">Invalid or Expired Link</h1>
        <p class="mt-3 text-[#5a3a3f]">This verification link is invalid or has expired. Log in below to request a new one.</p>
      <?php elseif ($status === 'error'): ?>
        <i class="bi bi-exclamation-triangle text-5xl text-amber-500"></i>
        <h1 class="mt-4 font-serif text-3xl font-bold">Something Went Wrong</h1>
        <p class="mt-3 text-[#5a3a3f]">Please try again or request a new verification link.</p>
      <?php else: ?>
        <i class="bi bi-envelope-check text-5xl text-[#b8860b]"></i>
        <h1 class="mt-4 font-serif text-3xl font-bold">Verify Your Email</h1>
        <p class="mt-3 text-[#5a3a3f]">Use the link sent to your inbox to confirm your email address. If you're logged in, you can resend the link below.</p>
      <?php endif; ?>

      <?php if ($resendMessage): ?>
        <div class="mt-6 rounded-xl bg-amber-50 border border-amber-200 px-4 py-3 text-sm text-amber-800"><?php echo htmlspecialchars($resendMessage); ?></div>
      <?php endif; ?>

      <?php if ($userId && ($status === 'invalid' || $status === 'error' || $status === '')): ?>
        <form method="post" action="./verify_email.php" class="mt-8" novalidate>
          <?php csrf_field(); ?>
          <button type="submit" class="inline-flex h-11 items-center justify-center rounded-full border border-[#e8d9b5] bg-white px-8 text-sm font-medium text-[#2b1a1e] hover:bg-[#fdf9f1]"><i class="bi bi-envelope-arrow-up mr-2"></i>Resend Verification Email</button>
        </form>
      <?php elseif ($status === 'invalid'): ?>
        <a href="./login.php" class="mt-8 inline-flex h-11 items-center justify-center rounded-full border border-[#e8d9b5] bg-white px-8 text-sm font-medium text-[#2b1a1e] hover:bg-[#fdf9f1]">Log In</a>
      <?php endif; ?>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; require_once __DIR__ . '/includes/scripts.php'; ?>
