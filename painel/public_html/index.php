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
$router->get('/', 'Painel\Http\Controllers\WebController@dashboard');
$router->get('/login', 'Painel\Http\Controllers\WebController@login');
$router->get('/media', 'Painel\Http\Controllers\WebController@media');
$router->get('/settings', 'Painel\Http\Controllers\WebController@settings');

// Legado: Redirecionar /types para /modulos (Rebranding)
$router->get('/types', function() { header('Location: /modulos', true, 301); exit; });
$router->get('/types/create', function() { header('Location: /modulos/create', true, 301); exit; });
$router->get('/types/(\d+)/edit', function($id) { header("Location: /modulos/$id/edit", true, 301); exit; });

// Rotas do Construtor de Módulos (CRUD de Módulos)
$router->get('/modulos', 'Painel\Http\Controllers\WebController@modules');
$router->get('/modulos/create', 'Painel\Http\Controllers\WebController@moduleCreate');
$router->get('/modulos/(\d+)/edit', 'Painel\Http\Controllers\WebController@moduleEdit');

// Blueprints (SaaS Backup)
$router->get('/blueprints', 'Painel\Http\Controllers\WebController@blueprints');

// Rotas de Entradas Dinâmicas (EAV / CMS Data)
// Captura qualquer string pós /entries/ e passa como argumento na variavel SLUG
$router->get('/entries/([a-z0-9_-]+)', 'Painel\Http\Controllers\WebController@entriesIndex');
$router->get('/entries/([a-z0-9_-]+)/create', 'Painel\Http\Controllers\WebController@entriesCreate');
$router->get('/entries/([a-z0-9_-]+)/(\d+)/edit', 'Painel\Http\Controllers\WebController@entriesEdit');

// Exceções 404
$router->set404(function() {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    echo json_encode(['error' => 'Not Found']);
});

$router->run();
