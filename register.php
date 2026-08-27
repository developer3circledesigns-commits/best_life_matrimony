<?php
require_once __DIR__ . '/includes/db.php';

// Already logged in? Send them to their profile instead of asking them to register.
if (is_logged_in()) {
  header('Location: ./profile.php');
  exit;
}

/* ── Handle registration BEFORE any output ──────── */
$errors = [];
$success = false;
$name = $email = $phone = $looking_for = '';
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  // CSRF check
  if (!csrf_verify()) {
    $errors['auth'] = 'Invalid request. Please try again.';
  }

  // Rate limit check
  if (!$errors && !rate_limit_check('register', 3, 600)) {
    $errors['auth'] = 'Too many registration attempts. Please wait 10 minutes and try again.';
  }

  $name = trim($_POST['full_name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  $password = $_POST['password'] ?? '';
  $password_confirm = $_POST['password_confirm'] ?? '';
  $looking_for = $_POST['looking_for'] ?? '';

  if (!$errors) {
    if ($name === '') $errors['full_name'] = 'Full name is required.';
    if (strlen($name) > 150) $errors['full_name'] = 'Full name must be 150 characters or fewer.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Valid email required.';
    if (strlen($email) > 191) $errors['email'] = 'Email must be 191 characters or fewer.';
    if (strlen($phone) < 8) $errors['phone'] = 'Valid phone required.';
    if (strlen($phone) > 30) $errors['phone'] = 'Phone must be 30 characters or fewer.';
    if (strlen($password) < 8) $errors['password'] = 'Password must be at least 8 characters.';
    if (strlen($password) > 255) $errors['password'] = 'Password must be 255 characters or fewer.';
    if ($password !== $password_confirm) $errors['password_confirm'] = 'Passwords do not match.';
    if (!in_array($looking_for, ['Bride', 'Groom'], true)) $errors['looking_for'] = 'Please select who you are looking for.';
  }

  if (!$errors) {
    try {
      $db = getDB();
      $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
      $stmt->execute([$email]);
      if ($stmt->fetch()) {
        $errors['email'] = 'An account with this email already exists.';
      } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare('INSERT INTO users (full_name, email, phone, password, looking_for) VALUES (?, ?, ?, ?, ?)');
        $stmt->execute([$name, $email, $phone, $hash, $looking_for]);
        $newId = $db->lastInsertId();
        issue_email_verification((int) $newId, $email);
        log_activity((int) $newId, 'register', 'user', (int) $newId, 'Account created as ' . $looking_for);
        session_regenerate_id(true);
        $_SESSION['user_id'] = $newId;
        $_SESSION['user_name'] = $name;
        rate_limit_reset('register');
        header('Location: ./profile.php');
        exit;
      }
    } catch (PDOException $e) {
      $errors['db'] = 'Something went wrong. Please try again later.';
    }
  }
}

/* ── Now render the page (output starts here) ──── */
$pageTitle = 'Register — BestLife Matrimony';
$pageDescription = 'Create your profile and begin your journey with BestLife Matrimony.';
$pageHeadExtra = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="flex-1 bg-[#f4f2ee]">
  <section class="mx-auto w-full max-w-6xl px-4 py-16 sm:px-6 sm:py-24">
    <div class="grid lg:grid-cols-2 gap-8 items-start">
      <div class="reveal rounded-none border border-[#f6e6b4]/20 bg-[#6b1020] p-8 sm:p-12 shadow-2xl">
        <h1 class="font-serif text-4xl font-bold tracking-tight text-[#fff6e8] sm:text-5xl">Register Now</h1>
        <p class="mt-6 text-lg text-[#f3e6d8]/90">Create your profile and begin your journey with BestLife Matrimony. It takes less than 3 minutes.</p>
        <ul class="mt-6 space-y-2 text-sm text-[#fff6e8]/80">
          <li>• Genuine, verified profiles</li><li>• Privacy with dignity</li><li>• Family collaboration</li>
        </ul>
        <a href="./index.php" class="mt-8 inline-flex h-10 items-center justify-center rounded-full border border-white/20 bg-white/5 px-6 text-sm font-medium text-[#fff6e8] hover:bg-white/10">Back to Home</a>
      </div>
      <form method="post" action="./register.php" novalidate class="reveal reveal-delay-1 rounded-none border border-[#f6e6b4]/20 bg-white p-6 sm:p-8 shadow-xl text-[#2b1a1e]">
        <h2 class="font-serif text-xl font-bold">Create your account</h2>
        <?php csrf_field(); ?>
        <?php if (!empty($errors['auth'])) echo '<div class="mt-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">'.$errors['auth'].'</div>'; ?>
        <?php if (!empty($errors['db'])) echo '<div class="mt-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">'.$errors['db'].'</div>'; ?>
        <div class="mt-6 grid gap-4">
          <label class="text-sm font-medium">Full Name
            <input type="text" name="full_name" maxlength="150" value="<?php echo htmlspecialchars($name); ?>" placeholder="e.g. Ananya S." class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($errors['full_name'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" required />
            <?php if (isset($errors['full_name'])) echo '<span class="text-xs text-red-600">'.$errors['full_name'].'</span>'; ?>
          </label>
          <label class="text-sm font-medium">Email
            <input type="email" name="email" maxlength="191" value="<?php echo htmlspecialchars($email); ?>" placeholder="you@example.com" class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($errors['email'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" required />
            <?php if (isset($errors['email'])) echo '<span class="text-xs text-red-600">'.$errors['email'].'</span>'; ?>
          </label>
          <label class="text-sm font-medium">Phone
            <input type="tel" name="phone" maxlength="30" value="<?php echo htmlspecialchars($phone); ?>" placeholder="+91 98765 43210" class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($errors['phone'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" required />
            <?php if (isset($errors['phone'])) echo '<span class="text-xs text-red-600">'.$errors['phone'].'</span>'; ?>
          </label>
          <label class="text-sm font-medium">Password
            <div class="relative mt-1">
              <input type="password" name="password" maxlength="255" placeholder="At least 8 characters" class="w-full rounded-xl border px-4 py-3 pr-11 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($errors['password'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" required />
              <button type="button" class="toggle-password absolute inset-y-0 right-0 flex w-11 items-center justify-center text-[#8a4a2f] hover:text-[#5a3a3f]" aria-label="Show password"><i class="bi bi-eye-slash"></i></button>
            </div>
            <?php if (isset($errors['password'])) echo '<span class="text-xs text-red-600">'.$errors['password'].'</span>'; ?>
          </label>
          <label class="text-sm font-medium">Confirm Password
            <div class="relative mt-1">
              <input type="password" name="password_confirm" maxlength="255" placeholder="Re-enter your password" class="w-full rounded-xl border px-4 py-3 pr-11 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($errors['password_confirm'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" required />
              <button type="button" class="toggle-password absolute inset-y-0 right-0 flex w-11 items-center justify-center text-[#8a4a2f] hover:text-[#5a3a3f]" aria-label="Show password"><i class="bi bi-eye-slash"></i></button>
            </div>
            <?php if (isset($errors['password_confirm'])) echo '<span class="text-xs text-red-600">'.$errors['password_confirm'].'</span>'; ?>
          </label>
          <label class="text-sm font-medium">Looking for
            <?php if (isset($errors['looking_for'])) echo '<span class="text-xs text-red-600">'.$errors['looking_for'].'</span>'; ?>
          </label>
          <div class="mt-1 grid grid-cols-2 gap-3">
            <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-[#e8d9b5] bg-[#fdf9f1] px-4 py-3 text-sm focus-within:ring-2 focus-within:ring-[#e3c877] has-[:checked]:border-[#e3c877] has-[:checked]:bg-[#e3c877]/15">
              <input type="radio" name="looking_for" value="Bride" class="sr-only" <?php echo $looking_for === 'Bride' ? 'checked' : ''; ?>>
              Bride <span class="text-xs text-[#5a3a3f]">(Female Partner)</span>
            </label>
            <label class="flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-[#e8d9b5] bg-[#fdf9f1] px-4 py-3 text-sm focus-within:ring-2 focus-within:ring-[#e3c877] has-[:checked]:border-[#e3c877] has-[:checked]:bg-[#e3c877]/15">
              <input type="radio" name="looking_for" value="Groom" class="sr-only" <?php echo $looking_for === 'Groom' ? 'checked' : ''; ?>>
              Groom <span class="text-xs text-[#5a3a3f]">(Male Partner)</span>
            </label>
          </div>
          <button type="submit" class="mt-2 inline-flex h-11 items-center justify-center rounded-full bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-sm font-bold text-[#3a0c15] hover:brightness-110 transition-all disabled:opacity-50">Create Profile</button>
          <p class="text-xs text-[#5a3a3f] text-center">By registering, you agree to our Terms & Privacy Policy.</p>
        </div>
      </form>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; require_once __DIR__ . '/includes/scripts.php'; ?>
