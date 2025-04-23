<?php
require 'db.php';

try {
    // First, check if the laboratories table exists and has data
    $stmt = $conn->query("SELECT * FROM laboratories");
    $labs = $stmt->fetchAll();
    
    if (empty($labs)) {
        // Create a test laboratory if none exists
        $stmt = $conn->prepare("INSERT INTO laboratories (lab_name, address, contact_number, email) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Test Lab', 'Test Address', '1234567890', 'test@lab.com']);
        $lab_id = $conn->lastInsertId();
        echo "<p>Created test laboratory with ID: $lab_id</p>";
    } else {
        $lab_id = $labs[0]['lab_id'];
        echo "<p>Using existing laboratory with ID: $lab_id</p>";
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

    // Try to insert a test lab admin
    $test_username = 'test_lab_admin_' . time();
    $test_password = password_hash('test123', PASSWORD_DEFAULT);
    $test_email = 'test_' . time() . '@example.com';

    $stmt = $conn->prepare("INSERT INTO users (username, password, email, user_type, lab_id) VALUES (?, ?, ?, 'lab_admin', ?)");
    $result = $stmt->execute([$test_username, $test_password, $test_email, $lab_id]);

    if ($result) {
        $user_id = $conn->lastInsertId();
        echo "<p>Successfully created test lab admin with ID: $user_id</p>";
        
        // Verify the created user
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
        
        echo "<h2>Created User Details:</h2>";
        echo "<pre>";
        print_r($user);
        echo "</pre>";
    } else {
        echo "<p>Failed to create test lab admin</p>";
        echo "<p>Error: " . print_r($stmt->errorInfo(), true) . "</p>";
    }

    // Show all users
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
    echo "Error: " . $e->getMessage();
    echo "<pre>";
    print_r($e->errorInfo);
    echo "</pre>";
}
?> 