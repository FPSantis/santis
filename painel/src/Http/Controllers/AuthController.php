<?php

namespace Painel\Http\Controllers;

use Painel\Core\Request;
use Painel\Core\Response;
use Painel\Models\User;
use Firebase\JWT\JWT;

class AuthController
{
    /**
     * Rota POST /api/login
     * Recebe email e password via JSON e retorna o JWT se válido.
     */
    public function login()
    {
        $request = new Request();
        $email = $request->input('email');
        $password = $request->input('password');

        if (!$email || !$password) {
            return Response::error('Email e Senha são obrigatórios.', 400);
        }

        $user = User::attempt($email, $password);

        if (!$user) {
            return Response::error('Credenciais inválidas.', 401);
        }

        // Emitir Token JWT
        $secretKey = getenv('JWT_SECRET') ?: 'santis_super_secret_development_key_2026';
        $issuedAt = time();
        $expire = $issuedAt + 7200; // 2 horas
        $serverName = getenv('SITE_URL') ?: 'https://painel.santis.ddev.site';

        $payload = [
            'iat'  => $issuedAt,         // Tempo de emissão
            'iss'  => $serverName,       // Issuer
            'nbf'  => $issuedAt,         // Not before
            'exp'  => $expire,           // Expiração
            'data' => [
                'userId' => $user['id'], // Apenas informações necessárias, PII é perigoso no JWT
                'tenant' => $user['tenant_id']
            ]
        ];

        $jwt = JWT::encode($payload, $secretKey, 'HS256');

        return Response::json(true, [
            'token' => $jwt,
            'expires_in' => 7200,
            'user' => $user
        ], 'Login autorizado e token emitido.');
    }
}
