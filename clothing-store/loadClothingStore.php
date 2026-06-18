<?php
/**
 * Load ClothingStore Database Script
 * This script will create all tables in the ClothingStore database
 * Tables are dropped before creating (only if they don't exist)
 */

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'ClothingStore';

// Create connection without database
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

echo "<h2>Loading ClothingStore Database</h2>";

// Create database if not exists
$sql = "CREATE DATABASE IF NOT EXISTS $dbname";
if ($conn->query($sql) === TRUE) {
    echo "<p>✓ Database ClothingStore created/exists</p>";
} else {
    echo "<p>✗ Error creating database: " . $conn->error . "</p>";
}

// Select database
$conn->select_db($dbname);

// Read SQL file
$sql_file = 'database/clothingstore.sql';
if (file_exists($sql_file)) {
    $sql_content = file_get_contents($sql_file);
    
    // Split into individual statements
    $statements = explode(';', $sql_content);
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && strpos($statement, '--') !== 0) {
            // Skip USE statement since we already selected the database
            if (stripos($statement, 'USE ') === 0) continue;
            if (stripos($statement, 'CREATE DATABASE') === 0) continue;
            
            if ($conn->query($statement) === TRUE) {
                $success_count++;
            } else {
                // Only show error if it's not a "table doesn't exist" error
                if (strpos($conn->error, "doesn't exist") === false) {
                    echo "<p>Note: " . $conn->error . "</p>";
                }
                $error_count++;
            }
        }
    }
    
    echo "<p>✓ Executed $success_count SQL statements</p>";
    echo "<p>Database setup complete!</p>";
} else {
    echo "<p>✗ SQL file not found at $sql_file</p>";
}

// Show table counts
echo "<h3>Table Summary:</h3>";
$tables = ['tblAdmin', 'tblUser', 'tblCategory', 'tblClothes', 'tblCart', 'tblWishlist', 'tblOrder', 'tblOrderItems', 'tblContact'];

foreach ($tables as $table) {
    $result = $conn->query("SELECT COUNT(*) as count FROM $table");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<p>$table: {$row['count']} records</p>";
    }
}

echo "<hr>";
echo "<p><a href='index.php'>Go to Homepage</a> | <a href='admin/login.php'>Admin Login</a></p>";

$conn->close();
?>
