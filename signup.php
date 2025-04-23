<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $full_name = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = $_POST['phone'];
    $user_type = $_POST['user_type'];
    $hospital_id = null;
    $error_message = '';

    // If admin, get hospital details
    if ($user_type === 'admin') {
        $hospital_name = $_POST['hospital_name'];
        $hospital_address = $_POST['hospital_address'];
        
        // First create hospital with default values for beds
        try {
            $stmt = $conn->prepare("INSERT INTO hospitals (hospital_name, address, total_beds, available_beds) VALUES (?, ?, 100, 100)");
            $stmt->execute([$hospital_name, $hospital_address]);
            $hospital_id = $conn->lastInsertId();
        } catch (PDOException $e) {
            $error_message = "Error creating hospital: " . $e->getMessage();
        }
    }

    // Only proceed with user creation if there's no error
    if (empty($error_message)) {
        if ($password !== $confirm_password) {
            $error_message = 'Passwords do not match.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Check if the username or email already exists
            $checkStmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ? OR email = ?");
            $checkStmt->execute([$username, $email]);
            $userExists = (int)$checkStmt->fetchColumn();

            if ($userExists > 0) {
                $error_message = 'Username or Email already exists.';
            } else {
                try {
                    // For patients, hospital_id will be null
                    // For admins, hospital_id will be set from the hospital creation
                    $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password, phone, user_type, hospital_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$full_name, $username, $email, $hashed_password, $phone, $user_type, $hospital_id]);

                    echo "<script>alert('Account created successfully!'); window.location.href='index.php';</script>";
                    exit;
                } catch (PDOException $e) {
                    $error_message = "Error creating user: " . $e->getMessage();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Hospital System</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="signup-container">
        <h1>Hospital System Registration</h1>
        <form id="signup-form" action="signup.php" method="POST">
            <select name="user_type" class="input-field" required onchange="toggleHospitalFields(this.value)">
                <option value="">Select User Type</option>
                <option value="patient">Patient</option>
                <option value="admin">Hospital Admin</option>
            </select>

            <input type="text" name="fullname" class="input-field" placeholder="Full Name" required>
            <input type="text" name="username" class="input-field" placeholder="Username" required>
            <input type="email" name="email" class="input-field" placeholder="Email" required>
            <input type="tel" name="phone" class="input-field" placeholder="Phone Number" required>
            
            <!-- Hospital fields (hidden by default) -->
            <div id="hospital-fields" style="display: none;">
                <input type="text" name="hospital_name" class="input-field" placeholder="Hospital Name">
                <input type="text" name="hospital_address" class="input-field" placeholder="Hospital Address">
            </div>

            <input type="password" name="password" class="input-field" placeholder="Password" required>
            <input type="password" name="confirm_password" class="input-field" placeholder="Confirm Password" required>
            
            <button type="submit" name="signup" class="signup-btn">Sign Up</button>
            
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
        </form>
        <p class="login-text">Already have an account? <a href="index.php">Log in</a></p>
    </div>

    <script>
        function toggleHospitalFields(userType) {
            const hospitalFields = document.getElementById('hospital-fields');
            if (userType === 'admin') {
                hospitalFields.style.display = 'block';
                hospitalFields.querySelectorAll('input').forEach(input => input.required = true);
            } else {
                hospitalFields.style.display = 'none';
                hospitalFields.querySelectorAll('input').forEach(input => input.required = false);
            }
        }
    </script>
</body>
</html>