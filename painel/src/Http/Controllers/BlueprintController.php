<?php

namespace Painel\Http\Controllers;

use Painel\Core\Request;
use Painel\Core\Response;
use Painel\Models\ContentType;
use Painel\Models\Setting;
use Painel\Models\AuditLog;
use Exception;
use ZipArchive;

class BlueprintController
{
    /**
     * GET /api/secure/blueprints/export
     * Emite um ZIP limpo englobando a arquitetura do BD, a Landing HTML e todo o Storage CDN.
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
                    'version' => '1.5.0-ZIP',
                    'generated_at' => date('c'),
                    'engine' => 'Santis Headless CMS Blueprint Generator'
                ],
                'content_types' => $cleanTypes,
                'settings' => $settings
            ];

            $jsonContent = json_encode($blueprint, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            // Criar Arquivo ZIP Temporário
            $zipFile = sys_get_temp_dir() . '/santis_blueprint_' . uniqid() . '.zip';
            $zip = new ZipArchive();
            if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new Exception("Não foi possível instanciar o compilador ZipArchive do lado servidor.");
            }

            // 1. O Cérebro: JSON com os Tipos EAV e Settings
            $zip->addFromString('blueprint.json', $jsonContent);

            // 2. O Músculo Visual: A Pasta do Site (WWW)
            $wwwRoot = dirname(__DIR__, 4) . '/www/public_html';
            self::addFolderToZip($wwwRoot, $zip, 'www/');

            // 3. O Espaço Operacional: A Raiz de Mídias (CDN)
            $cdnRoot = dirname(__DIR__, 4) . '/cdn/public_html';
            self::addFolderToZip($cdnRoot, $zip, 'cdn/');

            $zip->close();

            AuditLog::logAction($tenantId, $userId, 'exported', 'blueprints', 0, ['types_count' => count($cleanTypes), 'format' => 'zip_full_stack']);
            
            // Serve o ZIP Master para o Webmaster
            header('Content-Type: application/zip');
            header('Content-Length: ' . filesize($zipFile));
            header('Content-Disposition: attachment; filename="santis_agency_blueprint_'.date('Ymd_His').'.zip"');
            
            readfile($zipFile);
            @unlink($zipFile);
            exit;

        } catch (Exception $e) {
            return Response::error('Falha Crítica ao empacotar Blueprint do Tenant: ' . $e->getMessage(), 500);
        }
    }

    /**
     * POST /api/secure/blueprints/import
     */
    public function import()
    {
        $tenantId = 1; // Fixo MVP
        $userId = $_SERVER['AUTH_USER_ID'] ?? null;
        
        if (!isset($_FILES['blueprint_file']) || $_FILES['blueprint_file']['error'] !== UPLOAD_ERR_OK) {
            return Response::error('Nenhum pacote Blueprint ZIP válido fornecido para a esteira.', 400);
        }

        $tempUploadedZip = $_FILES['blueprint_file']['tmp_name'];
        $zip = new ZipArchive();

        if ($zip->open($tempUploadedZip) !== true) {
             return Response::error('O motor do servidor falhou em destrancar esse pacote ZIP.', 400);
        }

        $jsonStr = $zip->getFromName('blueprint.json');
        if ($jsonStr === false) {
             $zip->close();
             return Response::error('Corrupção Estrutural: O pacote fornecido não exibe o cérebro [blueprint.json].', 400);
        }

        $blueprint = json_decode($jsonStr, true);

        if (!$blueprint || !isset($blueprint['_meta']['engine']) || strpos($blueprint['_meta']['engine'], 'Santis') === false) {
             $zip->close();
             return Response::error('Rejeitado por Proteção de Autoria: Não é um Blueprint da Engine Santis.', 400);
        }

        try {
            // Importa o Cérebro Logico (Ignora chaves que já tem o mesmo slug no BD)
            if (isset($blueprint['content_types']) && is_array($blueprint['content_types'])) {
                foreach ($blueprint['content_types'] as $typeData) {
                    $exists = false;
                    foreach(ContentType::all($tenantId) as $existing) {
                        if($existing['slug'] === $typeData['slug']) { $exists = true; break; }
                    }
                    if(!$exists) { ContentType::create($tenantId, $typeData); }
                }
            }

            if (isset($blueprint['settings']) && is_array($blueprint['settings'])) {
                foreach ($blueprint['settings'] as $key => $val) {
                    Setting::set($tenantId, $key, $val, $userId);
                }
            }
            
            // Destrancando as Mídias Físicas Temporárias (CDN e WWW)
            $extractDir = sys_get_temp_dir() . '/santis_install_' . uniqid();
            $zip->extractTo($extractDir);
            $zip->close();

            $wwwRoot = dirname(__DIR__, 4) . '/www/public_html';
            $cdnRoot = dirname(__DIR__, 4) . '/cdn/public_html';

            // Mesclar Ativos da Subpasta WWW Extraida
            if (is_dir($extractDir . '/www')) {
                self::copyRecursive($extractDir . '/www', $wwwRoot);
            }
            // Mesclar Ativos da Subpasta CDN Extraida
            if (is_dir($extractDir . '/cdn')) {
                self::copyRecursive($extractDir . '/cdn', $cdnRoot);
            }

            self::deleteRecursive($extractDir); // Cleanup Memory
            AuditLog::logAction($tenantId, $userId, 'imported', 'blueprints', 0, ['version' => $blueprint['_meta']['version']]);

            return Response::json(true, [], 'Injeção de Blueprint SaaS Operante: Front-end, CDN e Tabelas mesclados com a Origem.');

        } catch (Exception $e) {
            return Response::error('A Síntese falhou no Banco de Dados ou IO do Host: ' . $e->getMessage(), 500);
        }
    }

    // Handlers Utilitários (Recursões Físicas)
    
    private static function addFolderToZip($dir, ZipArchive $zipArchive, $zipDirOffset = '') {
        if (!is_dir($dir)) return;
        if ($handle = opendir($dir)) {
            while (($file = readdir($handle)) !== false) {
                if ($file == '.' || $file == '..') continue;
                $filePath = $dir . DIRECTORY_SEPARATOR . $file;
                $localPath = $zipDirOffset . $file;
                if (is_dir($filePath)) {
                    $zipArchive->addEmptyDir($localPath);
                    self::addFolderToZip($filePath, $zipArchive, $localPath . '/');
                } else {
                    $zipArchive->addFile($filePath, $localPath);
                }
            }
            closedir($handle);
        }
    }

    private static function copyRecursive($src, $dst) {
        if (!is_dir($dst)) @mkdir($dst, 0777, true);
        $dir = opendir($src);
        while (($file = readdir($dir)) !== false) {
            if ($file == '.' || $file == '..') continue;
            if (is_dir($src . '/' . $file)) {
                self::copyRecursive($src . '/' . $file, $dst . '/' . $file);
            } else {
                @copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
        closedir($dir);
    }

    private static function deleteRecursive($dir) {
        if (!is_dir($dir)) return;
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (is_dir($dir . "/" . $object)) {
                    self::deleteRecursive($dir . "/" . $object);
                } else {
                    @unlink($dir . "/" . $object);
                }
            }
        }
        @rmdir($dir);
    }
}
