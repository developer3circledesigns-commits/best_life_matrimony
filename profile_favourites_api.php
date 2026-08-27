<?php
header('Content-Type: application/json');
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/auth.php';

require_login();

// Admin users cannot use user APIs - return 403
if (is_admin()) {
  http_response_code(403);
  echo json_encode(['error' => 'Admin users cannot access user APIs']);
  exit;
}

$userId = $_SESSION['user_id'];

try {
  $db = getDB();

  // Profiles the current user has favourited
  $stmt = $db->prepare('SELECT u.id, u.full_name, u.gender, u.date_of_birth, u.height, u.city, u.religion, u.mother_tongue, u.highest_education, u.occupation, u.marital_status, u.profile_photo, u.created_at FROM favourites f JOIN users u ON u.id = f.profile_id WHERE f.user_id = ? ORDER BY f.created_at DESC');
  $stmt->execute([$userId]);
  $myFavs = $stmt->fetchAll();

  // Profiles that have favourited the current user
  $stmt2 = $db->prepare('SELECT u.id, u.full_name, u.gender, u.date_of_birth, u.height, u.city, u.religion, u.mother_tongue, u.highest_education, u.occupation, u.marital_status, u.profile_photo, u.created_at FROM favourites f JOIN users u ON u.id = f.user_id WHERE f.profile_id = ? ORDER BY f.created_at DESC');
  $stmt2->execute([$userId]);
  $favByOthers = $stmt2->fetchAll();

  function formatProfile($r) {
    $age = null;
    if (!empty($r['date_of_birth'])) {
      $age = (new DateTime())->diff(new DateTime($r['date_of_birth']))->y;
    }
    return [
      'id' => (int) $r['id'],
      'name' => $r['full_name'] ?? '',
      'gender' => strtolower($r['gender'] ?? ''),
      'age' => $age,
      'height' => $r['height'] ?? '',
      'city' => $r['city'] ?? '',
      'religion' => $r['religion'] ?? '',
      'tongue' => $r['mother_tongue'] ?? '',
      'education' => $r['highest_education'] ?? '',
      'profession' => $r['occupation'] ?? '',
      'marital' => $r['marital_status'] ?? '',
      'photo' => photo_url($r['profile_photo']),
    ];
  }

  $result = [
    'my_favourites' => array_map('formatProfile', $myFavs),
    'favourited_by' => array_map('formatProfile', $favByOthers),
  ];

  echo json_encode($result);
} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(['my_favourites' => [], 'favourited_by' => [], 'error' => 'Database error']);
}
