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

// Unapproved/unverified users cannot manage shortlists
if (!can_interact()) {
  http_response_code(403);
  echo json_encode(['error' => 'Your profile needs verification or admin approval. Please verify email/phone or contact admin.']);
  exit;
}

$userId = $_SESSION['user_id'];

$action = $_GET['action'] ?? '';

try {
  $db = getDB();

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
    $stmt = $db->prepare(
      'SELECT u.id, u.full_name, u.gender, u.date_of_birth, u.city, u.occupation, u.profile_photo, s.note, s.created_at
       FROM shortlists s JOIN users u ON u.id = s.profile_id
       WHERE s.user_id = ? ORDER BY s.created_at DESC'
    );
    $stmt->execute([$userId]);
    $rows = $stmt->fetchAll();
    $out = [];
    foreach ($rows as $r) {
      $out[] = [
        'id' => (int)$r['id'],
        'name' => $r['full_name'] ?? '',
        'gender' => $r['gender'] ?? '',
        'city' => $r['city'] ?? '',
        'occupation' => $r['occupation'] ?? '',
        'photo' => photo_url($r['profile_photo']),
        'note' => $r['note'] ?? '',
        'created_at' => $r['created_at'],
      ];
    }
    echo json_encode(['shortlists' => $out]);
    exit;
  }

  if (($_SERVER['REQUEST_METHOD'] === 'POST' || $action === 'status') && $action === 'status') {
    $pid = (int) ($_GET['user_id'] ?? 0);
    $stmt = $db->prepare('SELECT id FROM shortlists WHERE user_id = ? AND profile_id = ?');
    $stmt->execute([$userId, $pid]);
    echo json_encode(['active' => (bool)$stmt->fetch()]);
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'toggle') {
    require_csrf();
    $raw = file_get_contents('php://input');
    $input = json_decode($raw ?: '', true) ?: [];
    $pid = (int) ($input['profile_id'] ?? 0);
    $note = trim($input['note'] ?? '');
    if ($pid <= 0) { echo json_encode(['error' => 'Missing profile']); exit; }
    $chk = $db->prepare('SELECT id FROM shortlists WHERE user_id = ? AND profile_id = ?');
    $chk->execute([$userId, $pid]);
    if ($chk->fetch()) {
      $db->prepare('DELETE FROM shortlists WHERE user_id = ? AND profile_id = ?')->execute([$userId, $pid]);
      log_activity($userId, 'shortlist_remove', 'user', $pid, 'Removed from shortlist');
      echo json_encode(['active' => false]);
    } else {
      $db->prepare('INSERT INTO shortlists (user_id, profile_id, note) VALUES (?, ?, ?)')->execute([$userId, $pid, $note === '' ? null : $note]);
      log_activity($userId, 'shortlist_add', 'user', $pid, 'Added to shortlist');
      echo json_encode(['active' => true]);
    }
    exit;
  }

  echo json_encode(['error' => 'Unknown action']);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Server error']);
}
