<?php
$path = __DIR__ . '/../database/database.sqlite';
$tables = ['children','device_connections'];
$pdo = new PDO('sqlite:' . $path);
foreach ($tables as $t) {
    $stmt = $pdo->query("SELECT COUNT(*) as c FROM \"$t\"");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['c'];
    echo "$t: $count\n";
    $stmt = $pdo->query("SELECT * FROM \"$t\" LIMIT 5");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $r) {
        echo "  - " . json_encode($r) . "\n";
    }
}
