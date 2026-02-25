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

    /**
     * Listagem dos Tipos de Conteúdos Existentes
     */
    public function types()
    {
        View::render('content_types/index.twig', [
            'title' => 'Construtor de Tipos de Conteúdo (Módulos)',
            'menu_active' => 'types'
        ]);
    }

    /**
     * Tela com Formulário JS de Construção EAV
     */
    public function typeCreate()
    {
        View::render('content_types/form.twig', [
            'title' => 'Criar Novo Tipo de Conteúdo',
            'menu_active' => 'types'
        ]);
    }

    /**
     * Listagem Genérica de Entradas (Recebe da rota Ex: "portifolio")
     */
    public function entriesIndex(string $typeSlug)
    {
        View::render('entries/index.twig', [
            'title' => 'Gerenciar Entradas',
            'menu_active' => 'entries_' . $typeSlug,
            'type_slug' => $typeSlug
        ]);
    }

    /**
     * Formulário de Criação Visual (Recebe da rota Ex: "portifolio")
     */
    public function entriesCreate(string $typeSlug)
    {
        View::render('entries/form.twig', [
            'title' => 'Adicionar Nova Entrada',
            'menu_active' => 'entries_' . $typeSlug,
            'type_slug' => $typeSlug
        ]);
    }
}
