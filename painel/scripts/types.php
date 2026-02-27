<?php
require_once __DIR__ . '/../vendor/autoload.php';
$db = Painel\Core\Database::getInstance();
$stmt = $db->query('SELECT id, slug, `schema` FROM content_types');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
