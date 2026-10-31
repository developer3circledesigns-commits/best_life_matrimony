<?php
require_once __DIR__ . '/_header.php';

$db = getDB();
$view = $_GET['view'] ?? '';
$search = trim($_GET['q'] ?? '');

// Single combined query for KPI counts
$kpiCounts = [];
try {
  // Query 1: Users stats
  $usersStmt = $db->query("SELECT 
    COUNT(*) AS total_users,
    SUM(CASE WHEN is_suspended = 0 THEN 1 ELSE 0 END) AS active_users,
    SUM(CASE WHEN is_admin = 1 THEN 1 ELSE 0 END) AS admin_count,
    SUM(CASE WHEN is_approved = 0 AND email_verified = 0 AND phone_verified = 0 AND id_verified = 0 AND is_admin = 0 THEN 1 ELSE 0 END) AS pending_approvals_count
  FROM users");
  $u = $usersStmt->fetch();
  $kpiCounts['total_users'] = (int)($u['total_users'] ?? 0);
  $kpiCounts['active_users'] = (int)($u['active_users'] ?? 0);
  $kpiCounts['admin_count'] = (int)($u['admin_count'] ?? 0);
  $kpiCounts['pending_approvals'] = (int)($u['pending_approvals_count'] ?? 0);
} catch (Exception $e) {}

// Query 2: Reports and moderation
try {
  $reportsStmt = $db->query("SELECT COUNT(*) AS open_reports FROM reports WHERE status = 'open'");
  $r = $reportsStmt->fetch();
  $kpiCounts['open_reports'] = (int)($r['open_reports'] ?? 0);
} catch (Exception $e) {}

try {
  $vmStmt = $db->query("SELECT COUNT(*) AS pending_verifications FROM verification_requests WHERE status = 'pending'");
  $v = $vmStmt->fetch();
  $kpiCounts['pending_verifications'] = (int)($v['pending_verifications'] ?? 0);
} catch (Exception $e) {}

try {
  $mmStmt = $db->query("SELECT COUNT(*) AS media_pending FROM media_moderation WHERE status = 'pending'");
  $m = $mmStmt->fetch();
  $kpiCounts['media_pending'] = (int)($m['media_pending'] ?? 0);
} catch (Exception $e) {}

// Query 3: Today's registrations
try {
  $regStmt = $db->query("SELECT COUNT(*) AS today_registrations FROM users WHERE DATE(created_at) = CURDATE()");
  $rg = $regStmt->fetch();
  $kpiCounts['today_registrations'] = (int)($rg['today_registrations'] ?? 0);
} catch (Exception $e) {}

// Query 4: Accepted interests
try {
  $intStmt = $db->query("SELECT COUNT(*) AS accepted_interests FROM interests WHERE status = 'accepted'");
  $i = $intStmt->fetch();
  $kpiCounts['accepted_interests'] = (int)($i['accepted_interests'] ?? 0);
} catch (Exception $e) {}

// Users list query - searchable by ID, name, email, phone, and display ID (BLM-YYYY-XXXXX)
$sql = 'SELECT id, full_name, email, phone, looking_for, is_admin, is_suspended, email_verified, is_approved, created_at FROM users';
$params = [];
if ($search !== '') {
  // Allow search by numeric ID, display ID, name, email, phone
  // Strip BLM- prefix if present (e.g. BLM-2026-00005 -> 5)
  $searchId = preg_replace('/^BLM-\d+-0*/i', '', $search);
  $searchId = ctype_digit($searchId) ? (int)$searchId : -1;
  $sql .= ' WHERE full_name LIKE ? OR email LIKE ? OR phone LIKE ? OR CAST(id AS CHAR) LIKE ? OR id = ?';
  $params = ['%' . $search . '%', '%' . $search . '%', '%' . $search . '%', '%' . $search . '%', $searchId];
}
$sql .= ' ORDER BY id DESC LIMIT 200';
$us = $db->prepare($sql);
$us->execute($params);
$users = $us->fetchAll();
?>
<div class="adm-alert ok">Admin console for moderators. Actions are logged and audited automatically.</div>

<?php if ($view === 'users' || $view === ''): ?>
  <?php if ($view === ''): ?>
    <div class="adm-grid">
      <div class="adm-card adm-kpi"><div class="label">Total Users</div><div class="value"><?php echo $kpiCounts['total_users'] ?? 0; ?></div><div class="sub"><?php echo $kpiCounts['active_users'] ?? 0; ?> active</div></div>
      <div class="adm-card adm-kpi"><div class="label">Registered Today</div><div class="value"><?php echo $kpiCounts['today_registrations'] ?? 0; ?></div></div>
      <div class="adm-card adm-kpi"><div class="label">Admin Accounts</div><div class="value"><?php echo $kpiCounts['admin_count'] ?? 0; ?></div></div>
      <div class="adm-card adm-kpi"><div class="label">Pending Approvals</div><div class="value"><?php echo $kpiCounts['pending_approvals'] ?? 0; ?></div></div>
      <div class="adm-card adm-kpi"><div class="label">Open Reports</div><div class="value"><?php echo $kpiCounts['open_reports'] ?? 0; ?></div></div>
      <div class="adm-card adm-kpi"><div class="label">Pending Verifications</div><div class="value"><?php echo $kpiCounts['pending_verifications'] ?? 0; ?></div></div>
      <div class="adm-card adm-kpi"><div class="label">Media To Moderate</div><div class="value"><?php echo $kpiCounts['media_pending'] ?? 0; ?></div></div>
      <div class="adm-card adm-kpi"><div class="label">Successful Matches</div><div class="value"><?php echo $kpiCounts['accepted_interests'] ?? 0; ?></div></div>
    </div>
  <?php endif; ?>

  <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1rem;flex-wrap:wrap;">
    <h2 class="adm-h2" style="margin:0;">Manage Users</h2>
    <form method="get" action="./index.php" style="display:flex;gap:.5rem;">
      <input type="hidden" name="view" value="users">
      <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search ID, name, email or phone (e.g. 5 or BLM-2026-00005)" class="adm-input" style="width:320px;">
      <button type="submit" class="btn btn-accent">Search</button>
    </form>
  </div>

  <div class="adm-card adm-card--scroll">
    <?php if (!$users): ?>
      <div class="adm-empty">No users found.</div>
    <?php else: ?>
    <table class="adm-table">
      <thead>
        <tr>
          <th>ID</th><th>User</th><th>Email / Phone</th><th>Looking For</th><th>Flags</th><th>Joined</th><th class="adm-th-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td class="adm-nowrap"><strong>#<?php echo (int)$u['id']; ?></strong><br><span class="adm-cell-sub">BLM-<?php echo date('Y', strtotime($u['created_at'])); ?>-<?php echo str_pad((string)$u['id'], 5, '0', STR_PAD_LEFT); ?></span></td>
          <td class="adm-name">
            <span class="adm-user-avatar"><?php echo strtoupper(mb_substr($u['full_name'], 0, 1)); ?></span>
            <?php echo htmlspecialchars($u['full_name']); ?>
          </td>
          <td><?php echo htmlspecialchars($u['email']); ?><br><span class="adm-cell-sub"><?php echo htmlspecialchars($u['phone'] ?: '—'); ?></span></td>
          <td><?php echo htmlspecialchars($u['looking_for'] ?: '—'); ?></td>
          <td>
            <?php if ($u['is_admin']): ?><span class="pill pill-blue">Admin</span><?php endif; ?>
            <?php if ($u['is_suspended']): ?><span class="pill pill-red">Suspended</span><?php else: ?><span class="pill pill-green">Active</span><?php endif; ?>
            <?php if ($u['email_verified']): ?><span class="pill pill-green">Verified</span><?php endif; ?>
            <?php if ($u['is_approved']): ?><span class="pill pill-green">Approved</span><?php else: ?><span class="pill pill-amber">Pending Approval</span><?php endif; ?>
          </td>
          <td class="adm-nowrap"><?php echo date('M j, Y', strtotime($u['created_at'])); ?></td>
          <td class="adm-actions">
            <a href="../profile_view.php?id=<?php echo (int)$u['id']; ?>" target="_blank" class="btn btn-outline btn-sm">View</a>
            <?php if (!$u['is_admin']): ?>
              <?php if ($u['is_approved']): ?>
                <button type="button" data-act="reject_user" data-id="<?php echo (int)$u['id']; ?>" class="btn btn-outline btn-sm">Reject</button>
              <?php else: ?>
                <button type="button" data-act="approve_user" data-id="<?php echo (int)$u['id']; ?>" class="btn btn-accent btn-sm">Approve</button>
              <?php endif; ?>
            <?php endif; ?>
            <?php if ($u['is_suspended']): ?>
              <button type="button" data-act="activate" data-id="<?php echo (int)$u['id']; ?>" class="btn btn-outline btn-sm">Reinstate</button>
            <?php else: ?>
              <button type="button" data-act="suspend" data-id="<?php echo (int)$u['id']; ?>" class="btn btn-danger btn-sm">Suspend</button>
            <?php endif; ?>

          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>
<?php endif; ?>
<script src="<?php echo asset('js/admin.js'); ?>"></script>
<?php adm_close(); ?>
