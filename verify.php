<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

// Admin users cannot access user verification - redirect to admin dashboard
if (is_admin()) {
  header('Location: ./admin/index.php');
  exit;
}

$userId = $_SESSION['user_id'];

$user = null;
$phoneVerified = false;
$emailVerified = false;
$idVerified = false;
$idRequestStatus = 'none';
try {
  $db = getDB();
  $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
  $stmt->execute([$userId]);
  $user = $stmt->fetch();
  $phoneVerified = (int) ($user['phone_verified'] ?? 0) === 1;
  $emailVerified = (int) ($user['email_verified'] ?? 0) === 1;
  $idVerified = (int) ($user['id_verified'] ?? 0) === 1;
  $vr = $db->prepare('SELECT status FROM verification_requests WHERE user_id = ? ORDER BY id DESC LIMIT 1');
  $vr->execute([$userId]);
  $idRequestStatus = $vr->fetchColumn() ?: 'none';
} catch (Exception $e) { /* ignore */ }

$msg = '';
$msgType = '';

// Handle POST actions
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && csrf_verify()) {
  $action = $_POST['action'] ?? '';

  // Send phone OTP
  if ($action === 'send_phone_otp' && !$phoneVerified) {
    if (!rate_limit_check('otp_phone_' . $userId, 3, 300)) {
      $msg = 'Too many OTP requests. Please wait a few minutes.'; $msgType = 'error';
    } else {
      $otp = issue_otp($userId, 'phone', 600);
      if ($otp === null) {
        $msg = 'Something went wrong. Please try again.'; $msgType = 'error';
      } else {
        rate_limit_increment('otp_phone_' . $userId);
        // Demo/dev: display the OTP since no real SMS gateway is configured.
        $_SESSION['demo_otp_phone'] = $otp;
        $msg = 'OTP sent to ' . htmlspecialchars($user['phone'] ?? 'your phone') .
               ' (Dev mode: your code is <strong>' . $otp . '</strong>). Enter it below.'; $msgType = 'success';
      }
    }
  }

  // Verify phone OTP
  elseif ($action === 'verify_phone_otp' && !$phoneVerified) {
    $code = trim($_POST['otp'] ?? '');
    if (strlen($code) !== 6 || !ctype_digit($code)) {
      $msg = 'Please enter the 6-digit code.'; $msgType = 'error';
    } elseif (verify_otp($userId, 'phone', $code)) {
      $db->prepare('UPDATE users SET phone_verified = 1 WHERE id = ?')->execute([$userId]);
      $phoneVerified = true;
      unset($_SESSION['demo_otp_phone']);
      $msg = 'Phone number verified successfully.'; $msgType = 'success';
    } else {
      $msg = 'Invalid or expired code. Please try again.'; $msgType = 'error';
    }
  }

  // Resend email verification
  elseif ($action === 'resend_email' && !$emailVerified) {
    if (!rate_limit_check('resend_email_' . $userId, 3, 300)) {
      $msg = 'Too many requests. Please wait a few minutes.'; $msgType = 'error';
    } else {
      $ok = issue_email_verification($userId, $user['email'] ?? '') !== null; // always true in practice
      rate_limit_increment('resend_email_' . $userId);
      $msg = 'A verification link has been sent to ' . htmlspecialchars($user['email'] ?? 'your email') . '.'; $msgType = 'success';
    }
  }

  // Request ID verification badge
  elseif ($action === 'request_id' && !$idVerified && $idRequestStatus === 'none') {
    if (!rate_limit_check('id_req_' . $userId, 2, 3600)) {
      $msg = 'Too many requests. Please wait.'; $msgType = 'error';
    } else {
      try {
        $db->prepare('INSERT INTO verification_requests (user_id, type) VALUES (?, ?)')->execute([$userId, 'id']);
        $idRequestStatus = 'pending';
        log_activity((int) $userId, 'verification_request', 'verification_request', (int) $db->lastInsertId(), 'Requested ID verification');
        notification_add($userId, 'verification', 'Your ID verification request has been submitted for review.');
        $msg = 'Your ID verification request has been submitted. We will review it shortly.'; $msgType = 'success';
      } catch (Exception $e) {
        $msg = 'Something went wrong. Please try again.'; $msgType = 'error';
      }
    }
  }
}

$pageTitle = 'Profile Verification — BestLife Matrimony';
$pageDescription = 'Verify your identity, phone and email to earn a trust badge.';
$pageHeadExtra = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<style>
  .vf-wrap{max-width:720px;margin:0 auto;padding:24px 16px 48px;font-family:Inter,system-ui,sans-serif;color:#1a1a1a}
  .vf-card{background:#fff;border:1px solid #eee;padding:24px;margin-bottom:16px}
  .vf-card h2{font-size:17px;margin:0 0 4px;display:flex;align-items:center;gap:8px}
  .vf-card .sub{font-size:13px;color:#666;margin:0 0 16px}
  .vf-head{background:#fff;border:1px solid #eee;padding:24px;margin-bottom:16px}
  .vf-head h1{font-size:22px;margin:0}
  .vf-head p{margin:4px 0 0;color:#666;font-size:13px}
  .vf-row{display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap}
  .vf-status{display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;padding:6px 12px;border-radius:999px}
  .vf-ok{background:#e7f6ec;color:#15803d}
  .vf-no{background:#f1f1f1;color:#666}
  .vf-pending{background:#fff7e0;color:#b45309}
  .vf-btn{height:38px;padding:0 16px;font-size:13px;border-radius:8px;border:1px solid #ddd;background:#fff;cursor:pointer;font-weight:600}
  .vf-btn-primary{background:#6b1020;color:#fff;border-color:#6b1020}
  .vf-msg{margin-bottom:16px;padding:12px 14px;font-size:13px;border-radius:8px}
  .vf-msg.success{background:#f0fdf4;border:1px solid #bbf7d0;color:#166534}
  .vf-msg.error{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}
  .vf-in{width:100%;padding:11px 14px;border:1px solid #ddd;border-radius:8px;font-size:14px;margin-top:8px}
  .vf-in:focus{outline:none;border-color:#6b1020}
</style>

<main class="bg-[#f4f2ee]" style="min-height:100vh;">
  <div class="vf-wrap">
    <div class="vf-head">
      <h1>Profile Verification</h1>
      <p>Verify your email, phone and ID to build trust with other members.</p>
    </div>

    <?php if ($msg): ?>
      <div class="vf-msg <?php echo $msgType; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>

    <!-- Email -->
    <div class="vf-card">
      <h2><i class="bi bi-envelope-check"></i> Email Verification</h2>
      <p class="sub"><?php echo htmlspecialchars($user['email'] ?? ''); ?></p>
      <div class="vf-row">
        <?php if ($emailVerified): ?>
          <span class="vf-status vf-ok"><i class="bi bi-check-circle-fill"></i> Verified</span>
        <?php else: ?>
          <span class="vf-status vf-no"><i class="bi bi-x-circle"></i> Not verified</span>
        <?php endif; ?>
        <?php if (!$emailVerified): ?>
          <form method="post" action="./verify.php"><input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>"><input type="hidden" name="action" value="resend_email"><button type="submit" class="vf-btn vf-btn-primary">Resend Email Link</button></form>
        <?php endif; ?>
        <?php if ($emailVerified): ?>
          <a href="./verify_email.php" class="vf-btn">Manage</a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Phone -->
    <div class="vf-card">
      <h2><i class="bi bi-phone"></i> Phone Verification</h2>
      <p class="sub"><?php echo htmlspecialchars($user['phone'] ?? ''); ?></p>
      <div class="vf-row">
        <?php if ($phoneVerified): ?>
          <span class="vf-status vf-ok"><i class="bi bi-check-circle-fill"></i> Verified</span>
        <?php else: ?>
          <span class="vf-status vf-no"><i class="bi bi-x-circle"></i> Not verified</span>
          <form method="post" action="./verify.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            <input type="hidden" name="action" value="send_phone_otp">
            <button type="submit" class="vf-btn vf-btn-primary"><i class="bi bi-send"></i> Send OTP</button>
          </form>
        <?php endif; ?>
      </div>
      <?php if (!$phoneVerified && !empty($_SESSION['demo_otp_phone'])): ?>
        <form method="post" action="./verify.php" style="margin-top:14px;border-top:1px dashed #eee;padding-top:14px;" novalidate>
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
          <input type="hidden" name="action" value="verify_phone_otp">
          <label style="font-size:13px;font-weight:600;">Enter 6-digit code
            <input type="text" name="otp" maxlength="6" class="vf-in" placeholder="••••••" inputmode="numeric">
          </label>
          <button type="submit" class="vf-btn vf-btn-primary" style="margin-top:12px;">Verify Phone</button>
        </form>
      <?php endif; ?>
    </div>

    <!-- ID Verification Badge -->
    <div class="vf-card">
      <h2><i class="bi bi-patch-check"></i> ID Verified Badge</h2>
      <p class="sub">Earn a verified badge shown on your profile. An identity document is reviewed by our team.</p>
      <div class="vf-row">
        <?php if ($idVerified): ?>
          <span class="vf-status vf-ok"><i class="bi bi-patch-check-fill"></i> ID Verified</span>
        <?php elseif ($idRequestStatus === 'pending'): ?>
          <span class="vf-status vf-pending"><i class="bi bi-hourglass-split"></i> Under review</span>
        <?php elseif ($idRequestStatus === 'rejected'): ?>
          <span class="vf-status vf-no"><i class="bi bi-x-circle"></i> Rejected</span>
        <?php else: ?>
          <span class="vf-status vf-no"><i class="bi bi-patch-question"></i> Not verified</span>
        <?php endif; ?>

        <?php if (!$idVerified && $idRequestStatus === 'none'): ?>
          <form method="post" action="./verify.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
            <input type="hidden" name="action" value="request_id">
            <button type="submit" class="vf-btn vf-btn-primary">Request Verification</button>
          </form>
        <?php elseif ($idRequestStatus === 'rejected'): ?>
          <span class="vf-btn" style="cursor:default;">Please contact support</span>
        <?php endif; ?>
      </div>
    </div>

    <p style="text-align:center;font-size:12px;color:#999;">Your information is kept private and only used for verification purposes.</p>
  </div>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; require_once __DIR__ . '/includes/scripts.php'; ?>
