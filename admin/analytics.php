<?php
require_once __DIR__ . '/_header.php';

$db = getDB();

// Registrations overview
$totReg   = (int) $db->query('SELECT COUNT(*) FROM users')->fetchColumn();
$monthReg = (int) $db->query('SELECT COUNT(*) FROM users WHERE created_at >= DATE_FORMAT(CURDATE(), "%Y-%m-01")')->fetchColumn();
$last30   = (int) $db->query('SELECT COUNT(*) FROM users WHERE created_at >= CURDATE() - INTERVAL 30 DAY')->fetchColumn();

// Monthly registrations (last 6 months)
$months = [];
for ($i = 5; $i >= 0; $i--) {
  $key = date('Y-m', strtotime("-$i months"));
  $label = date('M', strtotime("-$i months"));
  $st = $db->prepare('SELECT COUNT(*) FROM users WHERE DATE_FORMAT(created_at, "%Y-%m") = ?');
  $st->execute([$key]);
  $c = (int) $st->fetchColumn();
  $months[] = ['label' => $label, 'count' => $c];
}
$maxM = max(1, array_reduce(array_column($months, 'count'), 'max') * 1);

// Engagement (all-time + last 30 days)
$engage = [
  ['label' => 'Messages Sent', 'total' => (int)$db->query('SELECT COUNT(*) FROM messages')->fetchColumn(), 'recent' => (int)$db->query('SELECT COUNT(*) FROM messages WHERE created_at >= CURDATE() - INTERVAL 30 DAY')->fetchColumn()],
  ['label' => 'Interests', 'total' => (int)$db->query('SELECT COUNT(*) FROM interests')->fetchColumn(), 'recent' => (int)$db->query('SELECT COUNT(*) FROM interests WHERE created_at >= CURDATE() - INTERVAL 30 DAY')->fetchColumn()],
  ['label' => 'Shortlists', 'total' => (int)$db->query('SELECT COUNT(*) FROM shortlists')->fetchColumn(), 'recent' => (int)$db->query('SELECT COUNT(*) FROM shortlists WHERE created_at >= CURDATE() - INTERVAL 30 DAY')->fetchColumn()],
  ['label' => 'Favourites', 'total' => (int)$db->query('SELECT COUNT(*) FROM favourites')->fetchColumn(), 'recent' => (int)$db->query('SELECT COUNT(*) FROM favourites WHERE created_at >= CURDATE() - INTERVAL 30 DAY')->fetchColumn()],
  ['label' => 'Profile Views', 'total' => (int)$db->query('SELECT COUNT(*) FROM profile_views')->fetchColumn(), 'recent' => (int)$db->query('SELECT COUNT(*) FROM profile_views WHERE created_at >= CURDATE() - INTERVAL 30 DAY')->fetchColumn()],
  ['label' => 'Reports Filed', 'total' => (int)$db->query('SELECT COUNT(*) FROM reports')->fetchColumn(), 'recent' => (int)$db->query('SELECT COUNT(*) FROM reports WHERE created_at >= CURDATE() - INTERVAL 30 DAY')->fetchColumn()],
];

// Match funnel
$intsTotal   = max(1, (int)$db->query('SELECT COUNT(*) FROM interests')->fetchColumn());
$intsPending = (int)$db->query("SELECT COUNT(*) FROM interests WHERE status = 'pending'")->fetchColumn();
$intsAccepted= (int)$db->query("SELECT COUNT(*) FROM interests WHERE status = 'accepted'")->fetchColumn();
$matchRate   = round(($intsAccepted / $intsTotal) * 100);
?>
<h2 class="adm-h2">Platform Analytics</h2>

<div class="adm-grid">
  <div class="adm-card adm-kpi"><div class="label">Total Registrations</div><div class="value"><?php echo $totReg; ?></div><div class="sub">+<?php echo $last30; ?> in last 30 days</div></div>
  <div class="adm-card adm-kpi"><div class="label">This Month</div><div class="value"><?php echo $monthReg; ?></div></div>
  <div class="adm-card adm-kpi"><div class="label">Match Rate</div><div class="value"><?php echo $matchRate; ?>%</div><div class="sub"><?php echo $intsAccepted; ?> of <?php echo $intsTotal; ?> interests</div></div>
  <div class="adm-card adm-kpi"><div class="label">Pending Interests</div><div class="value"><?php echo $intsPending; ?></div></div>
</div>

<div class="adm-card" style="margin-bottom:1.5rem;">
  <h3 class="adm-h3">Registrations — Last 6 Months</h3>
  <div class="adm-stat-row">
    <?php foreach ($months as $m): ?>
      <div class="bar">
        <div class="adm-cell-sub" style="font-weight:600;"><?php echo $m['label']; ?></div>
        <div class="adm-bar-track"><div class="adm-bar-fill" style="width:<?php echo round(($m['count'] / $maxM) * 100); ?>%;"></div></div>
        <div class="adm-cell-sub" style="color:var(--adm-ink);font-weight:700;"><?php echo $m['count']; ?></div>
      </div>
    <?php endforeach; ?>
  </div>
</div>

<div class="adm-card adm-card--scroll">
  <h3 class="adm-h3">Engagement Metrics</h3>
  <table class="adm-table">
    <thead><tr><th>Metric</th><th>All Time</th><th>Last 30 Days</th></tr></thead>
    <tbody>
      <?php foreach ($engage as $row): ?>
      <tr>
        <td class="adm-name"><?php echo $row['label']; ?></td>
        <td><?php echo $row['total']; ?></td>
        <td><?php echo $row['recent']; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<?php adm_close(); ?>
