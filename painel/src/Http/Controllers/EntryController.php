<?php

namespace Painel\Http\Controllers;

use Painel\Core\Request;
use Painel\Core\Response;
use Painel\Models\Entry;
use Painel\Models\ContentType;
use Exception;

class EntryController
{
    /**
     * Traz todas Entradas de um Tipo de Módulo Dinâmico Específico (Passado via Regex no Roteador)
     * GET /api/secure/entries/{type_slug}
     */
    public function index(string $typeSlug)
    {
        $tenantId = 1; // MVP Santis fixo. Em multi-tenant extrairemos do $userId vindo do Middleware

        try {
            $entries = Entry::byTypeSlug($tenantId, $typeSlug);
            
            return Response::json(true, $entries, count($entries) . " entradas do tipo '{$typeSlug}' localizadas.");
        } catch (Exception $e) {
            return Response::error('Falha ao listar Entradas: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Rota de Leitura Pública do Frontend
     * GET /api/v1/entries/{type_slug}
     */
    public function publicIndex(string $typeSlug)
    {
        $tenantId = 1;

        try {
            // Em produção, filtrar apenas Entradas cujo status='published'
            $entries = Entry::byTypeSlug($tenantId, $typeSlug);
            
            // Decodificar JSON strings em Arrays para envio Limpo via Endpoint REST
            foreach ($entries as &$entry) {
                if (isset($entry['content_data']) && is_string($entry['content_data'])) {
                    $entry['content_data'] = json_decode($entry['content_data'], true);
                }
            }
            
            return Response::json(true, $entries, "Dados Públicos do Serviço '{$typeSlug}' servidos com sucesso.");
        } catch (Exception $e) {
            return Response::error('Erro ao prover dados: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Registra nova Entrada Atrelada ao Frontend Builder
     * POST /api/secure/entries/{type_slug}
     */
    public function store(string $typeSlug)
    {
        $tenantId = 1;
        $request = new Request();
        
        $title = $request->input('title');
        $slug = $request->input('slug');
        $contentData = $request->input('content_data'); // Pode chegar como JSON string ou Array associativo

        if (!$title || !$slug) {
            return Response::error('Os campos obrigatórios (title, slug) não foram preenchidos.', 400);
        }

        // 1. Achar o Identificador Físico do Módulo
        $type = ContentType::all(1); // Array temporário para simular search se 'find' n tiver overload
        // (Seria melhor ter findBySlug, faremos query custom ou refatoraremos num refactor futuro)
        
        // Pior cenário: Como o BD resolve o tenant e a slug, podemos chamar findBySlug no ContentType:
        $db = \Painel\Core\Database::getInstance();
        $stmtType = $db->prepare("SELECT id FROM content_types WHERE tenant_id = :t AND slug = :s LIMIT 1");
        $stmtType->execute(['t' => $tenantId, 's' => $typeSlug]);
        $typeInfo = $stmtType->fetch();

        if (!$typeInfo) {
            return Response::error("O Módulo '{$typeSlug}' não está registrado na arquitetura CMS.", 404);
        }

        $data = [
            'content_type_id' => $typeInfo['id'],
            'category_id' => $request->input('category_id'),
            'title' => $title,
            'slug' => strtolower($slug),
            'status' => $request->input('status') ?? 'published',
            // Decodifica se string HTTP para que o Model Json-Encode de novo virando string de Banco (Isolamento Sanitizado)
            'content_data' => is_string($contentData) ? json_decode($contentData, true) : ($contentData ?? [])
        ];

        try {
            $newId = Entry::create($tenantId, $data);
            return Response::json(true, ['id' => $newId, 'slug' => $slug], 'Entrada documentada e arquivada via EAV.', 201);
        } catch (Exception $e) {
            return Response::error('Engine Falhou na Criação da Entrada: ' . $e->getMessage(), 500);
        }
    }
}
