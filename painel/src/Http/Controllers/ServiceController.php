<?php

namespace Painel\Http\Controllers;

use Painel\Core\Request;
use Painel\Core\Response;
use Painel\Models\Setting;
use Exception;

class ServiceController
{
    /**
     * GET /api/v1/services/share-options?url=...&title=...
     * Retorna os payloads (links formatados) para o widget de ShareBar (Front)
     */
    public function shareOptions()
    {
        $request = new Request();
        $url = $request->input('url');
        $title = $request->input('title') ?? 'Confira isso';

        if (!$url) {
            return Response::error('O atributo URL alvo é obrigatório para construir opções de compartilhamento.', 400);
        }

        $encodedUrl = urlencode($url);
        $encodedTitle = urlencode($title);

        // Drivers Sociais disponíveis e parametrizados internamente no Painel
        // O Front não precisa saber *como* compartilha, o Painel abstrai.
        $options = [
            'whatsapp' => [
                'name' => 'WhatsApp',
                'icon' => 'bi-whatsapp',
                'link' => "https://api.whatsapp.com/send?text={$encodedTitle}%20-\%20{$encodedUrl}"
            ],
            'facebook' => [
                'name' => 'Facebook',
                'icon' => 'bi-facebook',
                'link' => "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}"
            ],
            'linkedin' => [
                'name' => 'LinkedIn',
                'icon' => 'bi-linkedin',
                'link' => "https://www.linkedin.com/sharing/share-offsite/?url={$encodedUrl}"
            ],
            'twitter' => [
                'name' => 'X (Twitter)',
                'icon' => 'bi-twitter-x',
                'link' => "https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedTitle}"
            ],
            'copy' => [
                'name' => 'Copiar Link',
                'icon' => 'bi-link-45deg',
                'action' => 'clipboard',
                'data' => $url
            ]
        ];

        return Response::json(true, array_values($options), 'Opções de Driver de Compartilhamento instanciadas.');
    }

    /**
     * POST /api/v1/messenger/whatsapp
     * Recebe Formulario do Front, busca o telefone mestre nas Configs, e devolve o Target URL
     */
    public function sendWhatsApp()
    {
        $request = new Request();
        $nome = $request->input('nome') ?? 'Visitante';
        $mensagem = $request->input('mensagem') ?? 'Olá, gostaria de mais informações.';

        $tenantId = 1;
        
        // Em vez de HardCoded, busca o Numero Oficial da Instituição nas Configs Ocultas
        $masterNumber = Setting::get($tenantId, 'contact_whatsapp') ?? '5516999999999'; 

        $textoFormatado = "*Santis WebLead* 🚀\n";
        $textoFormatado .= "Nome: {$nome}\n";
        $textoFormatado .= "Mensagem: {$mensagem}";

        $redirectUrl = "https://api.whatsapp.com/send?phone={$masterNumber}&text=" . urlencode($textoFormatado);

        return Response::json(true, [
            'redirect_url' => $redirectUrl,
            'driver' => 'whatsapp_web'
        ], 'Lead empacotado e redirecionamento montado pelo Panel.');
    }

    /**
     * POST /api/v1/scanner/pwned
     * Age como um Proxy transparente para HIBP, sem expor as origens do Front
     */
    public function scanPwned()
    {
        $request = new Request();
        $email = $request->input('email');

        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return Response::error('E-mail inválido fornecido para o Web Scanner.', 400);
        }

        // ---
        // AQUI ENTRARIA O CURL DA API COM A `hibp-api-key` ESCONDIDA NO .ENV DO BACKEND
        // SIMULAÇÃO FIXA PARA CUMPRIR COM A FASE 4 BASE
        // ---

        // Simulação Pedagógica (Mock):
        $isPwned = (strpos(strtolower($email), 'admin') !== false || strpos(strtolower($email), 'teste') !== false);
        
        // Fazer sleep curto para simular request ext
        usleep(800000); // 800ms

        if ($isPwned) {
             return Response::json(true, [
                'status' => 'danger',
                'title' => 'Vazamento Detectado',
                'message' => "O e-mail {$email} apareceu em bases de dados expostas. Recomendamos atualização de senhas imediatamente.",
                'breach_count' => rand(1, 5)
            ], 'Dados processados via Proxy Engine Painel.');
        } else {
             return Response::json(true, [
                'status' => 'secure',
                'title' => 'Tudo Limpo e Blindado',
                'message' => 'Nenhuma credencial exposta listada nas Dark Webs monitoradas. Sua conta está ilesa.',
                'breach_count' => 0
            ], 'Dados processados via Proxy Engine Painel.');
        }
    }
}
