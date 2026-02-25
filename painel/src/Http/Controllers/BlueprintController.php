<?php

namespace Painel\Http\Controllers;

use Painel\Core\Request;
use Painel\Core\Response;
use Painel\Models\ContentType;
use Painel\Models\Setting;
use Painel\Models\AuditLog;
use Exception;

class BlueprintController
{
    /**
     * GET /api/secure/blueprints/export
     * Emite um JSON limpo com a arquitetura de Tipos de Conteúdo e Configurações Globais
     */
    public function export()
    {
        $tenantId = 1; // Fixo no MVP
        $userId = $_SERVER['AUTH_USER_ID'] ?? null;

        try {
            $types = ContentType::all($tenantId);
            $settings = Setting::all($tenantId);

            // Sanitiza de Campos de ID Locais
            $cleanTypes = [];
            foreach ($types as $t) {
                // Remove infos do DB de Origem para que cheguem limpos no DB de Destino
                $cleanTypes[] = [
                    'name' => $t['name'],
                    'slug' => $t['slug'],
                    'description' => $t['description'],
                    'schema' => json_decode($t['schema'], true), // Schema Cru (Array)
                    'is_active' => $t['is_active']
                ];
            }

            $blueprint = [
                '_meta' => [
                    'version' => '1.0',
                    'generated_at' => date('c'),
                    'engine' => 'Santis Headless CMS Blueprint Generator'
                ],
                'content_types' => $cleanTypes,
                'settings' => $settings
            ];

            // Força o Download pelo Browser ao invés de exibir o JSON em tela (Opcional, mas util no MVP)
            header('Content-Type: application/json');
            header('Content-Disposition: attachment; filename="santis_blueprint_'.date('Ymd_His').'.json"');
            
            // Auditoria
            AuditLog::logAction($tenantId, $userId, 'exported', 'blueprints', 0, ['types_count' => count($cleanTypes)]);
            
            echo json_encode($blueprint, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            exit;

        } catch (Exception $e) {
            return Response::error('Falha ao exportar Blueprint: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/secure/blueprints/import
     */
    public function import()
    {
        $tenantId = 1; // Fixo MVP
        $userId = $_SERVER['AUTH_USER_ID'] ?? null;
        
        // Verifica upload nativo (form-data de Arquivo) em vez de JSON cru
        if (!isset($_FILES['blueprint_file']) || $_FILES['blueprint_file']['error'] !== UPLOAD_ERR_OK) {
            return Response::error('Nenhum arquivo Blueprint válido enviado.', 400);
        }

        $fileContent = file_get_contents($_FILES['blueprint_file']['tmp_name']);
        $blueprint = json_decode($fileContent, true);

        if (!$blueprint || !isset($blueprint['_meta']['engine']) || strpos($blueprint['_meta']['engine'], 'Santis') === false) {
            return Response::error('O arquivo fornecido não é um Blueprint válido do Santis CMS.', 400);
        }

        try {
            // Importa Content Types (Ignora SLUGs duplicados)
            if (isset($blueprint['content_types']) && is_array($blueprint['content_types'])) {
                foreach ($blueprint['content_types'] as $typeData) {
                    // Verifica se já existe para evitar colisão SQL
                    $exists = false;
                    foreach(ContentType::all($tenantId) as $existing) {
                        if($existing['slug'] === $typeData['slug']) { $exists = true; break; }
                    }
                    
                    if(!$exists) {
                        ContentType::create($tenantId, $typeData);
                    }
                }
            }

            // Importa Settings (Sobrescreve existentes)
            if (isset($blueprint['settings']) && is_array($blueprint['settings'])) {
                foreach ($blueprint['settings'] as $key => $val) {
                    Setting::set($tenantId, $key, $val, $userId);
                }
            }
            
            AuditLog::logAction($tenantId, $userId, 'imported', 'blueprints', 0, ['version' => $blueprint['_meta']['version']]);

            return Response::json(true, [], 'Blueprint injetado com sucesso na Organização.');

        } catch (Exception $e) {
            return Response::error('Falha crítica ao injetar dados do Blueprint: ' . $e->getMessage(), 500);
        }
    }
}
