<?php

namespace Painel\Core;

use Bramus\Router\Router as BramusRouter;

/**
 * Envelopa o Bramus Router para adicionar Middlewares Globais (CORS/JSON)
 * e Tratamento nativo de Exceptions do Santis CMS
 */
class Router
{
    private BramusRouter $bramus;

    public function __construct($basePath = '')
    {
        $this->bramus = new BramusRouter();
        if ($basePath) {
            $this->bramus->setBasePath($basePath);
        }
        $this->setupGlobalHandlers();
    }

    /**
     * Configura middlewares e tratamentos antes de executar as rotas
     */
    private function setupGlobalHandlers()
    {
        // 1. CORS / OPTIONS Preflight (Executa o mais cedo possível)
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With');
        header('Access-Control-Max-Age: 86400');

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }

        // Tratamento Global de Exceptions: Transforma erros fatais do PHP em JSON 500
        set_exception_handler(function (\Throwable $ex) {
            // Em produção agressiva não devemos deitar os detalhes ($ex->getMessage()),
            // Mas para o ambiente Development/Santis deixamos aberto para depuração
            Response::error('Internal Server Error', 500, [
                'exception' => get_class($ex),
                'message' => $ex->getMessage(),
                'file' => $ex->getFile(),
                'line' => $ex->getLine()
            ]);
        });

        // 404 Handler nativo da API
        $this->bramus->set404(function() {
            Response::error('Endpoint ou rota não encontrada na API do Santis CMS.', 404);
        });
    }

    /**
     * Retorna a instância crua do Bramus para injetar as rotas de api.php
     */
    public function getBramus(): BramusRouter
    {
        return $this->bramus;
    }

    /**
     * Dispara o processamento final
     */
    public function run()
    {
        $this->bramus->run();
    }
}
