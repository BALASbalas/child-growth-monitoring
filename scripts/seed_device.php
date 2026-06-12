<?php
$path = __DIR__ . '/../database/database.sqlite';
$pdo = new PDO('sqlite:' . $path);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$pdo->exec("INSERT INTO device_connections (user_id,device_name,serial_number,device_type,is_active,created_at,updated_at) VALUES (1,'Test Scale','SC-0001','scale',1,(datetime('now')),(datetime('now')))");
echo "Inserted sample device\n";
