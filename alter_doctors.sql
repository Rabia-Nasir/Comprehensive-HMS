ALTER TABLE doctors
ADD COLUMN doctor_name VARCHAR(255) NOT NULL,
ADD COLUMN email VARCHAR(255) NOT NULL,
ADD COLUMN phone VARCHAR(20) NOT NULL,
ADD COLUMN specialization VARCHAR(255) NOT NULL;

-- Update existing doctor_name column if it exists
UPDATE doctors SET doctor_name = doctor_name WHERE doctor_name IS NOT NULL;