<?php
// Admin AJAX endpoint — all actions require an authenticated admin.
header('Content-Type: application/json');
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

require_admin();
require_post();
require_csrf();

$adminId = $_SESSION['user_id'];
$action = $_GET['action'] ?? '';
$raw = file_get_contents('php://input');
$input = json_decode($raw ?: '', true) ?: [];
$id = (int) ($input['id'] ?? 0);

try {
  $db = getDB();

  switch ($action) {

    case 'suspend':
      if ($id <= 0) throw new Exception('Missing user');
      if ($id === $adminId) throw new Exception('You cannot suspend your own account');
      $stmt = $db->prepare('SELECT is_admin FROM users WHERE id = ?'); $stmt->execute([$id]);
      $target = $stmt->fetch();
      if (!$target) throw new Exception('User not found');
      if ((int) $target['is_admin'] === 1) {
        $adminCount = (int) $db->query('SELECT COUNT(*) FROM users WHERE is_admin = 1')->fetchColumn();
        if ($adminCount <= 1) throw new Exception('Cannot suspend the last admin account');
      }
      $db->prepare('UPDATE users SET is_suspended = 1 WHERE id = ?')->execute([$id]);
      log_activity($adminId, 'admin_suspend', 'user', $id, 'Suspended user');
      notification_add($id, 'admin', 'Your account has been suspended. Please contact support.');
      echo json_encode(['ok' => true, 'message' => 'User suspended']);
      exit;

    case 'activate':
      if ($id <= 0) throw new Exception('Missing user');
      $db->prepare('UPDATE users SET is_suspended = 0 WHERE id = ?')->execute([$id]);
      log_activity($adminId, 'admin_activate', 'user', $id, 'Reinstated user');
      echo json_encode(['ok' => true, 'message' => 'User reinstated']);
      exit;

    case 'make_admin':
      if ($id <= 0) throw new Exception('Missing user');
      $db->prepare('UPDATE users SET is_admin = 1, is_approved = 1 WHERE id = ?')->execute([$id]);
      log_activity($adminId, 'admin_grant', 'user', $id, 'Granted admin privileges');
      echo json_encode(['ok' => true, 'message' => 'Admin privileges granted']);
      exit;

    case 'revoke_admin':
      if ($id <= 0) throw new Exception('Missing user');
      if ($id === $adminId) throw new Exception('You cannot revoke your own admin access');
      $stmt = $db->prepare('SELECT is_admin FROM users WHERE id = ?'); $stmt->execute([$id]);
      $target = $stmt->fetch();
      if (!$target) throw new Exception('User not found');
      if ((int) $target['is_admin'] === 1) {
        $adminCount = (int) $db->query('SELECT COUNT(*) FROM users WHERE is_admin = 1')->fetchColumn();
        if ($adminCount <= 1) throw new Exception('Cannot revoke the last admin account');
      }
      $db->prepare('UPDATE users SET is_admin = 0 WHERE id = ?')->execute([$id]);
      log_activity($adminId, 'admin_revoke', 'user', $id, 'Revoked admin privileges');
      echo json_encode(['ok' => true, 'message' => 'Admin privileges revoked']);
      exit;

    case 'approve_user':
      if ($id <= 0) throw new Exception('Missing user');
      $db->prepare('UPDATE users SET is_approved = 1 WHERE id = ?')->execute([$id]);
      log_activity($adminId, 'admin_approve_user', 'user', $id, 'Approved user account');
      notification_add($id, 'admin', 'Your account has been approved by the admin. You can now access profiles and send messages!');
      echo json_encode(['ok' => true, 'message' => 'User account approved']);
      exit;

    case 'reject_user':
      if ($id <= 0) throw new Exception('Missing user');
      $db->prepare('UPDATE users SET is_approved = 0 WHERE id = ?')->execute([$id]);
      log_activity($adminId, 'admin_reject_user', 'user', $id, 'Rejected user account approval');
      notification_add($id, 'admin', 'Your account approval request was rejected. Please contact support.');
      echo json_encode(['ok' => true, 'message' => 'User account rejected']);
      exit;

    case 'dismiss_report':
      if ($id <= 0) throw new Exception('Missing report');
      $db->prepare("UPDATE reports SET status = 'dismissed' WHERE id = ?")->execute([$id]);
      log_activity($adminId, 'report_dismiss', 'report', $id, 'Dismissed report');
      echo json_encode(['ok' => true, 'message' => 'Report dismissed']);
      exit;

    case 'resolve_report':
      if ($id <= 0) throw new Exception('Missing report');
      $db->prepare("UPDATE reports SET status = 'resolved' WHERE id = ?")->execute([$id]);
      log_activity($adminId, 'report_resolve', 'report', $id, 'Resolved report');
      echo json_encode(['ok' => true, 'message' => 'Report resolved']);
      exit;

    case 'approve_verification':
      if ($id <= 0) throw new Exception('Missing request');
      $stmt = $db->prepare('SELECT user_id FROM verification_requests WHERE id = ?'); $stmt->execute([$id]);
      $uid = (int) $stmt->fetchColumn();
      if (!$uid) throw new Exception('Request not found');
      $db->prepare("UPDATE verification_requests SET status = 'approved' WHERE id = ?")->execute([$id]);
      $db->prepare('UPDATE users SET id_verified = 1 WHERE id = ?')->execute([$uid]);
      notification_add($uid, 'verification', 'Your identity verification was approved. You now have the ID Verified badge.');
      log_activity($adminId, 'verify_approve', 'verification_request', $id, 'Approved ID verification');
      echo json_encode(['ok' => true, 'message' => 'Verification approved']);
      exit;

    case 'reject_verification':
      if ($id <= 0) throw new Exception('Missing request');
      $db->prepare("UPDATE verification_requests SET status = 'rejected' WHERE id = ?")->execute([$id]);
      $stmt = $db->prepare('SELECT user_id FROM verification_requests WHERE id = ?'); $stmt->execute([$id]);
      $uid = (int) $stmt->fetchColumn();
      notification_add($uid, 'verification', 'Your identity verification was not approved. Please re-submit with a clear document.');
      log_activity($adminId, 'verify_reject', 'verification_request', $id, 'Rejected ID verification');
      echo json_encode(['ok' => true, 'message' => 'Verification rejected']);
      exit;

    case 'approve_media':
      if ($id <= 0) throw new Exception('Missing media');
      $db->prepare("UPDATE media_moderation SET status = 'approved' WHERE id = ?")->execute([$id]);
      log_activity($adminId, 'media_approve', 'media_moderation', $id, 'Approved media');
      echo json_encode(['ok' => true, 'message' => 'Media approved']);
      exit;

    case 'reject_media':
      if ($id <= 0) throw new Exception('Missing media');
      $db->prepare("UPDATE media_moderation SET status = 'rejected' WHERE id = ?")->execute([$id]);
      log_activity($adminId, 'media_reject', 'media_moderation', $id, 'Rejected media');
      echo json_encode(['ok' => true, 'message' => 'Media rejected']);
      exit;

    case 'delete_campaign':
      if ($id <= 0) throw new Exception('Missing campaign');
      $db->prepare('DELETE FROM email_campaigns WHERE id = ?')->execute([$id]);
      log_activity($adminId, 'campaign_delete', 'email_campaign', $id, 'Deleted campaign');
      echo json_encode(['ok' => true, 'message' => 'Campaign deleted']);
      exit;

    default:
      http_response_code(400);
      echo json_encode(['ok' => false, 'error' => 'Unknown action']);
      exit;
  }
} catch (Exception $e) {
  http_response_code(400);
  echo json_encode(['ok' => false, 'error' => $e->getMessage()]);
}
