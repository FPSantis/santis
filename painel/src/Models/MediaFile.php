<?php

namespace Painel\Models;

use Painel\Core\Database;
use PDO;

class MediaFile
{
    /**
     * Traz todas as mídias de um Tenant, com filtros
     */
    public static function all(int $tenantId, array $filters = [], int $limit = 100, int $offset = 0): array
    {
        $db = Database::getInstance();
        $query = "SELECT id, file_name, file_path, mime_type, size, caption, alt_text, folder_id, created_at, created_by 
                  FROM media_files 
                  WHERE tenant_id = :tenant_id";
        
        $params = [':tenant_id' => $tenantId];

        if (!empty($filters['q'])) {
            $query .= " AND (file_name LIKE :q OR caption LIKE :q OR alt_text LIKE :q)";
            $params[':q'] = '%' . $filters['q'] . '%';
        }

        if (!empty($filters['type'])) {
            switch ($filters['type']) {
                case 'image':
                    $query .= " AND mime_type LIKE 'image/%'";
                    break;
                case 'video':
                    $query .= " AND mime_type LIKE 'video/%'";
                    break;
                case 'audio':
                    $query .= " AND mime_type LIKE 'audio/%'";
                    break;
                case 'document':
                    $query .= " AND (mime_type = 'application/pdf' OR mime_type LIKE 'text/%')";
                    break;
            }
        }

        if (isset($filters['folder_id'])) {
            if ($filters['folder_id'] === 0 || $filters['folder_id'] === 'null' || $filters['folder_id'] === null) {
                $query .= " AND folder_id IS NULL";
            } else {
                $query .= " AND folder_id = :folder_id";
                $params[':folder_id'] = (int)$filters['folder_id'];
            }
        }

        $query .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $db->prepare($query);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll();
    }

    /**
     * Cria registro de arquivo no Banco de Dados
     */
    public static function create($tenantId, $data)
    {
        $db = Database::getInstance();
        
        $sql = "INSERT INTO media_files (tenant_id, file_name, file_path, mime_type, size, caption, alt_text, folder_id, created_by, created_at) 
                VALUES (:tenant_id, :file_name, :file_path, :mime_type, :size, :caption, :alt_text, :folder_id, :created_by, NOW())";
                
        $stmt = $db->prepare($sql);
        
        $stmt->execute([
            'tenant_id'  => $tenantId,
            'file_name'  => $data['filename'],
            'file_path'  => $data['path'],
            'mime_type'  => $data['mime_type'],
            'size'       => $data['size_bytes'],
            'caption'    => $data['caption'] ?? null, 
            'alt_text'   => $data['alt_text'] ?? null,
            'folder_id'  => $data['folder_id'] ?? null,
            'created_by' => $data['uploaded_by'] ?? null
        ]);

        return (int)$db->lastInsertId();
    }

    /**
     * Busca um arquivo pelo ID e Tenant para operações futuras (Delete/Update)
     */
    public static function find(int $id, int $tenantId): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM media_files WHERE id = :id AND tenant_id = :tenant_id LIMIT 1");
        $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Deleta o registro do banco
     */
    public static function delete(int $id, int $tenantId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM media_files WHERE id = :id AND tenant_id = :tenant_id");
        return $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);
    }

    /**
     * Atualiza metadados do arquivo
     */
    public static function update(int $id, int $tenantId, array $data): bool
    {
        $db = Database::getInstance();
        $sql = "UPDATE media_files SET file_name = :file_name, caption = :caption, alt_text = :alt_text, folder_id = :folder_id WHERE id = :id AND tenant_id = :tenant_id";
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenantId,
            'file_name' => $data['file_name'],
            'caption' => $data['caption'] ?? null,
            'alt_text' => $data['alt_text'] ?? null,
            'folder_id' => $data['folder_id'] ?? null
        ]);
    }

    /**
     * Métodos para Pastas
     */
    public static function allFolders(int $tenantId, ?int $parentId = null): array
    {
        $db = Database::getInstance();
        $query = "SELECT * FROM media_folders WHERE tenant_id = :tenant_id";
        $params = [':tenant_id' => $tenantId];

        if ($parentId === null || $parentId === 0 || $parentId === 'null' || $parentId === '') {
            $query .= " AND parent_id IS NULL";
        } else {
            $query .= " AND parent_id = :parent_id";
            $params[':parent_id'] = $parentId;
        }

        $stmt = $db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public static function createFolder(int $tenantId, string $name, string $path, ?int $parentId = null): int
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO media_folders (tenant_id, name, path, parent_id) VALUES (:tenant_id, :name, :path, :parent_id)");
        $stmt->execute([
            'tenant_id' => $tenantId,
            'name' => $name,
            'path' => $path,
            'parent_id' => $parentId
        ]);
        return (int)$db->lastInsertId();
    }

    public static function deleteFolder(int $id, int $tenantId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE media_files SET folder_id = NULL WHERE folder_id = :id AND tenant_id = :tenant_id");
        $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);

        $stmt = $db->prepare("DELETE FROM media_folders WHERE id = :id AND tenant_id = :tenant_id");
        return $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);
    }

    public static function findFolder(int $id, int $tenantId): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM media_folders WHERE id = :id AND tenant_id = :tenant_id LIMIT 1");
        $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Busca onde a mídia está sendo usada (indexação reversa)
     */
    public static function findUsage(int $tenantId, string $path): array
    {
        $db = Database::getInstance();
        // Busca no JSON content_data das entradas
        // Usamos LIKE para cobrir escapes de JSON (\/)
        $stmt = $db->prepare("
            SELECT e.id, e.title, e.content_type_id, ct.name as type_name, ct.slug as type_slug
            FROM entries e
            JOIN content_types ct ON e.content_type_id = ct.id
            WHERE e.tenant_id = :tenant_id 
            AND e.content_data LIKE :path
        ");
        
        // Escapar a barra para busca no JSON se necessário, ou usar o path direto
        $searchPath = str_replace('/', '\\/', $path); 
        $stmt->execute([
            'tenant_id' => $tenantId,
            'path' => '%' . $searchPath . '%'
        ]);
        
        return $stmt->fetchAll();
    }
}
