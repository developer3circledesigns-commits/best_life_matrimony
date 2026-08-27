-- BestLife Matrimony Database Schema
-- Suitable for Hostinger phpMyAdmin import

SET FOREIGN_KEY_CHECKS = 0;

-- Drop tables if they exist to allow clean re-import
DROP TABLE IF EXISTS `schema_meta`;
DROP TABLE IF EXISTS `favourites`;
DROP TABLE IF EXISTS `password_resets`;
DROP TABLE IF EXISTS `email_verifications`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `profile_views`;
DROP TABLE IF EXISTS `shortlists`;
DROP TABLE IF EXISTS `interests`;
DROP TABLE IF EXISTS `blocks`;
DROP TABLE IF EXISTS `reports`;
DROP TABLE IF EXISTS `otp_codes`;
DROP TABLE IF EXISTS `verification_requests`;
DROP TABLE IF EXISTS `media_moderation`;
DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `email_campaigns`;
DROP TABLE IF EXISTS `remember_tokens`;
DROP TABLE IF EXISTS `users`;

-- Create users table
CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(191) NOT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `looking_for` ENUM('Bride','Groom','') NOT NULL DEFAULT '',
  `date_of_birth` DATE DEFAULT NULL,
  `gender` ENUM('Male','Female','Other') DEFAULT NULL,
  `height` VARCHAR(20) DEFAULT NULL,
  `weight` INT UNSIGNED DEFAULT NULL,
  `body_type` ENUM('Slim','Average','Athletic','Muscular','Heavy') DEFAULT NULL,
  `complexion` ENUM('Very Fair','Fair','Wheatish','Dark') DEFAULT NULL,
  `blood_group` ENUM('A+','A-','B+','B-','AB+','AB-','O+','O-') DEFAULT NULL,
  `marital_status` ENUM('Never Married','Divorced','Widowed','Awaited Divorce') DEFAULT NULL,
  `about_self` TEXT DEFAULT NULL,
  `religion` ENUM('Hindu','Muslim','Christian','Sikh','Buddhist','Jain','Parsi','Other') DEFAULT NULL,
  `caste` VARCHAR(100) DEFAULT NULL,
  `sub_caste` VARCHAR(100) DEFAULT NULL,
  `gothram` VARCHAR(100) DEFAULT NULL,
  `star_sign` VARCHAR(50) DEFAULT NULL,
  `zodiac` VARCHAR(20) DEFAULT NULL,
  `dosham` ENUM('Yes','No','Not Sure') DEFAULT NULL,
  `mother_tongue` VARCHAR(50) DEFAULT NULL,
  `country` VARCHAR(60) DEFAULT NULL,
  `state` VARCHAR(100) DEFAULT NULL,
  `city` VARCHAR(100) DEFAULT NULL,
  `citizenship` VARCHAR(60) DEFAULT NULL,
  `residential_status` ENUM('Owned','Rented','Parents','Family') DEFAULT NULL,
  `highest_education` ENUM('High School','Bachelors','Masters','Doctorate','Professional') DEFAULT NULL,
  `education_detail` VARCHAR(255) DEFAULT NULL,
  `occupation` VARCHAR(150) DEFAULT NULL,
  `occupation_type` ENUM('Government','Private','Business','Self Employed','Freelance','Homemaker','Retired') DEFAULT NULL,
  `annual_income` VARCHAR(50) DEFAULT NULL,
  `family_type` ENUM('Joint','Nuclear') DEFAULT NULL,
  `family_status` ENUM('Middle Class','Upper Middle Class','Rich','Affluent') DEFAULT NULL,
  `family_values` ENUM('Traditional','Moderate','Orthodox','Liberal') DEFAULT NULL,
  `father_name` VARCHAR(150) DEFAULT NULL,
  `father_occupation` VARCHAR(150) DEFAULT NULL,
  `mother_name` VARCHAR(150) DEFAULT NULL,
  `mother_occupation` VARCHAR(150) DEFAULT NULL,
  `brothers` TINYINT UNSIGNED DEFAULT 0,
  `brothers_married` TINYINT UNSIGNED DEFAULT 0,
  `sisters` TINYINT UNSIGNED DEFAULT 0,
  `sisters_married` TINYINT UNSIGNED DEFAULT 0,
  `family_location` VARCHAR(150) DEFAULT NULL,
  `diet` ENUM('Vegetarian','Non-Vegetarian','Eggetarian','Vegan','Jain') DEFAULT NULL,
  `smoking` ENUM('No','Sometimes','Often') DEFAULT NULL,
  `drinking` ENUM('No','Sometimes','Often') DEFAULT NULL,
  `pref_age_min` TINYINT UNSIGNED DEFAULT NULL,
  `pref_age_max` TINYINT UNSIGNED DEFAULT NULL,
  `pref_height_min` VARCHAR(20) DEFAULT NULL,
  `pref_height_max` VARCHAR(20) DEFAULT NULL,
  `pref_education` VARCHAR(255) DEFAULT NULL,
  `pref_location` VARCHAR(255) DEFAULT NULL,
  `pref_other` TEXT DEFAULT NULL,
  `profile_photo` VARCHAR(255) DEFAULT NULL,
  `gallery_photo_1` VARCHAR(255) DEFAULT NULL,
  `gallery_photo_2` VARCHAR(255) DEFAULT NULL,
  `gallery_photo_3` VARCHAR(255) DEFAULT NULL,
  `gallery_photo_4` VARCHAR(255) DEFAULT NULL,
  `gallery_photo_5` VARCHAR(255) DEFAULT NULL,
  `email_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `phone_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `id_verified` TINYINT(1) NOT NULL DEFAULT 0,
  `is_admin` TINYINT(1) NOT NULL DEFAULT 0,
  `is_suspended` TINYINT(1) NOT NULL DEFAULT 0,
  `twofa_secret` VARCHAR(64) DEFAULT NULL,
  `is_approved` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create schema_meta table
CREATE TABLE `schema_meta` (
  `id` TINYINT UNSIGNED NOT NULL DEFAULT 1,
  `schema_version` VARCHAR(40) NOT NULL DEFAULT '',
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seed schema_meta version
INSERT INTO `schema_meta` (`id`, `schema_version`) VALUES (1, 'v7') ON DUPLICATE KEY UPDATE `schema_version` = 'v7';

-- Create favourites table
CREATE TABLE `favourites` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `profile_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_fav` (`user_id`,`profile_id`),
  KEY `fk_fav_profile` (`profile_id`),
  CONSTRAINT `fk_fav_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_fav_profile` FOREIGN KEY (`profile_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create password_resets table
CREATE TABLE `password_resets` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `token_hash` CHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `used` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pw_user` (`user_id`),
  KEY `idx_pw_token` (`token_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create email_verifications table
CREATE TABLE `email_verifications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `token_hash` CHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `used` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ev_user` (`user_id`),
  KEY `idx_ev_token` (`token_hash`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create messages table
CREATE TABLE `messages` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create notifications table
CREATE TABLE `notifications` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `type` VARCHAR(30) NOT NULL DEFAULT 'general',
  `message` VARCHAR(255) NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_notif_user` (`user_id`),
  KEY `idx_notif_read` (`user_id`,`is_read`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create profile_views table
CREATE TABLE `profile_views` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `viewer_id` INT UNSIGNED NOT NULL,
  `profile_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_pv_profile` (`profile_id`),
  KEY `idx_pv_viewer` (`viewer_id`),
  KEY `idx_pv_unique` (`viewer_id`,`profile_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create shortlists table
CREATE TABLE `shortlists` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `profile_id` INT UNSIGNED NOT NULL,
  `note` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_shortlist` (`user_id`,`profile_id`),
  KEY `idx_short_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create interests table
CREATE TABLE `interests` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create blocks table
CREATE TABLE `blocks` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `blocker_id` INT UNSIGNED NOT NULL,
  `blocked_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_block` (`blocker_id`,`blocked_id`),
  KEY `idx_block_blocker` (`blocker_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create reports table
CREATE TABLE `reports` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create otp_codes table
CREATE TABLE `otp_codes` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create verification_requests table
CREATE TABLE `verification_requests` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create media_moderation table
CREATE TABLE `media_moderation` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create activity_logs table
CREATE TABLE `activity_logs` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create email_campaigns table
CREATE TABLE `email_campaigns` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create remember_tokens table
CREATE TABLE `remember_tokens` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` INT UNSIGNED NOT NULL,
  `token_hash` CHAR(64) NOT NULL,
  `expires_at` DATETIME NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_rt_token` (`token_hash`),
  KEY `idx_rt_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
