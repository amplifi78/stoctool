<?php
if (session_status() === PHP_SESSION_NONE) session_start();

date_default_timezone_set('Australia/Sydney');

require_once __DIR__ . '/functions.php';

$db_path = __DIR__ . '/../database/inventory.db';
try {
    $db = new PDO("sqlite:$db_path");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Enforce foreign key constraints (off by default in SQLite, per connection)
    $db->exec('PRAGMA foreign_keys = ON');
    // Auto-initialize database if it doesn't exist
    if (filesize($db_path) === 0) {
        $sql = file_get_contents(__DIR__ . '/../database/schema.sql');
        $db->exec($sql);
    }
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
