<?php
/**
 * Database Connection Configuration
 * Rajarata University of Sri Lanka - ICT 2209 Web Technologies
 * Mini Project: Travel Itinerary Planner
 */

// Database credentials
$db_host = '127.0.0.1';
$db_port = '3306';
$db_name = 'travel_planner';
$db_user = 'root';
$db_pass = ''; // Default XAMPP password is empty

try {
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    // If the database does not exist yet, show a user-friendly setup notification
    die('<div style="font-family: sans-serif; max-width: 600px; margin: 50px auto; padding: 25px; border: 1px solid #f5c6cb; background-color: #f8d7da; color: #721c24; border-radius: 8px;">
        <h3>Database Connection Failed</h3>
        <p>Could not connect to the MySQL database <strong>' . htmlspecialchars($db_name) . '</strong>.</p>
        <p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>
        <hr>
        <p>Please make sure:</p>
        <ol>
            <li>MySQL is running in XAMPP / WAMP.</li>
            <li>You have imported <code>database.sql</code> into phpMyAdmin or MySQL.</li>
        </ol>
    </div>');
}
