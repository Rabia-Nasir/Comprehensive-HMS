USE hospital_system;

-- Add lab_id column to users table
ALTER TABLE users
ADD COLUMN lab_id INT NULL AFTER hospital_id,
ADD CONSTRAINT fk_users_lab
FOREIGN KEY (lab_id) REFERENCES laboratories(lab_id) ON DELETE SET NULL; 