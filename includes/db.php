<?php
// BestLife Matrimony - Database connection
// F6: config.php provides helpers (site_url, send_email, session) that db.php
// functions rely on. Loading it here makes db.php self-contained.
require_once __DIR__ . '/config.php';

// In Docker the web container sets these via environment; safe local defaults otherwise.
$dbConfig = [
  'host'     => getenv('DB_HOST')     ?: 'localhost',
  'port'     => getenv('DB_PORT')     ?: 3306,
  'name'     => getenv('DB_NAME')     ?: 'bestlife_matrimony',
  'user'     => getenv('DB_USER')     ?: 'root',
  'pass'     => getenv('DB_PASS')     ?: '',   // XAMPP default: empty password
  'charset'  => 'utf8mb4',
];

function getDB(array $cfg = null): PDO {
  static $pdo = null;
  if ($pdo !== null) return $pdo;
  $cfg = $cfg ?? $GLOBALS['dbConfig'];

  $dsn = "mysql:host={$cfg['host']};port={$cfg['port']};charset={$cfg['charset']}";
  $pdo = new PDO($dsn, $cfg['user'], $cfg['pass'], [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
  ]);

  // Auto-create database + table on first connect (local dev convenience)
  $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$cfg['name']}`
    DEFAULT CHARACTER SET {$cfg['charset']} COLLATE {$cfg['charset']}_unicode_ci");
  $pdo->exec("USE `{$cfg['name']}`");
  $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `full_name`   VARCHAR(150) NOT NULL,
    `email`       VARCHAR(191) NOT NULL,
    `phone`       VARCHAR(30)  NOT NULL,
    `password`    VARCHAR(255) NOT NULL,
    `looking_for` ENUM('Bride','Groom','') NOT NULL DEFAULT '',
    `email_verified` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`)
  ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");

  // Schema-version tracking table (F5): each deployed schema bump updates
  // SCHEMA_VERSION below. If the stored version matches, the idempotent
  // migration pass below is skipped. Bump SCHEMA_VERSION whenever the
  // schema changes so the migrations re-run on existing DBs.
  $pdo->exec("CREATE TABLE IF NOT EXISTS `schema_meta` (
    `id` TINYINT UNSIGNED NOT NULL DEFAULT 1,
    `schema_version` VARCHAR(40) NOT NULL DEFAULT '',
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");
  if (!defined('SCHEMA_VERSION')) define('SCHEMA_VERSION', 'v8');
  $storedVer = $pdo->query('SELECT schema_version FROM schema_meta WHERE id = 1')->fetchColumn();
  $schemaNeedsMigrate = ($storedVer === false) || ($storedVer !== SCHEMA_VERSION);

  if ($schemaNeedsMigrate) {
    // Auto-add profile columns if migration hasn't been run (safe to fail silently)
  try {
  $profileCols = [
    "`date_of_birth` DATE DEFAULT NULL",
    "`gender` ENUM('Male','Female','Other') DEFAULT NULL",
    "`height` VARCHAR(20) DEFAULT NULL",
    "`weight` INT UNSIGNED DEFAULT NULL",
    "`body_type` ENUM('Slim','Average','Athletic','Muscular','Heavy') DEFAULT NULL",
    "`complexion` ENUM('Very Fair','Fair','Wheatish','Dark') DEFAULT NULL",
    "`blood_group` ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL",
    "`marital_status` ENUM('Never Married','Divorced','Widowed','Awaited Divorce') DEFAULT NULL",
    "`about_self` TEXT DEFAULT NULL",
    "`religion` ENUM('Hindu','Muslim','Christian','Sikh','Buddhist','Jain','Parsi','Other') DEFAULT NULL",
    "`caste` VARCHAR(100) DEFAULT NULL",
    "`sub_caste` VARCHAR(100) DEFAULT NULL",
    "`gothram` VARCHAR(100) DEFAULT NULL",
    "`star_sign` VARCHAR(50) DEFAULT NULL",
    "`zodiac` VARCHAR(20) DEFAULT NULL",
    "`dosham` ENUM('Yes','No','Not Sure') DEFAULT NULL",
    "`mother_tongue` VARCHAR(50) DEFAULT NULL",
    "`country` VARCHAR(60) DEFAULT NULL",
    "`state` VARCHAR(100) DEFAULT NULL",
    "`city` VARCHAR(100) DEFAULT NULL",
    "`citizenship` VARCHAR(60) DEFAULT NULL",
    "`residential_status` ENUM('Owned','Rented','Parents','Family') DEFAULT NULL",
    "`highest_education` ENUM('High School','Bachelors','Masters','Doctorate','Professional') DEFAULT NULL",
    "`education_detail` VARCHAR(255) DEFAULT NULL",
    "`occupation` VARCHAR(150) DEFAULT NULL",
    "`occupation_type` ENUM('Government','Private','Business','Self Employed','Freelance','Homemaker','Retired') DEFAULT NULL",
    "`annual_income` VARCHAR(50) DEFAULT NULL",
    "`family_type` ENUM('Joint','Nuclear') DEFAULT NULL",
    "`family_status` ENUM('Middle Class','Upper Middle Class','Rich','Affluent') DEFAULT NULL",
    "`family_values` ENUM('Traditional','Moderate','Orthodox','Liberal') DEFAULT NULL",
    "`father_name` VARCHAR(150) DEFAULT NULL",
    "`father_occupation` VARCHAR(150) DEFAULT NULL",
    "`mother_name` VARCHAR(150) DEFAULT NULL",
    "`mother_occupation` VARCHAR(150) DEFAULT NULL",
    "`brothers` TINYINT UNSIGNED DEFAULT 0",
    "`brothers_married` TINYINT UNSIGNED DEFAULT 0",
    "`sisters` TINYINT UNSIGNED DEFAULT 0",
    "`sisters_married` TINYINT UNSIGNED DEFAULT 0",
    "`family_location` VARCHAR(150) DEFAULT NULL",
    "`diet` ENUM('Vegetarian','Non-Vegetarian','Eggetarian','Vegan','Jain') DEFAULT NULL",
    "`smoking` ENUM('No','Sometimes','Often') DEFAULT NULL",
    "`drinking` ENUM('No','Sometimes','Often') DEFAULT NULL",
    "`pref_age_min` TINYINT UNSIGNED DEFAULT NULL",
    "`pref_age_max` TINYINT UNSIGNED DEFAULT NULL",
    "`pref_height_min` VARCHAR(20) DEFAULT NULL",
    "`pref_height_max` VARCHAR(20) DEFAULT NULL",
    "`pref_education` VARCHAR(255) DEFAULT NULL",
    "`pref_location` VARCHAR(255) DEFAULT NULL",
    "`pref_other` TEXT DEFAULT NULL",
    "`profile_photo` VARCHAR(255) DEFAULT NULL",
    "`gallery_photo_1` VARCHAR(255) DEFAULT NULL",
    "`gallery_photo_2` VARCHAR(255) DEFAULT NULL",
    "`gallery_photo_3` VARCHAR(255) DEFAULT NULL",
    "`gallery_photo_4` VARCHAR(255) DEFAULT NULL",
    "`gallery_photo_5` VARCHAR(255) DEFAULT NULL",
    "`email_verified` TINYINT(1) NOT NULL DEFAULT 0",
    "`phone_verified` TINYINT(1) NOT NULL DEFAULT 0",
    "`id_verified` TINYINT(1) NOT NULL DEFAULT 0",
    "`is_admin` TINYINT(1) NOT NULL DEFAULT 0",
    "`is_suspended` TINYINT(1) NOT NULL DEFAULT 0",
    "`twofa_secret` VARCHAR(64) DEFAULT NULL",
    "`is_approved` TINYINT(1) NOT NULL DEFAULT 0",
  ];
  foreach ($profileCols as $colDef) {
    $colName = trim($colDef, '` ');
    $colName = explode(' ', $colName)[0];
    $colName = str_replace('`', '', $colName);
    $check = $pdo->query("SHOW COLUMNS FROM `users` LIKE '$colName'")->fetch();
    if (!$check) {
      $pdo->exec("ALTER TABLE `users` ADD COLUMN $colDef AFTER `looking_for`");
    }
  }
  } catch (Exception $e) { /* migration columns may already exist */ }

  // Auto-create favourites table
  try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `favourites` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` INT UNSIGNED NOT NULL,
      `profile_id` INT UNSIGNED NOT NULL,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uq_fav` (`user_id`,`profile_id`),
      KEY `fk_fav_profile` (`profile_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");

    // Add foreign key constraints (safe for existing tables)
    $fkChecks = $pdo->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'favourites' AND REFERENCED_TABLE_NAME IS NOT NULL")->fetchAll(PDO::FETCH_COLUMN);
    if (!in_array('fk_fav_user', $fkChecks, true) || !in_array('fk_fav_profile', $fkChecks, true)) {
      // Remove orphaned rows first so FK constraints can be added
      $pdo->exec("DELETE f FROM `favourites` f LEFT JOIN `users` u ON u.id = f.user_id WHERE u.id IS NULL");
      $pdo->exec("DELETE f FROM `favourites` f LEFT JOIN `users` u ON u.id = f.profile_id WHERE u.id IS NULL");
    }
    if (!in_array('fk_fav_user', $fkChecks, true)) {
      $pdo->exec("ALTER TABLE `favourites` ADD CONSTRAINT `fk_fav_user` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE");
    }
    if (!in_array('fk_fav_profile', $fkChecks, true)) {
      $pdo->exec("ALTER TABLE `favourites` ADD CONSTRAINT `fk_fav_profile` FOREIGN KEY (`profile_id`) REFERENCES `users`(`id`) ON DELETE CASCADE");
    }
  } catch (Exception $e) { /* ignore */ }

  // Auto-create password resets table
  try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `password_resets` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` INT UNSIGNED NOT NULL,
      `token_hash` CHAR(64) NOT NULL,
      `expires_at` DATETIME NOT NULL,
      `used` TINYINT(1) NOT NULL DEFAULT 0,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_pw_user` (`user_id`),
      KEY `idx_pw_token` (`token_hash`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");
  } catch (Exception $e) { /* ignore */ }

  // Auto-create email verifications table
  try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `email_verifications` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` INT UNSIGNED NOT NULL,
      `token_hash` CHAR(64) NOT NULL,
      `expires_at` DATETIME NOT NULL,
      `used` TINYINT(1) NOT NULL DEFAULT 0,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_ev_user` (`user_id`),
      KEY `idx_ev_token` (`token_hash`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");
  } catch (Exception $e) { /* ignore */ }

  // Auto-create messages table
  try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `messages` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `sender_id` INT UNSIGNED NOT NULL,
      `receiver_id` INT UNSIGNED NOT NULL,
      `body` TEXT NOT NULL,
      `is_read` TINYINT(1) NOT NULL DEFAULT 0,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_msg_sender` (`sender_id`),
      KEY `idx_msg_receiver` (`receiver_id`),
      KEY `idx_msg_pair` (`sender_id`,`receiver_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");
  } catch (Exception $e) { /* ignore */ }

  // Auto-create notifications table
  try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `notifications` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` INT UNSIGNED NOT NULL,
      `type` VARCHAR(30) NOT NULL DEFAULT 'general',
      `message` VARCHAR(255) NOT NULL,
      `is_read` TINYINT(1) NOT NULL DEFAULT 0,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_notif_user` (`user_id`),
      KEY `idx_notif_read` (`user_id`,`is_read`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");
  } catch (Exception $e) { /* ignore */ }

  // Auto-create profile views table
  try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `profile_views` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `viewer_id` INT UNSIGNED NOT NULL,
      `profile_id` INT UNSIGNED NOT NULL,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_pv_profile` (`profile_id`),
      KEY `idx_pv_viewer` (`viewer_id`),
      KEY `idx_pv_unique` (`viewer_id`,`profile_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");
  } catch (Exception $e) { /* ignore */ }

  // Auto-create shortlists table (persisted favourites lists)
  try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `shortlists` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` INT UNSIGNED NOT NULL,
      `profile_id` INT UNSIGNED NOT NULL,
      `note` VARCHAR(255) DEFAULT NULL,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uq_shortlist` (`user_id`,`profile_id`),
      KEY `idx_short_user` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");
  } catch (Exception $e) { /* ignore */ }

  // Auto-create interests table (Interest Express)
  try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `interests` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `sender_id` INT UNSIGNED NOT NULL,
      `receiver_id` INT UNSIGNED NOT NULL,
      `status` ENUM('pending','accepted','declined','withdrawn') NOT NULL DEFAULT 'pending',
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uq_interest` (`sender_id`,`receiver_id`),
      KEY `idx_int_receiver` (`receiver_id`),
      KEY `idx_int_sender` (`sender_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");
  } catch (Exception $e) { /* ignore */ }

  // Auto-create blocks table
  try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `blocks` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `blocker_id` INT UNSIGNED NOT NULL,
      `blocked_id` INT UNSIGNED NOT NULL,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uq_block` (`blocker_id`,`blocked_id`),
      KEY `idx_block_blocker` (`blocker_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");
  } catch (Exception $e) { /* ignore */ }

  // Auto-create reports table
  try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `reports` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `reporter_id` INT UNSIGNED NOT NULL,
      `reported_id` INT UNSIGNED NOT NULL,
      `reason` VARCHAR(255) NOT NULL,
      `details` TEXT DEFAULT NULL,
      `status` ENUM('open','resolved','dismissed') NOT NULL DEFAULT 'open',
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_rep_reporter` (`reporter_id`),
      KEY `idx_rep_reported` (`reported_id`),
      KEY `idx_rep_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");
  } catch (Exception $e) { /* ignore */ }

  // Auto-create OTP codes table
  try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `otp_codes` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` INT UNSIGNED NOT NULL,
      `purpose` VARCHAR(20) NOT NULL DEFAULT 'phone',
      `code_hash` VARCHAR(255) NOT NULL,
      `expires_at` DATETIME NOT NULL,
      `used` TINYINT(1) NOT NULL DEFAULT 0,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_otp_user` (`user_id`),
      KEY `idx_otp_used` (`user_id`,`purpose`,`used`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");
  } catch (Exception $e) { /* ignore */ }

  // Auto-create verification requests table (ID badge)
  try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `verification_requests` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` INT UNSIGNED NOT NULL,
      `type` VARCHAR(30) NOT NULL DEFAULT 'id',
      `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
      `note` VARCHAR(255) DEFAULT NULL,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_vr_user` (`user_id`),
      KEY `idx_vr_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");
  } catch (Exception $e) { /* ignore */ }

  // Auto-create media moderation table (photo moderation queue)
  try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `media_moderation` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` INT UNSIGNED NOT NULL,
      `field` VARCHAR(40) NOT NULL,
      `file_name` VARCHAR(255) DEFAULT NULL,
      `mime` VARCHAR(100) DEFAULT NULL,
      `size` INT UNSIGNED DEFAULT NULL,
      `status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_mm_user` (`user_id`),
      KEY `idx_mm_status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");
  } catch (Exception $e) { /* ignore */ }

  // Auto-create activity logs table (P4-22)
  try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `activity_logs` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` INT UNSIGNED DEFAULT NULL,
      `action` VARCHAR(60) NOT NULL,
      `entity_type` VARCHAR(40) DEFAULT NULL,
      `entity_id` INT UNSIGNED DEFAULT NULL,
      `details` VARCHAR(255) DEFAULT NULL,
      `ip` VARCHAR(45) DEFAULT NULL,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_al_user` (`user_id`),
      KEY `idx_al_action` (`action`),
      KEY `idx_al_created` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");
  } catch (Exception $e) { /* ignore */ }

  // Auto-create email campaigns table (P4-24)
  try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `email_campaigns` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `subject` VARCHAR(191) NOT NULL,
      `body_html` MEDIUMTEXT NOT NULL,
      `body_text` TEXT DEFAULT NULL,
      `audience` VARCHAR(30) NOT NULL DEFAULT 'all',
      `status` ENUM('draft','sent') NOT NULL DEFAULT 'sent',
      `recipients` INT UNSIGNED NOT NULL DEFAULT 0,
      `created_by` INT UNSIGNED DEFAULT NULL,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      KEY `idx_cmp_created` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");
  } catch (Exception $e) { /* ignore */ }

  // Auto-create remember_tokens table (DB-backed "remember me") for L5 fix
  try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `remember_tokens` (
      `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
      `user_id` INT UNSIGNED NOT NULL,
      `token_hash` CHAR(64) NOT NULL,
      `expires_at` DATETIME NOT NULL,
      `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uq_rt_token` (`token_hash`),
      KEY `idx_rt_user` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");
  } catch (Exception $e) { /* ignore */ }

  // Auto-create rate_limits table (DB-backed rate limiting)
  try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `rate_limits` (
      `k` VARCHAR(64) NOT NULL,
      `count` INT UNSIGNED NOT NULL DEFAULT 0,
      `first` DATETIME NOT NULL,
      PRIMARY KEY (`k`),
      KEY `idx_rl_first` (`first`)
    ) ENGINE=InnoDB DEFAULT CHARSET={$cfg['charset']} COLLATE={$cfg['charset']}_unicode_ci");
  } catch (Exception $e) { /* ignore */ }

  // Add rejected_reason column for approval flow
  try {
    $col = $pdo->query("SHOW COLUMNS FROM `users` LIKE 'rejected_reason'")->fetch();
    if (!$col) {
      $pdo->exec("ALTER TABLE `users` ADD COLUMN `rejected_reason` VARCHAR(255) DEFAULT NULL AFTER `is_approved`");
    }
  } catch (Exception $e) { /* ignore */ }

  // Record that this schema version has been applied (F5 versioning)
    $pdo->prepare('INSERT INTO schema_meta (id, schema_version) VALUES (1, ?) ON DUPLICATE KEY UPDATE schema_version = VALUES(schema_version)')
       ->execute([SCHEMA_VERSION]);
  }

  return $pdo;
}

// Create a notification for a user (lazy DB connection)
function notification_add(int $userId, string $type, string $message): void {
  try {
    $db = getDB();
    $db->prepare('INSERT INTO notifications (user_id, type, message) VALUES (?, ?, ?)')
       ->execute([$userId, $type, mb_substr($message, 0, 255)]);
  } catch (Exception $e) { /* ignore */ }
}

// Issue an email verification token and email the link. Returns token on success, null otherwise.
function issue_email_verification(int $userId, string $email): ?string {
  try {
    $db = getDB();
    // Invalidate any previous tokens
    $db->prepare('UPDATE email_verifications SET used = 1 WHERE user_id = ? AND used = 0')->execute([$userId]);
    $token = bin2hex(random_bytes(32));
    $tokenHash = hash('sha256', $token);
    $expires = date('Y-m-d H:i:s', time() + 24 * 3600); // 24 hours
    $db->prepare('INSERT INTO email_verifications (user_id, token_hash, expires_at) VALUES (?, ?, ?)')
       ->execute([$userId, $tokenHash, $expires]);
    $verifyUrl = site_url('verify_email.php?token=' . urlencode($token));
    $html = '<p>Hi,</p><p>Welcome to BestLife Matrimony! Please confirm your email address to activate your account:</p>'
          . '<p><a href="' . htmlspecialchars($verifyUrl) . '">Verify my email</a></p>'
          . '<p>This link expires in 24 hours.</p>';
    send_email($email, 'Confirm your BestLife Matrimony email', $html);
    return $token;
  } catch (Exception $e) {
    return null;
  }
}

// Record a profile view (one per viewer/profile/day to avoid spam). Returns true if newly recorded.
function record_profile_view(int $viewerId, int $profileId): bool {
  if ($viewerId <= 0 || $profileId <= 0 || $viewerId === $profileId) return false;
  try {
    $db = getDB();
    $since = date('Y-m-d H:i:s', time() - 86400); // 24h window
    $stmt = $db->prepare('SELECT id FROM profile_views WHERE viewer_id = ? AND profile_id = ? AND created_at > ? LIMIT 1');
    $stmt->execute([$viewerId, $profileId, $since]);
    if ($stmt->fetch()) return false;
    $db->prepare('INSERT INTO profile_views (viewer_id, profile_id) VALUES (?, ?)')->execute([$viewerId, $profileId]);
    return true;
  } catch (Exception $e) {
    return false;
  }
}

// Check whether either user has blocked the other
function is_blocked(int $a, int $b): bool {
  if ($a <= 0 || $b <= 0) return false;
  try {
    $db = getDB();
    $stmt = $db->prepare('SELECT id FROM blocks WHERE (blocker_id = ? AND blocked_id = ?) OR (blocker_id = ? AND blocked_id = ?) LIMIT 1');
    $stmt->execute([$a, $b, $b, $a]);
    return (bool) $stmt->fetch();
  } catch (Exception $e) {
    return false;
  }
}

// Generate and store a numeric OTP for a user/purpose. Returns the plaintext OTP (for demo/dev).
function issue_otp(int $userId, string $purpose, int $ttlSeconds = 600): ?string {
  try {
    $db = getDB();
    $db->prepare('UPDATE otp_codes SET used = 1 WHERE user_id = ? AND purpose = ? AND used = 0')->execute([$userId, $purpose]);
    $code = (string) random_int(100000, 999999);
    $hash = password_hash($code, PASSWORD_DEFAULT);
    $expires = date('Y-m-d H:i:s', time() + $ttlSeconds);
    $db->prepare('INSERT INTO otp_codes (user_id, purpose, code_hash, expires_at) VALUES (?, ?, ?, ?)')
       ->execute([$userId, $purpose, $hash, $expires]);
    return $code;
  } catch (Exception $e) {
    return null;
  }
}

// Verify a submitted OTP for a user/purpose. Returns true on success.
function verify_otp(int $userId, string $purpose, string $code): bool {
  if ($code === '') return false;
  try {
    $db = getDB();
    $stmt = $db->prepare('SELECT id, code_hash, expires_at FROM otp_codes WHERE user_id = ? AND purpose = ? AND used = 0 ORDER BY id DESC LIMIT 1');
    $stmt->execute([$userId, $purpose]);
    $row = $stmt->fetch();
    if (!$row || strtotime($row['expires_at']) < time()) return false;
    if (!password_verify($code, $row['code_hash'])) return false;
    $db->prepare('UPDATE otp_codes SET used = 1 WHERE id = ?')->execute([$row['id']]);
    return true;
  } catch (Exception $e) {
    return false;
  }
}

// Log an uploaded media file into the moderation queue for review.
function log_media_for_moderation(int $userId, string $field, string $fileName, string $mime, int $size): void {
  try {
    $db = getDB();
    $db->prepare('INSERT INTO media_moderation (user_id, field, file_name, mime, size) VALUES (?, ?, ?, ?, ?)')
       ->execute([$userId, $field, $fileName, $mime, $size]);
  } catch (Exception $e) { /* ignore */ }
}

// P4-22: Record a row into the user activity log. Safe to call anywhere; never throws.
function log_activity(?int $userId, string $action, ?string $entityType = null, ?int $entityId = null, ?string $details = null): void {
  try {
    $db = getDB();
    $db->prepare('INSERT INTO activity_logs (user_id, action, entity_type, entity_id, details, ip) VALUES (?, ?, ?, ?, ?, ?)')
       ->execute([$userId, $action, $entityType, $entityId, $details ? mb_substr($details, 0, 255) : null, $_SERVER['REMOTE_ADDR'] ?? null]);
  } catch (Exception $e) { /* ignore */ }
}

// Whether the current user's profile has been approved by an admin OR is verified.
// Verified users (email/phone/ID) are treated as approved for viewing profiles & messaging.
function is_approved(): bool {
  $u = current_user();
  if (!$u) return false;
  if ((int) ($u['is_approved'] ?? 0) === 1) return true;
  if ((int) ($u['is_admin'] ?? 0) === 1) return true;
  if ((int) ($u['email_verified'] ?? 0) === 1) return true;
  if ((int) ($u['phone_verified'] ?? 0) === 1) return true;
  if ((int) ($u['id_verified'] ?? 0) === 1) return true;
  return false;
}
function is_verified(): bool {
  $u = current_user(); if (!$u) return false;
  return (int)($u['email_verified']??0)===1 || (int)($u['phone_verified']??0)===1 || (int)($u['id_verified']??0)===1;
}
function is_approved_strict(): bool {
  $u = current_user(); return $u && (int)($u['is_approved']??0)===1;
}
function can_interact(): bool {
  $u = current_user(); if (!$u) return false;
  if ((int)($u['is_admin']??0)===1) return false; // admins never as matrimony actor
  if ((int)($u['is_suspended']??0)===1) return false;
  $require = getenv('APP_REQUIRE_ADMIN_APPROVAL');
  if ($require === false || $require === '') $require = $_ENV['APP_REQUIRE_ADMIN_APPROVAL'] ?? $_SERVER['APP_REQUIRE_ADMIN_APPROVAL'] ?? 'false';
  $require = strtolower(trim((string)$require));
  if ($require === 'true' || $require === '1') return is_approved_strict();
  return is_approved(); // relaxed: verified counts (current)
}
function profile_complete_percent(array $u): int {
  $need = ['full_name','date_of_birth','gender','height','religion','city','highest_education','occupation','profile_photo'];
  $filled = 0; foreach($need as $k) if(!empty($u[$k])) $filled++;
  return (int)round($filled/count($need)*100);
}
// DB-backed rate limiting (IP + key) — robust vs session clear
function rate_limit_check_db(string $k, int $max, int $window): bool {
  try {
    $db = getDB();
    $key = $k . ':' . ($_SERVER['REMOTE_ADDR'] ?? 'cli');
    $now = date('Y-m-d H:i:s');
    $row = $db->prepare('SELECT count, first FROM rate_limits WHERE k=?');
    $row->execute([$key]);
    $r = $row->fetch();
    if (!$r) return true;
    $first = strtotime($r['first']);
    if (time() - $first > $window) return true;
    return (int)$r['count'] < $max;
  } catch (Throwable $e) { return true; }
}
function rate_limit_increment_db(string $k, int $window = 300): void {
  try {
    $db = getDB();
    $key = $k . ':' . ($_SERVER['REMOTE_ADDR'] ?? 'cli');
    $now = date('Y-m-d H:i:s');
    $row = $db->prepare('SELECT count, first FROM rate_limits WHERE k=?');
    $row->execute([$key]);
    $r = $row->fetch();
    if (!$r) {
      $db->prepare('INSERT INTO rate_limits (k,count,first) VALUES (?,?,?)')->execute([$key,1,$now]);
    } else {
      $first = strtotime($r['first']);
      if (time() - $first > $window) {
        $db->prepare('UPDATE rate_limits SET count=1, first=? WHERE k=?')->execute([$now,$key]);
      } else {
        $db->prepare('UPDATE rate_limits SET count=count+1 WHERE k=?')->execute([$key]);
      }
    }
  } catch (Throwable $e) {}
}
function rate_limit_reset_db(string $k): void {
  try {
    $db = getDB();
    $key = $k . ':' . ($_SERVER['REMOTE_ADDR'] ?? 'cli');
    $db->prepare('DELETE FROM rate_limits WHERE k=?')->execute([$key]);
  } catch (Throwable $e) {}
}

// Guard: redirect unapproved users to the contact page so they can request approval.
// Call before any output on pages that require approval (profile_view, messages).
function require_approved(): void {
  if (empty($_SESSION['user_id'])) {
    header('Location: ./login.php');
    exit;
  }
  if (is_admin()) return;
  if (!can_interact()) {
    // Preserve original is_approved check for backwards compat, but use can_interact
    header('Location: ./contact.php?reason=approval');
    exit;
  }
}

// Current authenticated user's DB row (cached per request). Returns false/array.
function current_user(): array|false {
  static $u = false;
  if ($u !== false) return $u;
  $id = (int) ($_SESSION['user_id'] ?? 0);
  if (!$id) { $u = false; return $u; }
  try {
    $db = getDB();
    $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([$id]);
    $u = $stmt->fetch();
    return $u ?: false;
  } catch (Exception $e) {
    return false;
  }
}

// Whether the current user has admin privileges.
function is_admin(): bool {
  $u = current_user();
  return ($u && (int) ($u['is_admin'] ?? 0) === 1) ? true : false;
}

// Guard for admin pages: redirect to the admin login if not logged in, or to
// home if not an admin. Call before any output.
function require_admin(): void {
  if (empty($_SESSION['user_id'])) {
    // Send unauthenticated visitors to the separate admin login surface (not the public one)
    $_SESSION['auth_flash'] = 'Please log in with an admin account to access the admin area.';
    $target = ltrim($_SERVER['REQUEST_URI'] ?? '/admin/index.php', '/');
    if (strpos($target, 'admin/') !== 0) {
      $target = 'admin/' . ltrim($target, '/');
    }
    $_SESSION['admin_redirect'] = $target ?: 'admin/index.php';
    header('Location: ' . site_url('admin/login.php'));
    exit;
  }
  if (!is_admin()) {
    header('Location: ' . site_url('index.php'));
    exit;
  }
}

// Auto-login from remember-me cookie. Runs after getDB() is defined so the
// DB-backed token can be validated. Sets the session user id when valid.
if (empty($_SESSION['user_id'])) {
  $rmUserId = remember_me_validate();
  if ($rmUserId) {
    $_SESSION['user_id'] = $rmUserId;
  }
}

