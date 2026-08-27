<?php
require_once __DIR__ . '/includes/config.php';

$adErrors = [];
$adSuccess = false;
$adName = $adEmail = $adCompany = $adMessage = '';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
  if (!csrf_verify()) {
    $adErrors['auth'] = 'Invalid request. Please try again.';
  }
  if (!$adErrors && !rate_limit_check('advertise', 5, 300)) {
    $adErrors['auth'] = 'Too many enquiries. Please wait a few minutes.';
  }

  $adName = trim($_POST['name'] ?? '');
  $adEmail = trim($_POST['email'] ?? '');
  $adCompany = trim($_POST['company'] ?? '');
  $adMessage = trim($_POST['message'] ?? '');

  if (!$adErrors) {
    if ($adName === '') $adErrors['name'] = 'Name is required.';
    if (strlen($adName) > 150) $adErrors['name'] = 'Name must be 150 characters or fewer.';
    if (!filter_var($adEmail, FILTER_VALIDATE_EMAIL)) $adErrors['email'] = 'Valid email required.';
    if (strlen($adEmail) > 191) $adErrors['email'] = 'Email too long.';
    if (strlen($adCompany) > 150) $adErrors['company'] = 'Company must be 150 characters or fewer.';
    if ($adMessage === '') $adErrors['message'] = 'Message is required.';
    if (strlen($adMessage) > 5000) $adErrors['message'] = 'Message must be 5000 characters or fewer.';
  }

  if (!$adErrors) {
    rate_limit_increment('advertise');
    $subject = 'Advertising enquiry from BestLife Matrimony';
    $body = "Name: $adName\nEmail: $adEmail\nCompany: $adCompany\n\nMessage:\n$adMessage\n";
    $headers = "From: $adName <$adEmail>\r\nReply-To: $adEmail\r\n";
    @mail('info@bestlifematrimony.com', $subject, $body, $headers);
    $adSuccess = true;
    rate_limit_reset('advertise');
  }
}

$adPageTitle = 'Advertise With Us — BestLife Matrimony';
$pageDescription = 'Advertise where trust is earned. Brand-safe, moderated, verified audience.';
$pageHeadExtra = <<<HTML
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link href="./assets/css/advertise.css" rel="stylesheet">
HTML;
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<main class="flex-1 bg-[#f4f2ee] text-[#111]">
  <section class="mx-auto max-w-[900px] px-4 py-12 text-center">
    <h1 class="mt-3 text-[36px] font-bold leading-[1.05] tracking-[-.02em] text-[#111]">Advertise<br>where trust is<br>earned.</h1>
    <p class="mx-auto mt-3 max-w-[560px] text-[15px] leading-6 text-[#666]">Brand-safe, moderated, verified audience. No clutter. No dark patterns. Just families making one of life's most important decisions — and the brands they invite in.</p>
    <div class="mt-5 flex flex-wrap justify-center gap-2">
      <a href="#enquire" class="inline-flex items-center gap-2 bg-[#6b1020] px-5 py-3 text-sm font-semibold text-white hover:bg-[#e3c877] hover:text-[#3a0c15]">Enquire <i class="bi bi-arrow-right"></i></a>
      <a href="./contact.php" class="inline-flex items-center gap-2 border border-[#ddd] bg-white px-5 py-3 text-sm font-semibold text-[#111] hover:bg-[#fafafa]"><i class="bi bi-file-earmark-text"></i> Brand Guidelines PDF</a>
    </div>
    <p class="mt-3 text-xs text-[#8a7a6a]">Reply in 4 hours • Media kit • Rate card on request</p>
  </section>

  <section class="mx-auto grid max-w-[900px] grid-cols-1 gap-3.5 px-4 pb-6 md:grid-cols-3" aria-label="Trust pillars">
    <div class="border border-[#eee] bg-white px-4 py-6 text-center">
      <i class="bi bi-shield-lock text-[22px] text-[#111]"></i>
      <p class="mt-2 text-sm font-bold text-[#111]">Brand Safe</p>
      <p class="mt-1 text-xs leading-5 text-[#666]">Human moderated, verified profiles only</p>
    </div>
    <div class="border border-[#eee] bg-white px-4 py-6 text-center">
      <i class="bi bi-eye-slash text-[22px] text-[#111]"></i>
      <p class="mt-2 text-sm font-bold text-[#111]">No Tracking Tricks</p>
      <p class="mt-1 text-xs leading-5 text-[#666]">Consent-first, pixel optional</p>
    </div>
    <div class="border border-[#eee] bg-white px-4 py-6 text-center">
      <i class="bi bi-receipt text-[22px] text-[#111]"></i>
      <p class="mt-2 text-sm font-bold text-[#111]">Transparent</p>
      <p class="mt-1 text-xs leading-5 text-[#666]">Clear specs, invoices, reports</p>
    </div>
  </section>

  <section class="mx-auto max-w-[900px] px-4 pb-10 text-center">
    <p class="text-xs text-[#8a7a6a]">Trusted by 40+ partners • Jewellery, venues, hospitality</p>
    <div class="mt-3 flex justify-center gap-4 text-[22px] text-[#111] opacity-50">
      <i class="bi bi-gem" aria-hidden="true"></i>
      <i class="bi bi-buildings" aria-hidden="true"></i>
      <i class="bi bi-camera" aria-hidden="true"></i>
      <i class="bi bi-airplane" aria-hidden="true"></i>
    </div>
  </section>
  <section id="enquire" class="mx-auto max-w-[900px] px-4 pb-10 pt-2">
    <div class="border border-[#eee] bg-white p-6 sm:p-8">
      <h2 class="text-xl font-bold text-[#111]">Enquire about advertising</h2>
      <p class="mt-1 text-sm text-[#666]">Tell us about your brand and we'll send the media kit and rate card within 4 hours.</p>
      <?php if ($adSuccess): ?>
        <div class="mt-4 rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">Thank you, <?php echo htmlspecialchars($adName); ?>! Your enquiry has been received. We'll get back to you shortly.</div>
      <?php endif; ?>
      <?php if (!empty($adErrors['auth'])) echo '<div class="mt-4 rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">'.$adErrors['auth'].'</div>'; ?>
      <form method="post" action="./advertise_submit.php#enquire" id="adForm" novalidate class="mt-6 grid gap-4 sm:grid-cols-2">
        <?php csrf_field(); ?>
        <div id="adResult" class="sm:col-span-2"></div>
        <label class="text-sm font-medium text-[#111]">Your Name
          <input type="text" name="name" maxlength="150" value="<?php echo htmlspecialchars($adName); ?>" required class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#111] <?php echo isset($adErrors['name'])?'border-red-300 bg-red-50':'border-[#ddd]'; ?>" />
          <?php if (isset($adErrors['name'])) echo '<span class="text-xs text-red-600">'.$adErrors['name'].'</span>'; ?>
        </label>
        <label class="text-sm font-medium text-[#111]">Work Email
          <input type="email" name="email" maxlength="191" value="<?php echo htmlspecialchars($adEmail); ?>" required class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#111] <?php echo isset($adErrors['email'])?'border-red-300 bg-red-50':'border-[#ddd]'; ?>" />
          <?php if (isset($adErrors['email'])) echo '<span class="text-xs text-red-600">'.$adErrors['email'].'</span>'; ?>
        </label>
        <label class="text-sm font-medium text-[#111] sm:col-span-2">Company / Brand
          <input type="text" name="company" maxlength="150" value="<?php echo htmlspecialchars($adCompany); ?>" class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#111] <?php echo isset($adErrors['company'])?'border-red-300 bg-red-50':'border-[#ddd]'; ?>" />
          <?php if (isset($adErrors['company'])) echo '<span class="text-xs text-red-600">'.$adErrors['company'].'</span>'; ?>
        </label>
        <label class="text-sm font-medium text-[#111] sm:col-span-2">What would you like to promote?
          <textarea name="message" rows="4" maxlength="5000" required class="mt-1 w-full rounded-xl border px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#111] <?php echo isset($adErrors['message'])?'border-red-300 bg-red-50':'border-[#ddd]'; ?>"><?php echo htmlspecialchars($adMessage); ?></textarea>
          <?php if (isset($adErrors['message'])) echo '<span class="text-xs text-red-600">'.$adErrors['message'].'</span>'; ?>
        </label>
        <div class="sm:col-span-2">
          <button type="submit" class="inline-flex h-11 items-center justify-center rounded bg-[#6b1020] px-8 text-sm font-bold text-white hover:bg-[#e3c877] hover:text-[#3a0c15]">Send Enquiry</button>
        </div>
      </form>
    </div>
  </section>
</main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
<script>
(function(){
  var form = document.getElementById('adForm');
  if(!form) return;
  form.addEventListener('submit', function(e){
    e.preventDefault();
    if(form.dataset.busy) return;
    var btn = form.querySelector('button[type=submit]');
    var result = document.getElementById('adResult');
    result.innerHTML = '';
    var csrf = (document.querySelector('meta[name="csrf-token"]')||{}).content||'';
    var payload = {
      name: form.name.value, email: form.email.value,
      company: form.company.value, message: form.message.value,
      format: 'json'
    };
    form.dataset.busy = '1';
    var original = btn.textContent;
    btn.disabled = true; btn.textContent = 'Sending…';
    fetch('./advertise_submit.php', {
      method: 'POST',
      headers: {'Content-Type':'application/json','X-CSRF-Token':csrf},
      body: JSON.stringify(payload)
    }).then(function(r){ return r.json(); }).then(function(data){
      if(data.ok){
        result.innerHTML = '<div class="rounded-xl bg-emerald-50 border border-emerald-200 px-4 py-3 text-sm text-emerald-800">'+data.message+'</div>';
        form.reset();
      } else {
        var html = '<div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">';
        for(var k in data.errors){ html += '<p class="mb-1">'+data.errors[k]+'</p>'; }
        html += '</div>';
        result.innerHTML = html;
      }
    }).catch(function(){ result.innerHTML = '<div class="rounded-xl bg-red-50 border border-red-200 px-4 py-3 text-sm text-red-700">Something went wrong. Please try again.</div>'; })
      .finally(function(){ btn.disabled = false; btn.textContent = original; delete form.dataset.busy; });
  });
})();
</script>
<?php require_once __DIR__ . '/includes/scripts.php'; ?>
