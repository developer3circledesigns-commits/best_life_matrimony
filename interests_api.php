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

// Unapproved/unverified users cannot send/respond to interests
if (!can_interact()) {
  http_response_code(403);
  echo json_encode(['error' => 'Your profile needs verification or admin approval. Please verify email/phone or contact admin.']);
  exit;
}

$userId = $_SESSION['user_id'];

$action = $_GET['action'] ?? '';

try {
  $db = getDB();

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'status') {
    $pid = (int) ($_GET['user_id'] ?? 0);
    // Status from my perspective (I sent or received)
    $stmt = $db->prepare(
      'SELECT sender_id, receiver_id, status FROM interests
       WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) LIMIT 1'
    );
    $stmt->execute([$userId, $pid, $pid, $userId]);
    $row = $stmt->fetch();
    if (!$row) { echo json_encode(['status' => 'none']); exit; }
    echo json_encode([
      'status' => $row['status'],
      'direction' => (int)$row['sender_id'] === $userId ? 'sent' : 'received',
    ]);
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'list') {
    // Interests I've sent or received
    $received = $db->prepare(
      'SELECT i.id, i.status, i.created_at, u.id AS uid, u.full_name, u.gender, u.profile_photo
       FROM interests i JOIN users u ON u.id = i.sender_id
       WHERE i.receiver_id = ? ORDER BY i.created_at DESC'
    );
    $received->execute([$userId]);
    $sent = $db->prepare(
      'SELECT i.id, i.status, i.created_at, u.id AS uid, u.full_name, u.gender, u.profile_photo
       FROM interests i JOIN users u ON u.id = i.receiver_id
       WHERE i.sender_id = ? ORDER BY i.created_at DESC'
    );
    $sent->execute([$userId]);
    echo json_encode([
      'received' => $received->fetchAll(),
      'sent' => $sent->fetchAll(),
    ]);
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'express') {
    require_csrf();
    $raw = file_get_contents('php://input');
    $input = json_decode($raw ?: '', true) ?: [];
    $pid = (int) ($input['user_id'] ?? 0);
    if ($pid <= 0) { echo json_encode(['error' => 'Missing user']); exit; }
    if ($pid === $userId) { echo json_encode(['error' => 'Cannot send interest to yourself']); exit; }
    if (is_blocked($userId, $pid)) { echo json_encode(['error' => 'Unable to send interest']); exit; }

    // Existing interest either direction
    $stmt = $db->prepare('SELECT id, sender_id, receiver_id, status FROM interests WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?) LIMIT 1');
    $stmt->execute([$userId, $pid, $pid, $userId]);
    $row = $stmt->fetch();
    if ($row) {
      echo json_encode(['error' => 'An interest already exists between you and this member.']);
      exit;
    }
    $db->prepare('INSERT INTO interests (sender_id, receiver_id, status) VALUES (?, ?, ?)')->execute([$userId, $pid, 'pending']);
    $name = $db->prepare('SELECT full_name FROM users WHERE id = ?'); $name->execute([$userId]);
    notification_add($pid, 'interest', ($name->fetchColumn() ?: 'Someone') . ' expressed interest in your profile.');
    $target = $db->prepare('SELECT full_name FROM users WHERE id = ?'); $target->execute([$pid]);
    echo json_encode(['ok' => true, 'name' => $target->fetchColumn()]);
    log_activity($userId, 'interest_sent', 'user', $pid, 'Expressed interest');
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'respond') {
    require_csrf();
    $raw = file_get_contents('php://input');
    $input = json_decode($raw ?: '', true) ?: [];
    $interestId = (int) ($input['interest_id'] ?? 0);
    $status = $input['status'] ?? '';
    if (!in_array($status, ['accepted', 'declined'], true)) { echo json_encode(['error' => 'Invalid status']); exit; }
    // Only the receiver can accept/decline
    $upd = $db->prepare('UPDATE interests SET status = ? WHERE id = ? AND receiver_id = ?');
    $upd->execute([$status, $interestId, $userId]);
    if ($upd->rowCount() === 0) { echo json_encode(['error' => 'Interest not found']); exit; }
    $inf = $db->prepare('SELECT sender_id FROM interests WHERE id = ?'); $inf->execute([$interestId]);
    $sender = (int)$inf->fetchColumn();
    if ($status === 'accepted') {
      notification_add($sender, 'interest', 'Your interest was accepted — you are now connected.');
    }
    echo json_encode(['ok' => true]);
    log_activity($userId, 'interest_' . $status, 'interest', $interestId, $status === 'accepted' ? 'Accepted interest' : 'Declined interest');
    exit;
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'withdraw') {
    require_csrf();
    $raw = file_get_contents('php://input');
    $input = json_decode($raw ?: '', true) ?: [];
    $pid = (int) ($input['profile_id'] ?? 0);
    $db->prepare('UPDATE interests SET status = ? WHERE sender_id = ? AND receiver_id = ?')->execute(['withdrawn', $userId, $pid]);
    echo json_encode(['ok' => true]);
    log_activity($userId, 'interest_withdrawn', 'user', $pid, 'Withdrew interest');
    exit;
  }

  echo json_encode(['error' => 'Unknown action']);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Server error']);
}
