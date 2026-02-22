-- Migration: Add ai_details column to auctions table
-- Compatible with MySQL 5.7+ (ADD COLUMN IF NOT EXISTS is MySQL 8.0+ only)

SET @col_exists := (
  SELECT COUNT(*)
  FROM information_schema.columns
  WHERE table_schema = DATABASE()
    AND table_name = 'auctions'
    AND column_name = 'ai_details'
);

SET @ddl := IF(
  @col_exists = 0,
  'ALTER TABLE auctions ADD COLUMN ai_details TEXT DEFAULT NULL AFTER condition_description',
  'SELECT 1 -- column already exists, nothing to do'
);

PREPARE stmt FROM @ddl;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
