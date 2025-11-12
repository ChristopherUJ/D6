<?php

// Database configuration
$host = 'mysql';
$dbname = 'd6_db';
$username = 'user';
$password = 'password';

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Database Connection Test</h1>";
    echo "<p style='color: green;'>✓ Successfully connected to MySQL database!</p>";

    // Get MySQL version
    $version = $pdo->query('SELECT VERSION()')->fetchColumn();
    echo "<p>MySQL Version: <strong>$version</strong></p>";

    // Create a test table if it doesn't exist
    $createTableSQL = "
        CREATE TABLE IF NOT EXISTS test_users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )
    ";
    $pdo->exec($createTableSQL);
    echo "<p style='color: green;'>✓ Test table 'test_users' created/verified</p>";

    // Insert sample data
    $stmt = $pdo->prepare("INSERT INTO test_users (name, email) VALUES (?, ?)");
    $stmt->execute(['John Doe', 'john@example.com']);
    echo "<p style='color: green;'>✓ Sample data inserted</p>";

    // Fetch and display data
    $stmt = $pdo->query("SELECT * FROM test_users ORDER BY id DESC LIMIT 5");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<h2>Recent Users:</h2>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Created At</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['name']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>{$user['created_at']}</td>";
        echo "</tr>";
    }
    echo "</table>";

    echo "<hr>";
    echo "<p><a href='index.php'>Back to Home</a></p>";

} catch (PDOException $e) {
    echo "<h1>Database Connection Error</h1>";
    echo "<p style='color: red;'>✗ Connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<h3>Troubleshooting:</h3>";
    echo "<ul>";
    echo "<li>Make sure Docker containers are running: <code>docker-compose ps</code></li>";
    echo "<li>Check if MySQL container is ready: <code>docker-compose logs mysql</code></li>";
    echo "<li>Verify database credentials in docker-compose.yml</li>";
    echo "</ul>";
}
?>
