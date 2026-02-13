CREATE TABLE IF NOT EXISTS `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `role` ENUM('user', 'admin') DEFAULT 'user',
  `status` ENUM('pending', 'active', 'banned') DEFAULT 'pending',
  `verification_token` VARCHAR(64) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_email (`email`),
  INDEX idx_status (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `name` VARCHAR(100) NOT NULL,
  `icon` VARCHAR(50) DEFAULT NULL,
  `sort_order` INT DEFAULT 0,
  INDEX idx_slug (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `listings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT UNSIGNED NOT NULL,
  `category_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `condition` VARCHAR(50) DEFAULT NULL,
  `region` VARCHAR(100) DEFAULT NULL,
  `start_price` DECIMAL(10,2) NOT NULL,
  `buy_now_price` DECIMAL(10,2) DEFAULT NULL,
  `min_increment` DECIMAL(10,2) NOT NULL DEFAULT 1.00,
  `current_price` DECIMAL(10,2) NOT NULL,
  `highest_bidder_id` INT UNSIGNED DEFAULT NULL,
  `ends_at` DATETIME NOT NULL,
  `status` ENUM('active', 'ended', 'archived') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
  FOREIGN KEY (highest_bidder_id) REFERENCES users(id) ON DELETE SET NULL,
  INDEX idx_slug (`slug`),
  INDEX idx_status (`status`),
  INDEX idx_ends_at (`ends_at`),
  INDEX idx_category (`category_id`),
  INDEX idx_user (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `listing_images` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `listing_id` INT UNSIGNED NOT NULL,
  `path` VARCHAR(255) NOT NULL,
  `sort_order` INT DEFAULT 0,
  FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
  INDEX idx_listing (`listing_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `bids` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `listing_id` INT UNSIGNED NOT NULL,
  `user_id` INT UNSIGNED NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_listing (`listing_id`),
  INDEX idx_user (`user_id`),
  INDEX idx_created (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `pages` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `title` VARCHAR(255) NOT NULL,
  `content_html` TEXT NOT NULL,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_slug (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `blog_posts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `title` VARCHAR(255) NOT NULL,
  `excerpt` TEXT DEFAULT NULL,
  `content_html` TEXT NOT NULL,
  `published_at` TIMESTAMP NULL DEFAULT NULL,
  `status` ENUM('draft', 'published') DEFAULT 'draft',
  INDEX idx_slug (`slug`),
  INDEX idx_status (`status`),
  INDEX idx_published (`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `login_attempts` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `ip` VARCHAR(45) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_ip_email (`ip`, `email`),
  INDEX idx_created (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
