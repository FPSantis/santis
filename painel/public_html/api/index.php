<?php

/**
 * Santis CMS API - Bootstrapper Central
 * Opcionalmente chamado de Front Controller
 */

// Permite pegar erros graves (se ocorrerem antes do autoloader)
error_reporting(E_ALL);
ini_set('display_errors', '1');

// 1. Composer Autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

use Painel\Core\Router;

// 2. Carrega Variáveis de Ambiente do Tenant Host
if (file_exists(__DIR__ . '/../../config/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../config/');
    $dotenv->load();
}

// 3. Inicia o Roteador Envelopado (Que já ajusta Middlewares, Erros e CORS)
$routerCore = new Router('/api');
$api = $routerCore->getBramus();

// 4. Delega a definição das Endpoints para o arquivo separado de rotas (Mantendo o index limpo)
require_once __DIR__ . '/../../routes/api.php';

// 5. Fire!
$routerCore->run();
