<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['request_ambulance'])) {
    $patient_name = $_POST['patient_name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $emergency_type = $_POST['emergency_type'];
    $description = $_POST['description'];

    try {
        // Extract city/area from patient's address (assuming it's the last part before zip code)
        $address_parts = explode(',', $address);
        $patient_area = trim(end($address_parts));

        // Find hospital in the same area with available beds and doctors
        $stmt = $conn->prepare("
            SELECT h.*, COUNT(d.doctor_id) as available_doctors
            FROM hospitals h
            LEFT JOIN doctors d ON h.hospital_id = d.hospital_id
            WHERE h.available_beds > 0
            AND h.address LIKE ?
            GROUP BY h.hospital_id
            HAVING available_doctors > 0
            ORDER BY h.available_beds DESC, available_doctors DESC
            LIMIT 1
        ");
        $stmt->execute(['%' . $patient_area . '%']);
        $hospital = $stmt->fetch();

        if ($hospital) {
            // Insert emergency request with assigned hospital
            $stmt = $conn->prepare("
                INSERT INTO emergency_requests 
                (patient_name, phone, address, emergency_type, description, assigned_hospital_id, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'accepted')
            ");
            $stmt->execute([
                $patient_name, 
                $phone, 
                $address,
                $emergency_type, 
                $description,
                $hospital['hospital_id']
            ]);

            // Update hospital's available beds
            $stmt = $conn->prepare("
                UPDATE hospitals 
                SET available_beds = available_beds - 1 
                WHERE hospital_id = ?
            ");
            $stmt->execute([$hospital['hospital_id']]);

            $success_message = "Emergency request accepted! Hospital: " . $hospital['hospital_name'] . 
                             " (Contact: " . $hospital['contact_number'] . "). " .
                             "An ambulance has been dispatched to your location.";
        } else {
            $error_message = "No hospitals with available beds and doctors found in your area. Please try calling emergency services directly.";
        }
    } catch (PDOException $e) {
        $error_message = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Portal - Hospital System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .emergency-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .emergency-header {
            color: #dc3545;
            text-align: center;
            margin-bottom: 30px;
        }
        .emergency-btn {
            background: #dc3545;
            border: none;
            padding: 12px;
            font-size: 18px;
            width: 100%;
        }
        .emergency-btn:hover {
            background: #c82333;
        }
        .emergency-type {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .emergency-type label {
            flex: 1;
            padding: 10px;
            border: 2px solid #dc3545;
            border-radius: 5px;
            text-align: center;
            cursor: pointer;
        }
        .emergency-type input[type="radio"] {
            display: none;
        }
        .emergency-type input[type="radio"]:checked + label {
            background: #dc3545;
            color: white;
        }
    </style>
</head>
<body class="bg-light">
    <div class="emergency-container">
        <h1 class="emergency-header">Emergency Portal</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Patient Name</label>
                <input type="text" name="patient_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="tel" name="phone" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="3" required 
                    placeholder="Enter your complete address including street, city, and state"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Emergency Type</label>
                <div class="emergency-type">
                    <input type="radio" name="emergency_type" value="Accident" id="accident" required>
                    <label for="accident">Accident</label>
                    
                    <input type="radio" name="emergency_type" value="Heart Attack" id="heart">
                    <label for="heart">Heart Attack</label>
                    
                    <input type="radio" name="emergency_type" value="Stroke" id="stroke">
                    <label for="stroke">Stroke</label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Description (Optional)</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>

            <button type="submit" name="request_ambulance" class="btn btn-danger emergency-btn">
                Request Ambulance
            </button>
        </form>

        <div class="text-center mt-3">
            <p>For immediate assistance, call: <strong>911</strong></p>
            <a href="index.php" class="btn btn-link">Back to Home</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 