<?php
require 'db.php';

try {
    // Check users table
    $stmt = $conn->query("SELECT u.*, l.lab_name, l.address as lab_address 
                         FROM users u 
                         LEFT JOIN laboratories l ON u.lab_id = l.lab_id 
                         WHERE u.user_type = 'lab_admin'");
    $lab_admins = $stmt->fetchAll();

    echo "<h2>Lab Admins in Database:</h2>";
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
                <th>Lab Name</th>
                <th>Lab Address</th>
              </tr>";
        foreach ($lab_admins as $admin) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($admin['user_id']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['username']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['email']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['user_type']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['lab_id']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['lab_name']) . "</td>";
            echo "<td>" . htmlspecialchars($admin['lab_address']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }

    // Check laboratories table
    $stmt = $conn->query("SELECT * FROM laboratories");
    $labs = $stmt->fetchAll();

    echo "<h2>Laboratories in Database:</h2>";
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