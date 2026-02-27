<?php

namespace Painel\Http\Controllers;

use Painel\Core\Request;
use Painel\Core\Response;
use Painel\Models\ContentType;
use Exception;

class ContentTypeController
{
    /**
     * Traz todos os tipos de conteúdo do Tenant Autenticado (via header JWT)
     */
    public function index()
    {
        // Pega o UserID que foi decodificado e plantado globalmente pelo AuthMiddleware
        $userId = $_SERVER['AUTH_USER_ID'] ?? null;
        
        // Em um sistema real, buscaríamos o Tenant ID do Usuário, para simplificar e focar na mecânica:
        $tenantId = 1;

        try {
            $types = ContentType::all($tenantId);
            return Response::json(true, $types, count($types) . ' tipos de conteúdo encontrados.');
        } catch (Exception $e) {
            return Response::error('Erro ao listar Tipos de Conteúdo: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Cria um novo Tipo de Conteúdo (POST /api/secure/types)
     */
    public function store()
    {
        $userId = $_SERVER['AUTH_USER_ID'] ?? null;
        $tenantId = 1; // Fixo no MVP para o "Santis"

        $request = new Request();
        
        $name = $request->input('name');
        $slug = $request->input('slug');
        
        if (!$name || !$slug) {
            return Response::error('Os campos name e slug são obrigatórios.', 400);
        }

        $data = [
            'name' => $name,
            'icon' => $request->input('icon') ?? 'bx-collection',
            'slug' => strtolower($slug),
            'description' => $request->input('description'),
            'schema' => is_string($request->input('schema')) ? json_decode($request->input('schema'), true) : ($request->input('schema') ?? []),
            'is_active' => $request->input('is_active') !== null ? (int)$request->input('is_active') : 1
        ];

        try {
            $newId = ContentType::create($tenantId, $data);
            $newType = ContentType::find($newId, $tenantId);
            
            // Auto-create Media Folder for this module
            $folderName = $name;
            $folderPath = "/" . strtolower($slug);
            \Painel\Models\MediaFile::createFolder($tenantId, $folderName, $folderPath, null);
            
            return Response::json(true, $newType, 'Tipo de conteúdo criado com sucesso.', 201);
        } catch (Exception $e) {
            return Response::error('Falha ao registrar novo Tipo: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Busca um Tipo de Conteúdo específico
     */
    public function show(int $id)
    {
        $tenantId = 1;
        try {
            $type = ContentType::find($id, $tenantId);
            if (!$type) {
                return Response::error('Tipo de conteúdo não encontrado.', 404);
            }
            return Response::json(true, $type);
        } catch (Exception $e) {
            return Response::error('Erro ao buscar Tipo: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Atualiza um Tipo de Conteúdo
     */
    public function update(int $id)
    {
        $tenantId = 1;
        $request = new Request();
        
        $data = [
            'name' => $request->input('name'),
            'icon' => $request->input('icon'),
            'slug' => strtolower($request->input('slug')),
            'description' => $request->input('description'),
            'schema' => is_string($request->input('schema')) ? json_decode($request->input('schema'), true) : ($request->input('schema') ?? []),
            'is_active' => $request->input('is_active') !== null ? (int)$request->input('is_active') : 1
        ];

        try {
            ContentType::update($id, $tenantId, $data);
            return Response::json(true, null, 'Tipo de conteúdo atualizado com sucesso.');
        } catch (Exception $e) {
            return Response::error('Erro ao atualizar Tipo: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove um Tipo de Conteúdo
     */
    public function delete(int $id)
    {
        $tenantId = 1;
        try {
            ContentType::delete($id, $tenantId);
            return Response::json(true, null, 'Tipo de conteúdo removido com sucesso.');
        } catch (Exception $e) {
            return Response::error('Erro ao remover Tipo: ' . $e->getMessage(), 500);
        }
    }
}
