<?php

namespace App\Core;

use Bramus\Router\Router as BramusRouter;

class Router {
    private $router;

    public function __construct() {
        $this->router = new BramusRouter();
    }

    public function run() {
        // Rota Principal (Home)
        $this->router->get('/', function() {
            echo "
            <body style='background:#050A18; color:#00F2FF; font-family:sans-serif; display:flex; justify-content:center; align-items:center; height:100vh; margin:0; flex-direction:column;'>
                <h1 style='border: 2px solid #8A2BE2; padding: 20px; box-shadow: 0 0 20px #8A2BE2;'>
                    Santis <span style='color:#fff'>Engenharia Digital</span>
                </h1>
                <p style='color:#fff; opacity:0.7;'>Ambiente DDEV PHP 8.4 Online</p>
                <code style='background:#000; padding:5px; color:#00F2FF;'> > Protocolo de Defesa Ativo _</code>
            </body>";
        });

        // Rota de erro 404
        $this->router->set404(function() {
            header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
            echo 'Página não encontrada no servidor Santis.';
        });

        $this->router->run();
    }
}