<?php
require_once __DIR__ . '/_header.php';

$db = getDB();
$limit = (int) ($_GET['limit'] ?? 200);
if ($limit < 1 || $limit > 1000) $limit = 200;
$filter = trim($_GET['filter'] ?? '');

$sql = "SELECT l.*, u.full_name AS uname FROM activity_logs l LEFT JOIN users u ON u.id = l.user_id";
$params = [];
if ($filter !== '') {
  $allowed = ['login','register','profile_update','profile_view','favourite_add','favourite_remove','interest_sent','interest_accepted','interest_declined','interest_withdrawn','shortlist_add','shortlist_remove','message_sent','report','block','unblock','verification_request','admin_suspend','admin_activate','admin_grant','admin_revoke','report_dismiss','report_resolve','verify_approve','verify_reject','media_approve','media_reject','campaign_delete'];
  if (in_array($filter, $allowed, true)) {
    $sql .= ' WHERE l.action = ?';
    $params[] = $filter;
  }
}
$sql .= " ORDER BY l.id DESC LIMIT " . $limit;
$st = $db->prepare($sql);
$st->execute($params);
$logs = $st->fetchAll();
?>
<h2 class="adm-h2">User Activity Logs</h2>

<form method="get" action="./logs.php" style="display:flex;gap:.5rem;margin-bottom:1rem;flex-wrap:wrap;align-items:center;">
  <select name="filter" class="adm-select" style="width:auto;">
    <option value="">All actions</option>
    <?php $all = ['login','register','profile_update','profile_view','favourite_add','favourite_remove','interest_sent','interest_accepted','interest_declined','interest_withdrawn','shortlist_add','shortlist_remove','message_sent','report','block','unblock','verification_request','admin_suspend','admin_activate','admin_grant','admin_revoke','report_dismiss','report_resolve','verify_approve','verify_reject','media_approve','media_reject','campaign_delete'];
      foreach ($all as $a): ?>
      <option value="<?php echo $a; ?>" <?php echo $a === $filter ? 'selected' : ''; ?>><?php echo $a; ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit" class="btn btn-accent">Filter</button>
</form>

<div class="adm-card adm-card--scroll">
  <?php if (!$logs): ?>
    <div class="adm-empty">No activity logged.</div>
  <?php else: ?>
  <table class="adm-table adm-log">
    <thead><tr><th>ID</th><th>User</th><th>Action</th><th>Details</th><th>IP</th><th>Time</th></tr></thead>
    <tbody>
      <?php foreach ($logs as $l): ?>
      <tr>
        <td>#<?php echo (int)$l['id']; ?></td>
        <td class="adm-name"><?php echo htmlspecialchars($l['uname'] ?: ('user #' . $l['user_id'])); ?></td>
        <td><?php echo htmlspecialchars($l['action']); ?></td>
        <td class="adm-muted"><?php echo htmlspecialchars($l['details'] ?: '—'); ?></td>
        <td><?php echo htmlspecialchars($l['ip'] ?: '—'); ?></td>
        <td class="adm-nowrap"><?php echo date('M j, Y g:i A', strtotime($l['created_at'])); ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
<?php adm_close(); ?>
