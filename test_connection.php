<?php
// Database connection test
echo "<h2>Database Connection Test</h2>";

$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'shopwave_db';
$port = '3306';

echo "Testing connection with:<br>";
echo "Host: $host<br>";
echo "User: $user<br>";
echo "Database: $db<br>";
echo "Port: $port<br><br>";

// Test 1: Basic connection
echo "<strong>Test 1: Basic Connection</strong><br>";
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass);
    echo "✓ Connection successful!<br><br>";
    
    // Test 2: Check tables
    echo "<strong>Test 2: Checking Tables</strong><br>";
    $result = $pdo->query("SHOW TABLES");
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables found: " . count($tables) . "<br>";
    foreach($tables as $table) {
        echo "  - $table<br>";
    }
    
} catch (PDOException $e) {
    echo "✗ Connection failed!<br>";
    echo "Error: " . $e->getMessage() . "<br><br>";
    
    // Try alternative connection with socket
    echo "<strong>Trying alternative connection method...</strong><br>";
    try {
        $pdo = new PDO("mysql:unix_socket=/tmp/mysql.sock;dbname=$db;charset=utf8mb4", $user, $pass);
        echo "✓ Socket connection successful!<br>";
    } catch (PDOException $e2) {
        echo "✗ Socket connection also failed.<br>";
        echo "Error: " . $e2->getMessage() . "<br>";
    }
}
?>
