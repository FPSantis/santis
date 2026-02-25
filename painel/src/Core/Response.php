<?php

namespace Painel\Core;

/**
 * Handle outgoing API Responses
 */
class Response
{
    /**
     * Retorna uma resposta JSON Padronizada
     * 
     * @param bool $success Indica se a operação deu certo
     * @param array|null $data Os dados de retorno
     * @param string $message Uma mensagem descritiva opcional
     * @param int $statusCode O status HTTP (ex: 200, 404, 500)
     */
    public static function json(bool $success, ?array $data = null, string $message = '', int $statusCode = 200)
    {
        // Limpa qualquer output ob_start perdido para não "sujar" o JSON
        if (ob_get_length()) {
            ob_clean();
        }

        header('Content-Type: application/json; charset=utf-8');
        http_response_code($statusCode);

        $response = [
            'success' => $success
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($message !== '') {
            $response['message'] = $message;
        }

        echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * Atalhos de Erro
     */
    public static function error(string $message, int $statusCode = 400, ?array $details = null)
    {
        self::json(false, $details, $message, $statusCode);
    }
}
