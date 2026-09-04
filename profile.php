<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

// Admin users cannot access profile.php - redirect to admin dashboard
if (is_admin()) {
  header('Location: ./admin/index.php');
  exit;
}

$userId = $_SESSION['user_id'];
$errors = [];
$success = false;
$user = null;

/* ── Pre-fetch user data ────────────── */
try {
  $db = getDB();
  $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
  $stmt->execute([$userId]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) { /* ignore */ }

/* ── Handle form POST before any output ─────────── */
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && isset($_POST['save_profile'])) {
  if (!$userId) {
    $errors['auth'] = 'Please log in to save your profile.';
  } elseif (!csrf_verify()) {
    $errors['auth'] = 'Invalid request. Please try again.';
  } else {
    // 1. Text & Enum fields (weight removed from textFields to avoid duplicate column conflict)
    $textFields = [
      'full_name', 'phone', 'looking_for', 'date_of_birth', 'gender', 'height', 'body_type', 'complexion',
      'blood_group', 'marital_status', 'about_self', 'religion', 'caste', 'sub_caste', 'gothram',
      'star_sign', 'zodiac', 'dosham', 'mother_tongue', 'country', 'state', 'city', 'citizenship',
      'residential_status', 'highest_education', 'education_detail', 'occupation',
      'occupation_type', 'annual_income', 'family_type', 'family_status', 'family_values',
      'father_name', 'father_occupation', 'mother_name', 'mother_occupation',
      'family_location', 'diet', 'smoking', 'drinking',
      'pref_education', 'pref_location', 'pref_other', 'pref_height_min', 'pref_height_max'
    ];

    // 2. Integer fields
    $intFields = ['weight', 'brothers', 'brothers_married', 'sisters', 'sisters_married', 'pref_age_min', 'pref_age_max'];

    $setClauses = [];
    $params = [];

    // Input length limits (server-side)
    $lengthLimits = [
      'full_name' => 150, 'phone' => 30, 'date_of_birth' => 10, 'height' => 20,
      'about_self' => 5000, 'religion' => 50, 'caste' => 100, 'sub_caste' => 100,
      'gothram' => 100, 'star_sign' => 50, 'zodiac' => 20, 'dosham' => 20,
      'mother_tongue' => 50, 'country' => 60, 'state' => 100, 'city' => 100,
      'citizenship' => 60, 'residential_status' => 20, 'highest_education' => 50,
      'education_detail' => 255, 'occupation' => 150, 'occupation_type' => 50,
      'annual_income' => 50, 'family_type' => 20, 'family_status' => 30,
      'family_values' => 20, 'father_name' => 150, 'father_occupation' => 150,
      'mother_name' => 150, 'mother_occupation' => 150, 'family_location' => 150,
      'diet' => 30, 'smoking' => 20, 'drinking' => 20,
      'pref_education' => 255, 'pref_location' => 255, 'pref_other' => 5000,
      'pref_height_min' => 20, 'pref_height_max' => 20,
    ];

    foreach ($textFields as $f) {
      $val = isset($_POST[$f]) ? trim($_POST[$f]) : '';
      if ($val !== '' && isset($lengthLimits[$f]) && strlen($val) > $lengthLimits[$f]) {
        $val = substr($val, 0, $lengthLimits[$f]);
      }
      $setClauses[] = "`$f` = ?";
      $params[] = $val === '' ? null : $val;
    }

    foreach ($intFields as $f) {
      $val = isset($_POST[$f]) ? trim($_POST[$f]) : '';
      $setClauses[] = "`$f` = ?";
      $params[] = ($val === '' || !is_numeric($val)) ? null : intval($val);
    }

    // Validate Partner Age Range dropdowns — both must be within 18-50 and min <= max
    $prefAgeMinRaw = isset($_POST['pref_age_min']) ? trim($_POST['pref_age_min']) : '';
    $prefAgeMaxRaw = isset($_POST['pref_age_max']) ? trim($_POST['pref_age_max']) : '';
    $prefAgeMinVal = ($prefAgeMinRaw === '' || !is_numeric($prefAgeMinRaw)) ? null : intval($prefAgeMinRaw);
    $prefAgeMaxVal = ($prefAgeMaxRaw === '' || !is_numeric($prefAgeMaxRaw)) ? null : intval($prefAgeMaxRaw);
    if ($prefAgeMinVal !== null && ($prefAgeMinVal < 18 || $prefAgeMinVal > 50)) {
      $errors['pref_age_min'] = 'Partner min age must be between 18 and 50.';
    }
    if ($prefAgeMaxVal !== null && ($prefAgeMaxVal < 18 || $prefAgeMaxVal > 50)) {
      $errors['pref_age_max'] = 'Partner max age must be between 18 and 50.';
    }
    if ($prefAgeMinVal !== null && $prefAgeMaxVal !== null && $prefAgeMinVal > $prefAgeMaxVal) {
      $errors['pref_age_range'] = 'Partner min age cannot be greater than max age.';
    }
    // Auto-correct out-of-range values in params so DB never stores invalid ages (when no error, clamp is not needed — values already valid)
    if (isset($errors['pref_age_min']) || isset($errors['pref_age_max']) || isset($errors['pref_age_range'])) {
      // keep errors — will block save below
    }

    // Cross-validate looking_for vs gender (configurable via enforce_heterosexual)
    $formLookingFor = $_POST['looking_for'] ?? $user['looking_for'] ?? '';
    $formGender = $_POST['gender'] ?? $user['gender'] ?? '';
    $enforceHetero = !empty($GLOBALS['siteConfig']['enforce_heterosexual']);
    if ($enforceHetero && $formLookingFor !== '' && $formGender !== '' && $formGender !== 'Other') {
      if (($formLookingFor === 'Bride' && $formGender === 'Female') ||
          ($formLookingFor === 'Groom' && $formGender === 'Male')) {
        $errors['looking_for'] = 'Your selection doesn\'t match — looking for a Bride implies you\'re a Groom (Male) and looking for a Groom implies you\'re a Bride (Female).';
      }
    }

    // Guard against duplicate email if email were ever submitted (future-proofing)
    if (empty($errors) && isset($_POST['email']) && trim($_POST['email']) !== '' && strcasecmp(trim($_POST['email']), (string)$user['email']) !== 0) {
      try {
        $dbCheck = getDB();
        $dup = $dbCheck->prepare('SELECT id FROM users WHERE email = ? AND id != ?');
        $dup->execute([trim($_POST['email']), $userId]);
        if ($dup->fetch()) {
          $errors['email'] = 'That email is already in use by another account.';
        }
      } catch (PDOException $e) {
        // ignore, email is not editable in current form
      }
    }

    /* Handle Profile Photo — DB-only storage (no folder, base64 data URI in MEDIUMTEXT) */
    // Track whether profile photo was successfully uploaded (to avoid duplicate SET)
    $profilePhotoUploaded = false;
    $profilePhotoError = null;

    // Profile Photo File Upload (Create/Update) — takes precedence over delete — stores as data URI in DB
    if (!empty($_FILES['profile_photo_file']['tmp_name']) || (!empty($_FILES['profile_photo_file']['error']) && $_FILES['profile_photo_file']['error'] !== UPLOAD_ERR_NO_FILE)) {
      $err = $_FILES['profile_photo_file']['error'] ?? UPLOAD_ERR_NO_FILE;
      if ($err !== UPLOAD_ERR_OK) {
        if ($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE) $profilePhotoError = 'Profile photo too large — server allows ' . ini_get('upload_max_filesize') . ', limit is 5MB.';
        elseif ($err !== UPLOAD_ERR_NO_FILE) $profilePhotoError = 'Profile photo upload failed (error ' . $err . ').';
      } else {
        $tmpPath = $_FILES['profile_photo_file']['tmp_name'];
        $fileSize = $_FILES['profile_photo_file']['size'];
        $ext = strtolower(pathinfo($_FILES['profile_photo_file']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (!in_array($ext, $allowed)) {
          $profilePhotoError = 'Profile photo must be JPG, PNG or WebP.';
        } elseif ($fileSize > 5 * 1024 * 1024) {
          $profilePhotoError = 'Profile photo too large (max 5MB).';
        } elseif (!is_uploaded_file($tmpPath)) {
          $profilePhotoError = 'Invalid upload.';
        } else {
          $finfo = finfo_open(FILEINFO_MIME_TYPE);
          $mime = finfo_file($finfo, $tmpPath);
          finfo_close($finfo);
          if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
            $profilePhotoError = 'Invalid image file (must be JPEG/PNG/WebP).';
          } else {
            $raw = @file_get_contents($tmpPath);
            if ($raw === false || $raw === '') {
              $profilePhotoError = 'Could not read uploaded file.';
            } else {
              // Real watermark: bake project logo into the image so every stored photo carries it
              if (file_exists(__DIR__ . '/includes/watermark.php')) {
                require_once __DIR__ . '/includes/watermark.php';
                if (function_exists('watermark_apply_to_raw')) {
                  [$wmRaw, $wmMime] = watermark_apply_to_raw($raw, $mime);
                  if ($wmRaw !== '' && $wmRaw !== $raw) {
                    $raw = $wmRaw;
                    $mime = $wmMime;
                  }
                }
              }
              // Legacy cleanup: if old photo was a file path, try to delete the file (one-time)
              if (!empty($user['profile_photo']) && strpos($user['profile_photo'], 'data:') !== 0) {
                $oldFile = photo_fs_path($user['profile_photo'], __DIR__);
                if ($oldFile && file_exists($oldFile) && is_file($oldFile)) @unlink($oldFile);
              }
              $b64 = base64_encode($raw);
              $dataUri = 'data:' . $mime . ';base64,' . $b64;
              $setClauses[] = "`profile_photo` = ?";
              $params[] = $dataUri;
              // Log for moderation — file_name is synthetic for DB storage
              log_media_for_moderation($userId, 'profile_photo', 'db:' . $userId . '_' . time() . '.' . $ext, $mime, $fileSize);
              $profilePhotoUploaded = true;
            }
          }
        }
      }
      if ($profilePhotoError) $errors['profile_photo'] = $profilePhotoError;
    }

    // Explicit Delete Profile Photo (only if no new upload succeeded) — DB-only, no folder operation
    if (!$profilePhotoUploaded && !empty($_POST['delete_profile_photo']) && $_POST['delete_profile_photo'] === '1') {
      // Legacy cleanup: delete old file if it was a path
      if (!empty($user['profile_photo']) && strpos($user['profile_photo'], 'data:') !== 0) {
        $oldFile = photo_fs_path($user['profile_photo'], __DIR__);
        if ($oldFile && file_exists($oldFile) && is_file($oldFile)) @unlink($oldFile);
      }
      $setClauses[] = "`profile_photo` = NULL";
    }

    if (empty($errors)) {
      $params[] = $userId;
      try {
        $db = getDB();
        $sql = 'UPDATE users SET ' . implode(', ', $setClauses) . ' WHERE id = ?';
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        log_activity((int) $userId, 'profile_update', 'user', (int) $userId, 'Updated profile');

      // Reload updated user data
      $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
      $stmt->execute([$userId]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!empty($user['full_name'])) {
        $_SESSION['user_name'] = $user['full_name'];
      }

      $success = true;
      } catch (PDOException $e) {
        $errors['db'] = 'Could not save profile. Please try again.';
      }
    }
  }
}

/* ── Helpers ──────────────────────────────────────── */
function pv($user, $key, $default = '') {
  echo htmlspecialchars((string) ($user[$key] ?? $default));
}

function sv($user, $key, $compare) {
  return ((string) ($user[$key] ?? '')) === ((string) $compare) ? ' selected' : '';
}

function cv($user, $key, $compare) {
  return ((string) ($user[$key] ?? '')) === ((string) $compare) ? ' checked' : '';
}

/* ── Profile completion % ────────────────────────── */
// Human-readable labels — single source of truth, avoids hard-coded raw DB keys in UI
$fieldLabels = [
  'full_name' => 'Full Name',
  'date_of_birth' => 'Date of Birth',
  'gender' => 'Gender',
  'marital_status' => 'Marital Status',
  'height' => 'Height',
  'weight' => 'Weight',
  'body_type' => 'Body Type',
  'complexion' => 'Complexion',
  'blood_group' => 'Blood Group',
  'religion' => 'Religion',
  'caste' => 'Caste',
  'sub_caste' => 'Sub-Caste',
  'gothram' => 'Gothram',
  'star_sign' => 'Star / Nakshatra',
  'zodiac' => 'Rasi / Zodiac',
  'dosham' => 'Dosham',
  'mother_tongue' => 'Mother Tongue',
  'country' => 'Country',
  'state' => 'State',
  'city' => 'City',
  'citizenship' => 'Citizenship',
  'highest_education' => 'Highest Education',
  'education_detail' => 'Education Detail',
  'occupation' => 'Occupation',
  'occupation_type' => 'Occupation Type',
  'annual_income' => 'Annual Income',
  'family_type' => 'Family Type',
  'family_status' => 'Family Status',
  'family_values' => 'Family Values',
  'father_name' => "Father's Name",
  'mother_name' => "Mother's Name",
  'diet' => 'Diet',
  'smoking' => 'Smoking',
  'drinking' => 'Drinking',
  'about_self' => 'About Self',
  'pref_age_min' => 'Partner Min Age',
  'pref_age_max' => 'Partner Max Age',
  'pref_height_min' => 'Partner Min Height',
  'pref_education' => 'Preferred Education',
  'pref_location' => 'Preferred Location',
  'profile_photo' => 'Profile Photo',
];

$pct = 0;
$missing = [];
if ($user) {
  $checkFields = array_keys($fieldLabels);
  $filled = 0;
  foreach ($checkFields as $cf) {
    if (!empty($user[$cf])) $filled++;
  }
  $total = count($checkFields);
  $pct = $total > 0 ? round(($filled / $total) * 100) : 0;

  // Group fields by section for the "what's missing" checklist (UX #8) — uses same label map
  $sections = [
    'Basic & Contact' => ['full_name', 'date_of_birth', 'gender', 'marital_status'],
    'Physical' => ['height', 'weight', 'body_type', 'complexion', 'blood_group'],
    'Religious & Cultural' => ['religion', 'caste', 'sub_caste', 'gothram', 'star_sign', 'zodiac', 'dosham', 'mother_tongue'],
    'Location' => ['country', 'state', 'city', 'citizenship'],
    'Education & Career' => ['highest_education', 'education_detail', 'occupation', 'occupation_type', 'annual_income'],
    'Family' => ['family_type', 'family_status', 'family_values', 'father_name', 'mother_name'],
    'Lifestyle' => ['diet', 'smoking', 'drinking'],
    'About & Preferences' => ['about_self', 'pref_age_min', 'pref_age_max', 'pref_height_min', 'pref_education', 'pref_location'],
    'Gallery / Photo' => ['profile_photo'],
  ];
  foreach ($sections as $secName => $fields) {
    $secMissing = [];
    foreach ($fields as $f) {
      if (empty($user[$f])) $secMissing[] = $f;
    }
    if ($secMissing) $missing[$secName] = $secMissing;
  }
}

$initial = '?';
if ($user && !empty($user['full_name'])) {
  $initial = mb_strtoupper(mb_substr($user['full_name'], 0, 1));
}

$profilePhoto = '';
if ($user && !empty($user['profile_photo'])) {
  $profilePhoto = htmlspecialchars(photo_url($user['profile_photo']));
}

$nakshatras = ['Ashwini','Bharani','Krittika','Rohini','Mrigashira','Ardra','Punarvasu','Pushya','Ashlesha','Magha','Purva Phalguni','Uttara Phalguni','Hasta','Chitra','Swati','Vishakha','Anuradha','Jyeshtha','Mula','Purva Ashadha','Uttara Ashadha','Shravana','Dhanishta','Shatabhisha','Purva Bhadrapada','Uttara Bhadrapada','Revati'];
$incomes = ['Below 2 Lakhs','2 - 4 Lakhs','4 - 6 Lakhs','6 - 8 Lakhs','8 - 10 Lakhs','10 - 15 Lakhs','15 - 20 Lakhs','20 - 30 Lakhs','30 - 50 Lakhs','50 Lakhs+'];
$heights = [
  "4'6\" (137 cm)", "4'7\" (140 cm)", "4'8\" (142 cm)", "4'9\" (145 cm)", "4'10\" (147 cm)", "4'11\" (150 cm)",
  "5'0\" (152 cm)", "5'1\" (155 cm)", "5'2\" (157 cm)", "5'3\" (160 cm)", "5'4\" (163 cm)", "5'5\" (165 cm)",
  "5'6\" (168 cm)", "5'7\" (170 cm)", "5'8\" (173 cm)", "5'9\" (175 cm)", "5'10\" (178 cm)", "5'11\" (180 cm)",
  "6'0\" (183 cm)", "6'1\" (185 cm)", "6'2\" (188 cm)", "6'3\" (191 cm)", "6'4\" (193 cm)", "6'5\" (196 cm)"
];
?>
<?php
$pageTitle = 'My Profile — BestLife Matrimony';
$pageHeadExtra = '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"><link rel="stylesheet" href="' . asset('css/profile.css') . '">';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<div class="profile-page">

<!-- Header -->
<header class="profile-header">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
      <div class="d-flex align-items-center gap-3 flex-wrap">
      <div class="profile-photo" id="headerProfileAvatar">
        <?php if ($profilePhoto): ?>
          <img src="<?php echo $profilePhoto; ?>" alt="Profile Photo" id="headerAvatarImg">
        <?php else: ?>
          <span id="headerAvatarInitial"><?php echo $initial; ?></span>
        <?php endif; ?>
      </div>
      <div>
        <h1 class="profile-name"><?php echo $user ? htmlspecialchars($user['full_name'] ?? 'Your Name') : 'Your Name'; ?></h1>
        <?php if ($user): ?>
          <p class="profile-meta mb-0">
            ID: BLM-<?php echo !empty($user['created_at']) ? date('Y', strtotime($user['created_at'])) : date('Y'); ?>-<?php echo str_pad((string)($user['id'] ?? 1), 5, '0', STR_PAD_LEFT); ?>
            &middot; Member since <?php echo !empty($user['created_at']) ? date('M Y', strtotime($user['created_at'])) : date('M Y'); ?>
            <?php if (!empty($user['looking_for'])): ?>
              &middot; Looking for: <strong style="color:#6b1020;"><?php echo htmlspecialchars($user['looking_for']); ?></strong>
            <?php endif; ?>
          </p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</header>

<!-- Progress -->
<div class="progress-section">
  <div class="container">
    <div class="d-flex align-items-center justify-content-between mb-1">
      <span class="progress-label">Profile Completion</span>
      <span class="progress-percent"><?php echo $pct; ?>%</span>
    </div>
    <div class="progress-bar-wrapper">
      <div class="progress-bar-fill" style="width: <?php echo $pct; ?>%;"></div>
    </div>
    <?php if ($user && !empty($missing)): ?>
      <div class="missing-fields" style="margin-top:0.5rem;">
        <button type="button" class="missing-fields-toggle" data-toggle-open>
          <i class="bi bi-chevron-down"></i> Complete your profile — <?php echo array_sum(array_map('count', $missing)); ?> field<?php echo array_sum(array_map('count', $missing)) > 1 ? 's' : ''; ?> remaining
        </button>
        <div class="missing-fields-list">
          <?php foreach ($missing as $secName => $fields): ?>
            <div class="missing-sec">
              <strong><?php echo htmlspecialchars($secName); ?></strong>
              <span><?php echo htmlspecialchars(implode(', ', array_map(function($f) use ($fieldLabels) { return $fieldLabels[$f] ?? ucwords(str_replace('_', ' ', $f)); }, $fields))); ?></span>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<!-- Tabs -->
<div class="tabs-nav-wrapper">
  <div class="container">
    <ul class="tabs-nav" id="profileTabs">
      <li class="nav-tab active" data-tab="personal"><i class="bi bi-person tab-icon"></i><span class="tab-label">Personal</span></li>
      <li class="nav-tab" data-tab="religious"><i class="bi bi-book tab-icon"></i><span class="tab-label">Religious</span></li>
      <li class="nav-tab" data-tab="location"><i class="bi bi-geo-alt tab-icon"></i><span class="tab-label">Location</span></li>
      <li class="nav-tab" data-tab="education"><i class="bi bi-mortarboard tab-icon"></i><span class="tab-label">Education</span></li>
      <li class="nav-tab" data-tab="family"><i class="bi bi-people tab-icon"></i><span class="tab-label">Family</span></li>
      <li class="nav-tab" data-tab="lifestyle"><i class="bi bi-heart-pulse tab-icon"></i><span class="tab-label">Lifestyle</span></li>
      <li class="nav-tab" data-tab="preferences"><i class="bi bi-sliders tab-icon"></i><span class="tab-label">Preferences</span></li>
      <li class="nav-tab" data-tab="photos"><i class="bi bi-camera tab-icon"></i><span class="tab-label">Photos</span></li>
      <li class="nav-tab" data-tab="favourites"><i class="bi bi-heart tab-icon"></i><span class="tab-label">Favourites</span></li>
      <li class="nav-tab"><a href="./who_viewed_me.php" target="_blank" rel="noopener noreferrer" style="display:flex;align-items:center;gap:6px;color:inherit;text-decoration:none;"><i class="bi bi-eye tab-icon"></i><span class="tab-label">Who Viewed Me</span></a></li>
    </ul>
  </div>
</div>

<!-- Content -->
<div class="tab-content-area">
  <div class="container">

    <?php if ($success): ?>
      <div class="profile-alert success" id="profileAlert"><i class="bi bi-check-circle-fill"></i> Profile updated and saved successfully!</div>
    <?php endif; ?>
    <?php if (!empty($errors['db'])): ?>
      <div class="profile-alert error"><i class="bi bi-exclamation-circle-fill"></i> <?php echo $errors['db']; ?></div>
    <?php endif; ?>
    <?php if (!empty($errors['auth'])): ?>
      <div class="profile-alert error"><i class="bi bi-exclamation-circle-fill"></i> <?php echo $errors['auth']; ?></div>
    <?php endif; ?>
    <?php if (!empty($errors['looking_for'])): ?>
      <div class="profile-alert error"><i class="bi bi-exclamation-circle-fill"></i> <?php echo $errors['looking_for']; ?></div>
    <?php endif; ?>
    <?php if (!empty($errors['pref_age_range'])): ?>
      <div class="profile-alert error"><i class="bi bi-exclamation-circle-fill"></i> <?php echo htmlspecialchars($errors['pref_age_range']); ?></div>
    <?php endif; ?>
    <?php if (!empty($errors['pref_age_min'])): ?>
      <div class="profile-alert error"><i class="bi bi-exclamation-circle-fill"></i> <?php echo htmlspecialchars($errors['pref_age_min']); ?></div>
    <?php endif; ?>
    <?php if (!empty($errors['pref_age_max'])): ?>
      <div class="profile-alert error"><i class="bi bi-exclamation-circle-fill"></i> <?php echo htmlspecialchars($errors['pref_age_max']); ?></div>
    <?php endif; ?>
    <?php if (!empty($errors['profile_photo'])): ?>
      <div class="profile-alert error"><i class="bi bi-exclamation-circle-fill"></i> <?php echo htmlspecialchars($errors['profile_photo']); ?></div>
    <?php endif; ?>

    <?php if ($user && ((int)($user['email_verified'] ?? 0) !== 1)): ?>
      <div class="profile-alert" style="background:#fff7e0;border-color:#e3c877;color:#6b4f00;"><i class="bi bi-envelope-exclamation-fill"></i> Your email is not verified yet. <a href="./verify_email.php" style="font-weight:700;text-decoration:underline;">Verify your email</a> to activate all features.</div>
    <?php elseif ($user && ((int)($user['phone_verified'] ?? 0) !== 1)): ?>
      <div class="profile-alert" style="background:#fff7e0;border-color:#e3c877;color:#6b4f00;"><i class="bi bi-phone-vibrate-fill"></i> Verify your phone number to earn a trust badge. <a href="./verify.php" style="font-weight:700;text-decoration:underline;">Verify now</a></div>
    <?php endif; ?>

    <form id="profileForm" class="profile-form" method="post" action="./profile.php" enctype="multipart/form-data" novalidate>
      <input type="hidden" name="save_profile" value="1">
      <?php csrf_field(); ?>
      <input type="hidden" name="delete_profile_photo" id="delete_profile_photo" value="0">

      <!-- 1. PERSONAL -->
      <div class="tab-panel active" id="panel-personal">
        <div class="section-card">
          <h5><i class="bi bi-person"></i> Basic &amp; Contact Details</h5>
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">Full Name <span class="required-star">*</span></label>
              <input type="text" name="full_name" class="form-control" value="<?php pv($user, 'full_name'); ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Email Address (Registered)</label>
              <input type="email" class="form-control" value="<?php pv($user, 'email'); ?>" disabled readonly style="background-color:#f5f4f0; cursor:not-allowed;" title="Email address cannot be changed">
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone Number <span class="required-star">*</span></label>
              <input type="tel" name="phone" class="form-control" value="<?php pv($user, 'phone'); ?>" placeholder="e.g. +91 9876543210" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Looking For <span class="required-star">*</span></label>
              <select name="looking_for" class="form-select" required>
                <option value="">Select</option>
                <option value="Bride"<?php echo sv($user, 'looking_for', 'Bride'); ?>>Bride (Female Partner)</option>
                <option value="Groom"<?php echo sv($user, 'looking_for', 'Groom'); ?>>Groom (Male Partner)</option>
              </select>
            </div>
          </div>
        </div>

        <div class="section-card">
          <h5><i class="bi bi-card-text"></i> Personal Details</h5>
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">Date of Birth <span class="required-star">*</span></label>
              <input type="date" name="date_of_birth" class="form-control" value="<?php pv($user, 'date_of_birth'); ?>" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Gender <span class="required-star">*</span></label>
              <div class="d-flex gap-3 mt-1">
                <?php foreach (['Female','Male','Other'] as $g): ?>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="gender" id="gender<?php echo $g; ?>" value="<?php echo $g; ?>"<?php echo cv($user, 'gender', $g); ?>>
                    <label class="form-check-label" for="gender<?php echo $g; ?>"><?php echo $g; ?></label>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Height <span class="required-star">*</span></label>
              <input type="text" name="height" class="form-control" value="<?php pv($user, 'height'); ?>" placeholder="e.g. 5'8&quot; or 173 cm" list="heightOptions" required>
              <datalist id="heightOptions">
                <?php foreach ($heights as $ht): ?>
                  <option value="<?php echo $ht; ?>"></option>
                <?php endforeach; ?>
              </datalist>
            </div>
            <div class="col-md-6">
              <label class="form-label">Weight (kg)</label>
              <input type="number" name="weight" class="form-control" value="<?php pv($user, 'weight'); ?>" min="30" max="200" placeholder="e.g. 65">
            </div>
            <div class="col-md-6">
              <label class="form-label">Body Type</label>
              <select name="body_type" class="form-select">
                <option value="">Select</option>
                <?php foreach (['Slim','Average','Athletic','Muscular','Heavy'] as $bt): ?>
                  <option value="<?php echo $bt; ?>"<?php echo sv($user, 'body_type', $bt); ?>><?php echo $bt; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Complexion</label>
              <select name="complexion" class="form-select">
                <option value="">Select</option>
                <?php foreach (['Very Fair','Fair','Wheatish','Dark'] as $c): ?>
                  <option value="<?php echo $c; ?>"<?php echo sv($user, 'complexion', $c); ?>><?php echo $c; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Blood Group</label>
              <select name="blood_group" class="form-select">
                <option value="">Select</option>
                <?php foreach (['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                  <option value="<?php echo $bg; ?>"<?php echo sv($user, 'blood_group', $bg); ?>><?php echo $bg; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Marital Status <span class="required-star">*</span></label>
              <select name="marital_status" class="form-select" required>
                <option value="">Select</option>
                <?php foreach (['Never Married','Divorced','Widowed','Awaited Divorce'] as $ms): ?>
                  <option value="<?php echo $ms; ?>"<?php echo sv($user, 'marital_status', $ms); ?>><?php echo $ms; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label">About Self</label>
              <textarea name="about_self" class="form-control" rows="3" placeholder="Tell potential matches about your values, passions, lifestyle, and what makes you unique..."><?php pv($user, 'about_self'); ?></textarea>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <span></span>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-gold" data-tab-target="religious">Next: Religious <i class="bi bi-arrow-right ms-1"></i></button>
            <button type="submit" class="btn btn-burgundy"><i class="bi bi-check2"></i> Save Profile</button>
          </div>
        </div>
      </div>

      <!-- 2. RELIGIOUS -->
      <div class="tab-panel" id="panel-religious">
        <div class="section-card">
          <h5><i class="bi bi-book"></i> Religious Details</h5>
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">Religion <span class="required-star">*</span></label>
              <select name="religion" class="form-select" required>
                <option value="">Select</option>
                <?php foreach (['Hindu','Muslim','Christian','Sikh','Buddhist','Jain','Parsi','Other'] as $r): ?>
                  <option value="<?php echo $r; ?>"<?php echo sv($user, 'religion', $r); ?>><?php echo $r; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Caste</label>
              <input type="text" name="caste" class="form-control" value="<?php pv($user, 'caste'); ?>" placeholder="e.g. Brahmin, Iyer, Nadar, etc.">
            </div>
            <div class="col-md-6">
              <label class="form-label">Sub-Caste</label>
              <input type="text" name="sub_caste" class="form-control" value="<?php pv($user, 'sub_caste'); ?>" placeholder="e.g. Vadama, Smartha">
            </div>
            <div class="col-md-6">
              <label class="form-label">Gothram</label>
              <input type="text" name="gothram" class="form-control" value="<?php pv($user, 'gothram'); ?>" placeholder="e.g. Kashyapa, Bharadwaj">
            </div>
            <div class="col-md-6">
              <label class="form-label">Star / Nakshatra</label>
              <select name="star_sign" class="form-select">
                <option value="">Select</option>
                <?php foreach ($nakshatras as $n): ?>
                  <option value="<?php echo $n; ?>"<?php echo sv($user, 'star_sign', $n); ?>><?php echo $n; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Rasi / Moon Sign / Zodiac</label>
              <select name="zodiac" class="form-select">
                <option value="">Select</option>
                <?php foreach (['Aries (Mesha)','Taurus (Vrishabha)','Gemini (Mithuna)','Cancer (Karka)','Leo (Simha)','Virgo (Kanya)','Libra (Tula)','Scorpio (Vrischika)','Sagittarius (Dhanu)','Capricorn (Makara)','Aquarius (Kumbha)','Pisces (Meena)'] as $z): ?>
                  <?php $zKey = explode(' ', $z)[0]; ?>
                  <option value="<?php echo $zKey; ?>"<?php echo sv($user, 'zodiac', $zKey); ?>><?php echo $z; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Dosham / Manglik</label>
              <select name="dosham" class="form-select">
                <option value="">Select</option>
                <?php foreach (['No','Yes','Not Sure'] as $d): ?>
                  <option value="<?php echo $d; ?>"<?php echo sv($user, 'dosham', $d); ?>><?php echo $d; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Mother Tongue <span class="required-star">*</span></label>
              <select name="mother_tongue" class="form-select" required>
                <option value="">Select</option>
                <?php foreach (['Tamil','Telugu','Hindi','Kannada','Malayalam','Marathi','Bengali','Gujarati','Punjabi','Odia','Urdu','Other'] as $mt): ?>
                  <option value="<?php echo $mt; ?>"<?php echo sv($user, 'mother_tongue', $mt); ?>><?php echo $mt; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <button type="button" class="btn btn-outline-gold" data-tab-target="personal"><i class="bi bi-arrow-left me-1"></i> Previous</button>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-gold" data-tab-target="location">Next: Location <i class="bi bi-arrow-right ms-1"></i></button>
            <button type="submit" class="btn btn-burgundy"><i class="bi bi-check2"></i> Save Profile</button>
          </div>
        </div>
      </div>

      <!-- 3. LOCATION -->
      <div class="tab-panel" id="panel-location">
        <div class="section-card">
          <h5><i class="bi bi-geo-alt"></i> Location &amp; Residency Details</h5>
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">Country Living In <span class="required-star">*</span></label>
              <select name="country" class="form-select" required>
                <option value="">Select</option>
                <?php foreach (['India','USA','United Kingdom','Canada','Australia','UAE','Singapore','Malaysia','Germany','Other'] as $c): ?>
                  <option value="<?php echo $c; ?>"<?php echo sv($user, 'country', $c); ?>><?php echo $c; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">State <span class="required-star">*</span></label>
              <input type="text" name="state" class="form-control" value="<?php pv($user, 'state'); ?>" placeholder="e.g. Tamil Nadu, Maharashtra, California" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">City <span class="required-star">*</span></label>
              <input type="text" name="city" class="form-control" value="<?php pv($user, 'city'); ?>" placeholder="e.g. Chennai, Bangalore, Mumbai" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Citizenship</label>
              <input type="text" name="citizenship" class="form-control" value="<?php pv($user, 'citizenship'); ?>" placeholder="e.g. Indian, US Citizen, PR">
            </div>
            <div class="col-md-6">
              <label class="form-label">Residential Status</label>
              <select name="residential_status" class="form-select">
                <option value="">Select</option>
                <?php foreach (['Owned','Rented','Parents','Family'] as $rs): ?>
                  <option value="<?php echo $rs; ?>"<?php echo sv($user, 'residential_status', $rs); ?>><?php echo $rs; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <button type="button" class="btn btn-outline-gold" data-tab-target="religious"><i class="bi bi-arrow-left me-1"></i> Previous</button>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-gold" data-tab-target="education">Next: Education <i class="bi bi-arrow-right ms-1"></i></button>
            <button type="submit" class="btn btn-burgundy"><i class="bi bi-check2"></i> Save Profile</button>
          </div>
        </div>
      </div>

      <!-- 4. EDUCATION -->
      <div class="tab-panel" id="panel-education">
        <div class="section-card">
          <h5><i class="bi bi-mortarboard"></i> Education &amp; Career</h5>
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">Highest Education <span class="required-star">*</span></label>
              <select name="highest_education" class="form-select" required>
                <option value="">Select</option>
                <?php foreach (['Bachelors','Masters','Doctorate','Professional','High School'] as $e): ?>
                  <option value="<?php echo $e; ?>"<?php echo sv($user, 'highest_education', $e); ?>><?php echo $e; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Education Details / Degree</label>
              <input type="text" name="education_detail" class="form-control" value="<?php pv($user, 'education_detail'); ?>" placeholder="e.g. B.Tech / MBA / MS / MBBS">
            </div>
            <div class="col-md-6">
              <label class="form-label">Occupation / Job Title <span class="required-star">*</span></label>
              <input type="text" name="occupation" class="form-control" value="<?php pv($user, 'occupation'); ?>" placeholder="e.g. Software Engineer, Doctor, Architect" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Occupation Sector</label>
              <select name="occupation_type" class="form-select">
                <option value="">Select</option>
                <?php foreach (['Private','Government','Business','Self Employed','Freelance','Homemaker','Retired'] as $ot): ?>
                  <option value="<?php echo $ot; ?>"<?php echo sv($user, 'occupation_type', $ot); ?>><?php echo $ot; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Annual Income</label>
              <select name="annual_income" class="form-select">
                <option value="">Select</option>
                <?php foreach ($incomes as $inc): ?>
                  <option value="<?php echo $inc; ?>"<?php echo sv($user, 'annual_income', $inc); ?>><?php echo $inc; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <button type="button" class="btn btn-outline-gold" data-tab-target="location"><i class="bi bi-arrow-left me-1"></i> Previous</button>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-gold" data-tab-target="family">Next: Family <i class="bi bi-arrow-right ms-1"></i></button>
            <button type="submit" class="btn btn-burgundy"><i class="bi bi-check2"></i> Save Profile</button>
          </div>
        </div>
      </div>

      <!-- 5. FAMILY -->
      <div class="tab-panel" id="panel-family">
        <div class="section-card">
          <h5><i class="bi bi-people"></i> Family Background</h5>
          <div class="row g-2">
            <div class="col-md-6">
              <label class="form-label">Family Type</label>
              <select name="family_type" class="form-select">
                <option value="">Select</option>
                <?php foreach (['Nuclear','Joint'] as $ft): ?>
                  <option value="<?php echo $ft; ?>"<?php echo sv($user, 'family_type', $ft); ?>><?php echo $ft; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Family Status</label>
              <select name="family_status" class="form-select">
                <option value="">Select</option>
                <?php foreach (['Middle Class','Upper Middle Class','Rich','Affluent'] as $fs): ?>
                  <option value="<?php echo $fs; ?>"<?php echo sv($user, 'family_status', $fs); ?>><?php echo $fs; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Family Values</label>
              <select name="family_values" class="form-select">
                <option value="">Select</option>
                <?php foreach (['Traditional','Moderate','Orthodox','Liberal'] as $fv): ?>
                  <option value="<?php echo $fv; ?>"<?php echo sv($user, 'family_values', $fv); ?>><?php echo $fv; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Family Location / Native</label>
              <input type="text" name="family_location" class="form-control" value="<?php pv($user, 'family_location'); ?>" placeholder="e.g. Madurai, Tamil Nadu">
            </div>
            <div class="col-md-6">
              <label class="form-label">Father's Name</label>
              <input type="text" name="father_name" class="form-control" value="<?php pv($user, 'father_name'); ?>" placeholder="e.g. S. Ramanathan">
            </div>
            <div class="col-md-6">
              <label class="form-label">Father's Occupation</label>
              <input type="text" name="father_occupation" class="form-control" value="<?php pv($user, 'father_occupation'); ?>" placeholder="e.g. Retired Govt Officer / Business">
            </div>
            <div class="col-md-6">
              <label class="form-label">Mother's Name</label>
              <input type="text" name="mother_name" class="form-control" value="<?php pv($user, 'mother_name'); ?>" placeholder="e.g. R. Lakshmi">
            </div>
            <div class="col-md-6">
              <label class="form-label">Mother's Occupation</label>
              <input type="text" name="mother_occupation" class="form-control" value="<?php pv($user, 'mother_occupation'); ?>" placeholder="e.g. Homemaker / Teacher">
            </div>
            <div class="col-6 col-md-3">
              <label class="form-label">Brothers</label>
              <select name="brothers" class="form-select">
                <?php for ($i = 0; $i <= 10; $i++): ?>
                  <option value="<?php echo $i; ?>"<?php if (intval($user['brothers'] ?? 0) === $i) echo ' selected'; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="col-6 col-md-3">
              <label class="form-label">Brothers Married <span class="text-muted" style="font-weight:400;font-size:0.75rem;">(Optional)</span></label>
              <select name="brothers_married" class="form-select">
                <option value=""<?php if (!isset($user['brothers_married']) || $user['brothers_married'] === null || $user['brothers_married'] === '') echo ' selected'; ?>>Select</option>
                <?php for ($i = 0; $i <= 10; $i++): ?>
                  <option value="<?php echo $i; ?>"<?php if (isset($user['brothers_married']) && $user['brothers_married'] !== null && $user['brothers_married'] !== '' && intval($user['brothers_married']) === $i) echo ' selected'; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="col-6 col-md-3">
              <label class="form-label">Sisters</label>
              <select name="sisters" class="form-select">
                <?php for ($i = 0; $i <= 10; $i++): ?>
                  <option value="<?php echo $i; ?>"<?php if (intval($user['sisters'] ?? 0) === $i) echo ' selected'; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
              </select>
            </div>
            <div class="col-6 col-md-3">
              <label class="form-label">Sisters Married <span class="text-muted" style="font-weight:400;font-size:0.75rem;">(Optional)</span></label>
              <select name="sisters_married" class="form-select">
                <option value=""<?php if (!isset($user['sisters_married']) || $user['sisters_married'] === null || $user['sisters_married'] === '') echo ' selected'; ?>>Select</option>
                <?php for ($i = 0; $i <= 10; $i++): ?>
                  <option value="<?php echo $i; ?>"<?php if (isset($user['sisters_married']) && $user['sisters_married'] !== null && $user['sisters_married'] !== '' && intval($user['sisters_married']) === $i) echo ' selected'; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <button type="button" class="btn btn-outline-gold" data-tab-target="education"><i class="bi bi-arrow-left me-1"></i> Previous</button>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-gold" data-tab-target="lifestyle">Next: Lifestyle <i class="bi bi-arrow-right ms-1"></i></button>
            <button type="submit" class="btn btn-burgundy"><i class="bi bi-check2"></i> Save Profile</button>
          </div>
        </div>
      </div>

      <!-- 6. LIFESTYLE -->
      <div class="tab-panel" id="panel-lifestyle">
        <div class="section-card">
          <h5><i class="bi bi-heart-pulse"></i> Habits &amp; Lifestyle</h5>
          <div class="row g-2">
            <div class="col-md-4">
              <label class="form-label">Dietary Habit</label>
              <select name="diet" class="form-select">
                <option value="">Select</option>
                <?php foreach (['Vegetarian','Non-Vegetarian','Eggetarian','Vegan','Jain'] as $d): ?>
                  <option value="<?php echo $d; ?>"<?php echo sv($user, 'diet', $d); ?>><?php echo $d; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Smoking Habit</label>
              <select name="smoking" class="form-select">
                <option value="">Select</option>
                <?php foreach (['No','Sometimes','Often'] as $s): ?>
                  <option value="<?php echo $s; ?>"<?php echo sv($user, 'smoking', $s); ?>><?php echo $s; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Drinking Habit</label>
              <select name="drinking" class="form-select">
                <option value="">Select</option>
                <?php foreach (['No','Sometimes','Often'] as $d): ?>
                  <option value="<?php echo $d; ?>"<?php echo sv($user, 'drinking', $d); ?>><?php echo $d; ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <button type="button" class="btn btn-outline-gold" data-tab-target="family"><i class="bi bi-arrow-left me-1"></i> Previous</button>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-gold" data-tab-target="preferences">Next: Preferences <i class="bi bi-arrow-right ms-1"></i></button>
            <button type="submit" class="btn btn-burgundy"><i class="bi bi-check2"></i> Save Profile</button>
          </div>
        </div>
      </div>

      <!-- 7. PREFERENCES -->
      <div class="tab-panel" id="panel-preferences">
        <div class="section-card">
          <h5><i class="bi bi-sliders"></i> Partner Expectations</h5>
          <div class="row g-2">
            <div class="col-12 col-md-6">
              <label class="form-label">Partner Age Range (Years)</label>
              <div class="d-flex align-items-center gap-2">
                <select name="pref_age_min" id="pref_age_min" class="form-select" style="max-width:130px;">
                  <option value="">Min Age</option>
                  <?php for ($a = 18; $a <= 50; $a++): ?>
                    <option value="<?php echo $a; ?>"<?php echo sv($user, 'pref_age_min', $a); ?>><?php echo $a; ?> yrs</option>
                  <?php endfor; ?>
                </select>
                <span style="color:#888;">to</span>
                <select name="pref_age_max" id="pref_age_max" class="form-select" style="max-width:130px;">
                  <option value="">Max Age</option>
                  <?php for ($a = 18; $a <= 50; $a++): ?>
                    <option value="<?php echo $a; ?>"<?php echo sv($user, 'pref_age_max', $a); ?>><?php echo $a; ?> yrs</option>
                  <?php endfor; ?>
                </select>
              </div>
            </div>
            <div class="col-12 col-md-6">
              <label class="form-label">Partner Height Range</label>
              <div class="d-flex align-items-center gap-2">
                <input type="text" name="pref_height_min" class="form-control" style="max-width:160px;" value="<?php pv($user, 'pref_height_min'); ?>" placeholder="Min (e.g. 5'2&quot;)" list="heightOptions">
                <span style="color:#888;">to</span>
                <input type="text" name="pref_height_max" class="form-control" style="max-width:160px;" value="<?php pv($user, 'pref_height_max'); ?>" placeholder="Max (e.g. 6'0&quot;)" list="heightOptions">
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Preferred Education</label>
              <input type="text" name="pref_education" class="form-control" value="<?php pv($user, 'pref_education'); ?>" placeholder="e.g. Bachelors / Masters / Any Graduate">
            </div>
            <div class="col-md-6">
              <label class="form-label">Preferred Location / Country</label>
              <input type="text" name="pref_location" class="form-control" value="<?php pv($user, 'pref_location'); ?>" placeholder="e.g. Tamil Nadu, India / USA / Any">
            </div>
            <div class="col-12">
              <label class="form-label">Other Partner Preferences &amp; Expectations</label>
              <textarea name="pref_other" class="form-control" rows="3" placeholder="Specify any caste preferences, career expectations, lifestyle choices, or family preferences..."><?php pv($user, 'pref_other'); ?></textarea>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-3">
          <button type="button" class="btn btn-outline-gold" data-tab-target="lifestyle"><i class="bi bi-arrow-left me-1"></i> Previous</button>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-gold" data-tab-target="photos">Next: Photos <i class="bi bi-arrow-right ms-1"></i></button>
            <button type="submit" class="btn btn-burgundy"><i class="bi bi-check2"></i> Save Profile</button>
          </div>
        </div>
      </div>

      <!-- 8. PHOTOS -->
      <div class="tab-panel" id="panel-photos">
        <div class="section-card">
          <h5><i class="bi bi-camera"></i> Primary Profile Photo</h5>
          <div class="d-flex align-items-start gap-4 flex-wrap">
            <div class="photo-upload-box" id="mainPhotoBox">
              <?php if ($profilePhoto): ?>
                <img src="<?php echo $profilePhoto; ?>" alt="Profile Photo" id="mainPhotoPreview">
              <?php else: ?>
                <img src="" alt="" style="display:none;" id="mainPhotoPreview">
              <?php endif; ?>
              <div class="upload-placeholder" id="mainPhotoPlaceholder"<?php if ($profilePhoto) echo ' style="display:none;"'; ?>>
                <i class="bi bi-camera"></i>
                <span>Upload photo</span>
              </div>
              <input type="file" name="profile_photo_file" id="profile_photo_file" accept="image/jpeg,image/png,image/webp" onchange="previewMainPhoto(this)">
              <button class="remove-photo" id="mainPhotoRemoveBtn" data-remove-main type="button" title="Remove photo"<?php if (!$profilePhoto) echo ' style="display:none;"'; ?>><i class="bi bi-x"></i></button>
            </div>
            <div>
              <p class="mb-1" style="font-size:0.88rem;font-weight:600;color:#1a1a1a;">Upload a clear, recent photo</p>
              <p class="mb-2" style="font-size:0.8rem;color:#666;">This photo will be displayed on your profile card and search results. Supported: JPG, PNG, WEBP. Max 5MB.</p>
              <?php if ($profilePhoto): ?>
                <button type="button" class="btn btn-sm btn-outline-danger" data-remove-main><i class="bi bi-trash"></i> Delete Current Photo</button>
              <?php endif; ?>
            </div>
          </div>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
          <button type="button" class="btn btn-outline-gold" data-tab-target="preferences"><i class="bi bi-arrow-left me-1"></i> Previous</button>
          <div class="d-flex gap-2">
            <button type="submit" class="btn btn-burgundy"><i class="bi bi-check2"></i> Save Profile</button>
          </div>
        </div>
      </div>

      <!-- FAVOURITES -->
      <div class="tab-panel" id="panel-favourites">
        <div class="section-card">
          <h5><i class="bi bi-heart"></i> Our Favourites</h5>
          <div id="ourFavourites" class="fav-grid">
            <div class="fav-loading"><i class="bi bi-hourglass-split"></i> Loading...</div>
          </div>
        </div>
        <div class="section-card">
          <h5><i class="bi bi-heart-fill"></i> Who Favourited You</h5>
          <div id="favByOthers" class="fav-grid">
            <div class="fav-loading"><i class="bi bi-hourglass-split"></i> Loading...</div>
          </div>
        </div>
      </div>

      <!-- Main Sticky / Bottom Actions -->
      <div class="d-flex justify-content-end gap-2 mt-2 mb-3 flex-wrap pt-2 border-top">
        <button type="button" class="btn btn-outline-gold" data-cancel-form><i class="bi bi-arrow-counterclockwise"></i> Reset / Cancel</button>
        <button type="submit" class="btn btn-burgundy"><i class="bi bi-check2-circle"></i> Save All Changes</button>
      </div>

    </form>
  </div>
</div>

</div>

<?php
require_once __DIR__ . '/includes/footer.php';
$pageScripts = '<script src="' . asset('js/profile.js') . '"></script>';
require_once __DIR__ . '/includes/scripts.php';
?>
