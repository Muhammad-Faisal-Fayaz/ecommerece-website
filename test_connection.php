<?php
/**
 * Database connection test — http://localhost/ecommerece/test_connection.php
 */
echo '<h2>Database Connection Test</h2>';

$host = '127.0.0.1';
$user = 'root';
$pass = '';
$db   = 'shopwave_db';
$port = '3306';

echo '<p>Testing connection with:<br>';
echo 'Host: <strong>' . htmlspecialchars($host) . '</strong><br>';
echo 'User: <strong>' . htmlspecialchars($user) . '</strong><br>';
echo 'Database: <strong>' . htmlspecialchars($db) . '</strong><br>';
echo 'Port: <strong>' . htmlspecialchars($port) . '</strong></p>';

// Check if MySQL port is open
$socket = @fsockopen($host, (int) $port, $errno, $errstr, 2);
if (!$socket) {
    echo '<div style="background:#fdefed;border:1px solid #c0392b;padding:16px;border-radius:6px;margin-bottom:20px;">';
    echo '<strong>MySQL is not running</strong><br>';
    echo 'Nothing is listening on port 3306. Open <strong>XAMPP Control Panel</strong> and click <strong>Start</strong> next to <strong>MySQL</strong>.<br><br>';
    echo 'If Start fails, open <code>D:\\xampp\\mysql\\data\\mysql_error.log</code> and check the last lines.';
    echo '</div>';
} else {
    fclose($socket);
    echo '<p style="color:#27ae60;">✓ Port 3306 is open (MySQL appears to be running)</p>';
}

echo '<strong>PDO connection test</strong><br>';
try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4",
        $user,
        $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    echo '<p style="color:#27ae60;font-weight:bold;">✓ Connection successful!</p>';

    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo '<p><strong>Tables (' . count($tables) . '):</strong></p><ul>';
    foreach ($tables as $table) {
        echo '<li>' . htmlspecialchars($table) . '</li>';
    }
    echo '</ul>';
    echo '<p><a href="/ecommerece/index.php">→ Open ShopWave store</a></p>';
} catch (PDOException $e) {
    echo '<p style="color:#c0392b;">✗ Connection failed: ' . htmlspecialchars($e->getMessage()) . '</p>';
    if (strpos($e->getMessage(), '2002') !== false) {
        echo '<p>Fix: Start <strong>MySQL</strong> in XAMPP Control Panel, then refresh this page.</p>';
    }
    if (strpos($e->getMessage(), '1049') !== false) {
        echo '<p>Fix: Import <code>database.sql</code> in phpMyAdmin to create <strong>shopwave_db</strong>.</p>';
    }
}
