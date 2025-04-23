-- SQL queries to modify users table

-- Add new columns
ALTER TABLE users
ADD COLUMN address TEXT,
ADD COLUMN date_of_birth DATE,
ADD COLUMN gender ENUM('male', 'female', 'other');

-- Modify existing columns
ALTER TABLE users
MODIFY COLUMN phone VARCHAR(20);

-- Add or remove constraints
ALTER TABLE users
MODIFY COLUMN hospital_id INT NOT NULL;

-- Add new indexes
ALTER TABLE users
ADD INDEX idx_user_type (user_type),
ADD INDEX idx_status (status);

-- Note: Before running these queries, make sure to:
-- 1. Backup your database
-- 2. Test queries in a development environment first
-- 3. Consider the impact on existing data
-- 4. Run queries during low-traffic periods