<?php

namespace Painel\Http\Controllers;

use Painel\Core\View;

class WebController
{
    /**
     * Tela Principal do Painel (Dashboard)
     */
    public function dashboard()
    {
        // O JS fará a verificação real do JWT via LocalStorage no load da página
        View::render('dashboard.twig', [
            'title' => 'Painel de Controle'
        ]);
    }

    /**
     * Tela de Autenticação
     */
    public function login()
    {
        View::render('auth/login.twig', [
            'title' => 'Login | Santis CMS'
        ]);
    }

    /**
     * Módulo CDN (Lista de Arquivos)
     */
    public function media()
    {
        View::render('media.twig', [
            'title' => 'Gerenciador de Mídia | CDN',
            'menu_active' => 'media'
        ]);
    }

    /**
     * Interface de Configurações Globais
     */
    public function settings()
    {
        View::render('settings.twig', [
            'title' => 'Configurações Globais',
            'menu_active' => 'settings'
        ]);
    }
}
