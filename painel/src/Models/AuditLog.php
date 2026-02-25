<?php

namespace Painel\Models;

use Painel\Core\Database;

class AuditLog
{
    /**
     * Registra uma ação sensível (INSERT, UPDATE, DELETE) no Sistema
     *
     * @param int $tenantId ID do Inquilino
     * @param int $userId ID do Usuário Autenticado que gerou a ação (do JWT)
     * @param string $action Ex: "CREATED_ENTRY", "DELETED_USER", "LOGIN_FAILED"
     * @param string $tableName Tabela que sofreu a mutação
     * @param int|null $recordId ID do dado que foi alterado
     * @param array|null $oldData Estado antes do update/delete (para rollbacks)
     * @param array|null $newData Estado após o insert/update
     */
    public static function log(
        int $tenantId, 
        int $userId, 
        string $action, 
        string $tableName, 
        ?int $recordId = null, 
        ?array $oldData = null, 
        ?array $newData = null
    ): bool {
        $db = Database::getInstance();

        $sql = "INSERT INTO audit_logs (tenant_id, user_id, action, table_name, record_id, old_data, new_data, ip_address, created_at)
                VALUES (:tenant_id, :user_id, :action, :table_name, :record_id, :old_data, :new_data, :ip_address, NOW())";

        $stmt = $db->prepare($sql);

        // Sanitize arrays into JSON strings if present
        $oldJson = $oldData ? json_encode($oldData) : null;
        $newJson = $newData ? json_encode($newData) : null;
        
        // Pega IP Seguro (Bypass Proxies se aplicável)
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        try {
            return $stmt->execute([
                'tenant_id' => $tenantId,
                'user_id' => $userId,
                'action' => strtoupper($action),
                'table_name' => strtolower($tableName),
                'record_id' => $recordId,
                'old_data' => $oldJson,
                'new_data' => $newJson,
                'ip_address' => $ip
            ]);
        } catch (\Exception $e) {
            // Um erro de log não deve quebrar a aplicação do usuário se for um cenário de contingência,
            // mas localmente queremos estourar na tela para o Webmaster (Fernando) ver e arrumar o schema.
            if (getenv('APP_ENV') === 'local') {
                throw new \Exception("Erro no Sub-Sistema de Auditoria: " . $e->getMessage());
            }
            return false;
        }
    }

    /**
     * Traz todo o histórico global do administrador
     */
    public static function getLogs(int $tenantId, int $limit = 100): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT a.*, u.name as user_name FROM audit_logs a 
                              LEFT JOIN users u ON a.user_id = u.id 
                              WHERE a.tenant_id = :tenant_id 
                              ORDER BY a.created_at DESC 
                              LIMIT :limit");
                              
        // BindValue precisa ser usado no limit pra PDO não atuar com string type '100'
        $stmt->bindValue(':tenant_id', $tenantId, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
