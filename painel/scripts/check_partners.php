<?php
require_once __DIR__ . '/../vendor/autoload.php';
$db = Painel\Core\Database::getInstance();
$stmt = $db->prepare("SELECT e.*, ct.slug as type_slug 
                    FROM entries e 
                    JOIN content_types ct ON e.content_type_id = ct.id 
                    WHERE ct.slug = 'partners'");
$stmt->execute();
$rows = $stmt->fetchAll();
foreach ($rows as $row) {
    echo "ID: {$row['id']} | Title: {$row['title']}\n";
    echo "Data: {$row['content_data']}\n";
    echo "-------------------\n";
}
