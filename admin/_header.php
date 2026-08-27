<?php
require_once __DIR__ . '/../includes/db.php';
require_admin();

$admPage = basename($_SERVER['PHP_SELF']) === 'index.php' && ($_GET['view'] ?? '') === '' ? 'dashboard' : (basename($_SERVER['PHP_SELF']) === 'index.php' ? ($_GET['view'] ?? 'users') : str_replace('.php', '', basename($_SERVER['PHP_SELF'])));
$admTabs = [
  'dashboard' => 'Dashboard',
  'users'     => 'Users',
  'reports'   => 'Reports',
  'verification' => 'Verification',
  'moderation'   => 'Media Moderation',
  'analytics'    => 'Analytics',
  'logs'         => 'Activity Logs',
  'campaign'     => 'Email Campaigns',
];
$pageTitle = 'Admin — BestLife Matrimony';
$pageDescription = 'BestLife Matrimony admin & analytics dashboard.';
$pageHeadExtra = '<link rel="stylesheet" href="' . asset('css/admin.css') . '">';
require_once __DIR__ . '/../includes/header.php';
?>
<main class="flex-1 bg-[#f4f2ee] text-[#3a0c15]" style="min-height:100vh;">
  <div class="adm-wrap">
    <div class="adm-head">
      <div>
        <h1>Admin &amp; Analytics</h1>
        <p>Manage users, moderation queues, campaigns and platform analytics.</p>
      </div>
      <a href="./../index.php" class="btn btn-outline">&larr; Back to site</a>
    </div>
    <nav class="adm-tabs" aria-label="Admin sections">
      <?php foreach ($admTabs as $key => $label): ?>
        <?php $href = $key === 'dashboard' ? './index.php' : './' . $key . '.php'; ?>
        <?php $href = $key === 'users' ? './index.php?view=users' : $href; ?>
        <a href="<?php echo $href; ?>" class="<?php echo ($admPage === $key) ? 'active' : ''; ?>"><?php echo $label; ?></a>
      <?php endforeach; ?>
    </nav>
<?php
// End of partial — pages call adm_close() to close the document.
// The admin panel renders standalone: no site navbar or footer.
function adm_close(): void {
  // Close <main> and the two wrapper divs opened by includes/header.php, then the document.
  echo "</main>\n</div>\n</div>\n</body>\n</html>\n";
}
