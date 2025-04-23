-- Add status column to users table
ALTER TABLE users
ADD COLUMN status ENUM('active', 'inactive') NOT NULL DEFAULT 'active';

-- Note: This will:
-- 1. Add a new status column with ENUM type
-- 2. Set NOT NULL constraint to ensure all users have a status
-- 3. Set DEFAULT value as 'active' for existing and new records