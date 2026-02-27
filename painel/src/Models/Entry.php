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
            $data = json_decode($row['content_data'], true);
            
            // Normalização Santis: Garante que chaves padrão existam para o Painel JS
            if ($data) {
                if (!isset($data['title']) && isset($data['titulo'])) $data['title'] = $data['titulo'];
                if (!isset($data['name']) && isset($data['nome'])) $data['name'] = $data['nome'];
                if (isset($data['imagem']) && !isset($data['image'])) $data['image'] = $data['imagem'];
                if (isset($data['capa']) && !isset($data['cover'])) $data['cover'] = $data['capa'];
            }
            
            $row['content_data'] = $data;
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

    /**
     * Busca uma entrada específica por ID
     */
    public static function find(int $id, int $tenantId): ?array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM entries WHERE id = :id AND tenant_id = :tenant_id");
        $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);
        $row = $stmt->fetch();

        if ($row) {
            $data = json_decode($row['content_data'], true);
            // Normalização idêntica ao byTypeSlug
            if ($data) {
                if (!isset($data['title']) && isset($data['titulo'])) $data['title'] = $data['titulo'];
                if (!isset($data['name']) && isset($data['nome'])) $data['name'] = $data['nome'];
                if (isset($data['imagem']) && !isset($data['image'])) $data['image'] = $data['imagem'];
                if (isset($data['capa']) && !isset($data['cover'])) $data['cover'] = $data['capa'];
            }
            $row['content_data'] = $data;
            return $row;
        }

        return null;
    }

    /**
     * Atualiza uma entrada existente
     */
    public static function update(int $id, int $tenantId, array $data): bool
    {
        $db = Database::getInstance();
        $jsonPayload = isset($data['content_data']) ? json_encode($data['content_data']) : '{}';

        $sql = "UPDATE entries 
                SET title = :title, slug = :slug, status = :status, content_data = :data, updated_at = NOW()
                WHERE id = :id AND tenant_id = :tenant_id";
                
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            'id' => $id,
            'tenant_id' => $tenantId,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'status' => $data['status'] ?? 'published',
            'data' => $jsonPayload
        ]);
    }

    /**
     * Remove uma entrada
     */
    public static function delete(int $id, int $tenantId): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM entries WHERE id = :id AND tenant_id = :tenant_id");
        return $stmt->execute(['id' => $id, 'tenant_id' => $tenantId]);
    }
}
