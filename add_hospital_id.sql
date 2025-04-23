-- Add hospital_id column to users table (nullable for patients)
ALTER TABLE users
ADD COLUMN hospital_id INT NULL,
ADD FOREIGN KEY (hospital_id) REFERENCES hospitals(hospital_id);

-- Note: After adding the column, you may need to:
-- 1. Update existing users with appropriate hospital_id values
-- 2. Add NOT NULL constraint if required after data migration