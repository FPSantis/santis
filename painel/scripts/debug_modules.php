<?php
require_once __DIR__ . '/../vendor/autoload.php';
$db = Painel\Core\Database::getInstance();
$stmt = $db->query("SELECT * FROM content_types");
$rows = $stmt->fetchAll();
echo "Total Rows: " . count($rows) . "\n";
foreach($rows as $r) {
    echo "ID: {$r['id']} | Name: {$r['name']} | Tenant: {$r['tenant_id']}\n";
}
