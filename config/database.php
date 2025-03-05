<?php
$host = 'localhost';
$db = 'fitnesstracker';
$user = 'root';
$pass = ''; // Change if your MySQL has a password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    return new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    throw new Exception("Database connection failed: " . $e->getMessage());
}