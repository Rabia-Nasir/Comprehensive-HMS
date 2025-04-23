<?php
session_start();
require 'db.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $lab_name = $_POST['lab_name'];
    $lab_address = $_POST['lab_address'];
    $lab_contact = $_POST['lab_contact'];
    $lab_email = $_POST['lab_email'];

    try {
        // Debug: Print registration attempt
        error_log("Lab registration attempt - Username: $username, Lab Name: $lab_name");

        // Start transaction
        $conn->beginTransaction();

        // First, insert the laboratory
        $stmt = $conn->prepare("
            INSERT INTO laboratories (lab_name, address, contact_number, email) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$lab_name, $lab_address, $lab_contact, $lab_email]);
        $lab_id = $conn->lastInsertId();
        error_log("Laboratory created with ID: $lab_id");

        // Then, insert the lab admin user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
            INSERT INTO users (username, password, full_name, email, phone, user_type, lab_id) 
            VALUES (?, ?, ?, ?, ?, 'lab_admin', ?)
        ");
        $stmt->execute([$username, $hashed_password, $full_name, $email, $phone, $lab_id]);
        $user_id = $conn->lastInsertId();
        error_log("Lab admin user created with ID: $user_id");

        $conn->commit();
        $success_message = "Lab admin registration successful! You can now login.";
    } catch (PDOException $e) {
        $conn->rollBack();
        error_log("Registration error: " . $e->getMessage());
        $error_message = "Registration failed: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Admin Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Lab Admin Registration</h3>
                    </div>
                    <div class="card-body">
                        <?php if ($error_message): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
                        <?php endif; ?>

                        <?php if ($success_message): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="">
                            <h4 class="mb-3">Personal Information</h4>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="tel" name="phone" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" required>
                            </div>

                            <h4 class="mb-3">Laboratory Information</h4>
                            <div class="mb-3">
                                <label class="form-label">Laboratory Name</label>
                                <input type="text" name="lab_name" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Laboratory Address</label>
                                <textarea name="lab_address" class="form-control" rows="3" required></textarea>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Laboratory Contact Number</label>
                                    <input type="tel" name="lab_contact" class="form-control" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Laboratory Email</label>
                                    <input type="email" name="lab_email" class="form-control" required>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Register</button>
                                <a href="index.php" class="btn btn-secondary">Back to Login</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 