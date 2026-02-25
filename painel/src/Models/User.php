<?php

namespace Painel\Models;

/**
 * Modelo Básico de Usuário (Provisório, antes da implantação do ORM dinâmico)
 * Usado estritamente para simular e validar o fluxo de Autenticação JWT.
 */
class User
{
    /**
     * Valida as credenciais. Em produção, isso fará "SELECT * FROM users WHERE email = ?".
     */
    public static function attempt(string $email, string $password): ?array
    {
        // Mock de Administrador do Painel
        // Senha original era 'santis2026' mas deve ser checada usando password_verify
        // Aqui usaremos dados fixos para montar a Arquitetura do JWT.
        
        $mockUser = [
            'id' => 1,
            'name' => 'Fernando Santis',
            'email' => 'fpsantis@gmail.com',
            'tenant_id' => 1,
            // Hash gerada com password_hash('santis2026', PASSWORD_DEFAULT);
            'password_hash' => '$2y$12$wMcsBMq4csfL0Aua99B0gemoJaJcQp.uJJYrmcty2/WEg8lamQaSS', 
        ];

        // Validar e-mail e hash bcrypt
        if ($email === $mockUser['email'] && password_verify($password, $mockUser['password_hash'])) {
            unset($mockUser['password_hash']); // Nunca retorne o hash
            return $mockUser;
        }

        return null;
    }

    /**
     * Extrai os dados do usuário usando apenas o ID (Para ser usado no Middleware a partir do Token Payload)
     */
    public static function findById(int $id): ?array
    {
        if ($id === 1) {
            return [
                'id' => 1,
                'name' => 'Fernando Santis',
                'email' => 'fpsantis@gmail.com',
                'role' => 'webmaster',
                'tenant_id' => 1
            ];
        }
        return null;
    }
}
