<?php

namespace Painel\Models;

use Painel\Core\Database;

class Setting
{
    /**
     * Busca todas as configurações de um Tenant como Chave => Valor
     */
    public static function all(int $tenantId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT `key`, `value` FROM settings WHERE tenant_id = :tenant_id");
        $stmt->execute(['tenant_id' => $tenantId]);
        
        $results = $stmt->fetchAll();
        $settings = [];
        
        foreach ($results as $row) {
            $settings[$row['key']] = $row['value'];
        }
        
        return $settings;
    }

    /**
     * Atualiza ou Cria uma nova configuração
     */
    public static function set(int $tenantId, string $key, string $value, ?int $userId = null): void
    {
        $db = Database::getInstance();
        
        $sql = "INSERT INTO settings (tenant_id, `key`, `value`, created_by, created_at, updated_by, updated_at) 
                VALUES (:tenant_id, :key, :value, :user_id, NOW(), :user_id, NOW())
                ON DUPLICATE KEY UPDATE 
                `value` = :value, 
                updated_by = :user_id, 
                updated_at = NOW()";
                
        $stmt = $db->prepare($sql);
        $stmt->execute([
            'tenant_id' => $tenantId,
            'key'       => $key,
            'value'     => $value,
            'user_id'   => $userId
        ]);
    }
}
