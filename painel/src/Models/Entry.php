<?php

namespace Painel\Models;

use Painel\Core\Database;
use PDO;

class Entry
{
    /**
     * Busca Entradas de um Tipo Específico (Passando a Slug do Módulo na API)
     */
    public static function byTypeSlug(int $tenantId, string $typeSlug): array
    {
        $db = Database::getInstance();
        
        // 1. Resolve qual é o ID deste Type 
        $stmtType = $db->prepare("SELECT id FROM content_types WHERE tenant_id = :tenant_id AND slug = :slug LIMIT 1");
        $stmtType->execute(['tenant_id' => $tenantId, 'slug' => $typeSlug]);
        $type = $stmtType->fetch();

        if (!$type) {
            return [];
        }

        // 2. Busca todas as entradas atreladas àquele ID pai e injeta a subQuery de Categorias
        $sql = "
            SELECT e.*, c.name as category_name, c.slug as category_slug
            FROM entries e
            LEFT JOIN categories c ON e.category_id = c.id
            WHERE e.tenant_id = :tenant_id 
              AND e.content_type_id = :type_id
            ORDER BY e.created_at DESC
        ";

        $stmt = $db->prepare($sql);
        $stmt->execute([
            'tenant_id' => $tenantId,
            'type_id'   => $type['id']
        ]);
        
        $results = $stmt->fetchAll();

        // 3. EAV: Como 'content_data' do BD é de fato um JSON nativo, PHP extrai como String.
        // Iremos desmaterializá-lo para objeto real antes de devolver pra REST API.
        foreach ($results as &$row) {
            $row['content_data'] = json_decode($row['content_data'], true);
        }

        return $results;
    }

    /**
     * Insere nova Entrada no banco (Entity-Attribute-Value via JSON)
     */
    public static function create(int $tenantId, array $data): int
    {
        $db = Database::getInstance();
        
        // Proteção e Sanidade: Transforma os campos livres do App num Blob JSON nativo do MariaDB
        $jsonPayload = isset($data['content_data']) ? json_encode($data['content_data']) : '{}';
        
        $sql = "INSERT INTO entries (tenant_id, content_type_id, category_id, title, slug, status, content_data, created_at, updated_at) 
                VALUES (:tenant_id, :type_id, :cat_id, :title, :slug, :status, :data, NOW(), NOW())";
                
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'tenant_id' => $tenantId,
            'type_id'   => $data['content_type_id'],
            'cat_id'    => $data['category_id'] ?? null,
            'title'     => $data['title'],
            'slug'      => $data['slug'],
            'status'    => $data['status'] ?? 'published',
            'data'      => $jsonPayload
        ]);

        return (int)$db->lastInsertId();
    }
}
