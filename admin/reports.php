<?php
require_once __DIR__ . '/_header.php';

$db = getDB();
$status = $_GET['status'] ?? 'open';
if (!in_array($status, ['open', 'resolved', 'dismissed'], true)) $status = 'open';

$stmt = $db->prepare(
  'SELECT r.*, ru.full_name AS reporter_name, pdu.full_name AS reported_name
   FROM reports r
   JOIN users ru ON ru.id = r.reporter_id
   JOIN users pdu ON pdu.id = r.reported_id
   WHERE r.status = ?
   ORDER BY r.created_at DESC'
);
$stmt->execute([$status]);
$reports = $stmt->fetchAll();
?>
<h2 class="adm-h2">Reported Profiles</h2>

<div class="adm-tabs">
  <?php foreach (['open', 'resolved', 'dismissed'] as $s): ?>
    <a href="./reports.php?status=<?php echo $s; ?>" class="<?php echo $s === $status ? 'active' : ''; ?>"><?php echo ucfirst($s); ?></a>
  <?php endforeach; ?>
</div>

<div class="adm-card adm-card--scroll">
  <?php if (!$reports): ?>
    <div class="adm-empty">No <?php echo htmlspecialchars($status); ?> reports.</div>
  <?php else: ?>
  <table class="adm-table">
    <thead><tr><th>Reported User</th><th>Reason</th><th>Details</th><th>Reporter</th><th>Date</th><th class="adm-th-right">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($reports as $r): ?>
      <tr>
        <td class="adm-name">
          <?php echo htmlspecialchars($r['reported_name']); ?> <span class="pill pill-gray">#<?php echo (int)$r['reported_id']; ?></span>
        </td>
        <td><?php echo htmlspecialchars($r['reason']); ?></td>
        <td class="adm-clamp" style="max-width:280px;"><?php echo htmlspecialchars($r['details'] ?: '—'); ?></td>
        <td><?php echo htmlspecialchars($r['reporter_name']); ?></td>
        <td class="adm-nowrap"><?php echo date('M j, Y', strtotime($r['created_at'])); ?></td>
        <td class="adm-actions">
          <a href="../profile_view.php?id=<?php echo (int)$r['reported_id']; ?>" target="_blank" class="btn btn-outline btn-sm">View Profile</a>
          <?php if ($status === 'open'): ?>
            <button type="button" data-act="resolve_report" data-id="<?php echo (int)$r['id']; ?>" class="btn btn-accent btn-sm">Resolve</button>
          <?php endif; ?>
          <button type="button" data-act="dismiss_report" data-id="<?php echo (int)$r['id']; ?>" class="btn btn-outline btn-sm">Dismiss</button>
        </td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
<script src="<?php echo asset('js/admin.js'); ?>"></script>
<?php adm_close(); ?>
