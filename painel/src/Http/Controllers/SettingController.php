<?php

namespace Painel\Http\Controllers;

use Painel\Core\Request;
use Painel\Core\Response;
use Painel\Models\Setting;
use Painel\Models\AuditLog;

class SettingController
{
    /**
     * GET /api/secure/settings
     */
    public function index()
    {
        $tenantId = 1; // Fixo MVP
        $settings = Setting::all($tenantId);
        
        return Response::json(true, $settings, 'Configurações carregadas com sucesso.');
    }

    /**
     * POST /api/secure/settings
     * Salva múltiplas configurações de uma vez a partir de um JSON
     */
    public function store()
    {
        $request = new Request();
        $data = $request->getBody(); // Ex: ['site_name' => 'Santis CMS', 'contact_email' => '...']
        
        $tenantId = 1; // Fixo MVP
        $userId = $_SERVER['AUTH_USER_ID'] ?? null;

        if (empty($data)) {
            return Response::error('Nenhuma configuração fornecida para atualizar.', 400);
        }

        foreach ($data as $key => $value) {
            // O value pode ser string, numérico ou até arrays/objetos (converteremos arrays para JSON)
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value, JSON_UNESCAPED_UNICODE);
            }
            Setting::set($tenantId, (string)$key, (string)$value, $userId);
        }
        
        // Registrar Auditoria Simplificada
        AuditLog::logAction($tenantId, $userId, 'updated_batch', 'settings', 0, ['keys_updated' => array_keys($data)]);

        return Response::json(true, $data, 'Configurações globais salvas com sucesso.');
    }
}
