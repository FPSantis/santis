<?php

namespace Painel\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class View
{
    private static ?Environment $twig = null;

    /**
     * Renderiza um template Twig devolvendo o HTML limpo
     */
    public static function render(string $template, array $data = [])
    {
        if (self::$twig === null) {
            // Conta que a pasta views está na src/
            $loader = new FilesystemLoader(__DIR__ . '/../Views');
            
            // Opcionalmente podemos ligar 'cache' => __DIR__.'/../cache/twig' em Produção real
            self::$twig = new Environment($loader, [
                'cache' => false,
                'debug' => (getenv('APP_ENV') === 'local')
            ]);
            
            // Variáveis Globais para os Templates
            self::$twig->addGlobal('SITE_URL', getenv('SITE_URL') ?: 'https://painel.santis.ddev.site');
            self::$twig->addGlobal('ASSETS_URL', (getenv('SITE_URL') ?: 'https://painel.santis.ddev.site') . '/assets/admin_theme');
        }

        echo self::$twig->render($template, $data);
    }
}
