<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

header('Content-Type: application/json');

require_login();

// Admin users cannot use user APIs - return 403
if (is_admin()) {
  http_response_code(403);
  echo json_encode(['error' => 'Admin users cannot access user APIs']);
  exit;
}

// Unapproved/unverified users cannot use messaging
if (!can_interact()) {
  http_response_code(403);
  echo json_encode(['error' => 'Your profile needs verification or admin approval. Please verify email/phone or contact admin.']);
  exit;
}

$userId = $_SESSION['user_id'];

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

try {
  $db = getDB();

  if ($method === 'GET' && $action === 'conversations') {
    // All distinct users the logged-in user has conversed with
    $stmt = $db->prepare(
      'SELECT u.id, u.full_name, u.profile_photo,
              (SELECT COUNT(*) FROM messages m2 WHERE m2.receiver_id = ? AND m2.sender_id = u.id AND m2.is_read = 0) AS unread,
              (SELECT MAX(m3.created_at) FROM messages m3 WHERE (m3.sender_id = ? AND m3.receiver_id = u.id) OR (m3.sender_id = u.id AND m3.receiver_id = ?)) AS last_at,
              (SELECT m4.body FROM messages m4 WHERE (m4.sender_id = ? AND m4.receiver_id = u.id) OR (m4.sender_id = u.id AND m4.receiver_id = ?) ORDER BY m4.id DESC LIMIT 1) AS last_body
       FROM (
         SELECT DISTINCT CASE WHEN sender_id = ? THEN receiver_id ELSE sender_id END AS other_id
         FROM messages WHERE sender_id = ? OR receiver_id = ?
       ) t JOIN users u ON u.id = t.other_id
       ORDER BY last_at DESC'
    );
    $stmt->execute([$userId, $userId, $userId, $userId, $userId, $userId, $userId, $userId]);
    $rows = $stmt->fetchAll();
    $conversations = [];
    foreach ($rows as $r) {
      $conversations[] = [
        'user_id' => (int) $r['id'],
        'name' => $r['full_name'] ?? '',
        'photo' => photo_url($r['profile_photo']),
        'unread' => (int) $r['unread'],
        'last_at' => $r['last_at'],
        'last_body' => $r['last_body'] ?? '',
      ];
    }
    echo json_encode(['conversations' => $conversations]);
    exit;
  }

  if ($method === 'GET' && $action === 'thread') {
    $otherId = (int) ($_GET['user_id'] ?? 0);
    if (!$otherId) { echo json_encode(['error' => 'Missing user']); exit; }
    $stmt = $db->prepare(
      'SELECT id, sender_id, receiver_id, body, is_read, created_at
       FROM messages
       WHERE (sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)
       ORDER BY id ASC'
    );
    $stmt->execute([$userId, $otherId, $otherId, $userId]);
    $rows = $stmt->fetchAll();
    $messages = [];
    foreach ($rows as $r) {
      $messages[] = [
        'id' => (int) $r['id'],
        'sender_id' => (int) $r['sender_id'],
        'body' => $r['body'],
        'created_at' => $r['created_at'],
      ];
    }
    // Mark incoming as read
    $db->prepare('UPDATE messages SET is_read = 1 WHERE sender_id = ? AND receiver_id = ? AND is_read = 0')
       ->execute([$otherId, $userId]);
    // Other user basic info
    $p = $db->prepare('SELECT id, full_name, profile_photo FROM users WHERE id = ?');
    $p->execute([$otherId]);
    $other = $p->fetch();
    echo json_encode([
      'messages' => $messages,
      'other' => $other ? [
        'user_id' => (int) $other['id'],
        'name' => $other['full_name'],
        'photo' => photo_url($other['profile_photo']),
      ] : null,
    ]);
    exit;
  }

  if ($method === 'POST' && $action === 'send') {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw ?: '', true) ?: [];
    // CSRF via header for JSON
    if (!isset($_SERVER['HTTP_X_CSRF_TOKEN']) || !hash_equals(csrf_token(), $_SERVER['HTTP_X_CSRF_TOKEN'])) {
      http_response_code(403);
      echo json_encode(['error' => 'Invalid request']);
      exit;
    }
    $receiverId = (int) ($data['receiver_id'] ?? 0);
    $body = trim($data['body'] ?? '');
    if (!$receiverId) { echo json_encode(['error' => 'Missing receiver']); exit; }
    if ($body === '') { echo json_encode(['error' => 'Message cannot be empty']); exit; }
    if (mb_strlen($body) > 2000) { echo json_encode(['error' => 'Message too long']); exit; }

    $db->prepare('INSERT INTO messages (sender_id, receiver_id, body) VALUES (?, ?, ?)')
       ->execute([$userId, $receiverId, $body]);
    $id = (int) $db->lastInsertId();
    notification_add($receiverId, 'message', 'You received a new message.');
    echo json_encode(['ok' => true, 'id' => $id]);
    log_activity($userId, 'message_sent', 'user', $receiverId, 'Sent a message');
    exit;
  }

  echo json_encode(['error' => 'Unknown action']);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Server error']);
}
