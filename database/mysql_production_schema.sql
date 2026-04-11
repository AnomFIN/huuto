-- MySQL schema for huuto247.fi production server
-- Run this in phpMyAdmin to create the database structure

-- Categories table
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(50) UNIQUE NOT NULL,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Users table  
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) UNIQUE NOT NULL,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `first_name` VARCHAR(50),
    `last_name` VARCHAR(50),
    `full_name` VARCHAR(100),
    `phone` VARCHAR(20),
    `address` TEXT,
    `city` VARCHAR(100),
    `postal_code` VARCHAR(20),
    `country` VARCHAR(50) DEFAULT 'Finland',
    `is_verified` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_username` (`username`),
    INDEX `idx_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Auctions table
CREATE TABLE IF NOT EXISTS `auctions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `category_id` INT,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT,
    `short_summary` VARCHAR(255),
    `starting_price` DECIMAL(10,2) DEFAULT 0.00,
    `current_price` DECIMAL(10,2) DEFAULT 0.00,
    `reserve_price` DECIMAL(10,2),
    `buy_now_price` DECIMAL(10,2),
    `condition_id` INT,
    `location` VARCHAR(255),
    `status` VARCHAR(20) DEFAULT 'active',
    `featured` BOOLEAN DEFAULT FALSE,
    `views` INT DEFAULT 0,
    `end_time` TIMESTAMP NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL,
    INDEX `idx_status` (`status`),
    INDEX `idx_featured` (`featured`),
    INDEX `idx_end_time` (`end_time`),
    INDEX `idx_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bids table
CREATE TABLE IF NOT EXISTS `bids` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `auction_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`auction_id`) REFERENCES `auctions`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    INDEX `idx_auction` (`auction_id`),
    INDEX `idx_amount` (`amount`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Auction images table
CREATE TABLE IF NOT EXISTS `auction_images` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `auction_id` INT NOT NULL,
    `image_path` VARCHAR(500) NOT NULL,
    `caption` VARCHAR(255),
    `is_primary` BOOLEAN DEFAULT FALSE,
    `sort_order` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`auction_id`) REFERENCES `auctions`(`id`) ON DELETE CASCADE,
    INDEX `idx_auction` (`auction_id`),
    INDEX `idx_primary` (`is_primary`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Watchlist table
CREATE TABLE IF NOT EXISTS `watchlist` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `auction_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`auction_id`) REFERENCES `auctions`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_watch` (`user_id`, `auction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Auction metadata table
CREATE TABLE IF NOT EXISTS `auction_metadata` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `auction_id` INT NOT NULL,
    `field_name` VARCHAR(100) NOT NULL,
    `field_value` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`auction_id`) REFERENCES `auctions`(`id`) ON DELETE CASCADE,
    UNIQUE KEY `unique_field` (`auction_id`, `field_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sample categories for production
INSERT IGNORE INTO `categories` (`id`, `name`, `slug`, `description`) VALUES
(1, 'Elektroniikka', 'elektroniikka', 'Tietokoneet, puhelimet ja muut laitteet'),
(2, 'Kodinkoneet', 'kodinkoneet', 'Kodinkoneet ja kodin tarvikkeet'),
(3, 'Vaatteet', 'vaatteet', 'Vaatteet ja asusteet'),
(4, 'Urheilu', 'urheilu', 'Urheiluvälineet ja varusteet'),
(5, 'Kirjat', 'kirjat', 'Kirjat ja lehdet'),
(6, 'Lelut', 'lelut', 'Lelut ja pelit'),
(7, 'Autot', 'autot', 'Autot ja ajoneuvot'),
(8, 'Huonekalut', 'huonekalut', 'Huonekalut ja sisustus'),
(9, 'Musiikki', 'musiikki', 'Musiikkisoittimet ja levyt'),
(10, 'Muut', 'muut', 'Muut tavarat');