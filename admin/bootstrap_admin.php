<?php
// Local development admin bootstrap for BestLife Matrimony.
// Creates the admin account if it does not already exist.

require_once __DIR__ . '/../includes/db.php';

$email = 'admin@bestlifematrimony.com';
$fullName = 'BestLife Admin';
$phone = '00000000';
$password = 'Admin@12345';

function lbm_ensure_admin(PDO $db, string $email, string $fullName, string $phone, string $password): int {
  $stmt = $db->prepare('SELECT id FROM users WHERE email = ?');
  $stmt->execute([$email]);
  $row = $stmt->fetch();
  if ($row) { return (int) $row['id']; }
  $hash = password_hash($password, PASSWORD_DEFAULT);
  $stmt = $db->prepare('INSERT INTO users (full_name, email, phone, password, is_admin, is_approved, email_verified, is_suspended) VALUES (?, ?, ?, ?, 1, 1, 1, 0)');
  $stmt->execute([$fullName, $email, $phone, $hash]);
  return (int) $db->lastInsertId();
}

$db = getDB();
$adminId = lbm_ensure_admin($db, $email, $fullName, $phone, $password);
echo 'Admin ready. id=' . $adminId . ' email=' . $email;