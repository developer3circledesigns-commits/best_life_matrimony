<?php
header('Content-Type: application/json');
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

// Admin users cannot use user APIs - return 403
if (is_admin()) {
  http_response_code(403);
  echo json_encode(['error' => 'Admin users cannot access user APIs']);
  exit;
}

$userId = $_SESSION['user_id'];

try {
  $db = getDB();

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF via header
    if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !hash_equals(csrf_token(), $_SERVER['HTTP_X_CSRF_TOKEN'])) {
      http_response_code(403);
      echo json_encode(['error' => 'Invalid request']);
      exit;
    }
    $raw = file_get_contents('php://input');
    $input = json_decode($raw ?: '', true) ?: [];
    $reportedId = (int) ($input['reported_id'] ?? 0);
    $reason = trim($input['reason'] ?? '');
    $details = trim($input['details'] ?? '');
    if ($reportedId <= 0) { echo json_encode(['error' => 'Missing profile']); exit; }
    if ($reportedId === $userId) { echo json_encode(['error' => 'Cannot report yourself']); exit; }
    if ($reason === '') { echo json_encode(['error' => 'A reason is required']); exit; }
    if (mb_strlen($reason) > 255) { echo json_encode(['error' => 'Reason too long']); exit; }
    if (mb_strlen($details) > 2000) { echo json_encode(['error' => 'Details too long']); exit; }

    // Rate limit reports
    if (isset($_SESSION['rate_report_' . $userId]) && $_SESSION['rate_report_' . $userId] > 5) {
      echo json_encode(['error' => 'Too many reports. Please try again later.']);
      exit;
    }
    $_SESSION['rate_report_' . $userId] = ($_SESSION['rate_report_' . $userId] ?? 0) + 1;

    $db->prepare('INSERT INTO reports (reporter_id, reported_id, reason, details) VALUES (?, ?, ?, ?)')
       ->execute([$userId, $reportedId, $reason, $details === '' ? null : $details]);
    echo json_encode(['ok' => true]);
    log_activity($userId, 'report', 'user', $reportedId, 'Reported profile: ' . $reason);
    exit;
  }

  echo json_encode(['error' => 'Method not allowed']);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Server error']);
}
