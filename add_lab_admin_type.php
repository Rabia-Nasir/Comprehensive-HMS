<?php
require 'db.php';

try {
    // Modify the user_type column to include 'lab_admin'
    $stmt = $conn->prepare("ALTER TABLE users MODIFY COLUMN user_type ENUM('admin', 'patient', 'lab_admin') NOT NULL");
    $stmt->execute();
    
    echo "<p style='color: green;'>Successfully added 'lab_admin' to user_type options</p>";
    
    // Show the current structure of the users table
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

} catch (PDOException $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
    echo "<pre>";
    print_r($e->errorInfo);
    echo "</pre>";
}
?> 