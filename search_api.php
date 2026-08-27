<?php
header('Content-Type: application/json');
require_once __DIR__ . '/includes/db.php';

// Paginated profile search.
// Params (all optional): q, gender, min_age, max_age, religion, tongue, city,
//   education, occupation, marital, caste, page, per_page, sort
try {
  $db = getDB();

  $where = ['1=1'];
  $params = [];

  if (!empty($_GET['q'])) {
    $q = '%' . trim($_GET['q']) . '%';
    $where[] = '(`full_name` LIKE ? OR `city` LIKE ? OR `occupation` LIKE ? OR `hometown` LIKE ?)';
    $params[] = $q; $params[] = $q; $params[] = $q; $params[] = $q;
  }

  if (!empty($_GET['gender']) && in_array($_GET['gender'], ['Male', 'Female'], true)) {
    $where[] = '`gender` = ?';
    $params[] = $_GET['gender'];
  }

  if (!empty($_GET['min_age']) && (int)$_GET['min_age'] > 18) {
    $where[] = 'TIMESTAMPDIFF(YEAR, `date_of_birth`, CURDATE()) >= ?';
    $params[] = (int) $_GET['min_age'];
  }
  if (!empty($_GET['max_age']) && (int)$_GET['max_age'] < 70) {
    $where[] = 'TIMESTAMPDIFF(YEAR, `date_of_birth`, CURDATE()) <= ?';
    $params[] = (int) $_GET['max_age'];
  }

  // map-friendly single-value filters
  $filters = [
    'religion'    => 'religion',
    'tongue'      => 'mother_tongue',
    'city'        => 'city',
    'education'   => 'highest_education',
    'occupation'  => 'occupation',
    'marital'     => 'marital_status',
    'caste'       => 'caste',
    'income_min'  => null,
    'height_min'  => null,
  ];
  foreach ($filters as $paramKey => $dbCol) {
    if ($dbCol === null) continue;
    if (!empty($_GET[$paramKey])) {
      $where[] = "`$dbCol` = ?";
      $params[] = trim($_GET[$paramKey]);
    }
  }

  $sort = match ($_GET['sort'] ?? 'recommended') {
    'newest'      => '`created_at` DESC',
    'recently_active' => '`updated_at` DESC',
    'name_asc'    => '`full_name` ASC',
    default       => '`created_at` DESC',
  };

  $perPage = isset($_GET['per_page']) ? max(1, min(100, (int)$_GET['per_page'])) : 24;
  $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
  $offset = ($page - 1) * $perPage;

  $countSql = 'SELECT COUNT(*) FROM users WHERE ' . implode(' AND ', $where);
  $cstmt = $db->prepare($countSql);
  $cstmt->execute($params);
  $total = (int) $cstmt->fetchColumn();

  $sql = 'SELECT id, full_name, gender, date_of_birth, height, city, religion, mother_tongue,
                 highest_education, occupation, marital_status, caste, annual_income, profile_photo,
                 created_at, updated_at
          FROM users WHERE ' . implode(' AND ', $where)
        . ' ORDER BY ' . $sort . ' LIMIT ' . $perPage . ' OFFSET ' . $offset;

  $stmt = $db->prepare($sql);
  $stmt->execute($params);
  $rows = $stmt->fetchAll();

  $results = [];
  foreach ($rows as $r) {
    $age = null;
    if (!empty($r['date_of_birth'])) {
      $age = (int) (new DateTime())->diff(new DateTime($r['date_of_birth']))->y;
    }
    $results[] = [
      'id' => (int) $r['id'],
      'name' => $r['full_name'] ?? '',
      'gender' => $r['gender'] ?? '',
      'age' => $age,
      'height' => $r['height'] ?? '',
      'city' => $r['city'] ?? '',
      'religion' => $r['religion'] ?? '',
      'tongue' => $r['mother_tongue'] ?? '',
      'education' => $r['highest_education'] ?? '',
      'occupation' => $r['occupation'] ?? '',
      'marital' => $r['marital_status'] ?? '',
      'caste' => $r['caste'] ?? '',
      'salary' => $r['annual_income'] ?? '',
      'photo' => photo_url($r['profile_photo']),
    ];
  }

  echo json_encode([
    'results' => $results,
    'total' => $total,
    'page' => $page,
    'per_page' => $perPage,
    'pages' => (int) ceil($total / $perPage),
    'count' => count($results),
  ]);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['results' => [], 'total' => 0, 'count' => 0, 'pages' => 0, 'error' => 'Database error']);
}
