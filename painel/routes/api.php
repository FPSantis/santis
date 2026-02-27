<?php

/**
 * Santis CMS Painel - Rotas da API REST
 * Este arquivo é incluído pelo core do Router (index.php)
 */

use Painel\Core\Response;

// A variável $api é injetada automaticamente (Instância do BramusRouter)
// Use $api->get(), $api->post()
error_log("API Routing loaded. URI: " . $_SERVER['REQUEST_URI']);

// Rota de Health Check / Ping
$api->get('/ping', function() {
    Response::json(true, [
        'service' => 'Santis Headless CMS API',
        'version' => '1.0.0',
        'status' => 'operational',
        'timestamp' => date('c')
    ], 'API está online e conectada com sucesso.');
});

// Rotas Públicas (Operações sem JWT)
$api->post('/login', 'Painel\Http\Controllers\AuthController@login');

// Grupo V1 API Frontend Pública
$api->mount('/v1', function() use ($api) {
    // Configurações Globais (ReadOnly)
    $api->get('/settings', 'Painel\Http\Controllers\SettingController@index');
    
    
    // Entradas Públicas de Módulos (ReadOnly) ex: Portfólio, Serviços
    $api->get('/entries/([a-zA-Z0-9_-]+)', 'Painel\Http\Controllers\EntryController@publicIndex');

    // Serviços e Drivers Ativos (Acionados pelo Front, Executados no Painel)
    $api->get('/services/share-options', 'Painel\Http\Controllers\ServiceController@shareOptions');
    $api->post('/messenger/whatsapp', 'Painel\Http\Controllers\ServiceController@sendWhatsApp');
    $api->post('/scanner/pwned', 'Painel\Http\Controllers\ServiceController@scanPwned');
});

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
    $api->get('/types/(\d+)', 'Painel\Http\Controllers\ContentTypeController@show');
    $api->put('/types/(\d+)', 'Painel\Http\Controllers\ContentTypeController@update');
    $api->delete('/types/(\d+)', 'Painel\Http\Controllers\ContentTypeController@delete');

    // Módulos EAV Dinâmicos (Série de Entradas via Regex por slug do Tipo)
    $api->get('/entries/([a-zA-Z0-9_-]+)', 'Painel\Http\Controllers\EntryController@index');
    $api->post('/entries/([a-zA-Z0-9_-]+)', 'Painel\Http\Controllers\EntryController@store');
    $api->get('/entries/([a-zA-Z0-9_-]+)/(\d+)', 'Painel\Http\Controllers\EntryController@show');
    $api->put('/entries/([a-zA-Z0-9_-]+)/(\d+)', 'Painel\Http\Controllers\EntryController@update');
    $api->delete('/entries/([a-zA-Z0-9_-]+)/(\d+)', 'Painel\Http\Controllers\EntryController@delete');

    // Integração CDN e Upload de Mídia Físico
    $api->get('/media', 'Painel\Http\Controllers\MediaController@index');
    $api->post('/media/upload', 'Painel\Http\Controllers\MediaController@upload');
    $api->get('/media/(\d+)', 'Painel\Http\Controllers\MediaController@show');
    $api->put('/media/(\d+)', 'Painel\Http\Controllers\MediaController@update');
    $api->delete('/media/(\d+)', 'Painel\Http\Controllers\MediaController@delete');
    $api->post('/media/delete-bulk', 'Painel\Http\Controllers\MediaController@deleteMultiple');
    $api->post('/media/folders', 'Painel\Http\Controllers\MediaController@createFolder');
    $api->delete('/media/folders/(\d+)', 'Painel\Http\Controllers\MediaController@deleteFolder');

    // Configurações Globais Administrativas (Save)
    $api->post('/settings', 'Painel\Http\Controllers\SettingController@store');

    // Blueprints SaaS (Import/Export Core)
    $api->get('/blueprints/export', 'Painel\Http\Controllers\BlueprintController@export');
    $api->post('/blueprints/import', 'Painel\Http\Controllers\BlueprintController@import');

});
