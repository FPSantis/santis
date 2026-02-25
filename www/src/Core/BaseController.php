<?php

namespace App\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class BaseController {
    protected $twig;

    public function __construct() {
        // Inicializa o loader apontando para a pasta raiz do seu src (pra pegar Modules e Views)
        $loader = new FilesystemLoader([
            __DIR__ . '/../Views',
            __DIR__ . '/../Modules'
        ]);
        
        // Define opções do ambiente Twig
        $this->twig = new Environment($loader, [
            'cache' => false, // Desativado para desenvolvimento
            'debug' => true,
        ]);
        
        $this->twig->addExtension(new \Twig\Extension\DebugExtension());

        // Adiciona URL_BASE como variável global
        $urlBase = $_ENV['SITE_URL'] ?? 'https://santis.ddev.site/';
        if (substr($urlBase, -1) !== '/') {
            $urlBase .= '/';
        }
        $this->twig->addGlobal('URL_BASE', $urlBase);
    }

    /**
     * Renderiza o template Twig especificado com os dados fornecidos
     */
    protected function render($view, $data = []) {
        echo $this->twig->render($view . '.twig', $data);
    }
}
