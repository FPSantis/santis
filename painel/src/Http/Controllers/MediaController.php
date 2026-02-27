<?php

namespace Painel\Http\Controllers;

use Painel\Core\Request;
use Painel\Core\Response;
use Painel\Models\MediaFile;

class MediaController
{
    /**
     * Listar todos os arquivos (paginado e filtrado)
     */
    public function index()
    {
        $request = new Request();
        $user = $request->user();
        if (!$user || !isset($user['tenant'])) { return Response::error('Acesso não autorizado', 401); }
        
        $tenantId = $user['tenant'];
        $folderId = $request->get('folder_id');

        $filters = [
            'q' => $request->get('q'),
            'type' => $request->get('type'),
            'folder_id' => $folderId
        ];

        $page = (int)($request->get('page') ?: 1);
        $limit = (int)($request->get('limit') ?: 50);
        $offset = ($page - 1) * $limit;
        
        $files = MediaFile::all($tenantId, $filters, $limit, $offset);
        $folders = [];
        $currentFolder = null;

        if ($folderId) {
            $currentFolder = MediaFile::findFolder($folderId, $tenantId); // Need to add this method or handles inline
        }

        // Se estiver navegando (sem busca global), traz subpastas
        if (empty($filters['q']) && empty($filters['type'])) {
            $folders = MediaFile::allFolders($tenantId, $folderId ?: null);
        }
        
        return Response::json(true, [
            'files' => $files,
            'folders' => $folders,
            'current_folder' => $currentFolder,
            'page' => $page,
            'limit' => $limit
        ], 'Mídias recuperadas com sucesso');
    }

    public function upload()
    {
        $request = new Request();
        $user = $request->user();
        if (!$user || !isset($user['tenant'])) { return Response::error('Acesso não autorizado', 401); }
        
        $tenantId = $user['tenant'];
        $userId = $user['userId'];
        
        if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            return Response::error('Nenhum arquivo válido recebido', 400);
        }
        
        $file = $_FILES['file'];
        $folderId = $request->get('folder_id');
        
        // Determinar o caminho físico
        $relativePath = "/uploads";
        if ($folderId) {
            $db = \Painel\Core\Database::getInstance();
            $stmt = $db->prepare("SELECT path FROM media_folders WHERE id = :id AND tenant_id = :tenant_id");
            $stmt->execute(['id' => $folderId, 'tenant_id' => $tenantId]);
            $f = $stmt->fetch();
            if ($f && $f['path']) {
                $relativePath = $f['path'];
            }
        } else {
            // Default to yyyy/mm inside /uploads
            $relativePath .= "/" . date('Y/m');
        }

        // Validar MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        $allowedMimes = [
            'image/jpeg' => 'jpg', 'image/png'  => 'png', 'image/webp' => 'webp',
            'image/svg+xml' => 'svg', 'image/gif'  => 'gif', 'application/pdf' => 'pdf'
        ];

        if (!array_key_exists($mimeType, $allowedMimes)) {
            return Response::error('Tipo de arquivo não suportado', 415);
        }

        $cdnRoot = dirname(__DIR__, 3) . '/cdn/public_html';
        $targetDir = $cdnRoot . $relativePath;
        
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        $safeOriginalName = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
        $uniqueFilename = $safeOriginalName . '-' . uniqid();
        
        if (in_array($mimeType, ['image/jpeg', 'image/png'])) {
            $finalFilename = $uniqueFilename . '.webp';
            $absolutePath = $targetDir . '/' . $finalFilename;
            $dbPath = $relativePath . '/' . $finalFilename;
            try {
                \Painel\Core\ImageHelper::processToWebp($file['tmp_name'], $absolutePath, 1200, 1200, 85);
                $finalMime = 'image/webp';
                $finalSize = filesize($absolutePath);
            } catch (\Exception $e) {
                return Response::error('Conversão falhou: ' . $e->getMessage(), 500);
            }
        } else {
            $extension = $allowedMimes[$mimeType];
            $finalFilename = $uniqueFilename . '.' . $extension;
            $absolutePath = $targetDir . '/' . $finalFilename;
            $dbPath = $relativePath . '/' . $finalFilename;
            $finalMime = $mimeType;
            $finalSize = $file['size'];
            if (!move_uploaded_file($file['tmp_name'], $absolutePath)) {
                return Response::error('Falha ao gravar arquivo', 500);
            }
        }
        
        $mediaId = MediaFile::create($tenantId, [
            'filename'    => $finalFilename,
            'path'        => $dbPath,
            'mime_type'   => $finalMime,
            'size_bytes'  => $finalSize,
            'folder_id'   => $folderId ?: null,
            'uploaded_by' => $userId
        ]);
        
        return Response::json(true, [
            'id' => $mediaId,
            'path' => $dbPath,
            'url' => 'https://cdn.santis.net.br' . $dbPath
        ], 'Arquivo enviado com sucesso!');
    }

    public function createFolder()
    {
        $request = new Request();
        $user = $request->user();
        if (!$user || !isset($user['tenant'])) { return Response::error('Acesso não autorizado', 401); }
        
        $name = $request->get('name');
        $parentId = $request->get('parent_id') ?: null;
        
        if (!$name) { return Response::error('Nome da pasta é obrigatório', 400); }
        
        // Determinar o path físico
        $baseDir = "/uploads";
        if ($parentId) {
            $db = \Painel\Core\Database::getInstance();
            $stmt = $db->prepare("SELECT path FROM media_folders WHERE id = :id AND tenant_id = :tenant_id");
            $stmt->execute(['id' => $parentId, 'tenant_id' => $user['tenant']]);
            $parent = $stmt->fetch();
            if ($parent) $baseDir = $parent['path'];
        }
        
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
        $relativePath = $baseDir . "/" . $safeName;
        
        // Criar fisicamente no CDN
        $cdnRoot = dirname(__DIR__, 3) . '/cdn/public_html';
        $absolutePath = $cdnRoot . $relativePath;
        
        if (!is_dir($absolutePath)) {
            mkdir($absolutePath, 0755, true);
        }
        
        $id = MediaFile::createFolder($user['tenant'], $name, $relativePath, $parentId);
        return Response::json(true, ['id' => $id, 'path' => $relativePath], 'Pasta criada com sucesso');
    }

    public function deleteFolder($id)
    {
        $request = new Request();
        $user = $request->user();
        if (!$user || !isset($user['tenant'])) { return Response::error('Acesso não autorizado', 401); }
        
        MediaFile::deleteFolder($id, $user['tenant']);
        return Response::json(true, null, 'Pasta deletada com sucesso');
    }

    /**
     * Detalhes de uma mídia específica
     */
    public function show($id)
    {
        $request = new Request();
        $user = $request->user();
        if (!$user || !isset($user['tenant'])) { return Response::error('Acesso não autorizado', 401); }
        
        $tenantId = $user['tenant'];
        $media = MediaFile::find($id, $tenantId);
        
        if (!$media) { return Response::error('Arquivo não localizado', 404); }
        
        $media['url'] = 'https://cdn.santis.net.br' . $media['file_path'];
        $media['size_formatted'] = number_format($media['size'] / 1024, 1) . ' KB';
        
        return Response::json(true, $media, 'Mídia recuperada com sucesso');
    }

    /**
     * Excluir Arquivo
     */
    public function delete($id)
    {
        $request = new Request();
        $user = $request->user();
        if (!$user || !isset($user['tenant'])) { return Response::error('Acesso não autorizado', 401); }
        
        $tenantId = $user['tenant'];
        $media = MediaFile::find($id, $tenantId);
        
        if (!$media) { return Response::error('Arquivo não localizado', 404); }
        
        $cdnRoot = dirname(__DIR__, 3) . '/cdn/public_html';
        $filePath = $cdnRoot . $media['file_path'];
        
        if (file_exists($filePath)) { @unlink($filePath); }
        MediaFile::delete($id, $tenantId);
        
        return Response::json(true, null, 'Arquivo deletado com sucesso');
    }

    /**
     * Atualiza metadados (incluindo Alt Text)
     */
    public function update($id)
    {
        $request = new Request();
        $user = $request->user();
        if (!$user || !isset($user['tenant'])) { return Response::error('Acesso não autorizado', 401); }
        
        $tenantId = $user['tenant'];
        $data = $request->all();
        
        if (MediaFile::update($id, $tenantId, $data)) {
            return Response::json(true, null, 'Metadados atualizados com sucesso');
        }
        
        return Response::error('Erro ao atualizar', 500);
    }
    
    public function deleteMultiple()
    {
        $request = new Request();
        $user = $request->user();
        if (!$user || !isset($user['tenant'])) { return Response::error('Acesso não autorizado', 401); }
        
        $tenantId = $user['tenant'];
        $ids = $request->all()['ids'] ?? [];
        
        if (empty($ids) || !is_array($ids)) { return Response::error('Nenhum ID fornecido.', 400); }
        
        $cdnRoot = dirname(__DIR__, 3) . '/cdn/public_html';
        foreach ($ids as $id) {
            $media = MediaFile::find($id, $tenantId);
            if ($media) {
                $filePath = $cdnRoot . $media['file_path'];
                if (file_exists($filePath)) { @unlink($filePath); }
                MediaFile::delete($id, $tenantId);
            }
        }
        
        return Response::json(true, null, 'Arquivos deletados com sucesso.');
    }
}
