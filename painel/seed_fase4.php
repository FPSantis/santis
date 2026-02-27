<?php

/**
 * Módulo Seeding EAV Santis CMS: Fase 4
 * Alimentando Settings e Content Types Base do Cliente no Banco Oficial.
 */

try {
    // Pegar Senhas do Ambiente DDEV
    $host = 'db';
    $db   = 'db';
    $user = 'db';
    $pass = 'db';
    $charset = 'utf8mb4';
    
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    print "Conectando ao banco DDEV MYSQL...\n";
    $pdo = new PDO($dsn, $user, $pass, $options);
    $tenantId = 1;

    print "1. Esvaziando a mesa de Settings...\n";
    $pdo->exec("DELETE FROM settings WHERE tenant_id = 1");

    print "2. Alocando Configurações Globais (Settings)...\n";
    $stmt = $pdo->prepare("INSERT INTO settings (tenant_id, `key`, `value`) VALUES (?, ?, ?)");
    
    $settings = [
        'slogan' => 'Soluções em <strong>Performance</strong> e <strong>Tecnologia.</strong>',
        'contact_email' => 'contato@santis.eng.br', // Trocado '.com.br' por '.eng.br' do front end real
        'contact_city' => 'São Carlos - SP | Brasil',
        'copyright_msg' => 'Santis: Inteligência, Tecnologia e Performance.',
        'logo_master' => '/config/logo-santis.svg',
        'qr_whatsapp' => '/config/whatsapp-qr.svg',
        'social_facebook' => 'https://facebook.com/santis',
        'social_instagram' => 'https://instagram.com/santis',
        'social_linkedin' => 'https://linkedin.com/company/santis'
    ];

    foreach($settings as $k => $v) {
        $stmt->execute([$tenantId, $k, $v]);
    }

    print "3. Esvaziando Content Types Residuais do MVP...\n";
    $pdo->exec("DELETE FROM content_types WHERE tenant_id = 1");

    print "4. Criando Entidades Dinamicas EAV...\n";
    $stmtTypes = $pdo->prepare("INSERT INTO content_types (tenant_id, name, slug, description, `schema`) VALUES (?, ?, ?, ?, ?)");
    
    $types = [
        [
            'Serviços',
            'services',
            'Módulo para gerenciar os pilares (Otimização e Painel)',
            json_encode([
                'fields' => [
                    ['name' => 'icone', 'type' => 'text'],
                    ['name' => 'nome', 'type' => 'text'],
                    ['name' => 'label', 'type' => 'text'],
                    ['name' => 'titulo', 'type' => 'richtext'],
                    ['name' => 'corpo', 'type' => 'richtext'],
                    ['name' => 'btn_action', 'type' => 'text'],
                    ['name' => 'btn_description', 'type' => 'text'],
                    ['name' => 'imagem', 'type' => 'image', 'rules' => 'cdn:/config/|format:png,webp']
                ]
            ], JSON_UNESCAPED_UNICODE)
        ],
        [
            'Parceiros',
            'partners',
            'Stack de Tecnologia e Integradores',
            json_encode([
                'fields' => [
                    ['name' => 'nome', 'type' => 'text'],
                    ['name' => 'link', 'type' => 'url'],
                    ['name' => 'logo', 'type' => 'image', 'rules' => 'cdn:/partners/YYYY/MM/|format:svg']
                ]
            ], JSON_UNESCAPED_UNICODE)
        ],
        [
            'Portfólio / Projetos',
            'portfolio',
            'Coleção de Ativos dos Clientes',
            json_encode([
                'fields' => [
                    ['name' => 'titulo', 'type' => 'text'],
                    ['name' => 'categoria', 'type' => 'select', 'options' => ['Online', 'Offline']],
                    ['name' => 'descricao', 'type' => 'text'],
                    ['name' => 'url_site', 'type' => 'url'],
                    ['name' => 'capa', 'type' => 'image', 'rules' => 'cdn:/portfolio/YYYY/MM/|format:webp|max:1200x1200']
                ]
            ], JSON_UNESCAPED_UNICODE)
        ],
        [
            'Redes Sociais',
            'social_networks',
            'Carrossel e Links de Redes do Rodapé/Middle',
            json_encode([
                'fields' => [
                    ['name' => 'nome', 'type' => 'text'],
                    ['name' => 'icone', 'type' => 'text'],
                    ['name' => 'cor', 'type' => 'color'],
                    ['name' => 'link', 'type' => 'url']
                ]
            ], JSON_UNESCAPED_UNICODE)
        ],
        [
            'Radar Santis (Blog)',
            'blog',
            'Inteligencias Técnicas',
            json_encode([
                'fields' => [
                    ['name' => 'titulo', 'type' => 'text'],
                    ['name' => 'resumo', 'type' => 'textarea'],
                    ['name' => 'corpo', 'type' => 'html'],
                    ['name' => 'imagem', 'type' => 'image', 'rules' => 'cdn:/blog/YYYY/MM/|format:webp|max:1200x1200'],
                    ['name' => 'categoria', 'type' => 'relation', 'table' => 'categories']
                ]
            ], JSON_UNESCAPED_UNICODE)
        ]
    ];

    foreach($types as $type) {
        $stmtTypes->execute([$tenantId, $type[0], $type[1], $type[2], $type[3]]);
    }
    
    print "SUCESSO TÁTICO! Settings e Tipos EAV semeados no DB DDEV!\n";

} catch (PDOException $e) {
    print "FALHA GRAVE PDO: " . $e->getMessage() . "\n";
}
