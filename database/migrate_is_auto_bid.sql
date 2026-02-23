-- Migration: Add is_auto_bid column to bids table (if not already present)
-- Compatible with MySQL 5.7+ (ADD COLUMN IF NOT EXISTS is MySQL 8.0+ only)

SET @col_exists := (
  SELECT COUNT(*)
  FROM information_schema.columns
  WHERE table_schema = DATABASE()
    AND table_name = 'bids'
    AND column_name = 'is_auto_bid'
);

SET @ddl := IF(
  @col_exists = 0,
  'ALTER TABLE bids ADD COLUMN is_auto_bid TINYINT(1) NOT NULL DEFAULT 0 AFTER bid_time',
  'SELECT 1 -- column already exists, nothing to do'
);

PREPARE stmt FROM @ddl;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
