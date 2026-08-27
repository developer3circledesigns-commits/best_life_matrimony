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
  $action = $_GET['action'] ?? '';

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'block') {
    require_csrf();
    $raw = file_get_contents('php://input');
    $input = json_decode($raw ?: '', true) ?: [];
    $pid = (int) ($input['profile_id'] ?? 0);
    if ($pid <= 0) { echo json_encode(['error' => 'Missing profile']); exit; }
    if ($pid === $userId) { echo json_encode(['error' => 'Cannot block yourself']); exit; }
    $db->prepare('INSERT IGNORE INTO blocks (blocker_id, blocked_id) VALUES (?, ?)')->execute([$userId, $pid]);
    echo json_encode(['ok' => true]);
    log_activity($userId, 'block', 'user', $pid, 'Blocked profile');
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'unblock') {
    require_csrf();
    $raw = file_get_contents('php://input');
    $input = json_decode($raw ?: '', true) ?: [];
    $pid = (int) ($input['profile_id'] ?? 0);
    $db->prepare('DELETE FROM blocks WHERE blocker_id = ? AND blocked_id = ?')->execute([$userId, $pid]);
    echo json_encode(['ok' => true]);
    log_activity($userId, 'unblock', 'user', $pid, 'Unblocked profile');
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
    $stmt = $db->prepare(
      'SELECT u.id, u.full_name, u.profile_photo FROM blocks b JOIN users u ON u.id = b.blocked_id
       WHERE b.blocker_id = ? ORDER BY b.created_at DESC'
    );
    $stmt->execute([$userId]);
    $rows = $stmt->fetchAll();
    foreach ($rows as &$r) { $r['photo'] = photo_url($r['profile_photo']); unset($r['profile_photo']); }
    echo json_encode(['blocked' => $rows]);
    exit;
  }

  echo json_encode(['error' => 'Unknown action']);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Server error']);
}
