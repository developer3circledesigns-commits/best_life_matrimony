<?php
require_once __DIR__ . '/_header.php';

$db = getDB();
$status = $_GET['status'] ?? 'pending';
if (!in_array($status, ['pending', 'approved', 'rejected'], true)) $status = 'pending';

$stmt = $db->prepare(
  'SELECT vr.*, u.full_name, u.email
   FROM verification_requests vr
   JOIN users u ON u.id = vr.user_id
   WHERE vr.status = ?
   ORDER BY vr.created_at DESC'
);
$stmt->execute([$status]);
$reqs = $stmt->fetchAll();
?>
<h2 class="adm-h2">Identity Verification Requests</h2>

<div class="adm-tabs">
  <?php foreach (['pending', 'approved', 'rejected'] as $s): ?>
    <a href="./verification.php?status=<?php echo $s; ?>" class="<?php echo $s === $status ? 'active' : ''; ?>"><?php echo ucfirst($s); ?></a>
  <?php endforeach; ?>
</div>

<div class="adm-card adm-card--scroll">
  <?php if (!$reqs): ?>
    <div class="adm-empty">No <?php echo htmlspecialchars($status); ?> verification requests.</div>
  <?php else: ?>
  <table class="adm-table">
    <thead><tr><th>User</th><th>Email</th><th>Type</th><th>Status</th><th>Submitted</th><th class="adm-th-right">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($reqs as $r): ?>
      <tr>
        <td class="adm-name"><?php echo htmlspecialchars($r['full_name']); ?> <span class="pill pill-gray">#<?php echo (int)$r['user_id']; ?></span></td>
        <td><?php echo htmlspecialchars($r['email']); ?></td>
        <td><span class="pill pill-blue"><?php echo htmlspecialchars($r['type']); ?></span></td>
        <td><span class="pill pill-<?php echo $r['status'] === 'approved' ? 'green' : ($r['status'] === 'rejected' ? 'red' : 'amber'); ?>"><?php echo $r['status']; ?></span></td>
        <td class="adm-nowrap"><?php echo date('M j, Y', strtotime($r['created_at'])); ?></td>
        <td class="adm-actions">
          <a href="../profile_view.php?id=<?php echo (int)$r['user_id']; ?>" target="_blank" class="btn btn-outline btn-sm">View</a>
          <?php if ($status === 'pending'): ?>
            <button type="button" data-act="approve_verification" data-id="<?php echo (int)$r['id']; ?>" class="btn btn-accent btn-sm">Approve</button>
            <button type="button" data-act="reject_verification" data-id="<?php echo (int)$r['id']; ?>" class="btn btn-danger btn-sm">Reject</button>
          <?php endif; ?>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
<script src="<?php echo asset('js/admin.js'); ?>"></script>
<?php adm_close(); ?>
