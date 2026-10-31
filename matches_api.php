<?php
header('Content-Type: application/json');
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';
require_login();

try {
  $db = getDB();

  $where = ['1=1'];
  $params = [];

  // Never surface admin accounts or self in public match results
  $where[] = '(`is_admin` IS NULL OR `is_admin` = 0)';
  $selfId = (int)($_SESSION['user_id'] ?? 0);
  if ($selfId > 0) {
    $where[] = '`id` != ?';
    $params[] = $selfId;
  }

  $gender = strtolower(trim((string)($_GET['gender'] ?? '')));
  if ($gender === 'male' || $gender === 'female') {
    $where[] = '`gender` = ?';
    $params[] = ucfirst($gender);
  }

  foreach (['religion' => 'religions', 'mother_tongue' => 'tongues', 'city' => 'locations', 'highest_education' => 'education', 'occupation' => 'professions', 'marital_status' => 'maritalStatuses', 'caste' => 'castes'] as $dbCol => $paramKey) {
    if (!empty($_GET[$paramKey])) {
      $vals = explode(',', $_GET[$paramKey]);
      $vals = array_filter(array_map('trim', $vals));
      if ($vals) {
        $placeholders = implode(',', array_fill(0, count($vals), '?'));
        $where[] = "`$dbCol` IN ($placeholders)";
        $params = array_merge($params, $vals);
      }
    }
  }

  if (!empty($_GET['q'])) {
    $q = '%' . $_GET['q'] . '%';
    $where[] = '(`full_name` LIKE ? OR `city` LIKE ? OR `occupation` LIKE ?)';
    $params[] = $q;
    $params[] = $q;
    $params[] = $q;
  }

  $ageMin = (int)($_GET['ageMin'] ?? 18);
  $ageMax = (int)($_GET['ageMax'] ?? 70);
  $hMin = (int)($_GET['hMin'] ?? 54);
  $hMax = (int)($_GET['hMax'] ?? 76);
  $salMin = (int)($_GET['salaryMin'] ?? 1);
  $salMax = (int)($_GET['salaryMax'] ?? 50);

  // Add age filtering in SQL using date_of_birth comparison
  // Age = current_year - birth_year approximately
  // We calculate: birth_date should be between (now - ageMax years) and (now - ageMin years)
  $minDob = date('Y-m-d', strtotime("-$ageMax . ' year'));
  $maxDob = date('Y-m-d', strtotime("-$ageMin . ' year'));
  $where[] = "`date_of_birth` BETWEEN ? AND ?";
  $params[] = $minDob;
  $params[] = $maxDob;

  // Add basic height guard in SQL (height column starts with digit)
  $where[] = "`height` REGEXP '^[0-9]'";

  // Add basic salary guard in SQL
  $where[] = "`annual_income` REGEXP '^[0-9]'";

  $sql = 'SELECT `id`,`full_name`,`gender`,`date_of_birth`,`height`,`city`,`religion`,`mother_tongue`,`highest_education`,`occupation`,`marital_status`,`caste`,`annual_income`,`profile_photo`,`created_at`,`updated_at` FROM users WHERE ' . implode(' AND ', $where);

  $orderBy = match ($_GET['sort'] ?? 'recommended') {
    'newest' => '`created_at` DESC',
    'recently_active' => '`updated_at` DESC',
    default => '`created_at` DESC',
  };

  $sql = 'SELECT `id`,`full_name`,`gender`,`date_of_birth`,`height`,`city`,`religion`,`mother_tongue`,`highest_education`,`occupation`,`marital_status`,`caste`,`annual_income`,`profile_photo`,`created_at`,`updated_at` FROM users WHERE ' . implode(' AND ', $where) . ' ORDER BY ' . $orderBy;

  $stmt = $db->prepare($sql);
  $stmt->execute($params);
  $rows = $stmt->fetchAll();

  $profiles = [];
  foreach ($rows as $r) {
    $age = null;
    if (!empty($r['date_of_birth'])) {
      $dob = new DateTime($r['date_of_birth']);
      $age = (new DateTime())->diff($dob)->y;
    }
    $heightCm = null;
    $heightInches = null;
    if (!empty($r['height'])) {
      $h = trim($r['height']);
      // Format "5'8\"" (e.g. 5'8" or 5'8" (173 cm))
      if (preg_match("/(\d+)'\s*(\d+)/", $h, $m)) {
        $heightInches = (int)$m[1] * 12 + (int)$m[2];
        $heightCm = round($heightInches * 2.54);
      } else {
        // Plain centimeters e.g. "173"
        $cm = (int) preg_replace('/[^0-9]/', '', $h);
        if ($cm > 30 && $cm < 240) {
          $heightCm = $cm;
          $heightInches = round($cm / 2.54);
        }
      }
    }
    $salary = null;
    if (!empty($r['annual_income']) && preg_match('/(\d+)/', (string)$r['annual_income'], $mSal)) {
      $salary = (int) $mSal[1];
    }
    // Apply exact age, height, and salary filters in PHP (now on filtered subset only)
    if ($age === null || $age < $ageMin || $age > $ageMax) { continue; }
    if ($heightInches !== null && ($heightInches < $hMin || $heightInches > $hMax)) { continue; }
    if ($salary !== null && ($salary < $salMin || $salary > $salMax)) { continue; }
    $profiles[] = [
      'id' => $r['id'],
      'name' => $r['full_name'] ?? '',
      'gender' => strtolower($r['gender'] ?? ''),
      'age' => $age,
      'height' => $r['height'] ?? '',
      'heightCm' => $heightCm,
      'heightInches' => $heightInches,
      'city' => $r['city'] ?? '',
      'religion' => $r['religion'] ?? '',
      'tongue' => $r['mother_tongue'] ?? '',
      'education' => $r['highest_education'] ?? '',
      'profession' => $r['occupation'] ?? '',
      'marital' => $r['marital_status'] ?? '',
      'caste' => $r['caste'] ?? '',
      'salary' => $r['annual_income'] ?? '',
      'photo' => photo_url($r['profile_photo']),
      'created' => strtotime($r['created_at'] ?? 'now') * 1000,
      'active' => strtotime($r['updated_at'] ?? 'now') * 1000,
    ];
  }

  $total = count($profiles);

  // Pagination: fetch the full filtered set, then slice the requested page in PHP
  $perPage = isset($_GET['per_page']) ? max(1, min(100, (int)$_GET['per_page'])) : 24;
  $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;

  $offset = ($page - 1) * $perPage;
  $profiles = array_slice($profiles, $offset, $perPage);

  echo json_encode([
    'profiles' => $profiles,
    'total' => $total,
    'page' => $page,
    'per_page' => $perPage,
    'pages' => (int) ceil($total / $perPage),
    'count' => count($profiles),
  ]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['profiles' => [], 'total' => 0, 'count' => 0, 'pages' => 0, 'error' => 'Database error']);
}