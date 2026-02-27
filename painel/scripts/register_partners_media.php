<?php
require_once __DIR__ . '/../vendor/autoload.php';
$db = Painel\Core\Database::getInstance();
$dir = '/var/www/html/cdn/public_html/partners';
$files = glob($dir . '/*.svg');
$tenantId = 1;
if (!$files) { echo "No SVG files found\n"; return; }
foreach ($files as $f) {
    $filename = basename($f);
    $path = '/partners/' . $filename;
    $stmt = $db->prepare("SELECT id FROM media_files WHERE file_path = :path AND tenant_id = :tenant_id");
    $stmt->execute(['path' => $path, 'tenant_id' => $tenantId]);
    if (!$stmt->fetch()) {
        $ins = $db->prepare("INSERT INTO media_files (tenant_id, file_name, file_path, mime_type, size, created_at) 
                            VALUES (:tenant_id, :filename, :path, 'image/svg+xml', :size, NOW())");
        $ins->execute([
            'tenant_id' => $tenantId, 
            'filename' => $filename, 
            'path' => $path, 
            'size' => filesize($f)
        ]);
        echo "Registered: $filename\n";
    } else {
        echo "Skipped: $filename\n";
    }
}
