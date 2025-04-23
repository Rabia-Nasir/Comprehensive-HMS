<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $user_type = $_POST['user_type'];

    // Debug: Print all POST data
    error_log("Login attempt - Username: $username, User Type: $user_type");

    try {
        // First, check if the user exists
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if (!$user) {
            error_log("No user found with username: $username");
            $_SESSION['error_message'] = "Invalid username or password.";
            header("Location: index.php");
            exit;
        }

        // Debug: Print user data
        error_log("User found: " . print_r($user, true));

        // Verify password
        if (!password_verify($password, $user['password'])) {
            error_log("Password verification failed for user: $username");
            $_SESSION['error_message'] = "Invalid username or password.";
            header("Location: index.php");
            exit;
        }

        // Set session variables
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['user_type'];
        
        // Set appropriate ID based on user type
        if ($user['user_type'] === 'lab_admin') {
            $_SESSION['lab_id'] = $user['lab_id'];
            error_log("Lab admin logged in. Lab ID: " . $user['lab_id']);
        } else if ($user['user_type'] === 'admin') {
            $_SESSION['hospital_id'] = $user['hospital_id'];
        }

        // Debug: Print session data
        error_log("Session data after login: " . print_r($_SESSION, true));

        // Redirect based on user type
        switch ($user['user_type']) {
            case 'admin':
                header("Location: admin/dashboard.php");
                break;
            case 'lab_admin':
                header("Location: admin/lab_dashboard.php");
                break;
            case 'patient':
                header("Location: patient/dashboard.php");
                break;
            default:
                header("Location: index.php");
        }
        exit;
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        $_SESSION['error_message'] = "Login failed. Please try again.";
        header("Location: index.php");
        exit;
    }
}
?> 