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

        // 1. Validação de Tamanho
        if ($file['size'] > self::MAX_SIZE) {
            return Response::error('O arquivo excede o tamanho máximo de 5MB.', 413);
        }

        // 2. Validação de MIME Type (Não confie na extensão do cliente, use fileinfo do servidor)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!array_key_exists($mimeType, self::ALLOWED_MIMES)) {
            return Response::error("Tipo de arquivo não suportado: {$mimeType}. São permitidos apenas imagens (JPG, PNG, WEBP, GIF) e PDFs.", 415);
        }

        // 3. Montar a Árvore de Diretórios (Ano/Mês)
        $year = date('Y');
        $month = date('m');
        
        // Caminho Absoluto dentro do Servidor: /mnt/d/_WEB/santis/cdn/public_html/uploads/2026/02/
        $cdnRoot = dirname(__DIR__, 4) . '/cdn/public_html';
        $relativeDir = "/uploads/{$year}/{$month}";
        $absoluteDir = $cdnRoot . $relativeDir;

        // Se não existir, crie fisicamente as pastas
        if (!is_dir($absoluteDir)) {
            mkdir($absoluteDir, 0777, true);
        }

        // 4. Gerar nome de arquivo seguro + Hash único para evitar sobreposições
        $extension = self::ALLOWED_MIMES[$mimeType];
        $safeOriginalName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
        $uniqueFilename = $safeOriginalName . '-' . uniqid() . '.' . $extension;
        
        $absolutePath = $absoluteDir . '/' . $uniqueFilename;
        
        // Caminho Salvo no BD que a API vai entregar (Esse caminho + $SITE_URL_CDN = Sucesso Visual)
        $dbPath = $relativeDir . '/' . $uniqueFilename;

        // 5. O Pulo do Gato (O Move físico entre o Buffer do PHP e a CDN real)
        if (move_uploaded_file($file['tmp_name'], $absolutePath)) {
            
            // 6. Arquivou com sucesso? Armazene na Tabela `media_files` do MariaDB
            $data = [
                'filename'      => $uniqueFilename,
                'original_name' => $file['name'],
                'mime_type'     => $mimeType,
                'size_bytes'    => $file['size'],
                'path'          => $dbPath,
                'uploaded_by'   => $userId
            ];

            try {
                $newId = MediaFile::create($tenantId, $data);
                $fileRecord = MediaFile::find($newId, $tenantId);
                
                // Anexa a URL Completa pro Front usar agora mesmo se quiser
                $cdnDomain = getenv('CDN_URL') ?: 'https://cdn.santis.ddev.site';
                $fileRecord['full_url'] = $cdnDomain . $fileRecord['path'];

                return Response::json(true, $fileRecord, 'Upload realizado com sucesso transferido para CDN.', 201);
            } catch (Exception $e) {
                // Em caso raro da query errar DEPOIS de upar, excluímos a imagem órfã da CDN.
                @unlink($absolutePath);
                return Response::error('Erro ao guardar rastreio no Banco: ' . $e->getMessage(), 500);
            }

        } else {
            return Response::error('Falha crítica de I/O ao tentar interligar as caixas Painel -> CDN.', 500);
        }
    }
}
