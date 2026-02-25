<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Bramus\Router\Router;

// Dotenv configuration (optional based on if an env file exists)
$dotenvPath = __DIR__ . '/../config';
if (file_exists($dotenvPath . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable($dotenvPath);
    $dotenv->load();
}

$router = new Router();

// API Endpoints Response Headers (Always JSON)
$router->before('GET|POST|PUT|DELETE', '/api/.*', function() {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *'); 
});

// Hello World API Test
$router->get('/api/test', function() {
    echo json_encode([
        'status' => 'success',
        'message' => 'Santis API is alive and decoupled.',
        'timestamp' => time()
    ]);
});

// Admin Panel Routes Handled by Twig Controller
$router->get('/', function() {
    echo "Painel Administrativo da Santis (Em Breve com Sneat Pro).<br><a href='/api/test'>Acesse a API Teste</a>";
});

// Exceções 404
$router->set404(function() {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    echo json_encode(['error' => 'Not Found']);
});

$router->run();
