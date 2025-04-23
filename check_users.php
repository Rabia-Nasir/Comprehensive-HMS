<?php
require 'db.php';

try {
    // Check users table
    $stmt = $conn->query("SELECT * FROM users");
    $users = $stmt->fetchAll();

    echo "<h2>All Users in Database:</h2>";
    if (empty($users)) {
        echo "<p>No users found in the database.</p>";
    } else {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>User Type</th><th>Lab ID</th><th>Hospital ID</th></tr>";
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

    // Check laboratories table
    $stmt = $conn->query("SELECT * FROM laboratories");
    $labs = $stmt->fetchAll();

    echo "<h2>Laboratories in Database:</h2>";
    if (empty($labs)) {
        echo "<p>No laboratories found in the database.</p>";
    } else {
        echo "<table border='1'>";
        echo "<tr><th>ID</th><th>Name</th><th>Address</th><th>Contact</th><th>Email</th></tr>";
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