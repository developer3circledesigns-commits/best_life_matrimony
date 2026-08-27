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

$userId = $_SESSION['user_id'];

$action = $_GET['action'] ?? ($_POST['action'] ?? '');

try {
  $db = getDB();

  if ($action === 'list') {
    $res = $db->prepare('SELECT id, type, message, is_read, created_at FROM notifications WHERE user_id = ? ORDER BY id DESC LIMIT 50');
    $res->execute([$userId]);
    $rows = $res->fetchAll();
    $items = [];
    foreach ($rows as $r) {
      $items[] = [
        'id' => (int) $r['id'],
        'type' => $r['type'],
        'message' => $r['message'],
        'is_read' => (int) $r['is_read'],
        'created_at' => $r['created_at'],
      ];
    }
    $c = $db->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
    $c->execute([$userId]);
    echo json_encode(['notifications' => $items, 'unread' => (int) $c->fetchColumn()]);
    exit;
  }

  if ($action === 'mark_read') {
    $ids = [];
    if (isset($_POST['id'])) $ids = [(int) $_POST['id']];
    elseif (isset($_GET['id'])) $ids = [(int) $_GET['id']];
    $all = ($_GET['all'] ?? ($_POST['all'] ?? '0')) === '1' || empty($ids);
    if ($all) {
      $db->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?')->execute([$userId]);
    } else {
      $in = implode(',', array_fill(0, count($ids), '?'));
      $params = array_merge($ids, [$userId]);
      $db->prepare("UPDATE notifications SET is_read = 1 WHERE id IN ($in) AND user_id = ?")->execute($params);
    }
    echo json_encode(['ok' => true]);
    exit;
  }

  echo json_encode(['error' => 'Unknown action']);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['error' => 'Server error']);
}
