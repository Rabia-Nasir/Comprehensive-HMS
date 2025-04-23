<?php
require 'db.php';

try {
    // Check and create laboratories table if it doesn't exist
    $conn->exec("CREATE TABLE IF NOT EXISTS laboratories (
        lab_id INT AUTO_INCREMENT PRIMARY KEY,
        lab_name VARCHAR(255) NOT NULL,
        address TEXT NOT NULL,
        contact_number VARCHAR(20) NOT NULL,
        email VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Check and modify users table if needed
    $conn->exec("ALTER TABLE users 
        MODIFY COLUMN user_type ENUM('admin', 'patient', 'lab_admin') NOT NULL,
        ADD COLUMN IF NOT EXISTS lab_id INT NULL,
        ADD FOREIGN KEY IF NOT EXISTS (lab_id) REFERENCES laboratories(lab_id) ON DELETE SET NULL");

    echo "<h2>Database Structure Verified and Updated</h2>";
    echo "<p>Tables and columns have been checked and updated if necessary.</p>";

    // Show current users
    $stmt = $conn->query("SELECT * FROM users WHERE user_type = 'lab_admin'");
    $lab_admins = $stmt->fetchAll();

    echo "<h2>Current Lab Admins:</h2>";
    if (empty($lab_admins)) {
        echo "<p>No lab admins found in the database.</p>";
    } else {
        echo "<table border='1' style='width:100%; border-collapse: collapse;'>";
        echo "<tr style='background-color: #f2f2f2;'>
                <th>ID</th>
                <th>Username</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Lab ID</th>
              </tr>";
        foreach ($lab_admins as $admin) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($admin['user_id']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['username']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['user_type']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['lab_id'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // Show current laboratories
    $stmt = $conn->query("SELECT * FROM laboratories");
    $labs = $stmt->fetchAll();

    echo "<h2>Current Laboratories:</h2>";
    if (empty($labs)) {
        echo "<p>No laboratories found in the database.</p>";
    } else {
        echo "<table border='1' style='width:100%; border-collapse: collapse;'>";
        echo "<tr style='background-color: #f2f2f2;'>
                <th>ID</th>
                <th>Name</th>
                <th>Address</th>
                <th>Contact</th>
                <th>Email</th>
              </tr>";
        foreach ($labs as $lab) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($lab['lab_id']) . "</td>";
            echo "<td>" . htmlspecialchars($lab['lab_name']) . "</td>";
            echo "<td>" . htmlspecialchars($lab['address']) . "</td>";
            echo "<td>" . htmlspecialchars($lab['contact_number']) . "</td>";
            echo "<td>" . htmlspecialchars($lab['email']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 