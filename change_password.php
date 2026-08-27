<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

// Admin users cannot access user password change - redirect to admin dashboard
if (is_admin()) {
  header('Location: ./admin/index.php');
  exit;
}

$userId = $_SESSION['user_id'];

$errors = [];
$success = false;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  if (!csrf_verify()) {
    $errors['auth'] = 'Invalid request. Please try again.';
  }

  $current = $_POST['current_password'] ?? '';
  $newPass = $_POST['new_password'] ?? '';
  $confirm = $_POST['new_password_confirm'] ?? '';

  if (!$errors) {
    if (strlen($current) < 1) $errors['current_password'] = 'Enter your current password.';
    if (strlen($newPass) < 8) $errors['new_password'] = 'New password must be at least 8 characters.';
    if (strlen($newPass) > 255) $errors['new_password'] = 'New password must be 255 characters or fewer.';
    if ($newPass !== $confirm) $errors['new_password_confirm'] = 'New passwords do not match.';
  }

  if (!$errors) {
    try {
      $db = getDB();
      $stmt = $db->prepare('SELECT password FROM users WHERE id = ?');
      $stmt->execute([$userId]);
      $u = $stmt->fetch();
      if (!$u || !password_verify($current, $u['password'])) {
        $errors['current_password'] = 'Current password is incorrect.';
        rate_limit_increment('change_pw_' . $userId);
      } else {
        $hash = password_hash($newPass, PASSWORD_DEFAULT);
        $db->prepare('UPDATE users SET password = ? WHERE id = ?')->execute([$hash, $userId]);
        // Regenerate session id to prevent session fixation
        session_regenerate_id(true);
        // Clear active remember-me sessions
        remember_me_clear();
        rate_limit_reset('change_pw_' . $userId);
        $success = true;
        $_SESSION['change_pw_success'] = true;
      }
    } catch (Exception $e) {
      $errors['db'] = 'Something went wrong. Please try again.';
    }
  }
}

$pageTitle = 'Change Password — BestLife Matrimony';
$pageDescription = 'Update your BestLife Matrimony account password.';
$pageHeadExtra = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="flex-1 bg-[#f4f2ee] text-[#2b1a1e]">
  <section class="mx-auto w-full max-w-xl px-4 py-16 sm:px-6 sm:py-20">
    <div class="rounded-none border border-[#f6e6b4]/20 bg-white p-6 sm:p-10 shadow-xl">
      <h1 class="font-serif text-3xl font-bold">Change Password</h1>
      <p class="mt-2 text-[#5a3a3f]">Keep your account secure. Use a password you don't use for other sites.</p>

      <?php if ($success): ?>
        <div class="mt-6 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800"><i class="bi bi-check-circle-fill mr-1"></i> Your password has been updated successfully. You'll need to sign in again on other devices.</div>
      <?php endif; ?>
      <?php if (!empty($errors['auth'])) echo '<div class="mt-6 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">'.$errors['auth'].'</div>'; ?>
      <?php if (!empty($errors['db'])) echo '<div class="mt-6 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">'.$errors['db'].'</div>'; ?>

      <form method="post" action="./change_password.php" novalidate class="mt-6 grid gap-4">
        <?php csrf_field(); ?>
        <label class="text-sm font-medium">Current Password
          <div class="relative mt-1">
            <input type="password" name="current_password" maxlength="255" required class="w-full rounded-xl border px-4 py-3 pr-11 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($errors['current_password'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" />
            <button type="button" class="toggle-password absolute inset-y-0 right-0 flex w-11 items-center justify-center text-[#8a4a2f]" aria-label="Show password"><i class="bi bi-eye-slash"></i></button>
          </div>
          <?php if (isset($errors['current_password'])) echo '<span class="text-xs text-red-600">'.$errors['current_password'].'</span>'; ?>
        </label>
        <label class="text-sm font-medium">New Password
          <div class="relative mt-1">
            <input type="password" name="new_password" maxlength="255" placeholder="At least 8 characters" required class="w-full rounded-xl border px-4 py-3 pr-11 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($errors['new_password'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" />
            <button type="button" class="toggle-password absolute inset-y-0 right-0 flex w-11 items-center justify-center text-[#8a4a2f]" aria-label="Show password"><i class="bi bi-eye-slash"></i></button>
          </div>
          <?php if (isset($errors['new_password'])) echo '<span class="text-xs text-red-600">'.$errors['new_password'].'</span>'; ?>
        </label>
        <label class="text-sm font-medium">Confirm New Password
          <div class="relative mt-1">
            <input type="password" name="new_password_confirm" maxlength="255" placeholder="Re-enter new password" required class="w-full rounded-xl border px-4 py-3 pr-11 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($errors['new_password_confirm'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" />
            <button type="button" class="toggle-password absolute inset-y-0 right-0 flex w-11 items-center justify-center text-[#8a4a2f]" aria-label="Show password"><i class="bi bi-eye-slash"></i></button>
          </div>
          <?php if (isset($errors['new_password_confirm'])) echo '<span class="text-xs text-red-600">'.$errors['new_password_confirm'].'</span>'; ?>
        </label>
        <div class="flex flex-wrap gap-3 pt-2">
          <button type="submit" class="inline-flex h-11 items-center justify-center rounded-full bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-sm font-bold text-[#3a0c15] hover:brightness-110"><i class="bi bi-shield-lock mr-1"></i>Update Password</button>
          <a href="./profile.php" class="inline-flex h-11 items-center justify-center rounded-full border border-[#e8d9b5] bg-white px-6 text-sm font-medium text-[#2b1a1e] hover:bg-[#fdf9f1]">Back to Profile</a>
        </div>
      </form>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; require_once __DIR__ . '/includes/scripts.php'; ?>
