<?php
/**
 * Santis Engenharia Digital - Front Controller
 */

require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Router;

// Carrega variáveis de ambiente (.env) se existirem
if (file_exists(__DIR__ . '/../config/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../config/');
    $dotenv->load();
}

// Inicia o Roteador
$router = new Router();
$router->run();