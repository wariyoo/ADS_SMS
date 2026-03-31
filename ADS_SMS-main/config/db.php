<?php
/**
 * Database Connection Configuration
 * Updated for MySQL on XAMPP (2026)
 * Uses PDO for secure prepared statements.
 */

$host    = 'localhost';
$db      = 'ads_sms';      // Based on your schema's note: 'sm_system'
$user    = 'root';           // Default XAMPP user
$pass    = '';               // Default XAMPP password is empty
$port    = "3306";           // Default MySQL port
$charset = 'utf8mb4';

// DSN changed from pgsql to mysql
$dsn = "mysql:host=$host;dbname=$db;port=$port;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
}
catch (\PDOException $e) {
    // Note: In production, log this error instead of echoing it.
    die("Database connection failed: " . $e->getMessage());
}

/**
 * Session Start
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>