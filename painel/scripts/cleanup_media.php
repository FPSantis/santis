<?php
/**
 * Script para limpar e organizar as mídias do Santis CDN.
 * 
 * Ações:
 * 1. Renomeia arquivos para MD5 hashes.
 * 2. Move arquivos de módulos dinâmicos para subpastas YYYY/MM.
 * 3. Atualiza as tabelas media_files e entries (content_data).
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Painel\Core\Database;

$db = Database::getInstance();
$cdnRoot = dirname(__DIR__, 2) . '/cdn/public_html';
$tenantId = 1;

echo "Iniciando limpeza e organização das mídias...\n";

// 1. Obter todas as mídias registradas
$stmt = $db->prepare("SELECT id, file_name, file_path, folder_id FROM media_files WHERE tenant_id = :tenant");
$stmt->execute(['tenant' => $tenantId]);
$mediaFiles = $stmt->fetchAll();

$updatesMedia = [];
$pathMap = []; // Antigo Path => Novo Path

foreach ($mediaFiles as $media) {
    $oldPath = $media['file_path'];
    $oldFullPath = $cdnRoot . $oldPath;
    
    if (!file_exists($oldFullPath)) {
        echo "Aviso: Arquivo não encontrado: $oldPath\n";
        continue;
    }

    $pathInfo = pathInfo($oldPath);
    $dir = $pathInfo['dirname'];
    $ext = strtolower($pathInfo['extension']);
    
    // Obter informações da pasta pai para decidir se move para YYYY/MM
    $moduleName = '';
    $parentId = null;
    if ($media['folder_id']) {
        $stF = $db->prepare("SELECT name, parent_id FROM media_folders WHERE id = :id");
        $stF->execute(['id' => $media['folder_id']]);
        $folder = $stF->fetch();
        if ($folder) {
            $moduleName = strtolower($folder['name']);
            $parentId = $folder['parent_id'];
        }
    }

    // Regras de subpastas (Se for na raiz do módulo ou já estiver em subpasta de data)
    // Agora Partners TAMBÉM entra na regra YYYY/MM
    $modularModules = ['portfolio', 'radar', 'services', 'partners'];
    $newRelativeDir = $dir;
    
    if (in_array($moduleName, $modularModules)) {
        // Se ainda não estiver numa subpasta de data YYYY/MM
        if (!preg_match('/\/\d{4}\/\d{2}$/', $dir)) {
            $newRelativeDir = '/' . $moduleName . '/' . date('Y/m');
        }
    }

    // Verificar se o arquivo já está hashado (32 chars + .ext)
    $filenameOnly = pathinfo($oldPath, PATHINFO_FILENAME);
    $isAlreadyHashed = preg_match('/^[a-f0-9]{32}$/i', $filenameOnly);

    if ($isAlreadyHashed && $newRelativeDir === $dir) {
        echo "Ignorado (Já está ok): $oldPath\n";
        continue;
    }

    // Gerar novo nome (Hash) se necessário, ou manter se já for hash mas mover pasta
    $newFileName = $isAlreadyHashed ? basename($oldPath) : md5(uniqid(rand(), true)) . '.' . $ext;
    $newRelativePath = rtrim($newRelativeDir, '/') . '/' . $newFileName;
    $newFullDir = $cdnRoot . $newRelativeDir;

    // Criar diretório se não existir
    if (!is_dir($newFullDir)) {
        mkdir($newFullDir, 0755, true);
    }

    // Mover arquivo
    $newFullPath = $cdnRoot . $newRelativePath;
    if (rename($oldFullPath, $newFullPath)) {
        echo "Movido: $oldPath -> $newRelativePath\n";
        
        // Se mudou de pasta, garantir que o folder_id no DB seja o da pasta YYYY/MM
        $targetFolderId = $media['folder_id'];
        if ($newRelativeDir !== $dir) {
            $targetFolderId = ensureFolderExists($tenantId, date('Y/m'), $newRelativeDir, $media['folder_id']);
        }

        $pathMap[$oldPath] = $newRelativePath;
        
        // Atualizar media_files
        $stU = $db->prepare("UPDATE media_files SET file_path = :path, folder_id = :folder WHERE id = :id");
        $stU->execute([
            'path' => $newRelativePath,
            'folder' => $targetFolderId,
            'id' => $media['id']
        ]);
    } else {
        echo "Erro ao mover: $oldPath\n";
    }
}

// 2. Atualizar as Entradas (Entries)
echo "\nAtualizando referências nas entradas...\n";
$stmtE = $db->prepare("SELECT id, content_data FROM entries WHERE tenant_id = :tenant");
$stmtE->execute(['tenant' => $tenantId]);
$entries = $stmtE->fetchAll();

foreach ($entries as $entry) {
    if (!$entry['content_data']) continue;
    
    $data = json_decode($entry['content_data'], true);
    if (!is_array($data)) continue;

    $changed = false;
    array_walk_recursive($data, function(&$value) use ($pathMap, &$changed) {
        if (is_string($value)) {
            // Limpar escapes de barra se vierem do JSON
            $cleanValue = str_replace('\\/', '/', $value);
            if (isset($pathMap[$cleanValue])) {
                $value = $pathMap[$cleanValue];
                $changed = true;
            }
        }
    });

    if ($changed) {
        $stUE = $db->prepare("UPDATE entries SET content_data = :data WHERE id = :id");
        $stUE->execute([
            'data' => json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'id' => $entry['id']
        ]);
        echo "Entrada ID {$entry['id']} atualizada.\n";
    }
}

echo "\nOrganização concluída com sucesso!\n";

function ensureFolderExists(int $tenantId, string $name, string $path, ?int $parentId): int
{
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT id FROM media_folders WHERE path = :path AND tenant_id = :tenant_id");
    $stmt->execute(['path' => $path, 'tenant_id' => $tenantId]);
    $folder = $stmt->fetch();
    
    if ($folder) return (int)$folder['id'];
    
    $stmtI = $db->prepare("INSERT INTO media_folders (tenant_id, name, path, parent_id) VALUES (:t, :n, :p, :parent)");
    $stmtI->execute([
        't' => $tenantId,
        'n' => $name,
        'p' => $path,
        'parent' => $parentId
    ]);
    
    return (int)$db->lastInsertId();
}
