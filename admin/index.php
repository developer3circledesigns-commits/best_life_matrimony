<?php
require_once __DIR__ . '/_header.php';

$db = getDB();
$view = $_GET['view'] ?? '';
$search = trim($_GET['q'] ?? '');

// KPI queries for the dashboard tab
$kpiUsers   = (int) $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
$kpiActive  = (int) $db->query('SELECT COUNT(*) FROM users WHERE is_suspended = 0')->fetchColumn();
$kpiAdmins  = (int) $db->query('SELECT COUNT(*) FROM users WHERE is_admin = 1')->fetchColumn();
$kpiReports = (int) $db->query("SELECT COUNT(*) FROM reports WHERE status = 'open'")->fetchColumn();
$kpiPendingV = (int) $db->query("SELECT COUNT(*) FROM verification_requests WHERE status = 'pending'")->fetchColumn();
$kpiPendingM = (int) $db->query("SELECT COUNT(*) FROM media_moderation WHERE status = 'pending'")->fetchColumn();
$kpiTodayReg = (int) $db->query('SELECT COUNT(*) FROM users WHERE DATE(created_at) = CURDATE()')->fetchColumn();
$kpiInts    = (int) $db->query("SELECT COUNT(*) FROM interests WHERE status = 'accepted'")->fetchColumn();
$kpiPendingApprovals = (int) $db->query('SELECT COUNT(*) FROM users WHERE is_approved = 0 AND is_admin = 0')->fetchColumn();

// Users list query
$sql = 'SELECT id, full_name, email, phone, looking_for, is_admin, is_suspended, email_verified, is_approved, created_at FROM users';
$params = [];
if ($search !== '') {
  $sql .= ' WHERE full_name LIKE ? OR email LIKE ?';
  $params = ['%' . $search . '%', '%' . $search . '%'];
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
      <div class="adm-card adm-kpi"><div class="label">Total Users</div><div class="value"><?php echo $kpiUsers; ?></div><div class="sub"><?php echo $kpiActive; ?> active</div></div>
      <div class="adm-card adm-kpi"><div class="label">Registered Today</div><div class="value"><?php echo $kpiTodayReg; ?></div></div>
      <div class="adm-card adm-kpi"><div class="label">Admin Accounts</div><div class="value"><?php echo $kpiAdmins; ?></div></div>
      <div class="adm-card adm-kpi"><div class="label">Pending Approvals</div><div class="value"><?php echo $kpiPendingApprovals; ?></div></div>
      <div class="adm-card adm-kpi"><div class="label">Open Reports</div><div class="value"><?php echo $kpiReports; ?></div></div>
      <div class="adm-card adm-kpi"><div class="label">Pending Verifications</div><div class="value"><?php echo $kpiPendingV; ?></div></div>
      <div class="adm-card adm-kpi"><div class="label">Media To Moderate</div><div class="value"><?php echo $kpiPendingM; ?></div></div>
      <div class="adm-card adm-kpi"><div class="label">Successful Matches</div><div class="value"><?php echo $kpiInts; ?></div></div>
    </div>
  <?php endif; ?>

  <div style="display:flex;justify-content:space-between;align-items:center;gap:1rem;margin-bottom:1rem;flex-wrap:wrap;">
    <h2 class="adm-h2" style="margin:0;">Manage Users</h2>
    <form method="get" action="./index.php" style="display:flex;gap:.5rem;">
      <input type="hidden" name="view" value="users">
      <input type="text" name="q" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search name or email" class="adm-input" style="width:260px;">
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
          <th>User</th><th>Email / Phone</th><th>Looking For</th><th>Flags</th><th>Joined</th><th class="adm-th-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
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
            <?php if ($u['is_admin']): ?>
              <button type="button" data-act="revoke_admin" data-id="<?php echo (int)$u['id']; ?>" class="btn btn-outline btn-sm">Revoke Admin</button>
            <?php else: ?>
              <button type="button" data-act="make_admin" data-id="<?php echo (int)$u['id']; ?>" class="btn btn-accent btn-sm">Make Admin</button>
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
