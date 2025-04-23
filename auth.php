<?php
session_start();
require 'db.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /hospital management system/index.php");
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: /hospital management system/patient/dashboard.php");
        exit;
    }
}

function logout() {
    session_destroy();
    header("Location: /hospital management system/index.php");
    exit;
}

// Handle Signup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
    $full_name = $_POST['fullname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $confirm_password = $_POST['confirm_password'];

    if ($_POST['password'] !== $confirm_password) {
        echo "Passwords do not match.";
        exit;
    }

    // Check if the username already exists
    $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM users WHERE username = ?");
    $checkStmt->execute([$username]);
    $userExists = $checkStmt->fetchColumn();

    if ($userExists > 0) {
        echo "Username already exists. Please choose a different one.";
        exit;
    }

    try {
        $stmt = $conn->prepare("INSERT INTO users (full_name, username, email, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$full_name, $username, $email, $password]);

        // Get the newly created user's ID
        $user_id = $conn->lastInsertId();
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $username;

        header("Location: homepage.php");
        exit;
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Handle Login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['user_type'] = $user['user_type'];

        if ($user['user_type'] === 'admin') {
            header("Location: admin/dashboard.php");
        } else {
            header("Location: patient/dashboard.php");
        }
        exit;
    } else {
        $error_message = "Invalid email or password.";
    }
}

// Handle Logout
if (isset($_GET['logout'])) {
    logout();
}
?>