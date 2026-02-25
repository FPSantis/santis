<?php

namespace Painel\Models;

use Painel\Core\Database;
use PDO;

class Category
{
    /**
     * Traz todas as categorias atreladas a um Tipo Específico
     */
    public static function allByType(int $tenantId, int $contentTypeId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM categories WHERE tenant_id = :tenant_id AND content_type_id = :type_id ORDER BY `name` ASC");
        $stmt->execute(['tenant_id' => $tenantId, 'type_id' => $contentTypeId]);
        return $stmt->fetchAll();
    }

    /**
     * Cria Nova Categoria (Ex: "Design" -> Dentro de "Portfólio")
     */
    public static function create(int $tenantId, array $data): int
    {
        $db = Database::getInstance();
        
        $sql = "INSERT INTO categories (tenant_id, content_type_id, name, slug, created_at, updated_at) 
                VALUES (:tenant_id, :type_id, :name, :slug, NOW(), NOW())";
                
        $stmt = $db->prepare($sql);
        
        $stmt->execute([
            'tenant_id' => $tenantId,
            'type_id'   => $data['content_type_id'],
            'name'      => $data['name'],
            'slug'      => $data['slug']
        ]);

        return (int)$db->lastInsertId();
    }
}
