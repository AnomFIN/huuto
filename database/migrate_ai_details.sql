-- Migration: Add ai_details column to auctions table
-- Run this on existing databases to add AI-generated category-specific details support

ALTER TABLE auctions ADD COLUMN IF NOT EXISTS ai_details TEXT DEFAULT NULL AFTER condition_description;
