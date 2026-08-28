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

$userId = (int)($_SESSION['user_id'] ?? 0);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  try {
    $db = getDB();
    $stmt = $db->prepare('SELECT profile_id FROM favourites WHERE user_id = ?');
    $stmt->execute([$userId]);
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode(['favourites' => array_map('intval', $ids)]);
  } catch (Exception $e) {
    echo json_encode(['favourites' => []]);
  }
  exit;
}

if ($method === 'POST') {
  require_csrf();
  $input = json_decode(file_get_contents('php://input'), true);
  $profileId = intval($input['profile_id'] ?? 0);
  if (!$profileId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing profile_id']);
    exit;
  }
  if ($profileId === $userId) {
    http_response_code(400);
    echo json_encode(['error' => 'Cannot favourite yourself']);
    exit;
  }
  try {
    $db = getDB();
    $stmt = $db->prepare('INSERT IGNORE INTO favourites (user_id, profile_id) VALUES (?, ?)');
    $stmt->execute([$userId, $profileId]);
    $added = $stmt->rowCount() > 0;
    if ($added) {
      // Notify the profile owner of a new favourite (skip self already handled above)
      try {
        $u = $db->prepare('SELECT full_name FROM users WHERE id = ?');
        $u->execute([$userId]);
        $name = $u->fetchColumn() ?: 'Someone';
        notification_add($profileId, 'favourite', $name . ' has added your profile to their favourites.');
      } catch (Exception $e) { /* ignore */ }
    }
    echo json_encode(['status' => 'added', 'profile_id' => $profileId, 'new' => $added]);
    log_activity($userId, 'favourite_add', 'user', $profileId, 'Added to favourites');
  } catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
  }
  exit;
}

if ($method === 'DELETE') {
  require_csrf();
  $input = json_decode(file_get_contents('php://input'), true);
  $profileId = intval($input['profile_id'] ?? 0);
  if (!$profileId) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing profile_id']);
    exit;
  }
  try {
    $db = getDB();
    $stmt = $db->prepare('DELETE FROM favourites WHERE user_id = ? AND profile_id = ?');
    $stmt->execute([$userId, $profileId]);
    echo json_encode(['status' => 'removed', 'profile_id' => $profileId]);
    log_activity($userId, 'favourite_remove', 'user', $profileId, 'Removed from favourites');
  } catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error']);
  }
  exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);
