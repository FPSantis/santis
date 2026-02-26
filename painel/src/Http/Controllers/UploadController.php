<?php

namespace Painel\Http\Controllers;

use Painel\Core\Response;
use Painel\Models\MediaFile;
use Exception;

class UploadController
{
    /**
     * Tamanho Máximo em Bytes (5MB)
     */
    private const MAX_SIZE = 5 * 1024 * 1024;

    /**
     * Tipos de Arquivos Permitidos (Proteção Nativa no Back)
     */
    private const ALLOWED_MIMES = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/svg+xml' => 'svg',
        'image/gif'  => 'gif',
        'application/pdf' => 'pdf'
    ];

    /**
     * Lista todas as mídias do Tenant
     * GET /api/secure/media
     */
    public function index()
    {
        $tenantId = 1; // Fixo MVP
        $files = MediaFile::all($tenantId);
        
        // Formata as saídas para compatibilizar com DataTables se necessário
        $cdnDomain = getenv('CDN_URL') ?: 'https://cdn.santis.ddev.site';
        
        foreach ($files as &$file) {
            $file['full_url'] = $cdnDomain . $file['path'];
            $file['size_formatted'] = number_format($file['size_bytes'] / 1024, 2) . ' KB';
        }

        return Response::json(true, $files, 'Lista de mídias carregada com sucesso.');
    }

    /**
     * Endpoint para receber POST multipart/form-data com o binário
     * POST /api/secure/upload
     */
    public function store()
    {
        $tenantId = 1; // Fixo MVP
        $userId = $_SERVER['AUTH_USER_ID'] ?? null; // Plantado pelo AuthMiddleware

        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return Response::error('Nenhum arquivo enviado ou ocorreu um erro durante a transferência.', 400);
        }

        $file = $_FILES['file'];
        // Configuração de Rota Customizável via Painel (Ex: upload pra /config, /portfolio, /blog)
        $destinationFolder = $request->input('destination_folder') ?? 'uploads';
        $destinationFolder = preg_replace('/[^a-zA-Z0-9_\-]/', '', $destinationFolder); // Sanitize

        // 1. Validação de Tamanho
        if ($file['size'] > self::MAX_SIZE) {
            return Response::error('O arquivo excede o tamanho máximo de 5MB.', 413);
        }

        // 2. Validação de MIME Type (Não confie na extensão do cliente, use fileinfo do servidor)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!array_key_exists($mimeType, self::ALLOWED_MIMES)) {
            return Response::error("Tipo de arquivo não suportado: {$mimeType}. São permitidos apenas imagens (JPG, PNG, WEBP, SVG, GIF) e PDFs.", 415);
        }

        // 3. Montar a Árvore de Diretórios
        $year = date('Y');
        $month = date('m');
        
        $cdnRoot = dirname(__DIR__, 4) . '/cdn/public_html';
        
        // Exceção: A pasta /config não leva data, é global.
        if ($destinationFolder === 'config') {
             $relativeDir = "/{$destinationFolder}";
        } else {
             $relativeDir = "/{$destinationFolder}/{$year}/{$month}";
        }
        
        $absoluteDir = $cdnRoot . $relativeDir;

        // Se não existir, crie fisicamente as pastas
        if (!is_dir($absoluteDir)) {
            mkdir($absoluteDir, 0777, true);
        }

        // 4. Lógica de Conversão e Salvar fisicamente o Arquivo
        $safeOriginalName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
        $uniqueFilename = $safeOriginalName . '-' . uniqid();
        
        $isConvertibleImage = in_array($mimeType, ['image/jpeg', 'image/png']);
        
        // Se for WEBP (já nativo), pulamos recompressão e só movemos limpo (a menos que precise forçar resize)
        // Se for SVG, não converte e move.
        // Se for JPG/PNG e a extensão GD estiver ON, processa!

        if ($isConvertibleImage) {
            $finalFilename = $uniqueFilename . '.webp';
            $absolutePath = $absoluteDir . '/' . $finalFilename;
            $dbPath = $relativeDir . '/' . $finalFilename;
            
            try {
                \Painel\Core\ImageHelper::processToWebp($file['tmp_name'], $absolutePath, 1200, 1200, 85);
                $finalMime = 'image/webp';
                // Calculamos o novo tamanho pós compressão WEBP (Pode reduzir 90%!)
                $finalSize = filesize($absolutePath);
            } catch (Exception $e) {
                 return Response::error('A compressão do Arquivo falhou GD API: ' . $e->getMessage(), 500);
            }
        } else {
            // SVGs, WEBP e PDFs movem direto (Zero GD compression)
            $extension = self::ALLOWED_MIMES[$mimeType] ?? 'bin';
            $finalFilename = $uniqueFilename . '.' . $extension;
            $absolutePath = $absoluteDir . '/' . $finalFilename;
            $dbPath = $relativeDir . '/' . $finalFilename; $finalMime = $mimeType;
            $finalSize = $file['size'];

            if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
                return Response::error('Falha crítica de I/O ao tentar mover o arquivo para a CDN.', 500);
            }
        }

        // 6. Arquivou com sucesso? Armazene na Tabela `media_files` do MariaDB
        $data = [
            'filename'      => $finalFilename,
            'original_name' => $file['name'],
            'mime_type'     => $finalMime,
            'size_bytes'    => $finalSize,
            'path'          => $dbPath,
            'uploaded_by'   => $userId
        ];

        try {
            $newId = MediaFile::create($tenantId, $data);
            $fileRecord = MediaFile::find($newId, $tenantId);
            
            $cdnDomain = getenv('CDN_URL') ?: 'https://cdn.santis.ddev.site';
            $fileRecord['full_url'] = $cdnDomain . $fileRecord['path'];

            return Response::json(true, $fileRecord, 'Upload WEBP/Global realizado com sucesso transferido para CDN.', 201);
        } catch (Exception $e) {
            @unlink($absolutePath);
            return Response::error('Erro ao guardar rastreio no Banco: ' . $e->getMessage(), 500);
        }
    }
}
