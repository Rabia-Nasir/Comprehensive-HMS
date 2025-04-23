-- Insert sample pharmacies
INSERT INTO pharmacies (name, address, contact_number, email) VALUES
('City Pharmacy', '123 Main Street, City Center', '9876543210', 'citypharmacy@example.com'),
('Health Plus', '456 Medical Road, Health District', '9876543211', 'healthplus@example.com'),
('MediCare', '789 Wellness Avenue, Care Zone', '9876543212', 'medicare@example.com');

-- Insert sample medicines for City Pharmacy
INSERT INTO medicines (pharmacy_id, name, description, manufacturer, category, price, quantity, expiry_date) VALUES
(1, 'Paracetamol 500mg', 'Pain reliever and fever reducer', 'Generic Pharma', 'Pain Relief', 10.00, 100, '2025-12-31'),
(1, 'Vitamin C 500mg', 'Immune system support', 'Health Labs', 'Vitamins', 15.00, 50, '2024-12-31'),
(1, 'Cetirizine 10mg', 'Antihistamine for allergies', 'Allergy Care', 'Allergy', 20.00, 75, '2024-06-30');

-- Insert sample medicines for Health Plus
INSERT INTO medicines (pharmacy_id, name, description, manufacturer, category, price, quantity, expiry_date) VALUES
(2, 'Ibuprofen 400mg', 'Anti-inflammatory pain reliever', 'Pain Free Inc', 'Pain Relief', 25.00, 60, '2024-09-30'),
(2, 'Vitamin D 1000IU', 'Bone health supplement', 'Sunshine Health', 'Vitamins', 30.00, 40, '2024-12-31'),
(2, 'Loratadine 10mg', 'Non-drowsy allergy relief', 'Allergy Care', 'Allergy', 35.00, 30, '2024-08-31');

-- Insert sample medicines for MediCare
INSERT INTO medicines (pharmacy_id, name, description, manufacturer, category, price, quantity, expiry_date) VALUES
(3, 'Aspirin 75mg', 'Blood thinner and pain reliever', 'Heart Health', 'Cardiac', 12.00, 80, '2024-11-30'),
(3, 'Multivitamin Complex', 'Complete daily vitamin supplement', 'Wellness Labs', 'Vitamins', 40.00, 45, '2024-10-31'),
(3, 'Omeprazole 20mg', 'Acid reducer for heartburn', 'Digest Health', 'Digestive', 45.00, 55, '2024-07-31'); 