<?php

namespace Painel\Models;

use Painel\Core\Database;
use Exception;

class MediaFile
{
    /**
     * Traz todas as mídias de um Tenant
     */
    public static function all(int $tenantId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM media_files WHERE tenant_id = :tenant_id ORDER BY created_at DESC");
        $stmt->execute(['tenant_id' => $tenantId]);
        return $stmt->fetchAll();
    }

    /**
     * Cópia o Arquivo para o Banco de Dados e retorna o Model Persistido
     */
    public static function create(int $tenantId, array $data): int
    {
        $db = Database::getInstance();
        
        $sql = "INSERT INTO media_files (tenant_id, filename, original_name, mime_type, size_bytes, path, uploaded_by, created_at) 
                VALUES (:tenant_id, :filename, :original_name, :mime_type, :size_bytes, :path, :uploaded_by, NOW())";
                
        $stmt = $db->prepare($sql);
        
        $stmt->execute([
            'tenant_id'     => $tenantId,
            'filename'      => $data['filename'],
            'original_name' => $data['original_name'],
            'mime_type'     => $data['mime_type'],
            'size_bytes'    => $data['size_bytes'],
            'path'          => $data['path'],
            'uploaded_by'   => $data['uploaded_by'] ?? null
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
}
