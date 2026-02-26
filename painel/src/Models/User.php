<?php

namespace Painel\Models;

/**
 * Modelo Básico de Usuário (Provisório, antes da implantação do ORM dinâmico)
 * Usado estritamente para simular e validar o fluxo de Autenticação JWT.
 */
class User
{
    /**
     * Valida as credenciais buscando no Banco de Dados
     */
    public static function attempt(string $email, string $password): ?array
    {
        $db = \Painel\Core\Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            unset($user['password_hash']); // Nunca retorne o hash
            return $user;
        }

        return null;
    }

    /**
     * Extrai os dados do usuário usando apenas o ID (Para ser usado no Middleware a partir do Token Payload)
     */
    public static function findById(int $id): ?array
    {
        $db = \Painel\Core\Database::getInstance();
        $stmt = $db->prepare("SELECT id, tenant_id, name, email, role FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        return $user ?: null;
    }
}
