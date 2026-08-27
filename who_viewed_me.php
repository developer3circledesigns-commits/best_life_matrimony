<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

// Admin users cannot access who viewed me - redirect to admin dashboard
if (is_admin()) {
  header('Location: ./admin/index.php');
  exit;
}

$userId = $_SESSION['user_id'];

function fmtProfile($r) {
  $age = null;
  if (!empty($r['date_of_birth'])) {
    $age = (new DateTime())->diff(new DateTime($r['date_of_birth']))->y;
  }
  return [
    'id' => (int) $r['id'],
    'name' => $r['full_name'] ?? '',
    'gender' => $r['gender'] ?? '',
    'age' => $age,
    'city' => $r['city'] ?? '',
    'occupation' => $r['occupation'] ?? '',
    'education' => $r['highest_education'] ?? '',
    'photo' => photo_url($r['profile_photo']),
    'viewed_at' => $r['last_viewed'] ?? '',
    'views' => (int) $r['views'],
  ];
}

$viewers = [];
$totalViews = 0;
try {
  $db = getDB();
  // Unique viewers of my profile, most recent view, and total view count per viewer
  $stmt = $db->prepare(
    'SELECT u.id, u.full_name, u.gender, u.date_of_birth, u.city, u.occupation, u.highest_education, u.profile_photo,
            MAX(pv.created_at) AS last_viewed, COUNT(*) AS views
     FROM profile_views pv
     JOIN users u ON u.id = pv.viewer_id
     WHERE pv.profile_id = ?
     GROUP BY u.id
     ORDER BY last_viewed DESC'
  );
  $stmt->execute([$userId]);
  $viewers = array_map('fmtProfile', $stmt->fetchAll());

  $cnt = $db->prepare('SELECT COUNT(*) FROM profile_views WHERE profile_id = ?');
  $cnt->execute([$userId]);
  $totalViews = (int) $cnt->fetchColumn();
} catch (Exception $e) { /* ignore */ }

$pageTitle = 'Who Viewed My Profile — BestLife Matrimony';
$pageDescription = 'See who has viewed your profile on BestLife Matrimony.';
$pageHeadExtra = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>
<style>
  .wv-wrap{max-width:760px;margin:0 auto;padding:24px 16px 48px;font-family:Inter,system-ui,sans-serif;color:#1a1a1a}
  .wv-head{background:#fff;border:1px solid #eee;padding:24px;display:flex;align-items:center;gap:14px}
  .wv-head h1{font-size:22px;margin:0}
  .wv-head p{margin:4px 0 0;color:#666;font-size:13px}
  .wv-list{margin-top:16px;display:flex;flex-direction:column;gap:12px}
  .wv-card{display:flex;align-items:center;gap:14px;background:#fff;border:1px solid #eee;padding:16px}
  .wv-avatar{width:52px;height:52px;border-radius:50%;overflow:hidden;background:#f0ece6;display:flex;align-items:center;justify-content:center;color:#999;flex-shrink:0}
  .wv-avatar img{width:100%;height:100%;object-fit:cover}
  .wv-avatar i{font-size:26px}
  .wv-mid{flex:1;min-width:0}
  .wv-name{font-weight:700;font-size:15px}
  .wv-meta{font-size:13px;color:#666}
  .wv-time{font-size:12px;color:#999;white-space:nowrap}
  .wv-empty{background:#fff;border:1px dashed #ddd;padding:40px;text-align:center;color:#888;font-size:14px;margin-top:16px}
</style>
<main class="bg-[#f4f2ee]" style="min-height:100vh;">
  <div class="wv-wrap">
    <div class="wv-head">
      <i class="bi bi-people" style="font-size:34px;color:#6b1020;"></i>
      <div>
        <h1>Who Viewed My Profile</h1>
        <p><?php echo $totalViews; ?> total profile views · <?php echo count($viewers); ?> unique members</p>
      </div>
    </div>

    <?php if (!$viewers): ?>
      <div class="wv-empty"><i class="bi bi-eye-slash" style="font-size:34px;display:block;margin-bottom:8px;"></i>No one has viewed your profile yet. <a href="./matches.php" style="color:#6b1020;font-weight:600;">Browse matches</a> to get noticed.</div>
    <?php else: ?>
      <div class="wv-list">
        <?php foreach ($viewers as $v): ?>
          <a href="./profile_view.php?id=<?php echo $v['id']; ?>" class="wv-card" style="text-decoration:none;color:inherit;">
            <div class="wv-avatar">
              <?php if ($v['photo']): ?><img src="<?php echo htmlspecialchars($v['photo']); ?>" alt=""><?php else: ?><i class="bi bi-person-fill"></i><?php endif; ?>
            </div>
            <div class="wv-mid">
              <div class="wv-name"><?php echo htmlspecialchars($v['name']); ?></div>
              <div class="wv-meta">
                <?php
                  $parts = [];
                  if ($v['age']) $parts[] = $v['age'] . ' yrs';
                  if ($v['city']) $parts[] = $v['city'];
                  if ($v['occupation']) $parts[] = $v['occupation'];
                  echo htmlspecialchars(implode(' · ', $parts) ?: 'Member');
                ?>
              </div>
            </div>
            <div class="wv-time"><?php
              if ($v['viewed_at']) {
                $dt = new DateTime($v['viewed_at']);
                echo htmlspecialchars($dt->format('d M, g:ia'));
              } else { echo '—'; }
            ?><?php if ($v['views'] > 1): ?><br><?php echo $v['views']; ?> visits<?php endif; ?></div>
          </a>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</main>
<?php
require_once __DIR__ . '/includes/footer.php';
require_once __DIR__ . '/includes/scripts.php';
?>
