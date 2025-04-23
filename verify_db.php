<?php
require 'db.php';

try {
    // Check if tables exist
    $tables = ['users', 'laboratories'];
    foreach ($tables as $table) {
        $stmt = $conn->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() == 0) {
            echo "<h2>Error: Table '$table' does not exist!</h2>";
        }
    }

    // Check users table structure
    echo "<h2>Users Table Structure:</h2>";
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

    // Check all users
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

    // Check laboratories table
    echo "<h2>Laboratories in Database:</h2>";
    $stmt = $conn->query("SELECT * FROM laboratories");
    $labs = $stmt->fetchAll();
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