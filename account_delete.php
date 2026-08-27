<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

// Admin users cannot access user account deletion - redirect to admin dashboard
if (is_admin()) {
  header('Location: ./admin/index.php');
  exit;
}

$userId = $_SESSION['user_id'];

$name = '';
try {
  $db = getDB();
  $stmt = $db->prepare('SELECT full_name FROM users WHERE id = ?');
  $stmt->execute([$userId]);
  $u = $stmt->fetch();
  $name = $u['full_name'] ?? '';
} catch (Exception $e) { /* noop */ }

$deleteErrors = [];
$deleteSuccess = false;
$pendingPreview = false;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  if (!csrf_verify()) {
    $deleteErrors['auth'] = 'Invalid request. Please try again.';
  } else {
    $action = $_POST['action'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_delete'] ?? '';

    if ($action === 'delete') {
      if ($confirm !== 'DELETE') {
        $deleteErrors['confirm'] = 'Please type DELETE to confirm.';
      }
      if (strlen($password) < 8) {
        $deleteErrors['password'] = 'Enter your current password to confirm.';
      }
      if (!$deleteErrors) {
        try {
          $stmt = $db->prepare('SELECT password FROM users WHERE id = ?');
          $stmt->execute([$userId]);
          $p = $stmt->fetch();
          if ($p && password_verify($password, $p['password'])) {
            // Remove related data then delete the account
            $db->prepare('DELETE FROM notifications WHERE user_id = ?')->execute([$userId]);
            $db->prepare('DELETE FROM messages WHERE sender_id = ? OR receiver_id = ?')->execute([$userId, $userId]);
            $db->prepare('DELETE FROM email_verifications WHERE user_id = ?')->execute([$userId]);
            $db->prepare('DELETE FROM password_resets WHERE user_id = ?')->execute([$userId]);
            $db->prepare('DELETE FROM users WHERE id = ?')->execute([$userId]); // favourites cascade
            remember_me_clear();
            session_regenerate_id(true);
            session_unset();
            session_destroy();
            header('Location: ./index.php?account=deleted');
            exit;
          } else {
            $deleteErrors['password'] = 'Incorrect password. Your account was not deleted.';
            rate_limit_increment('delete_' . $userId);
          }
        } catch (Exception $e) {
          $deleteErrors['db'] = 'Something went wrong. Please try again.';
        }
      }
    } elseif ($action === 'preview') {
      if (strlen($password) < 8) {
        $deleteErrors['password'] = 'Enter your current password to preview.';
      } else {
        try {
          $stmt = $db->prepare('SELECT password FROM users WHERE id = ?');
          $stmt->execute([$userId]);
          $p = $stmt->fetch();
          if ($p && password_verify($password, $p['password'])) {
            $pendingPreview = true;
          } else {
            $deleteErrors['password'] = 'Incorrect password.';
            rate_limit_increment('delete_' . $userId);
          }
        } catch (Exception $e) {
          $deleteErrors['db'] = 'Something went wrong. Please try again.';
        }
      }
    }
  }
}

$pageTitle = 'Delete Account — BestLife Matrimony';
$pageDescription = 'Delete your account and remove your data from BestLife Matrimony.';
$pageHeadExtra = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="flex-1 bg-[#f4f2ee] text-[#2b1a1e]">
  <section class="mx-auto w-full max-w-3xl px-4 py-16 sm:px-6 sm:py-20">
    <div class="rounded-none border border-[#f6e6b4]/20 bg-white p-6 sm:p-10 shadow-xl">
      <h1 class="font-serif text-3xl font-bold">Delete Account</h1>
      <p class="mt-2 text-[#5a3a3f]">Permanently remove your profile, photos, matches, messages and data from BestLife Matrimony. This cannot be undone.</p>

      <?php if ($deleteSuccess): ?>
        <div class="mt-6 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800"><i class="bi bi-check-circle-fill mr-1"></i> Your account has been deleted. Goodbye and thank you.</div>
      <?php endif; ?>
      <?php if (!empty($deleteErrors['auth'])) echo '<div class="mt-6 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">'.$deleteErrors['auth'].'</div>'; ?>
      <?php if (!empty($deleteErrors['db'])) echo '<div class="mt-6 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">'.$deleteErrors['db'].'</div>'; ?>

      <div class="mt-8 rounded-xl border border-red-200 bg-red-50/60 p-5">
        <h2 class="font-bold text-red-800">What happens when you delete?</h2>
        <ul class="mt-3 space-y-2 text-sm text-red-900/90">
          <li>• Your profile and photos are removed immediately</li>
          <li>• All favourites, messages and notifications are deleted</li>
          <li>• Your email address can be used again later</li>
          <li>• This action is permanent and irreversible</li>
        </ul>
      </div>

      <?php if ($pendingPreview): ?>
        <div class="mt-6 rounded-xl border border-amber-300 bg-amber-50 p-5 text-sm text-amber-900">
          <strong>Ready to delete.</strong> Re-enter your password once more and type <strong>DELETE</strong> to permanently remove your account.
        </div>
      <?php endif; ?>

      <form method="post" action="./account_delete.php" novalidate class="mt-6 grid gap-4">
        <?php csrf_field(); ?>
        <label class="text-sm font-medium">Current Password
          <input type="password" name="password" maxlength="255" required class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 <?php echo isset($deleteErrors['password'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" />
          <?php if (isset($deleteErrors['password'])) echo '<span class="text-xs text-red-600">'.$deleteErrors['password'].'</span>'; ?>
        </label>
        <label class="text-sm font-medium">Type <span class="font-bold text-red-600">DELETE</span> to confirm
          <input type="text" name="confirm_delete" maxlength="20" required placeholder="DELETE" class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-400 <?php echo isset($deleteErrors['confirm'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" />
          <?php if (isset($deleteErrors['confirm'])) echo '<span class="text-xs text-red-600">'.$deleteErrors['confirm'].'</span>'; ?>
        </label>
        <div class="flex flex-wrap gap-3 pt-2">
          <button type="submit" name="action" value="delete" class="inline-flex h-11 items-center justify-center rounded-full bg-red-700 px-8 text-sm font-bold text-white hover:bg-red-800"><i class="bi bi-trash3 mr-2"></i>Permanently Delete My Account</button>
          <a href="./profile.php" class="inline-flex h-11 items-center justify-center rounded-full border border-[#e8d9b5] bg-white px-6 text-sm font-medium text-[#2b1a1e] hover:bg-[#fdf9f1]">Cancel</a>
        </div>
      </form>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; require_once __DIR__ . '/includes/scripts.php'; ?>
