<?php
require_once __DIR__ . '/../vendor/autoload.php';
$db = Painel\Core\Database::getInstance();
try {
    $db->exec("ALTER TABLE media_files ADD COLUMN caption VARCHAR(255) NULL AFTER size");
    echo "Column 'caption' added successfully.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
