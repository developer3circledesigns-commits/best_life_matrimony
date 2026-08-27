<?php
header('Content-Type: application/json');
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

// Compatibility score between current user and a target profile.
// Params: ?user_id=X (target). Returns 0-100 score + breakdown.
require_login();

// Admin users cannot use user APIs - return 403
if (is_admin()) {
  http_response_code(403);
  echo json_encode(['error' => 'Admin users cannot access user APIs']);
  exit;
}

$userId = $_SESSION['user_id'];

$targetId = isset($_GET['user_id']) ? (int) $_GET['user_id'] : 0;
if (!$targetId || $targetId === $userId) {
  http_response_code(400);
  echo json_encode(['error' => 'Invalid target']);
  exit;
}

try {
  $db = getDB();
  $s = $db->prepare('SELECT * FROM users WHERE id = ?');
  $s->execute([$userId]);
  $me = $s->fetch();
  $s->execute([$targetId]);
  $them = $s->fetch();
  if (!$me || !$them) {
    http_response_code(404);
    echo json_encode(['error' => 'Profile not found']);
    exit;
  }

  $breakdown = [];
  $score = 0;

  // 1. Religion (20)
  if (isset($me['religion'], $them['religion']) && $me['religion'] !== '' && $them['religion'] !== '') {
    if ($me['religion'] === $them['religion']) $score += 20;
    $breakdown[] = ['label' => 'Religion', 'points' => $me['religion'] === $them['religion'] ? 20 : 0, 'match' => $me['religion'] === $them['religion']];
  }

  // 2. Caste (15)
  if (isset($me['caste'], $them['caste']) && $me['caste'] !== '' && $them['caste'] !== '') {
    if (strcasecmp($me['caste'], $them['caste']) === 0) $score += 15;
    $breakdown[] = ['label' => 'Caste', 'points' => strcasecmp($me['caste'], $them['caste']) === 0 ? 15 : 0, 'match' => strcasecmp($me['caste'], $them['caste']) === 0];
  }

  // 3. Mother Tongue (10)
  if (isset($me['mother_tongue'], $them['mother_tongue']) && $me['mother_tongue'] !== '' && $them['mother_tongue'] !== '') {
    if (strcasecmp($me['mother_tongue'], $them['mother_tongue']) === 0) $score += 10;
    $breakdown[] = ['label' => 'Mother Tongue', 'points' => strcasecmp($me['mother_tongue'], $them['mother_tongue']) === 0 ? 10 : 0, 'match' => strcasecmp($me['mother_tongue'], $them['mother_tongue']) === 0];
  }

  // 4. Location: state (10) / same city (full 10)
  if (isset($me['city'], $them['city']) && $me['city'] !== '' && $them['city'] !== '') {
    $loc = (strcasecmp($me['city'], $them['city']) === 0) ? 10 : 0;
    $score += $loc;
    $breakdown[] = ['label' => 'City', 'points' => $loc, 'match' => $loc > 0];
  }

  // 5. Marital status (5)
  if (isset($me['marital_status'], $them['marital_status']) && $me['marital_status'] !== '' && $them['marital_status'] !== '') {
    if ($me['marital_status'] === $them['marital_status']) $score += 5;
    $breakdown[] = ['label' => 'Marital Status', 'points' => $me['marital_status'] === $them['marital_status'] ? 5 : 0, 'match' => $me['marital_status'] === $them['marital_status']];
  }

  // 6. Education (10)
  if (isset($me['highest_education'], $them['highest_education']) && $me['highest_education'] !== '' && $them['highest_education'] !== '') {
    $ed = ($me['highest_education'] === $them['highest_education']) ? 10 : 0;
    $score += $ed;
    $breakdown[] = ['label' => 'Education', 'points' => $ed, 'match' => $ed > 0];
  }

  // 7. Age compatibility (10) — target within my preference range if set, else within +/-5 years
  $ageMe = null; $ageThem = null;
  if (!empty($me['date_of_birth'])) $ageMe = (int) (new DateTime())->diff(new DateTime($me['date_of_birth']))->y;
  if (!empty($them['date_of_birth'])) $ageThem = (int) (new DateTime())->diff(new DateTime($them['date_of_birth']))->y;
  if ($ageMe !== null && $ageThem !== null) {
    $compatible = true;
    if (!empty($me['pref_age_min']) && $ageThem < (int)$me['pref_age_min']) $compatible = false;
    if (!empty($me['pref_age_max']) && $ageThem > (int)$me['pref_age_max']) $compatible = false;
    if (empty($me['pref_age_min']) && empty($me['pref_age_max']) && abs($ageThem - $ageMe) > 5) $compatible = false;
    if ($compatible) $score += 10;
    $breakdown[] = ['label' => 'Age', 'points' => $compatible ? 10 : 0, 'match' => $compatible];
  }

  // 8. Height (5)
  if (!empty($me['height']) && !empty($them['height'])) {
    $h1 = (int) preg_replace('/[^0-9]/', '', $me['height']);
    $h2 = (int) preg_replace('/[^0-9]/', '', $them['height']);
    $ok = ($h1 > 0 && $h2 > 0 && abs($h1 - $h2) <= 10) ? 5 : 0;
    $score += $ok;
    $breakdown[] = ['label' => 'Height', 'points' => $ok, 'match' => $ok > 0];
  }

  // 9. Diet (5)
  if (isset($me['diet'], $them['diet']) && $me['diet'] !== '' && $them['diet'] !== '') {
    if ($me['diet'] === $them['diet']) $score += 5;
    $breakdown[] = ['label' => 'Diet', 'points' => $me['diet'] === $them['diet'] ? 5 : 0, 'match' => $me['diet'] === $them['diet']];
  }

  // 10. Companion: prefs location & education (10)
  $prefPoints = 0;
  if (!empty($me['pref_location'])) {
    $words = explode(',', strtolower($me['pref_location']));
    foreach ($words as $w) {
      $w = trim($w);
      if ($w !== '' && (strcasecmp($w, (string)($them['city'] ?? '')) === 0 || strcasecmp($w, (string)($them['state'] ?? '')) === 0)) {
        $prefPoints += 5; break;
      }
    }
  } else {
    $prefPoints += 0;
  }
  if (!empty($me['pref_education'])) {
    $prefEdu = strtolower($me['pref_education']);
    if (strpos($prefEdu, strtolower((string)($them['highest_education'] ?? ''))) !== false) $prefPoints += 5;
  }
  $score += $prefPoints;
  if ($prefPoints > 0) $breakdown[] = ['label' => 'Your Preferences', 'points' => $prefPoints, 'match' => true];
  else $breakdown[] = ['label' => 'Your Preferences', 'points' => 0, 'match' => false];

  // Normalize to 0-100 (max possible is 100)
  $score = max(0, min(100, $score));

  $level = 'Low';
  $color = '#b91c1c';
  if ($score >= 80) { $level = 'Excellent'; $color = '#15803d'; }
  elseif ($score >= 60) { $level = 'Good'; $color = '#15803d'; }
  elseif ($score >= 40) { $level = 'Fair'; $color = '#d97706'; }

  echo json_encode([
    'score' => $score,
    'level' => $level,
    'color' => $color,
    'breakdown' => $breakdown,
  ]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Server error']);
}
