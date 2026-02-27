<?php
require_once __DIR__ . '/../vendor/autoload.php';
$db = Painel\Core\Database::getInstance();

// 1. Check a media entry
$stmt = $db->query("SELECT id FROM media_files LIMIT 1");
$row = $stmt->fetch();
if (!$row) {
    echo "No media found to test.\n";
    exit;
}
$id = $row['id'];

// Mock Request/Response for Controller call
$_SERVER['AUTH_USER_ID'] = 1;
$user = ['userId' => 1, 'tenant' => 1];

$controller = new Painel\Http\Controllers\MediaController();

echo "Testing Show($id)...\n";
// show() returns Response::json which echoes it out directly and dies/exits in some implementations, 
// let's see how Response::json is implemented.
// Wait, Response::json probably doesn't exit if it's returning.

// Actually, let's just use the models directly for verification if controller is hard to test in CLI.
$media = \Painel\Models\MediaFile::find($id, 1);
echo "Found Media: " . $media['file_name'] . " | Caption: " . ($media['caption'] ?? 'NULL') . "\n";

echo "Testing Update($id)...\n";
$success = \Painel\Models\MediaFile::update($id, 1, ['file_name' => 'updated_test.png', 'caption' => 'Test Caption']);
if ($success) {
    $updated = \Painel\Models\MediaFile::find($id, 1);
    echo "Updated Media: " . $updated['file_name'] . " | Caption: " . $updated['caption'] . "\n";
} else {
    echo "Update failed.\n";
}
