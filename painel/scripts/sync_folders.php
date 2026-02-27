<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Painel\Core\Database;

$db = Database::getInstance();
$tenantId = 1;

// 1. Identify existing folders from file paths
$stmt = $db->query("SELECT DISTINCT SUBSTRING_INDEX(file_path, '/', 2) as folder_path FROM media_files WHERE tenant_id = $tenantId");
$foldersFound = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($foldersFound as $relPath) {
    if (empty($relPath) || $relPath === '/') continue;

    $folderName = ucfirst(trim($relPath, '/'));
    
    // Check if folder already exists in media_folders
    $check = $db->prepare("SELECT id FROM media_folders WHERE path = :path AND tenant_id = :tenant_id");
    $check->execute(['path' => $relPath, 'tenant_id' => $tenantId]);
    $folder = $check->fetch();

    if (!$folder) {
        $ins = $db->prepare("INSERT INTO media_folders (tenant_id, name, path, created_at) VALUES (:tenant_id, :name, :path, NOW())");
        $ins->execute([
            'tenant_id' => $tenantId,
            'name' => $folderName,
            'path' => $relPath
        ]);
        $folderId = $db->lastInsertId();
        echo "Created folder: $folderName ($relPath)\n";
    } else {
        $folderId = $folder['id'];
        echo "Folder exists: $folderName ($relPath)\n";
    }

    // 2. Link files to this folder if not already linked
    $update = $db->prepare("UPDATE media_files SET folder_id = :folder_id WHERE file_path LIKE :pattern AND folder_id IS NULL AND tenant_id = :tenant_id");
    $update->execute([
        'folder_id' => $folderId,
        'pattern' => $relPath . '/%',
        'tenant_id' => $tenantId
    ]);
    
    $affected = $update->rowCount();
    if ($affected > 0) {
        echo "Linked $affected files to $folderName\n";
    }
}

echo "Sync completed.\n";
