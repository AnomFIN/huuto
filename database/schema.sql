-- Huuto Auction Platform Database Schema

CREATE DATABASE IF NOT EXISTS huuto_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE huuto_db;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    parent_id INT DEFAULT NULL,
    icon VARCHAR(50),
    sort_order INT DEFAULT 0,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_parent (parent_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Auctions table
CREATE TABLE IF NOT EXISTS auctions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    short_summary VARCHAR(255) NULL COMMENT 'Brief auction description for listings',
    starting_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    current_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    reserve_price DECIMAL(10,2) DEFAULT NULL,
    buy_now_price DECIMAL(10,2) DEFAULT NULL,
    bid_increment DECIMAL(10,2) NOT NULL DEFAULT 1.00,
    start_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_time TIMESTAMP NOT NULL,
    status ENUM('draft', 'active', 'ended', 'cancelled') DEFAULT 'draft',
    featured TINYINT(1) DEFAULT 0 COMMENT 'Featured auction flag',
    views INT DEFAULT 0,
    location VARCHAR(200),
    condition_description VARCHAR(50),
    condition_grade VARCHAR(50) NULL COMMENT 'Overall condition grade',
    seller_notes TEXT NULL COMMENT 'Additional seller information',
    pickup_info TEXT NULL COMMENT 'Pickup instructions and details',
    shipping_info TEXT NULL COMMENT 'Shipping options and costs',
    payment_info TEXT NULL COMMENT 'Payment methods and deadlines',
    inspection_info TEXT NULL COMMENT 'Inspection opportunities',
    included_items TEXT NULL COMMENT 'What is included in the sale',
    defects TEXT NULL COMMENT 'Known defects and issues',
    warranty_info VARCHAR(255) NULL COMMENT 'Warranty status',
    model_reference VARCHAR(100) NULL COMMENT 'Model number or reference',
    serial_number VARCHAR(100) NULL COMMENT 'Serial number if applicable',
    delivery_available TINYINT(1) DEFAULT 0 COMMENT 'Delivery option available',
    pickup_available TINYINT(1) DEFAULT 1 COMMENT 'Pickup option available',
    payment_deadline_days INT DEFAULT 1 COMMENT 'Payment deadline in days',
    storage_fee_info VARCHAR(255) NULL COMMENT 'Storage fee details',
    seller_commitment BOOLEAN DEFAULT FALSE,
    ai_details TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    INDEX idx_status (status),
    INDEX idx_end_time (end_time),
    INDEX idx_category (category_id),
    INDEX idx_user (user_id),
    INDEX idx_status_end_time (status, end_time),
    INDEX idx_featured (featured),
    INDEX idx_category_status_end (category_id, status, end_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Auction images table
CREATE TABLE IF NOT EXISTS auction_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auction_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    caption VARCHAR(255) NULL COMMENT 'Image caption or description',
    is_primary BOOLEAN DEFAULT FALSE,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE,
    INDEX idx_auction (auction_id),
    INDEX idx_auction_primary_sort (auction_id, is_primary, sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Bids table
CREATE TABLE IF NOT EXISTS bids (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auction_id INT NOT NULL,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    bid_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    is_auto_bid BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_auction (auction_id),
    INDEX idx_user (user_id),
    INDEX idx_amount (amount)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Watchlist table
CREATE TABLE IF NOT EXISTS watchlist (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    auction_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_watch (user_id, auction_id),
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Auction metadata table for category-specific fields
CREATE TABLE IF NOT EXISTS auction_metadata (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auction_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    field_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_auction_field (auction_id, field_name),
    INDEX idx_auction (auction_id),
    INDEX idx_field (field_name),
    INDEX idx_auction_field (auction_id, field_name),
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seller profiles for enhanced seller information
CREATE TABLE IF NOT EXISTS seller_profiles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    business_name VARCHAR(255) NULL,
    description TEXT NULL,
    logo_path VARCHAR(255) NULL,
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_sales INT DEFAULT 0,
    active_auctions INT DEFAULT 0,
    response_rate DECIMAL(5,2) DEFAULT 100.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_rating (rating),
    INDEX idx_active (active_auctions)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default categories
INSERT INTO categories (name, slug, description, icon, sort_order) VALUES
('Kiinteistöt', 'kiinteistot', 'Tontit, maa-alueet ja kiinteistöt', '🏠', 1),
('Ajoneuvot', 'ajoneuvot', 'Autot, moottoripyörät ja muut ajoneuvot', '🚗', 2),
('Elektroniikka', 'elektroniikka', 'Tietokoneet, puhelimet ja elektroniikka', '💻', 3),
('Kodin tavarat', 'kodin-tavarat', 'Huonekalut ja kodin sisustus', '🏡', 4),
('Urheilu', 'urheilu', 'Urheiluvälineet ja -varusteet', '⚽', 5),
('Vaatteet', 'vaatteet', 'Vaatteet ja asusteet', '👕', 6),
('Keräily', 'keraily', 'Keräilyesineet ja antiikki', '🎨', 7),
('Muut', 'muut', 'Muut tuotteet', '📦', 8);
