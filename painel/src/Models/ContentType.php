<?php

namespace Painel\Models;

use Painel\Core\Database;
use PDO;

class ContentType
{
    /**
     * Traz todos os tipos de conteúdo do Tenant selecionado.
     */
    public static function all(int $tenantId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM content_types WHERE tenant_id = :tenant_id ORDER BY `name` ASC");
        $stmt->execute(['tenant_id' => $tenantId]);
        return $stmt->fetchAll();
    }

    /**
     * Traz um tipo específico via ID
     */
    public static function find(int $id, int $tenantId): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM content_types WHERE id = :id AND tenant_id = :tenant_id");
        $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Cria um novo Tipo de Conteúdo (Ex: "Portfólio", "Produtos")
     */
    public static function create(int $tenantId, array $data): int
    {
        $db = Database::getInstance();
        
        $sql = "INSERT INTO content_types (tenant_id, name, slug, description, `schema`, is_active, created_at, updated_at) 
                VALUES (:tenant_id, :name, :slug, :description, :schema, :is_active, NOW(), NOW())";
                
        $stmt = $db->prepare($sql);
        
        // Conversao automatica do JSON schema
        $jsonSchema = isset($data['schema']) && is_array($data['schema']) 
            ? json_encode($data['schema']) 
            : null;

        $stmt->execute([
            'tenant_id' => $tenantId,
            'name' => $data['name'],
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'schema' => $jsonSchema,
            'is_active' => $data['is_active'] ?? 1
        ]);

        return (int)$db->lastInsertId();
    }
}
