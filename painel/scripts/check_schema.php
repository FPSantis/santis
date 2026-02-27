<?php
require_once __DIR__ . '/../vendor/autoload.php';
$db = Painel\Core\Database::getInstance();
$stmt = $db->query("SELECT * FROM content_types WHERE id = 1");
$row = $stmt->fetch();
echo "ID 1 Schema Raw:\n" . $row['schema'] . "\n";
