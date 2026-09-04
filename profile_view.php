<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();
if (is_admin()) {
  // Admins view via admin panel, not public profile view
  header('Location: ./admin/index.php');
  exit;
}

$profileId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$currentUserId = $_SESSION['user_id'] ?? null;

if (!$profileId) {
  header('Location: ./matches.php');
  exit;
}

// Unapproved/unverified users can view their own profile but not others (uses can_interact)
if ($currentUserId && (int)$currentUserId !== $profileId && !is_admin() && !can_interact()) {
  header('Location: ./contact.php?reason=approval');
  exit;
}

$profile = null;
$viewCount = 0;
try {
  $db = getDB();

  // If the viewing user is blocked (either direction), show a blocked notice
  if ($currentUserId && $currentUserId !== $profileId && is_blocked($currentUserId, $profileId)) {
    $profile = null;
    $profile = ['__blocked__' => true];
  } else {
    $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$profileId]);
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($profile) {
      // Record a view from a distinct logged-in viewer (not the owner)
      if ($currentUserId && $currentUserId !== $profileId) {
        $isNewView = record_profile_view($currentUserId, $profileId);
        log_activity((int) $currentUserId, 'profile_view', 'user', $profileId, 'Viewed profile');
        if ($isNewView) {
          try {
            $vn = $db->prepare('SELECT full_name FROM users WHERE id = ?');
            $vn->execute([$currentUserId]);
            $viewerName = $vn->fetchColumn() ?: 'Someone';
            notification_add($profileId, 'view', $viewerName . ' viewed your profile.');
          } catch (Exception $e) { /* ignore */ }
        }
      }
      // Total unique count of viewers
      $cnt = $db->prepare('SELECT COUNT(DISTINCT viewer_id) FROM profile_views WHERE profile_id = ?');
      $cnt->execute([$profileId]);
      $viewCount = (int) $cnt->fetchColumn();
    }
  }
} catch (PDOException $e) { /* ignore */ }

if (isset($profile['__blocked__'])) {
  $pageTitle = 'Account Blocked — BestLife Matrimony';
  $pageDescription = 'This profile is not available.';
  require_once __DIR__ . '/includes/header.php';
  require_once __DIR__ . '/includes/navbar.php';
  echo '<main class="bg-[#f4f2ee]" style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:40px 16px;"><div style="background:#fff;border:1px solid #ddd;padding:40px;max-width:420px;text-align:center;font-family:Inter,system-ui,sans-serif;color:#1a1a1a;"><i class="bi bi-shield-lock" style="font-size:40px;color:#6b1020;"></i><h1 style="margin:12px 0 8px;font-size:20px;">Profile Unavailable</h1><p style="color:#666;font-size:14px;">This profile cannot be viewed at this time.</p><a href="./matches.php" class="pv-btn" style="margin-top:16px;display:inline-flex;height:40px;padding:0 20px;align-items:center;border:1px solid #ddd;background:#fff;"><i class="bi bi-arrow-left"></i> Back to Matches</a></div></main>';
  require_once __DIR__ . '/includes/footer.php';
  require_once __DIR__ . '/includes/scripts.php';
  exit;
}

if (!$profile) {
  header('Location: ./matches.php');
  exit;
}

function v($p, $key) {
  $val = $p[$key] ?? '';
  if ($val === '' || $val === null) return null;
  return htmlspecialchars((string)$val);
}

/* Build a small fact table of available/interesting fields */
$facts = [];
$basicRows = [
  'Age' => v($profile, 'date_of_birth') ? (new DateTime($profile['date_of_birth']))->diff(new DateTime())->y . ' years' : null,
  'Gender' => v($profile, 'gender'),
  'Marital Status' => v($profile, 'marital_status'),
  'Height' => v($profile, 'height'),
  'Weight' => v($profile, 'weight') ? v($profile, 'weight') . ' kg' : null,
  'Body Type' => v($profile, 'body_type'),
  'Complexion' => v($profile, 'complexion'),
  'Blood Group' => v($profile, 'blood_group'),
];
$religiousRows = [
  'Religion' => v($profile, 'religion'),
  'Caste' => v($profile, 'caste'),
  'Sub Caste' => v($profile, 'sub_caste'),
  'Gothram' => v($profile, 'gothram'),
  'Star Sign' => v($profile, 'star_sign'),
  'Zodiac' => v($profile, 'zodiac'),
  'Dosham' => v($profile, 'dosham'),
  'Mother Tongue' => v($profile, 'mother_tongue'),
  'Time of Birth' => v($profile, 'time_of_birth'),
  'Place of Birth' => v($profile, 'place_of_birth'),
  'Rashi' => v($profile, 'rashi'),
];
$locationRows = [
  'Country' => v($profile, 'country'),
  'State' => v($profile, 'state'),
  'City' => v($profile, 'city'),
  'Citizenship' => v($profile, 'citizenship'),
];
$careerRows = [
  'Education' => v($profile, 'highest_education'),
  'Details' => v($profile, 'education_detail'),
  'Occupation' => v($profile, 'occupation'),
  'Occupation Type' => v($profile, 'occupation_type'),
  'Annual Income' => v($profile, 'annual_income'),
];
$familyRows = [
  'Family Type' => v($profile, 'family_type'),
  'Family Status' => v($profile, 'family_status'),
  'Family Values' => v($profile, 'family_values'),
  'Father' => v($profile, 'father_name'),
  'Mother' => v($profile, 'mother_name'),
];
$lifestyleRows = [
  'Diet' => v($profile, 'diet'),
  'Smoking' => v($profile, 'smoking'),
  'Drinking' => v($profile, 'drinking'),
];

$hasFacts = false;
foreach (array_merge($basicRows, $religiousRows, $locationRows, $careerRows, $familyRows, $lifestyleRows) as $k => $val) {
  if ($val !== null) { $hasFacts = true; break; }
}

$isOwner = ($currentUserId && $currentUserId === $profileId);

$pageTitle = 'View Profile — BestLife Matrimony';
$pageDescription = 'View a member profile on BestLife Matrimony.';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

function renderRow($label, $value) {
  if ($value === null) return '';
  return '<div class="pv-row"><span class="pv-label">' . htmlspecialchars($label) . '</span><span class="pv-value">' . $value . '</span></div>';
}
function renderSection($title, $rows) {
  $html = '';
  foreach ($rows as $k => $v) { if ($v !== null) $html .= renderRow($k, $v); }
  if (!$html) return '';
  return '<div class="pv-section"><h3>' . htmlspecialchars($title) . '</h3>' . $html . '</div>';
}
?>
<style>
  .pv-wrap { max-width: 860px; margin: 0 auto; padding: 24px 16px 48px; font-family: Inter, system-ui, sans-serif; color: #1a1a1a; }
  .pv-card { background: #fff; border: 1px solid #ddd; }
  .pv-head { display: flex; gap: 18px; align-items: center; padding: 20px; border-bottom: 1px solid #eee; flex-wrap: wrap; }
  .pv-photo { width: 92px; height: 92px; border-radius: 8px; overflow: hidden; background: #f0ece6; flex-shrink: 0; display:flex; align-items:center; justify-content:center; color:#999; position: relative; }
  .pv-photo img { width: 100%; height: 100%; object-fit: cover; user-select: none; -webkit-user-drag: none; }
  .pv-photo:has(img)::after { content: ""; position: absolute; right: 4px; bottom: 4px; width: 56px; height: 14px; background-image: url('/assets/images/logo.png'); background-size: contain; background-repeat: no-repeat; background-position: right bottom; opacity: 0.68; pointer-events: none; z-index: 3; filter: drop-shadow(0 1px 2px rgba(0,0,0,0.45)); }
  .pv-photo i { font-size: 40px; }
  .pv-name { font-size: 22px; font-weight: 700; margin: 0; }
  .pv-tagline { font-size: 13px; color: #666; margin: 4px 0 0; }
  .pv-about { padding: 18px 20px; border-bottom: 1px solid #eee; }
  .pv-about h3 { font-size: 14px; margin: 0 0 6px; }
  .pv-about p { margin: 0; font-size: 13px; color: #444; line-height: 1.6; }
  .pv-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 16px; padding: 18px 20px; }
  .pv-section h3 { font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; color: #6b1020; margin: 0 0 8px; border-bottom: 1px solid #eee; padding-bottom: 6px; }
  .pv-row { display: flex; justify-content: space-between; gap: 12px; padding: 5px 0; font-size: 13px; border-bottom: 1px dashed #f0ece6; }
  .pv-row:last-child { border-bottom: none; }
  .pv-label { color: #888; }
  .pv-value { font-weight: 500; text-align: right; }
  .pv-actions { padding: 16px 20px 20px; display: flex; gap: 10px; border-top: 1px solid #eee; flex-wrap: wrap; }
  .pv-btn { height: 40px; padding: 0 20px; font-size: 13px; border-radius: 0; display: inline-flex; align-items: center; gap: 6px; cursor: pointer; border: 1px solid #ddd; background: #fff; }
  .pv-btn-primary { background: #6b1020; color: #fff; border-color: #6b1020; }
  .pv-empty { text-align: center; color: #888; font-size: 13px; padding: 24px; }
  .pv-photo:has(img) { cursor: zoom-in; }
  /* Lightbox — full image on click, same-page modal */
  .pv-lightbox[hidden] { display: none !important; }
  .pv-lightbox { position: fixed; inset: 0; display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 16px; }
  .pv-lightbox-backdrop { position: absolute; inset: 0; background: rgba(0,0,0,0.78); backdrop-filter: blur(4px); }
  .pv-lightbox-content { position: relative; max-width: min(92vw, 720px); max-height: 88vh; background: #fff; padding: 8px; border-radius: 10px; box-shadow: 0 20px 60px rgba(0,0,0,0.4); display: flex; flex-direction: column; align-items: center; }
  .pv-lightbox-photo { position: relative; display: flex; align-items: center; justify-content: center; max-width: 100%; max-height: 80vh; overflow: hidden; background: #f0ece6; border-radius: 8px; }
  .pv-lightbox-photo img { max-width: 100%; max-height: 80vh; width: auto; height: auto; object-fit: contain; display: block; user-select: none; -webkit-user-drag: none; }
  .pv-lightbox-photo:has(img)::after { content: ""; position: absolute; right: 8px; bottom: 8px; width: min(36%, 140px); height: 20px; background-image: url('/assets/images/logo.png'); background-size: contain; background-repeat: no-repeat; background-position: right bottom; opacity: 0.62; pointer-events: none; z-index: 3; filter: drop-shadow(0 1px 3px rgba(0,0,0,0.45)); }
  .pv-lightbox-close { position: absolute; top: -12px; right: -12px; width: 36px; height: 36px; border: none; border-radius: 50%; background: #1a1a1a; color: #fff; font-size: 22px; line-height: 1; cursor: pointer; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 4; }
  .pv-lightbox-close:hover { background: #000; }
  .pv-lightbox-caption { margin-top: 8px; font-size: 13px; font-weight: 600; color: #333; text-align: center; }
</style>
<main class="bg-[#f4f2ee]" style="min-height:100vh;">
  <div class="pv-wrap">
    <?php if ($isOwner): ?>
      <div style="background:#dcb04a;color:#3a0c15;padding:10px 14px;font-size:13px;margin-bottom:14px;">
        <i class="bi bi-person-badge"></i> This is your profile — <a href="./profile.php" style="color:inherit;font-weight:700;">edit it here</a>.
      </div>
    <?php endif; ?>
    <div class="pv-card">
      <div class="pv-head">
        <div class="pv-photo">
          <?php if (!empty($profile['profile_photo'])): ?>
            <img src="<?php echo htmlspecialchars(photo_url($profile['profile_photo'])); ?>" alt="Profile photo" draggable="false" oncontextmenu="return false" ondragstart="return false">
          <?php else: ?>
            <i class="bi bi-person-circle"></i>
          <?php endif; ?>
        </div>
        <div>
          <h1 class="pv-name">
            <?php echo v($profile, 'full_name') ?: 'Member'; ?>
            <?php
              $isFullVerified = (int)($profile['email_verified'] ?? 0) === 1 && (int)($profile['phone_verified'] ?? 0) === 1;
              $isIdVerified = (int)($profile['id_verified'] ?? 0) === 1;
              if ($isIdVerified):
            ?>
              <span title="ID Verified" style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;color:#15803d;background:#e7f6ec;border:1px solid #bef0cd;padding:2px 8px;border-radius:999px;vertical-align:middle;"><i class="bi bi-patch-check-fill"></i> ID Verified</span>
            <?php elseif ($isFullVerified): ?>
              <span title="Verified" style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:700;color:#15803d;background:#e7f6ec;border:1px solid #bef0cd;padding:2px 8px;border-radius:999px;vertical-align:middle;"><i class="bi bi-patch-check-fill"></i> Verified</span>
            <?php endif; ?>
          </h1>
          <p class="pv-tagline">
            <?php
              $chips = [];
              if (v($profile, 'city')) $chips[] = v($profile, 'city');
              if (v($profile, 'occupation')) $chips[] = v($profile, 'occupation');
              if (v($profile, 'highest_education')) $chips[] = v($profile, 'highest_education');
              echo htmlspecialchars(implode(' · ', $chips));
            ?>
          </p>
          <p class="pv-tagline"><i class="bi bi-eye"></i> <?php echo $viewCount; ?> people viewed this profile<?php if ($isOwner): ?> · <a href="./who_viewed_me.php" target="_blank" rel="noopener noreferrer" style="color:#6b1020;font-weight:600;">Who viewed me</a><?php endif; ?></p>
        </div>
      </div>

      <?php if (v($profile, 'about_self')): ?>
        <div class="pv-about">
          <h3>About <?php echo v($profile, 'full_name') ?: 'Me'; ?></h3>
          <p><?php echo nl2br(v($profile, 'about_self')); ?></p>
        </div>
      <?php endif; ?>

      <?php if ($hasFacts): ?>
        <div class="pv-grid">
          <?php
            echo renderSection('Basic', $basicRows);
            echo renderSection('Religious & Cultural', $religiousRows);
            echo renderSection('Location', $locationRows);
            echo renderSection('Education & Career', $careerRows);
            echo renderSection('Family', $familyRows);
            echo renderSection('Lifestyle', $lifestyleRows);
          ?>
        </div>
      <?php else: ?>
        <div class="pv-empty">This member hasn't added their profile details yet.</div>
      <?php endif; ?>

      <?php if (!empty($profile['kattam_image'])): ?>
        <div class="pv-about">
          <h3>Kattam (Birth Chart)</h3>
          <div style="margin-top:8px; max-width:420px;">
            <img src="<?php echo htmlspecialchars(photo_url($profile['kattam_image'])); ?>" alt="Kattam" style="width:100%;height:auto;border:1px solid #eee;border-radius:8px;cursor:zoom-in;user-select:none;-webkit-user-drag:none;" draggable="false" oncontextmenu="return false" ondragstart="return false" onclick="window.open(this.src,'_blank')">
          </div>
        </div>
      <?php endif; ?>
    </div>

    <div class="pv-actions" style="padding:0;margin-top:16px;border:none;">
      <a href="./matches.php" class="pv-btn"><i class="bi bi-arrow-left"></i> Back to Matches</a>
      <?php if (!$isOwner): ?>
        <?php if ($currentUserId): ?>
          <a href="./messages.php?user=<?php echo $profileId; ?>" class="pv-btn pv-btn-primary"><i class="bi bi-chat-dots"></i> Send Message</a>
          <button type="button" class="pv-btn" id="shortlistBtn" data-id="<?php echo $profileId; ?>"><i class="bi bi-bookmark"></i> <span id="shortlistLabel">Shortlist</span></button>
          <button type="button" class="pv-btn" id="interestBtn" data-id="<?php echo $profileId; ?>"><i class="bi bi-suit-heart"></i> <span id="interestLabel">Express Interest</span></button>
          <button type="button" class="pv-btn" id="reportBtn" data-id="<?php echo $profileId; ?>"><i class="bi bi-flag"></i> Report</button>
          <button type="button" class="pv-btn" id="blockBtn" data-id="<?php echo $profileId; ?>" style="color:#b91c1c;"><i class="bi bi-slash-circle"></i> Block</button>
        <?php else: ?>
          <a href="./login.php" class="pv-btn pv-btn-primary"><i class="bi bi-chat-dots"></i> Login to Message</a>
        <?php endif; ?>
      <?php endif; ?>
    </div>

    <?php if (!$isOwner && $currentUserId): ?>
      <div class="pv-chip" id="interestStatus" style="margin-top:12px;display:none;"></div>
    <?php endif; ?>
  </div>
</main>

<!-- Full-image lightbox (same-page, triggered by clicking .pv-photo) -->
<div id="pvLightbox" class="pv-lightbox" hidden aria-hidden="true" role="dialog" aria-label="Profile photo">
  <div class="pv-lightbox-backdrop" data-close-lightbox></div>
  <div class="pv-lightbox-content">
    <button type="button" class="pv-lightbox-close" data-close-lightbox aria-label="Close">&times;</button>
    <div class="pv-lightbox-photo">
      <img id="pvLightboxImg" src="" alt="Full profile photo" draggable="false" oncontextmenu="return false" ondragstart="return false">
    </div>
    <div class="pv-lightbox-caption" id="pvLightboxCaption"></div>
  </div>
</div>

<script>
(function () {
  var photoEl = document.querySelector('.pv-photo img');
  var box = document.querySelector('.pv-photo');
  var lb = document.getElementById('pvLightbox');
  var lbImg = document.getElementById('pvLightboxImg');
  var lbCap = document.getElementById('pvLightboxCaption');
  if (!photoEl || !box || !lb || !lbImg) return;
  var nameEl = document.querySelector('.pv-name');
  var nameText = nameEl ? nameEl.textContent.trim().split('\n')[0].trim() : '';
  function openLb() {
    var src = photoEl.getAttribute('src');
    if (!src) return;
    lbImg.src = src;
    if (lbCap) lbCap.textContent = nameText || 'Profile photo';
    lb.hidden = false;
    lb.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }
  function closeLb() {
    lb.hidden = true;
    lb.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
    // keep src to avoid flicker, or clear: lbImg.removeAttribute('src');
  }
  box.addEventListener('click', openLb);
  photoEl.addEventListener('click', function (e) { e.stopPropagation(); openLb(); });
  lb.addEventListener('click', function (e) {
    if (e.target.hasAttribute('data-close-lightbox') || e.target.closest('[data-close-lightbox]')) closeLb();
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && !lb.hidden) closeLb();
  });
})();
</script>

<?php if (!$isOwner && $currentUserId): ?>
<script>
(function () {
  var profileId = <?php echo $profileId; ?>;
  var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

  // Match score
  var scoreWrap = document.createElement('div');
  scoreWrap.style.cssText = 'max-width:860px;margin:14px auto 0;padding:0 16px;';
  scoreWrap.innerHTML = '<div style="background:#fff;border:1px solid #eee;padding:18px 20px;"><strong style="font-size:14px;">Compatibility with you</strong><div id="matchScoreBox" style="margin-top:10px;color:#666;font-size:13px;">Computing match score…</div></div>';
  var wrap = document.querySelector('.pv-wrap');
  if (wrap) wrap.appendChild(scoreWrap);

  fetch('./match_score_api.php?user_id=' + profileId)
    .then(function (r) { return r.json(); })
    .then(function (d) {
      var box = document.getElementById('matchScoreBox');
      if (!box) return;
      if (d.error) { box.innerHTML = 'Not available.'; return; }
      box.innerHTML = '<div style="margin-bottom:10px;"><span style="font-size:28px;font-weight:800;color:' + d.color + ';">' + d.score + '</span><span style="font-size:14px;color:#666;"> / 100 — ' + d.level + '</span></div>'
        + '<div>' + d.breakdown.map(function (b) {
          return '<div style="display:flex;justify-content:space-between;padding:3px 0;border-bottom:1px dashed #f0ece6;color:' + (b.match ? '#15803d' : '#9a9a9a') + ';"><span>' + b.label + '</span><span>' + b.points + '</span></div>';
        }).join('') + '</div>';
    })
    .catch(function () { var b = document.getElementById('matchScoreBox'); if (b) b.innerHTML = 'Match score unavailable.'; });

  function flashChip(msg) {
    var chip = document.getElementById('interestStatus');
    chip.style.display = 'block';
    chip.style.background = '#fff7e0';
    chip.style.border = '1px solid #e3c877';
    chip.style.color = '#6b4f00';
    chip.style.padding = '10px 14px';
    chip.style.fontSize = '13px';
    chip.innerHTML = msg;
    setTimeout(function () { chip.style.display = 'none'; }, 4000);
  }

  var shortlistBtn = document.getElementById('shortlistBtn');
  var shortlistLabel = document.getElementById('shortlistLabel');
  var interestBtn = document.getElementById('interestBtn');
  var interestLabel = document.getElementById('interestLabel');

  function apiPost(url, body, cb) {
    return fetch(url, { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf }, body: JSON.stringify(body) })
      .then(function (r) { return r.json(); });
  }

  if (shortlistBtn) {
    shortlistBtn.addEventListener('click', function () {
      apiPost('./shortlists_api.php?action=toggle', { profile_id: profileId }).then(function (d) {
        if (d.error) { flashChip(d.error); return; }
        shortlistLabel.textContent = d.active ? 'Shortlisted ✓' : 'Shortlist';
      }).catch(function () { flashChip('Could not update shortlist.'); });
    });
  }

  if (interestBtn) {
    // Load current interest status
    fetch('./interests_api.php?action=status&user_id=' + profileId)
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (d.status === 'accepted') interestLabel.textContent = 'Connected ✓';
        else if (d.status === 'pending') interestLabel.textContent = 'Interest Sent';
        else if (d.status === 'declined') interestLabel.textContent = 'Declined';
      }).catch(function () {});

    interestBtn.addEventListener('click', function () {
      apiPost('./interests_api.php?action=express', { user_id: profileId }).then(function (d) {
        if (d.error) { flashChip(d.error); return; }
        interestLabel.textContent = 'Interest Sent';
        flashChip('Your interest has been sent to ' + (d.name || 'this member') + '.');
      }).catch(function () { flashChip('Could not send interest.'); });
    });
  }

  var reportBtn = document.getElementById('reportBtn');
  if (reportBtn) {
    reportBtn.addEventListener('click', function () {
      var reason = prompt('Please select a reason to report this profile:\n\n1. Fake profile\n2. Inappropriate content\n3. Harassment\n4. Scam / fraud\n5. Other');
      if (!reason) return;
      apiPost('./reports_api.php', { reported_id: profileId, reason: reason }).then(function (d) {
        flashChip(d.error ? d.error : 'Thank you. This profile has been reported and our team will review it.');
      }).catch(function () { flashChip('Could not submit report.'); });
    });
  }

  var blockBtn = document.getElementById('blockBtn');
  if (blockBtn) {
    blockBtn.addEventListener('click', function () {
      if (!window.confirm('Block this member? They will no longer be able to view your profile or message you.')) return;
      apiPost('./blocks_api.php?action=block', { profile_id: profileId }).then(function (d) {
        if (d.error) { flashChip(d.error); return; }
        window.location.reload();
      }).catch(function () { flashChip('Could not block member.'); });
    });
  }
})();
</script>
<?php endif; ?>
<?php
require_once __DIR__ . '/includes/footer.php';
require_once __DIR__ . '/includes/scripts.php';
?>
