<?php
/**
 * Create Table Script - Past Times Clothing Store
 * This script will check if tblUser exists, delete it, and recreate it
 * Then load data from userData.txt
 */
require_once 'includes/DBConn.php';

echo "<h2>Creating Tables for ClothingStore Database</h2>";

// Drop tblUser if exists
$drop_sql = "DROP TABLE IF EXISTS tblUser";
if ($conn->query($drop_sql) === TRUE) {
    echo "<p>✓ Dropped tblUser table (if existed)</p>";
} else {
    echo "<p>✗ Error dropping table: " . $conn->error . "</p>";
}

// Create tblUser table
$create_sql = "CREATE TABLE tblUser (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_verified TINYINT(1) DEFAULT 0,
    is_seller TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB";

if ($conn->query($create_sql) === TRUE) {
    echo "<p>✓ Created tblUser table</p>";
} else {
    echo "<p>✗ Error creating table: " . $conn->error . "</p>";
}

// Load data from userData.txt
$userData_file = 'data/userData.txt';
if (file_exists($userData_file)) {
    $lines = file($userData_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $insert_count = 0;
    
    foreach ($lines as $line) {
        // Skip comment lines
        if (strpos($line, '#') === 0) continue;
        
        $data = explode(',', $line);
        if (count($data) >= 3) {
            $username = trim($data[0]);
            $email = trim($data[1]);
            $password = trim($data[2]);
            $is_verified = isset($data[3]) ? (int)trim($data[3]) : 1;
            
            $insert_sql = "INSERT INTO tblUser (username, email, password, is_verified) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("sssi", $username, $email, $password, $is_verified);
            
            if ($stmt->execute()) {
                $insert_count++;
                echo "<p>✓ Inserted user: $username</p>";
            } else {
                echo "<p>✗ Error inserting user $username: " . $stmt->error . "</p>";
            }
        }
    }
    
    echo "<p><strong>Total users inserted: $insert_count</strong></p>";
} else {
    echo "<p>✗ userData.txt file not found at $userData_file</p>";
}

echo "<hr>";
echo "<p><a href='index.php'>Go to Homepage</a> | <a href='admin/login.php'>Admin Login</a></p>";

$conn->close();
?>
