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

    // Modify the user_type column to include 'lab_admin'
    $conn->exec("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin', 'patient', 'lab_admin') NOT NULL");

    // Add lab_id column if it doesn't exist
    $conn->exec("ALTER TABLE users ADD COLUMN IF NOT EXISTS lab_id INT NULL");

    echo "<h2>Table Structure Updated</h2>";
    echo "<p>The users table has been updated to support lab_admin type.</p>";

    // Show current users
    $stmt = $conn->query("SELECT * FROM users");
    $users = $stmt->fetchAll();

    echo "<h2>Current Users:</h2>";
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
    echo "Error: " . $e->getMessage();
}
?> 