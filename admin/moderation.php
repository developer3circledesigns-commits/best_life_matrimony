<?php
require_once __DIR__ . '/_header.php';

$db = getDB();
$status = $_GET['status'] ?? 'pending';
if (!in_array($status, ['pending', 'approved', 'rejected'], true)) $status = 'pending';
$limit = (int) ($_GET['limit'] ?? 100);
if ($limit < 1 || $limit > 500) $limit = 100;

$stmt = $db->prepare(
  'SELECT mm.*, u.full_name
   FROM media_moderation mm
   JOIN users u ON u.id = mm.user_id
   WHERE mm.status = ?
   ORDER BY mm.created_at DESC
   LIMIT ' . $limit
);
$stmt->execute([$status]);
$rows = $stmt->fetchAll();
?>
<h2 class="adm-h2">Media Moderation Queue</h2>

<div class="adm-tabs">
  <?php foreach (['pending', 'approved', 'rejected'] as $s): ?>
    <a href="./moderation.php?status=<?php echo $s; ?>" class="<?php echo $s === $status ? 'active' : ''; ?>"><?php echo ucfirst($s); ?></a>
  <?php endforeach; ?>
</div>

<div class="adm-card adm-card--scroll">
  <?php if (!$rows): ?>
    <div class="adm-empty">No <?php echo htmlspecialchars($status); ?> media items.</div>
  <?php else: ?>
  <table class="adm-table">
    <thead><tr><th>User</th><th>Field</th><th>File</th><th>Type</th><th>Size</th><th>Status</th><th>Uploaded</th><th class="adm-th-right">Actions</th></tr></thead>
    <tbody>
      <?php foreach ($rows as $r): ?>
      <tr>
        <td class="adm-name">
          <span class="adm-user-avatar"><?php echo strtoupper(mb_substr($r['full_name'], 0, 1)); ?></span>
          <?php echo htmlspecialchars($r['full_name']); ?> <span class="pill pill-gray">#<?php echo (int)$r['user_id']; ?></span>
        </td>
        <td><span class="pill pill-blue"><?php echo htmlspecialchars($r['field']); ?></span></td>
        <td class="adm-clamp" style="max-width:200px;"><?php echo htmlspecialchars($r['file_name'] ?: '—'); ?></td>
        <td><?php echo htmlspecialchars($r['mime'] ?: '—'); ?></td>
        <td><?php echo $r['size'] ? round($r['size'] / 1024) . ' KB' : '—'; ?></td>
        <td><span class="pill pill-<?php echo $r['status'] === 'approved' ? 'green' : ($r['status'] === 'rejected' ? 'red' : 'amber'); ?>"><?php echo $r['status']; ?></span></td>
        <td class="adm-nowrap"><?php echo date('M j, Y', strtotime($r['created_at'])); ?></td>
        <td class="adm-actions">
          <a href="../profile_view.php?id=<?php echo (int)$r['user_id']; ?>" target="_blank" class="btn btn-outline btn-sm">View</a>
          <?php if ($status === 'pending'): ?>
            <button type="button" data-act="approve_media" data-id="<?php echo (int)$r['id']; ?>" class="btn btn-accent btn-sm">Approve</button>
            <button type="button" data-act="reject_media" data-id="<?php echo (int)$r['id']; ?>" class="btn btn-danger btn-sm">Reject</button>
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
