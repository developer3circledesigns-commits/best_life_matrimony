<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();
// Admin users have no matrimony profile/matches — send to admin dashboard
if (is_admin()) {
  header('Location: ./admin/index.php');
  exit;
}
$pageTitle = 'Profile Matches — BestLife Matrimony';
$pageDescription = 'Explore compatible profiles curated just for you on BestLife Matrimony.';
$pageHeadExtra = '<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">' . "\n"
  . '  <link rel="stylesheet" href="' . asset('css/matches.css') . '" />';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

function getFilterCounts(string $column): array {
  try {
    $db = getDB();
    $stmt = $db->query("SELECT `$column`, COUNT(*) AS cnt FROM users WHERE `$column` IS NOT NULL AND `$column` != '' GROUP BY `$column` ORDER BY cnt DESC");
    $result = [];
    while ($row = $stmt->fetch()) {
      $result[$row[$column]] = number_format($row['cnt']);
    }
    return $result;
  } catch (Exception $e) {
    return [];
  }
}

$religions   = getFilterCounts('religion');
$castes      = getFilterCounts('caste');
$tongues     = getFilterCounts('mother_tongue');
$locations   = getFilterCounts('city');
$education   = getFilterCounts('highest_education');
$professions = getFilterCounts('occupation');
$marital     = getFilterCounts('marital_status');

$totalProfiles = 0;
try {
  $db = getDB();
  $totalProfiles = (int) $db->query("SELECT COUNT(*) FROM users WHERE `is_admin` IS NULL OR `is_admin` = 0")->fetchColumn();
} catch (Exception $e) {}

function render_filter_group(string $title, array $options, string $group): void {
  echo '<h3 class="m-heading">' . htmlspecialchars($title) . '</h3>';
  if (empty($options)) {
    echo '<p style="font-size:0.8rem;color:#999;margin:0;">No data yet</p>';
    return;
  }
  foreach ($options as $label => $count) {
    echo '<label class="m-option">'
      . '<span><input type="checkbox" class="filter-checkbox" data-group="' . htmlspecialchars($group) . '" value="' . htmlspecialchars($label) . '"> ' . htmlspecialchars($label) . '</span>'
      . '<span class="m-count">' . $count . '</span>'
      . '</label>';
  }
}
?>

<?php
$currentUserApproved = 0;
if (!empty($_SESSION['user_id'])) {
  $currentUserApproved = is_approved() ? 1 : 0;
}
?>
<main class="matches-page" data-user-id="<?php echo intval($_SESSION['user_id'] ?? 0); ?>" data-approved="<?php echo $currentUserApproved; ?>">
  <div class="m-container">
    <div class="m-header">
      <h1 class="m-title">Profile Matches</h1>
      <p class="m-subtitle" id="resultCount"><strong><?php echo $totalProfiles; ?></strong> profiles found</p>
      <span id="sidebarCount" hidden><?php echo $totalProfiles; ?></span>
    </div>

    <div class="m-toolbar">
      <div class="m-search">
        <i class="bi bi-search"></i>
        <input type="text" id="mSearch" placeholder="Search by name, city, profession" autocomplete="off">
      </div>
      <select id="sortSelect" class="m-select" aria-label="Sort profiles">
        <option value="recommended">Recommended</option>
        <option value="newest">Newest</option>
        <option value="recently_active">Recently Active</option>
        <option value="distance">Nearest</option>
        <option value="age_asc">Age: Low to High</option>
        <option value="age_desc">Age: High to Low</option>
      </select>
      <button type="button" class="m-btn m-hide-desktop" id="mobileFilterBtn"><i class="bi bi-sliders"></i> Filters</button>
    </div>

    <div class="m-chips" id="filterChips"></div>

    <div class="m-layout">
      <aside class="m-sidebar" aria-label="Filters">
        <div class="m-sidebar-head">
          <h3>Filters</h3>
          <button type="button" class="m-link" id="sidebarClear">Clear all</button>
        </div>
        <h3 class="m-heading">Looking For</h3>
        <label class="m-option"><span><input type="radio" name="m-gender" value="women"> Women</span></label>
        <label class="m-option"><span><input type="radio" name="m-gender" value="men"> Men</span></label>

        <h3 class="m-heading">Age <span class="js-age-display">18 – 70 years</span></h3>
        <div class="m-range js-age-wrap">
          <div class="m-track"></div><div class="m-fill"></div>
          <input type="range" class="js-age-min" min="18" max="70" value="18">
          <input type="range" class="js-age-max" min="18" max="70" value="70">
        </div>

        <h3 class="m-heading">Height <span class="js-height-display">4'6" — 6'4"</span></h3>
        <div class="m-range js-height-wrap">
          <div class="m-track"></div><div class="m-fill"></div>
          <input type="range" class="js-h-min" min="54" max="76" value="54">
          <input type="range" class="js-h-max" min="54" max="76" value="76">
        </div>

        <h3 class="m-heading">Annual Salary <span class="js-salary-display">₹1 – 50 LPA</span></h3>
        <div class="m-range js-salary-wrap">
          <div class="m-track"></div><div class="m-fill"></div>
          <input type="range" class="js-salary-min" min="1" max="50" value="1">
          <input type="range" class="js-salary-max" min="1" max="50" value="50">
        </div>

        <?php
        render_filter_group('Religion', $religions, 'religions');
        render_filter_group('Caste', $castes, 'castes');
        render_filter_group('Mother Tongue', $tongues, 'tongues');
        render_filter_group('Location', $locations, 'locations');
        render_filter_group('Education', $education, 'education');
        render_filter_group('Profession', $professions, 'professions');
        render_filter_group('Marital Status', $marital, 'maritalStatuses');
        ?>
      </aside>

      <section class="m-main">
        <div class="m-grid" id="profileGrid"></div>

        <div class="m-empty" id="emptyState" hidden>
          <i class="bi bi-search"></i>
          <h4>No profiles found</h4>
          <p>Try adjusting your filters or search.</p>
          <button type="button" class="m-btn m-btn-primary" id="emptyClear">Clear all filters</button>
        </div>
      </section>
    </div>
  </div>

  <!-- Filter drawer -->
  <div class="m-backdrop" id="drawerBackdrop"></div>
  <div class="m-drawer" id="filterDrawer" aria-hidden="true">
    <div class="m-drawer-head">
      <h3>Filters</h3>
      <button type="button" class="m-drawer-close" aria-label="Close">&times;</button>
    </div>
    <div class="m-drawer-body">
      <h3 class="m-heading">Looking For</h3>
      <label class="m-option"><span><input type="radio" name="m-gender" value="women"> Women</span></label>
      <label class="m-option"><span><input type="radio" name="m-gender" value="men"> Men</span></label>

      <h3 class="m-heading">Age <span class="js-age-display">18 – 70 years</span></h3>
      <div class="m-range js-age-wrap">
        <div class="m-track"></div><div class="m-fill"></div>
        <input type="range" class="js-age-min" min="18" max="70" value="18">
        <input type="range" class="js-age-max" min="18" max="70" value="70">
      </div>

      <h3 class="m-heading">Height <span class="js-height-display">4'6" — 6'4"</span></h3>
      <div class="m-range js-height-wrap">
        <div class="m-track"></div><div class="m-fill"></div>
        <input type="range" class="js-h-min" min="54" max="76" value="54">
        <input type="range" class="js-h-max" min="54" max="76" value="76">
      </div>

      <h3 class="m-heading">Annual Salary <span class="js-salary-display">₹1 – 50 LPA</span></h3>
      <div class="m-range js-salary-wrap">
        <div class="m-track"></div><div class="m-fill"></div>
        <input type="range" class="js-salary-min" min="1" max="50" value="1">
        <input type="range" class="js-salary-max" min="1" max="50" value="50">
      </div>

      <?php
      render_filter_group('Religion', $religions, 'religions');
      render_filter_group('Caste', $castes, 'castes');
      render_filter_group('Mother Tongue', $tongues, 'tongues');
      render_filter_group('Location', $locations, 'locations');
      render_filter_group('Education', $education, 'education');
      render_filter_group('Profession', $professions, 'professions');
      render_filter_group('Marital Status', $marital, 'maritalStatuses');
      ?>
    </div>
    <div class="m-drawer-foot">
      <button type="button" class="m-btn" id="drawerClear">Clear</button>
      <button type="button" class="m-btn m-btn-primary" id="drawerApply">Apply</button>
    </div>
  </div>
</main>

<?php
$pageScripts = '<script src="' . asset('js/matches.js') . '" defer></script>';
require_once __DIR__ . '/includes/footer.php';
require_once __DIR__ . '/includes/scripts.php';
?>
