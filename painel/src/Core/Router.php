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

    public function __construct()
    {
        $this->bramus = new BramusRouter();
        $this->setupGlobalHandlers();
    }

    /**
     * Configura middlewares e tratamentos antes de executar as rotas
     */
    private function setupGlobalHandlers()
    {
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

        // Middleware Global (CORS Básico Padrão SaaS API) Executa em TODOS os métodos ANTES da rota
        $this->bramus->before('ALL', '.*', function() {
            // Permite que qualquer (Origin) ou apenas os Domains registrados na tb_tenants acessem a API
            header('Access-Control-Allow-Origin: *'); // TODO: No futuro, checar se a $_SERVER['HTTP_ORIGIN'] está na lista da API Key
            header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
            header('Access-Control-Allow-Headers: Authorization, Content-Type, X-Requested-With');
            header('Access-Control-Max-Age: 86400'); // Cacheia o Preflight por 24h para economizar Queries
            
            // Tratamento do OPTIONS (Preflight Cross-Origin)
            if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
                http_response_code(200);
                exit();
            }
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
