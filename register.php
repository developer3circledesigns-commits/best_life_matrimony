<?php
$pageTitle = 'Register — BestLife Matrimony';
$pageDescription = 'Create your profile and begin your journey with BestLife Matrimony.';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
$success = false;
$errors = [];
if (($_SERVER['REQUEST_METHOD'] ?? 'GET')==='POST') {
  $name = trim($_POST['full_name'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $phone = trim($_POST['phone'] ?? '');
  if ($name==='') $errors['full_name']='Full name is required.';
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors['email']='Valid email required.';
  if (strlen($phone)<8) $errors['phone']='Valid phone required.';
  if (!$errors) $success = true;
}
?>
<main class="flex-1 bg-transparent">
  <section class="mx-auto w-full max-w-6xl px-4 py-16 sm:px-6 sm:py-24">
    <div class="grid lg:grid-cols-2 gap-8 items-start">
      <div class="reveal rounded-3xl border border-[#f6e6b4]/20 bg-black/35 p-8 sm:p-12 backdrop-blur-md shadow-2xl">
        <h1 class="font-serif text-4xl font-bold tracking-tight text-[#fff6e8] sm:text-5xl">Register Now</h1>
        <p class="mt-6 text-lg text-[#f3e6d8]/90">Create your profile and begin your journey with BestLife Matrimony. It takes less than 3 minutes.</p>
        <ul class="mt-6 space-y-2 text-sm text-[#fff6e8]/80">
          <li>• Genuine, verified profiles</li><li>• Privacy with dignity</li><li>• Family collaboration</li>
        </ul>
        <a href="./index.php" class="mt-8 inline-flex h-10 items-center justify-center rounded-full border border-white/20 bg-white/5 px-6 text-sm font-medium text-[#fff6e8] hover:bg-white/10">Back to Home</a>
      </div>
      <form method="post" action="./register.php" novalidate class="reveal reveal-delay-1 rounded-3xl border border-[#f6e6b4]/20 bg-white p-6 sm:p-8 shadow-xl text-[#2b1a1e]">
        <h2 class="font-serif text-xl font-bold">Create your account</h2>
        <?php if ($success): ?>
          <div class="mt-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">Welcome, <?php echo htmlspecialchars($name); ?>! Your registration was successful (demo). Check your email at <?php echo htmlspecialchars($email); ?>.</div>
        <?php endif; ?>
        <div class="mt-6 grid gap-4">
          <label class="text-sm font-medium">Full Name
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>" placeholder="e.g. Ananya S." class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($errors['full_name'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" required />
            <?php if (isset($errors['full_name'])) echo '<span class="text-xs text-red-600">'.$errors['full_name'].'</span>'; ?>
          </label>
          <label class="text-sm font-medium">Email
            <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" placeholder="you@example.com" class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($errors['email'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" required />
            <?php if (isset($errors['email'])) echo '<span class="text-xs text-red-600">'.$errors['email'].'</span>'; ?>
          </label>
          <label class="text-sm font-medium">Phone
            <input type="tel" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>" placeholder="+91 98765 43210" class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($errors['phone'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" required />
            <?php if (isset($errors['phone'])) echo '<span class="text-xs text-red-600">'.$errors['phone'].'</span>'; ?>
          </label>
          <label class="text-sm font-medium">Looking for
            <select name="looking_for" class="mt-1 w-full rounded-xl border border-[#e8d9b5] bg-[#fdf9f1] px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877]">
              <option>Bride</option><option>Groom</option>
            </select>
          </label>
          <button type="submit" class="mt-2 inline-flex h-11 items-center justify-center rounded-full bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-sm font-bold text-[#3a0c15] hover:brightness-110 transition-all disabled:opacity-50">Create Profile</button>
          <p class="text-xs text-[#5a3a3f] text-center">By registering, you agree to our Terms & Privacy Policy.</p>
        </div>
      </form>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; require_once __DIR__ . '/includes/scripts.php'; ?>
