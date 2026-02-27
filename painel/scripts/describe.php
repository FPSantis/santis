<?php
require_once __DIR__ . '/../vendor/autoload.php';
$db = Painel\Core\Database::getInstance();

echo "=== content_types ===\n";
$stmt = $db->query("DESCRIBE content_types");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "\n=== entries ===\n";
$stmt2 = $db->query("DESCRIBE entries");
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));
