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
        $results = $stmt->fetchAll();
        foreach ($results as &$row) {
            $row['schema'] = self::normalizeSchema($row['schema'] ?? []);
        }
        return $results;
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
        if ($result) {
            $result['schema'] = self::normalizeSchema($result['schema'] ?? []);
            return $result;
        }
        return null;
    }

    /**
     * Normaliza o Schema para garantir que sempre retorne um Array de campos [key, label, type, required, order]
     */
    private static function normalizeSchema($schema): array
    {
        if (is_string($schema)) {
            $schema = json_decode($schema, true);
        }
        
        if (!$schema) return [];

        // Trata legado: Se vier como objeto {fields: [...]}
        if (is_array($schema) && isset($schema['fields']) && is_array($schema['fields'])) {
            $schema = $schema['fields'];
        }

        if (!is_array($schema)) return [];

        $normalized = [];
        foreach ($schema as $index => $field) {
            if (!is_array($field)) continue;

            // Mapeia name (legado) para key/label (novo)
            $key = $field['key'] ?? $field['name'] ?? 'field_' . $index;
            $label = $field['label'] ?? ucfirst($field['name'] ?? $key);
            $type = $field['type'] ?? 'text';
            $required = isset($field['required']) ? (bool)$field['required'] : false;
            $order = $field['order'] ?? ($index + 1);

            $normalized[] = [
                'key' => $key,
                'label' => $label,
                'type' => $type,
                'required' => $required,
                'order' => (int)$order
            ];
        }

        return $normalized;
    }

    /**
     * Cria um novo Tipo de Conteúdo (Ex: "Portfólio", "Produtos")
     */
    public static function create(int $tenantId, array $data): int
    {
        $db = Database::getInstance();
        
        $sql = "INSERT INTO content_types (tenant_id, name, icon, slug, description, `schema`, is_active, created_at, updated_at) 
                VALUES (:tenant_id, :name, :icon, :slug, :description, :schema, :is_active, NOW(), NOW())";
                
        $stmt = $db->prepare($sql);
        
        // Conversao automatica do JSON schema
        $jsonSchema = isset($data['schema']) && is_array($data['schema']) 
            ? json_encode($data['schema']) 
            : null;

        $stmt->execute([
            'tenant_id' => $tenantId,
            'name' => $data['name'],
            'icon' => $data['icon'] ?? 'bx-collection',
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'schema' => $jsonSchema,
            'is_active' => $data['is_active'] ?? 1
        ]);

        return (int)$db->lastInsertId();
    }

    /**
     * Atualiza um Tipo de Conteúdo existente
     */
    public static function update(int $id, int $tenantId, array $data): bool
    {
        $db = Database::getInstance();
        
        $jsonSchema = isset($data['schema']) && is_array($data['schema']) 
            ? json_encode($data['schema']) 
            : null;

        $sql = "UPDATE content_types 
                SET name = :name, icon = :icon, slug = :slug, description = :description, 
                    `schema` = :schema, is_active = :is_active, updated_at = NOW()
                WHERE id = :id AND tenant_id = :tenant_id";
                
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenantId,
            'name' => $data['name'],
            'icon' => $data['icon'] ?? 'bx-collection',
            'slug' => $data['slug'],
            'description' => $data['description'] ?? null,
            'schema' => $jsonSchema,
            'is_active' => $data['is_active'] ?? 1
        ]);
    }

    /**
     * Remove um Tipo de Conteúdo e suas respectivas entradas (Cascade manual ou via SQL)
     */
    public static function delete(int $id, int $tenantId): bool
    {
        $db = Database::getInstance();
        
        // Em um sistema robusto, deveríamos deletar as entradas (entries) primeiro 
        // ou garantir que as FKs estejam em ON DELETE CASCADE no MySQL.
        $stmt = $db->prepare("DELETE FROM content_types WHERE id = :id AND tenant_id = :tenant_id");
        return $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);
    }
}
