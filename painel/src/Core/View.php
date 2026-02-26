<?php

namespace Painel\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\Extension\DebugExtension;
use Painel\Models\ContentType;

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
                'cache' => false, // false durante o desenvolvimento MVP
                'debug' => (getenv('APP_ENV') === 'local')
            ]);
            
            // Se houver debug, adiciona extensões
            if (getenv('APP_ENV') === 'local') {
                self::$twig->addExtension(new DebugExtension());
            }

            // Injetar Variáveis Globais (Disponíveis em todos os templates Twig)
            self::$twig->addGlobal('SITE_URL', getenv('SITE_URL') ?: 'https://painel.santis.ddev.site');
            self::$twig->addGlobal('ASSETS_URL', (getenv('SITE_URL') ?: 'https://painel.santis.ddev.site') . '/assets/admin_theme');
            
            // [Fase 3.2] Injetar Módulos Dinâmicos para a Sidebar (Tenant Fixo p/ MVP)
            // Isso permite a sidebar imprimir "Blog", "Portfólio" de acordo com o DB!
            try {
                $dynamicModules = ContentType::all(1); // Assuming tenant_id 1 for MVP
                self::$twig->addGlobal('MENU_DYNAMIC_MODULES', $dynamicModules);
            } catch (\Exception $e) {
                // Log the error if necessary
                self::$twig->addGlobal('MENU_DYNAMIC_MODULES', []);
            }
        }

        echo self::$twig->render($template, $data);
    }
}
