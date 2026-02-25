<?php

/**
 * Santis CMS Painel - Rotas da API REST
 * Este arquivo é incluído pelo core do Router (index.php)
 */

use Painel\Core\Response;

// A variável $api é injetada automaticamente (Instância do BramusRouter)
// Use $api->get(), $api->post()

// Rota de Health Check / Ping
$api->get('/ping', function() {
    Response::json(true, [
        'service' => 'Santis Headless CMS API',
        'version' => '1.0.0',
        'status' => 'operational',
        'timestamp' => date('c')
    ], 'API está online e conectada com sucesso.');
});

// Rotas Públicas
$api->post('/login', 'Painel\Http\Controllers\AuthController@login');

// Grupo de Rotas Protegidas (Exigem JWT)
$api->mount('/secure', function() use ($api) {
    // Middleware verificador (Executa antes de qualquer rota dentro do mount)
    $api->before('GET|POST|PUT|DELETE', '/.*', function() {
        $request = new \Painel\Core\Request();
        $userId = \Painel\Http\Middleware\AuthMiddleware::handle($request);
        
        // Injeta o ID verificado globalmente para os controllers usarem
        $_SERVER['AUTH_USER_ID'] = $userId;
    });

    // Rota de Teste de Sessão
    $api->get('/me', function() {
        $userId = $_SERVER['AUTH_USER_ID'];
        $user = \Painel\Models\User::findById($userId);
        
        Response::json(true, [
            'user' => $user,
            'permissions' => ['all']
        ], 'Sessão JWT válida e decodificada com sucesso.');
    });

    // Content Types / Modelagem Dinâmica (CMS Builder)
    $api->get('/types', 'Painel\Http\Controllers\ContentTypeController@index');
    $api->post('/types', 'Painel\Http\Controllers\ContentTypeController@store');

    // Módulos EAV Dinâmicos (Série de Entradas via Regex por slug do Tipo)
    $api->get('/entries/(\w+)', 'Painel\Http\Controllers\EntryController@index');
    $api->post('/entries/(\w+)', 'Painel\Http\Controllers\EntryController@store');

});
