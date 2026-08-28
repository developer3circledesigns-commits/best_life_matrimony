<?php
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$contactErrors = [];
$contactSuccess = false;
$contactName = $contactEmail = $contactPhone = $contactMessage = '';
$sentViaMail = false;
$isApprovalRequest = ($_GET['reason'] ?? '') === 'approval';

// Pre-fill form for logged-in users requesting approval
if ($isApprovalRequest && !empty($_SESSION['user_id'])) {
  $cu = current_user();
  if ($cu) {
    $contactName = $cu['full_name'] ?? '';
    $contactEmail = $cu['email'] ?? '';
    $contactPhone = $cu['phone'] ?? '';
    $contactMessage = "Hello,\n\nI have registered on BestLife Matrimony and completed my profile. I would like to request approval so I can view profiles and connect with matches.\n\nMy user ID is: " . ($_SESSION['user_id'] ?? '') . "\nName: " . ($cu['full_name'] ?? '') . "\n\nPlease review and approve my profile.\n\nThank you.";
  }
}

// Handle re-request approval (clears rejected_reason)
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['request_approval']) && !empty($_SESSION['user_id'])) {
  if (!csrf_verify()) {
    $contactErrors['auth'] = 'Invalid request.';
  } elseif (!rate_limit_check('contact', 5, 300)) {
    $contactErrors['auth'] = 'Too many requests. Please wait.';
  } else {
    try {
      $db = getDB();
      $uid = (int)$_SESSION['user_id'];
      $db->prepare('UPDATE users SET rejected_reason = NULL WHERE id = ?')->execute([$uid]);
      log_activity($uid, 'approval_request', 'user', $uid, 'Requested approval again via contact');
      $contactSuccess = true;
      $contactErrors = [];
    } catch (Throwable $e) {
      $contactErrors['auth'] = 'Could not submit request. Please try again.';
    }
  }
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  if (!csrf_verify()) {
    $contactErrors['auth'] = 'Invalid request. Please try again.';
  }

  if (!$contactErrors && !rate_limit_check('contact', 5, 300)) {
    $contactErrors['auth'] = 'Too many messages. Please wait a few minutes.';
  }

  $contactName = trim($_POST['name'] ?? '');
  $contactEmail = trim($_POST['email'] ?? '');
  $contactPhone = trim($_POST['phone'] ?? '');
  $contactMessage = trim($_POST['message'] ?? '');

  if (!$contactErrors) {
    if ($contactName === '') $contactErrors['name'] = 'Name is required.';
    if (strlen($contactName) > 150) $contactErrors['name'] = 'Name must be 150 characters or fewer.';
    if (!filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) $contactErrors['email'] = 'Valid email required.';
    if (strlen($contactEmail) > 191) $contactErrors['email'] = 'Email too long.';
    if (strlen($contactPhone) < 8 || strlen($contactPhone) > 30) $contactErrors['phone'] = 'Valid phone required.';
    if ($contactMessage === '') $contactErrors['message'] = 'Message is required.';
    if (strlen($contactMessage) > 5000) $contactErrors['message'] = 'Message must be 5000 characters or fewer.';
  }

  if (!$contactErrors) {
    // Rate-limit success
    rate_limit_increment('contact');

    $to = 'info@bestlifematrimony.com';
    $subject = 'New contact enquiry from BestLife Matrimony website';
    $body = "Name: $contactName\nEmail: $contactEmail\nPhone: $contactPhone\n\nMessage:\n$contactMessage\n";
    $headers = "From: $contactName <$contactEmail>\r\nReply-To: $contactEmail\r\n";

    if (@mail($to, $subject, $body, $headers)) {
      $sentViaMail = true;
      $contactSuccess = true;
    } else {
      // Demo fallback: if mail() is unavailable, still acknowledge
      $sentViaMail = false;
      $contactSuccess = true;
    }
    rate_limit_reset('contact');
  }
}

$pageTitle = 'Contact — BestLife Matrimony';
$pageDescription = 'Get in touch with the BestLife Matrimony team.';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="flex-1 bg-[#f4f2ee]">
  <?php if ($isApprovalRequest): ?>
  <section class="mx-auto w-full max-w-6xl px-4 pt-8 sm:px-6">
    <div class="rounded-none border border-[#e3c877] bg-[#fff9e6] p-5 sm:p-6 shadow-sm" style="border-left: 4px solid #dcb04a;">
      <div class="flex items-start gap-3">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#b8860b" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 mt-0.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/></svg>
        <div>
          <h3 class="font-serif text-lg font-bold text-[#6b4f00]" style="margin:0 0 6px;">Profile Approval Required</h3>
          <p class="text-sm text-[#7a6520]" style="margin:0 0 8px; line-height:1.6;">Your profile is <strong>pending admin approval</strong>. To view other profiles and start connecting with matches, please contact our team using the form below. We will review your profile and approve it as soon as possible.</p>
          <ul class="text-sm text-[#7a6520]" style="margin:0; padding-left:18px; line-height:1.8;">
            <li>✅ You <strong>can</strong> browse matches and add favourites</li>
            <li>✅ You <strong>can</strong> edit and complete your profile</li>
            <li>🔒 Profile viewing and messaging require admin approval</li>
          </ul>
          <?php if (!empty($_SESSION['user_id']) && ($rr = (current_user()['rejected_reason'] ?? null))): ?>
            <div class="mt-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">
              <strong>Rejected:</strong> <?php echo htmlspecialchars($rr); ?><br>
              Please fix your profile and request again below.
            </div>
            <form method="post" class="mt-3">
              <?php csrf_field(); ?>
              <input type="hidden" name="request_approval" value="1">
              <button type="submit" class="inline-flex h-9 items-center justify-center rounded-full bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-6 text-sm font-bold text-[#3a0c15]">Request Approval Again</button>
            </form>
          <?php elseif (!empty($_SESSION['user_id'])): ?>
            <form method="post" class="mt-3">
              <?php csrf_field(); ?>
              <input type="hidden" name="request_approval" value="1">
              <button type="submit" class="inline-flex h-9 items-center justify-center rounded-full bg-white border border-[#e3c877] px-6 text-sm font-bold text-[#6b4f00]">Request Approval</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>
  <?php endif; ?>
  <section class="mx-auto w-full max-w-6xl px-4 py-16 sm:px-6 sm:py-24">
    <div class="grid lg:grid-cols-2 gap-8">
      <div class="reveal max-w-3xl rounded-none border border-[#f6e6b4]/20 bg-[#6b1020] p-8 sm:p-12 shadow-2xl">
        <h1 class="font-serif text-4xl font-bold tracking-tight text-[#fff6e8] sm:text-5xl">Contact us</h1>
        <p class="mt-6 text-lg text-[#f3e6d8]/90">Get in touch with the BestLife Matrimony team. We usually respond within 24 hours.</p>
        <ul class="mt-6 space-y-3 text-sm text-[#fff6e8]/80">
          <li class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-[#e3c877]"></span> +91 98765 43210</li>
          <li class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-[#e3c877]"></span> info@bestlifematrimony.com</li>
          <li class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-[#e3c877]"></span> Chennai, Tamil Nadu, India</li>
        </ul>
        <a href="./index.php" class="mt-8 inline-flex h-11 items-center justify-center rounded-full border border-[#f6e6b4]/50 bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 font-semibold text-[#3a0c15] shadow-lg hover:scale-105 transition-transform">Back to Home</a>
      </div>
      <!-- Contact Form — preserves fields/labels/validation parity -->
      <form class="reveal reveal-delay-1 rounded-none border border-[#f6e6b4]/20 bg-white p-6 sm:p-8 shadow-xl text-[#2b1a1e]" id="contactForm" method="post" action="./contact_submit.php" novalidate>
        <h2 class="font-serif text-xl font-bold">Send us a message</h2>
        <?php csrf_field(); ?>
        <?php if (!empty($contactErrors['auth'])) echo '<div class="mt-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">'.$contactErrors['auth'].'</div>'; ?>
        <?php if ($contactSuccess): ?>
          <div class="mt-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">Thank you, <?php echo htmlspecialchars($contactName); ?>! <?php echo $sentViaMail ? 'Your message has been sent. We\'ll get back to you soon.' : 'Your message has been received. We\'ll get back to you soon.'; ?></div>
        <?php endif; ?>
        <div id="contactResult"></div>
        <div class="mt-6 grid gap-4">
          <label class="text-sm font-medium">Full Name
            <input type="text" name="name" maxlength="150" value="<?php echo htmlspecialchars($contactName); ?>" required placeholder="Your full name" class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($contactErrors['name'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" />
            <?php if (isset($contactErrors['name'])) echo '<span class="text-xs text-red-600">'.$contactErrors['name'].'</span>'; ?>
          </label>
          <label class="text-sm font-medium">Email
            <input type="email" name="email" maxlength="191" value="<?php echo htmlspecialchars($contactEmail); ?>" required placeholder="you@example.com" class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($contactErrors['email'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" />
            <?php if (isset($contactErrors['email'])) echo '<span class="text-xs text-red-600">'.$contactErrors['email'].'</span>'; ?>
          </label>
          <label class="text-sm font-medium">Phone No
            <input type="tel" name="phone" maxlength="30" value="<?php echo htmlspecialchars($contactPhone); ?>" required placeholder="+91 98765 43210" class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($contactErrors['phone'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>" />
            <?php if (isset($contactErrors['phone'])) echo '<span class="text-xs text-red-600">'.$contactErrors['phone'].'</span>'; ?>
          </label>
          <label class="text-sm font-medium">Message
            <textarea name="message" rows="4" maxlength="5000" required placeholder="How can we help?" class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#e3c877] <?php echo isset($contactErrors['message'])?'border-red-300 bg-red-50':'border-[#e8d9b5] bg-[#fdf9f1]'; ?>"><?php echo htmlspecialchars($contactMessage); ?></textarea>
            <?php if (isset($contactErrors['message'])) echo '<span class="text-xs text-red-600">'.$contactErrors['message'].'</span>'; ?>
          </label>
          <button type="submit" class="inline-flex h-11 items-center justify-center rounded-full bg-gradient-to-r from-[#dcb04a] via-[#e3c877] to-[#dcb04a] px-8 text-sm font-bold text-[#3a0c15] hover:brightness-110 transition-all">Send Message</button>
        </div>
      </form>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script>
(function(){
  var form = document.getElementById('contactForm');
  if(!form) return;
  form.addEventListener('submit', function(e){
    e.preventDefault();
    if(form.dataset.busy) return;
    var btn = form.querySelector('button[type=submit]');
    var result = document.getElementById('contactResult');
    result.innerHTML = '';
    var csrf = (document.querySelector('meta[name="csrf-token"]')||{}).content||'';
    var payload = {
      name: form.name.value, email: form.email.value,
      phone: form.phone.value, message: form.message.value,
      format: 'json'
    };
    form.dataset.busy = '1';
    var original = btn.textContent;
    btn.disabled = true; btn.textContent = 'Sending…';
    fetch('./contact_submit.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-Token':csrf},
      body: JSON.stringify(payload)
    }).then(function(r){ return r.json(); }).then(function(data){
      if(data.ok){
        result.innerHTML = '<div class="mt-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">'+data.message+'</div>';
        form.reset();
      } else {
        var html = '<div class="mt-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">';
        for(var k in data.errors){ html += '<p class="mb-1">'+data.errors[k]+'</p>'; }
        html += '</div>';
        result.innerHTML = html;
      }
    }).catch(function(){ result.innerHTML = '<div class="mt-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">Something went wrong. Please try again.</div>'; })
      .finally(function(){ btn.disabled = false; btn.textContent = original; delete form.dataset.busy; });
  });
})();
</script>
<?php require_once __DIR__ . '/includes/scripts.php'; ?>
