<?php
require_once __DIR__ . '/../vendor/autoload.php';
$db = Painel\Core\Database::getInstance();
$stmt = $db->query("SELECT * FROM content_types WHERE id = 2");
$row = $stmt->fetch();
echo "ID: " . $row['id'] . "\n";
echo "Name: " . $row['name'] . "\n";
echo "Schema: " . $row['schema'] . "\n";
