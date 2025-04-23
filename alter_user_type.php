<?php
require 'db.php';

try {
    // First, check the current structure of the users table
    echo "<h2>Current Users Table Structure:</h2>";
    $stmt = $conn->query("DESCRIBE users");
    $columns = $stmt->fetchAll();
    echo "<table border='1' style='width:100%; border-collapse: collapse;'>";
    echo "<tr style='background-color: #f2f2f2;'><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($col['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($col['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($col['Extra']) . "</td>";
        echo "</tr>";
    }
    echo "</table>";

    // First, drop the existing ENUM constraint
    $stmt = $conn->prepare("ALTER TABLE users MODIFY COLUMN user_type VARCHAR(20) NOT NULL");
    $stmt->execute();
    echo "<p style='color: green;'>Successfully removed ENUM constraint</p>";

    // Then add the new ENUM with all required values
    $stmt = $conn->prepare("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin', 'patient', 'lab_admin') NOT NULL");
    $stmt->execute();
    echo "<p style='color: green;'>Successfully added new ENUM with lab_admin option</p>";

    // Create a new lab admin user
    $username = 'labadmin';
    $password = password_hash('labadmin123', PASSWORD_DEFAULT);
    $email = 'labadmin@example.com';
    $user_type = 'lab_admin';

    // First check if user already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = ?");
    $stmt->execute([$username]);
    if ($stmt->fetchColumn() > 0) {
        echo "<p style='color: red;'>User 'labadmin' already exists</p>";
    } else {
        // Insert new lab admin user
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, user_type) VALUES (?, ?, ?, ?)");
        $result = $stmt->execute([$username, $password, $email, $user_type]);
        
        if ($result) {
            echo "<p style='color: green;'>Successfully created new lab admin user</p>";
            echo "<p>Username: labadmin</p>";
            echo "<p>Password: labadmin123</p>";
        } else {
            echo "<p style='color: red;'>Failed to create lab admin user</p>";
        }
    }

    // Show all users to verify the changes
    echo "<h2>All Users in Database:</h2>";
    $stmt = $conn->query("SELECT * FROM users");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "<p>No users found in the database.</p>";
    } else {
        echo "<table border='1' style='width:100%; border-collapse: collapse;'>";
        echo "<tr style='background-color: #f2f2f2;'>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Lab ID</th>
                <th>Hospital ID</th>
              </tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($user['user_id']) . "</td>";
            echo "<td>" . htmlspecialchars($user['username']) . "</td>";
            echo "<td>" . htmlspecialchars($user['email']) . "</td>";
            echo "<td>" . htmlspecialchars($user['user_type']) . "</td>";
            echo "<td>" . htmlspecialchars($user['lab_id'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($user['hospital_id'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>";
    print_r($e->errorInfo);
    echo "</pre>";
}
?> 