<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $lab_name = $_POST['lab_name'];
    $address = $_POST['address'];
    $contact_number = $_POST['contact_number'];
    $lab_email = $_POST['lab_email'];

    try {
        // Start transaction
        $conn->beginTransaction();

        // First, check if username already exists
        $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
        $stmt->execute([$username]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Username already exists");
        }

        // Insert the laboratory
        $stmt = $conn->prepare("INSERT INTO laboratories (lab_name, address, contact_number, email) VALUES (?, ?, ?, ?)");
        $stmt->execute([$lab_name, $address, $contact_number, $lab_email]);
        $lab_id = $conn->lastInsertId();

        // Create the lab admin user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, user_type, lab_id) VALUES (?, ?, ?, 'lab_admin', ?)");
        $stmt->execute([$username, $hashed_password, $email, $lab_id]);

        // Verify the user was created
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if (!$user) {
            throw new Exception("Failed to create user");
        }

        $conn->commit();
        $_SESSION['success_message'] = "Lab admin account created successfully! You can now login.";
        header("Location: index.php");
        exit;
    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['error_message'] = "Registration failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Lab Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Register Lab Admin</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['error_message'])): ?>
                            <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
                        <?php endif; ?>
                        
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="lab_name" class="form-label">Laboratory Name</label>
                                <input type="text" class="form-control" id="lab_name" name="lab_name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="address" class="form-label">Laboratory Address</label>
                                <input type="text" class="form-control" id="address" name="address" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="text" class="form-control" id="contact_number" name="contact_number" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="lab_email" class="form-label">Laboratory Email</label>
                                <input type="email" class="form-control" id="lab_email" name="lab_email" required>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Register</button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-3">
                            <a href="index.php">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 