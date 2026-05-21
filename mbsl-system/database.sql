-- ============================================================
-- MBSL Insurance Policy Management System
-- Fixed Database Schema + Sample Data
-- Database: mbsl_new_db
-- ============================================================

CREATE DATABASE IF NOT EXISTS `mbsl_new_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `mbsl_new_db`;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `policy_images`;
DROP TABLE IF EXISTS `policies`;
DROP TABLE IF EXISTS `users`;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `users` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(120) NOT NULL,
  `email` VARCHAR(180) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `profile_pic` VARCHAR(500) DEFAULT NULL,
  `role` ENUM('Admin','User') NOT NULL DEFAULT 'User',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `policies` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `holder_name` VARCHAR(120) DEFAULT NULL,
  `category` VARCHAR(80) NOT NULL,
  `premium_amount` DECIMAL(12,2) NOT NULL DEFAULT 0.00,
  `description` TEXT DEFAULT NULL,
  `status` ENUM('Active','Draft','Lapsed','Cancelled') NOT NULL DEFAULT 'Active',
  `user_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_category` (`category`),
  INDEX `idx_status` (`status`),
  CONSTRAINT `fk_policy_user`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `policy_images` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `policy_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(500) NOT NULL,
  `sort_order` SMALLINT NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_policy_id` (`policy_id`),
  CONSTRAINT `fk_image_policy`
    FOREIGN KEY (`policy_id`) REFERENCES `policies` (`id`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`) VALUES
(1, 'Admin User', 'admin@insurance.com', '$2y$12$P8NiNi/0/hZDKMKYI8dFJOPZc5qHqv9s9GQnX8L8R4UqvCl0E3rn6', 'Admin'),
(2, 'Normal User', 'user@insurance.com', '$2y$12$7QJzK5pX6GRNn4YM9L5e4O8d9q3x2r1v5s8k7j9m2l6n0p3t4w5y', 'User');

-- ⚠️ NOTE: Please run fix-demo-credentials.php after importing this database to ensure correct password hashes!
-- Demo Credentials: admin@insurance.com / admin123 | user@insurance.com / user123

INSERT INTO `policies` (`title`, `holder_name`, `category`, `premium_amount`, `description`, `status`, `user_id`, `created_at`) VALUES
('Comprehensive Auto Shield', 'Kasun Perera', 'Motor', 1240.00, 'Full comprehensive motor coverage with roadside assistance.', 'Active', 1, DATE_SUB(NOW(), INTERVAL 5 MONTH)),
('Family Health Secure Plan', 'Nimali Silva', 'Health', 850.00, 'Family health insurance plan with hospital and specialist coverage.', 'Active', 1, DATE_SUB(NOW(), INTERVAL 4 MONTH)),
('Home Protection Plus', 'Amal Fernando', 'Property', 1450.00, 'Home and property protection for fire, theft and natural damage.', 'Active', 1, DATE_SUB(NOW(), INTERVAL 3 MONTH)),
('Business Risk Cover', 'MBSL Traders', 'Business', 2200.00, 'Business liability and operational risk insurance.', 'Active', 1, DATE_SUB(NOW(), INTERVAL 2 MONTH)),
('Travel Safe International', 'Sarah Jayawardena', 'Travel', 430.00, 'International travel insurance with emergency cover.', 'Active', 1, DATE_SUB(NOW(), INTERVAL 1 MONTH)),
('Cyber Security Shield', 'Elena B.', 'Cyber', 1750.00, 'Cyber risk protection policy for data and digital assets.', 'Active', 1, NOW()),
('Life Care Premium', 'Michael R.', 'Life', 3000.00, 'Life insurance policy with long-term protection benefits.', 'Active', 1, NOW()),
('Motorcycle Essential', 'Normal User', 'Motor', 450.00, 'Essential motorcycle coverage including third-party liability.', 'Active', 2, NOW());
