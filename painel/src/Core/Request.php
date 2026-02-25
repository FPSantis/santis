<?php

namespace Painel\Core;

/**
 * Handle incoming API Requests
 */
class Request
{
    private array $data;
    private string $method;
    private array $headers;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->headers = getallheaders();
        $this->parseBody();
    }

    /**
     * Extrai os dados do corpo (JSON ou form-data)
     */
    private function parseBody()
    {
        $this->data = $_REQUEST; // Combina $_GET e $_POST

        // Se a requisição enviou um JSON no body (Padrão API REST)
        if (strpos($this->getHeader('Content-Type'), 'application/json') !== false) {
            $json = file_get_contents('php://input');
            $decoded = json_decode($json, true);
            if (is_array($decoded)) {
                $this->data = array_merge($this->data, $decoded);
            }
        }
    }

    /**
     * Retorna todos os dados da requisição
     */
    public function all(): array
    {
        return $this->data;
    }

    /**
     * Pega um campo específico
     */
    public function input(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    /**
     * Pega um header HTTP
     */
    public function getHeader(string $name, $default = '')
    {
        $name = strtolower($name);
        foreach ($this->headers as $k => $v) {
            if (strtolower($k) === $name) {
                return $v;
            }
        }
        return $default;
    }

    /**
     * Extrai o Token JWT do header Authorization: Bearer <token>
     */
    public function getBearerToken(): ?string
    {
        $header = $this->getHeader('Authorization');
        if (!empty($header)) {
            if (preg_match('/Bearer\s(\S+)/', $header, $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    /**
     * Retorna o Método HTTP atual
     */
    public function getMethod(): string
    {
        return $this->method;
    }
}
