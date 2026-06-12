<?php
$host = '127.0.0.1';
$port = 3306;
$db = null;
$user = 'root';
$pass = '';
$charset = 'utf8mb4';
try {
    $dsn = "mysql:host={$host};port={$port}";
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `laravel` CHARACTER SET {$charset} COLLATE {$charset}_unicode_ci");
    echo "OK\n";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
