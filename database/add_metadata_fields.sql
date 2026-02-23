-- Migration: Add auction metadata table for category-specific fields
-- This script adds support for category-specific form fields

-- Add metadata table for additional auction information
CREATE TABLE IF NOT EXISTS auction_metadata (
    id INT AUTO_INCREMENT PRIMARY KEY,
    auction_id INT NOT NULL,
    field_name VARCHAR(100) NOT NULL,
    field_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (auction_id) REFERENCES auctions(id) ON DELETE CASCADE,
    UNIQUE KEY unique_auction_field (auction_id, field_name),
    INDEX idx_auction (auction_id),
    INDEX idx_field (field_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add map coordinates to auctions table
ALTER TABLE auctions ADD COLUMN IF NOT EXISTS map_coordinates VARCHAR(50) NULL;

-- Add seller commitment flag for terms compliance
ALTER TABLE auctions ADD COLUMN IF NOT EXISTS seller_commitment BOOLEAN DEFAULT FALSE;

-- Ensure auction_images table has caption column
ALTER TABLE auction_images ADD COLUMN IF NOT EXISTS caption VARCHAR(255) NULL;