<?php

namespace App\Core;

use Bramus\Router\Router as BramusRouter;
use App\Controllers\SiteController;

class Router {
    private $router;

    public function __construct() {
        $this->router = new BramusRouter();
    }

    public function run() {
        // Rotas Públicas Principais
        $this->router->get('/', 'App\Controllers\SiteController@index');
        $this->router->get('/radar', 'App\Controllers\SiteController@blog');
        $this->router->get('/artigo', function() {
            // Como o URL atual recebe por query parameter (artigo?id=X), pegamos via $_GET
            $id = $_GET['id'] ?? 1;
            $controller = new \App\Controllers\SiteController();
            $controller->post($id);
        });

        // Retrocompatibilidade / Rotas antigas limpas
        $this->router->get('/blog', 'App\Controllers\SiteController@blog');
        $this->router->get('/post', function() {
            $id = $_GET['id'] ?? 1;
            $controller = new SiteController();
            $controller->post($id);
        });

        // Rota de erro 404
        $this->router->set404(function() {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            echo 'Página não encontrada no servidor Santis. (MVC Ativo)';
        });

        $this->router->run();
    }
}