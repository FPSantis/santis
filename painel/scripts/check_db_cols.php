<?php
require_once __DIR__ . '/../vendor/autoload.php';
$db = Painel\Core\Database::getInstance();
$stmt = $db->query("DESCRIBE media_files");
while($row = $stmt->fetch()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
