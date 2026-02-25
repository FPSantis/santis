<?php

namespace Painel\Http\Middleware;

use Painel\Core\Request;
use Painel\Core\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthMiddleware
{
    /**
     * Intercepta a requisição para checar o JWT.
     * Retorna o ID do Usuário se for válido, ou encerra a aplicação com um 401.
     */
    public static function handle(Request $request): int
    {
        $token = $request->getBearerToken();

        if (!$token) {
            Response::error('Acesso negado. Token não fornecido.', 401);
            exit; // Prevenção dupla de segurança
        }

        try {
            $secretKey = getenv('JWT_SECRET') ?: 'santis_super_secret_development_key_2026';
            $decoded = JWT::decode($token, new Key($secretKey, 'HS256'));

            // Podemos anexar os dados decodificados num Singleton do Request futuro, 
            // mas por enquanto apenas retornamos o userID decodificado com sucesso.
            return $decoded->data->userId;

        } catch (\Firebase\JWT\ExpiredException $e) {
            Response::error('Token expirado. Faça login novamente.', 401);
            exit;
        } catch (Exception $e) {
            Response::error('Token inválido ou corrompido.', 401);
            exit;
        }
    }
}
