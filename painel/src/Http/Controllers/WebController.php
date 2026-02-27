<?php

namespace Painel\Http\Controllers;

use Painel\Core\View;

class WebController
{
    private function getSiteVariables(): array
    {
        $host = $_SERVER['HTTP_HOST'] ?? 'painel.santis.net.br';
        $domain = preg_replace('/^painel\./', '', $host);
        
        // Em um sistema real multi-tenant, o tenant_id viria da sessão/domínio. 
        // Usando o Tenant 1 provisoriamente.
        $settings = \Painel\Models\Setting::all(1);
        $logoPath = $settings['logo_master'] ?? '/config/logo-santis.svg';
        $iconPath = $settings['icon_master'] ?? '/config/icon-santis.svg';

        return [
            'cdn_url'   => "https://cdn.{$domain}",
            'site_logo' => "https://cdn.{$domain}{$logoPath}",
            'site_icon' => "https://cdn.{$domain}{$iconPath}",
            'site_url'  => "https://www.{$domain}/"
        ];
    }

    /**
     * Tela Principal do Painel (Dashboard)
     */
    public function dashboard()
    {
        $vars = $this->getSiteVariables();
        $vars['title']      = 'Dashboard | Santis CMS';
        $vars['page_title'] = 'Dashboard';
        $vars['page_icon']  = 'bx-home-circle';
        $vars['menu_active'] = 'dashboard';
        View::render('dashboard.twig', $vars);
    }

    public function login()
    {
        $vars = $this->getSiteVariables();
        $vars['title'] = 'Login | Santis CMS';
        View::render('auth/login.twig', $vars);
    }

    public function media()
    {
        $vars = $this->getSiteVariables();
        $vars['title']      = 'Gerenciador de Mídia | CDN';
        $vars['page_title'] = 'Gerenciador de Mídia';
        $vars['page_icon']  = 'bx-images';
        $vars['menu_active'] = 'media';
        View::render('media.twig', $vars);
    }

    public function settings()
    {
        $vars = $this->getSiteVariables();
        $vars['title']      = 'Configurações Globais';
        $vars['page_title'] = 'Configurações';
        $vars['page_icon']  = 'bx-cog';
        $vars['menu_active'] = 'settings';
        View::render('settings.twig', $vars);
    }

    public function modules()
    {
        $vars = $this->getSiteVariables();
        $vars['title']      = 'Módulos de Conteúdo';
        $vars['page_title'] = 'Módulos';
        $vars['page_icon']  = 'bx-collection';
        $vars['menu_active'] = 'modules';
        View::render('content_types/index.twig', $vars);
    }

    public function moduleCreate()
    {
        $vars = $this->getSiteVariables();
        $vars['title']      = 'Criar Novo Módulo';
        $vars['page_title'] = 'Novo Módulo';
        $vars['page_icon']  = 'bx-plus-circle';
        $vars['menu_active'] = 'modules';
        View::render('content_types/form.twig', $vars);
    }

    public function moduleEdit(int $id)
    {
        $vars = $this->getSiteVariables();
        $vars['title']      = 'Editar Módulo';
        $vars['page_title'] = 'Editar Módulo';
        $vars['page_icon']  = 'bx-edit';
        $vars['menu_active'] = 'modules';
        $vars['type_id']    = $id;
        View::render('content_types/form.twig', $vars);
    }

    public function blueprints()
    {
        $vars = $this->getSiteVariables();
        $vars['title']      = 'Blueprints SaaS';
        $vars['page_title'] = 'Blueprints SaaS';
        $vars['page_icon']  = 'bx-package';
        $vars['menu_active'] = 'blueprints';
        View::render('blueprints.twig', $vars);
    }

    public function entriesIndex(string $typeSlug)
    {
        $vars = $this->getSiteVariables();
        $vars['title']      = 'Gerenciar Entradas';
        $vars['page_title'] = 'Entradas';
        $vars['page_icon']  = 'bx-list-ul';
        $vars['menu_active'] = 'entries_' . $typeSlug;
        $vars['type_slug']  = $typeSlug;
        View::render('entries/index.twig', $vars);
    }

    public function entriesCreate(string $typeSlug)
    {
        $vars = $this->getSiteVariables();
        $vars['title']      = 'Adicionar Nova Entrada';
        $vars['page_title'] = 'Nova Entrada';
        $vars['page_icon']  = 'bx-plus';
        $vars['menu_active'] = 'entries_' . $typeSlug;
        $vars['type_slug']  = $typeSlug;
        View::render('entries/form.twig', $vars);
    }

    public function entriesEdit(string $typeSlug, int $id)
    {
        $vars = $this->getSiteVariables();
        $vars['title']      = 'Editar Entrada';
        $vars['page_title'] = 'Editar Entrada';
        $vars['page_icon']  = 'bx-edit';
        $vars['menu_active'] = 'entries_' . $typeSlug;
        $vars['type_slug']  = $typeSlug;
        $vars['entry_id']   = $id;
        View::render('entries/form.twig', $vars);
    }
}
