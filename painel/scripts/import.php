<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Painel\Core\Database;

$db = Database::getInstance();
$tenant_id = 1; // Assuming primary tenant is 1
$user_id = 1; // Assuming webmaster ID is 1

// Define target directories
$cdnBasePath = rtrim(dirname(__DIR__, 2) . '/cdn/public_html', '/');
$wwwBasePath = rtrim(dirname(__DIR__, 2) . '/www/public_html', '/');

// Clean existing data to avoid duplicates
echo "Cleaning existing entries and media...\n";
$db->exec("DELETE FROM entries WHERE tenant_id = $tenant_id");
$db->exec("DELETE FROM media_files WHERE tenant_id = $tenant_id");

function copyAndRegisterMedia($sourceLocalPath, $schemaFolder, $db, $tenant_id, $user_id) {
    global $cdnBasePath, $wwwBasePath;
    
    // User requested folder 'radar' for blog
    $targetFolderName = ($schemaFolder === 'blog') ? 'radar' : $schemaFolder;
    
    // Check if source exists
    $absoluteSource = $wwwBasePath . '/' . ltrim($sourceLocalPath, '/');
    if (!file_exists($absoluteSource)) {
        return '';
    }
    
    $filename = basename($absoluteSource);
    $mime = mime_content_type($absoluteSource);
    $size = filesize($absoluteSource);
    
    // Create new name to avoid conflicts
    $newName = md5(uniqid()) . '_' . $filename;
    $relPath = '/' . $targetFolderName;
    $targetDir = $cdnBasePath . $relPath;
    
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    
    $targetPath = $targetDir . '/' . $newName;
    copy($absoluteSource, $targetPath);
    
    $logicalPath = $relPath . '/' . $newName;
    
    // Insert into media_files
    $sql = "INSERT INTO media_files (tenant_id, file_name, file_path, mime_type, size, created_by, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        $tenant_id, $filename, $logicalPath, $mime, $size, $user_id
    ]);
    
    return $logicalPath;
}

function getContentTypeId($slug, $db) {
    $stmt = $db->prepare("SELECT id FROM content_types WHERE slug = ?");
    $stmt->execute([$slug]);
    return $stmt->fetchColumn();
}

function insertEntry($typeSlug, $title, $data, $db, $tenantId, $userId) {
    $typeId = getContentTypeId($typeSlug, $db);
    if (!$typeId) {
        echo "Type $typeSlug not found!\n";
        return;
    }
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
    
    $sql = "INSERT INTO entries (tenant_id, content_type_id, title, slug, status, content_data, published_by, created_by, published_at, created_at) 
            VALUES (?, ?, ?, ?, 'published', ?, ?, ?, NOW(), NOW())";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        $tenantId, $typeId, $title, $slug, json_encode($data), $userId, $userId
    ]);
}

// 1. SERVICES (from index.html)
echo "Inserting Services...\n";
insertEntry('services', 'Site Seguro', [
    'icone' => '<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>',
    'nome' => 'Site Seguro',
    'label' => 'Sua Presença Profissional',
    'titulo' => 'Sites que dão Credibilidade.',
    'corpo' => 'Muitas vezes o cliente te encontra nas redes sociais, mas busca o seu site para confiar. É o seu cartão de visitas oficial que prova que sua marca é real e profissional.',
    'btn_action' => '#contato',
    'btn_description' => 'Solicitar Orçamento Web',
    'imagem' => copyAndRegisterMedia('assets/img/web_presence.png', 'services', $db, $tenant_id, $user_id)
], $db, $tenant_id, $user_id);

insertEntry('services', 'Otimização de Sistemas', [
    'icone' => '<svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>',
    'nome' => 'Otimização e Performance',
    'label' => 'Cuidado Técnico Profissional',
    'titulo' => 'Otimização Especializada, Seu PC Revigorado.',
    'corpo' => 'Muito mais do que apenas "formatar". Nossos especialistas realizam uma análise completa do seu sistema e indicam melhorias reais para entregar a fluidez que você precisa, seja para estudar ou trabalhar.',
    'btn_action' => '#contato',
    'btn_description' => 'Falar com um Especialista',
    'imagem' => copyAndRegisterMedia('assets/img/system_optimization.png', 'services', $db, $tenant_id, $user_id)
], $db, $tenant_id, $user_id);

// 2. PORTFOLIO
echo "Inserting Portfolio...\n";
$portfolioData = [
    [
        "title" => "PortoRun",
        "url" => "portorun.com.br",
        "category" => "Eventos & Esporte",
        "image" => "assets/img/portorun_portfolio_v2.png",
        "fullDesc" => "Sistema completo de inscrições e gestão de cronometragem. Desenvolvido em PHP 8.4 com foco em alta carga de acessos e segurança MySQL."
    ],
    [
        "title" => "Advocacia Santis",
        "url" => "advocaciasantis.com.br",
        "category" => "Landing Page Pro",
        "image" => "assets/img/advocacia_portfolio_v2.png",
        "fullDesc" => "Página focada em conversão jurídica com blindagem de dados sensíveis e total conformidade com as normas da LGPD."
    ],
    [
        "title" => "Santis Control",
        "url" => "demo.santis.eng.br",
        "category" => "SaaS / Admin",
        "image" => "assets/img/santis_control_portfolio_v2.png",
        "fullDesc" => "Nosso dashboard proprietário. Uma interface limpa e segura para que o cliente gerencie leads e veja a saúde do seu site."
    ]
];
foreach ($portfolioData as $item) {
    insertEntry('portfolio', $item['title'], [
        'titulo' => $item['title'],
        'categoria' => $item['category'],
        'descricao' => $item['fullDesc'],
        'url_site' => $item['url'],
        'capa' => copyAndRegisterMedia($item['image'], 'portfolio', $db, $tenant_id, $user_id)
    ], $db, $tenant_id, $user_id);
}

// 3. BLOG
echo "Inserting Blog...\n";
$blogPosts = [
    [
        "title" => "Alerta de Segurança: Mega Vazamento expõe 200M de credenciais",
        "category" => "Segurança",
        "image" => "assets/img/system_optimization.png",
        "summary" => "Um novo banco de dados massivo foi encontrado em fóruns de cibercrime, contendo e-mails e senhas de serviços populares. Saiba como se proteger.",
        "content" => "<p>O cenário de ameaças digitais acaba de sofrer um novo abalo. Pesquisadores de segurança identificaram um repositório com mais de 200 milhões de registros expostos. Este vazamento, apelidado de Santis Alert #1, destaca a importância de senhas únicas e autenticação em dois fatores.</p>"
    ],
    [
        "title" => "Dica de Otimização: Como reduzir a latência no Windows 11",
        "category" => "Otimização",
        "image" => "assets/img/web_presence.png",
        "summary" => "Pequenos ajustes nas configurações de energia e serviços de segundo plano podem transformar a fluidez do seu sistema.",
        "content" => "<p>Muitos usuários sofrem com micro-travamentos no Windows 11 devido a telemetria excessiva e serviços de indexação pesados. Neste guia, mostramos como desabilitar processos desnecessários com segurança para ganhar performance real em jogos e trabalho.</p>"
    ],
    [
        "title" => "Vulnerabilidade Crítica em Roteadores Domésticos",
        "category" => "Segurança",
        "image" => "assets/img/santis_control.png",
        "summary" => "Uma falha de dia zero permite execução remota de código em marcas populares. Verifique se o seu firmware está atualizado.",
        "content" => "<p>A segurança da sua rede começa no roteador. Uma nova falha descoberta permite que atacantes assumam o controle total do tráfego de rede sem interação do usuário. A Santis recomenda a atualização imediata dos patches de segurança fornecidos pelos fabricantes.</p>"
    ]
];
foreach ($blogPosts as $item) {
    insertEntry('blog', $item['title'], [
        'titulo' => $item['title'],
        'resumo' => $item['summary'],
        'corpo' => $item['content'],
        'categoria' => $item['category'], // This might break if category is a relation ID instead of text. Let's send text, or leave blank
        'imagem' => copyAndRegisterMedia($item['image'], 'blog', $db, $tenant_id, $user_id)
    ], $db, $tenant_id, $user_id);
}

// 4. PARTNERS
echo "Inserting Partners...\n";
$partners = [
    ['nome' => 'DigitalOcean', 'logo' => 'uploads/partners/digitalocean.svg'],
    ['nome' => 'Google Cloud', 'logo' => 'uploads/partners/google-cloud.svg'],
    ['nome' => 'MongoDB', 'logo' => 'uploads/partners/google-gemini.svg'],
    ['nome' => 'Meta', 'logo' => 'uploads/partners/meta.svg'],
    ['nome' => 'MySQL', 'logo' => 'uploads/partners/mysql.svg']
];
foreach ($partners as $partner) {
    insertEntry('partners', $partner['nome'], [
        'nome' => $partner['nome'],
        'link' => '#',
        'logo' => copyAndRegisterMedia($partner['logo'], 'partners', $db, $tenant_id, $user_id)
    ], $db, $tenant_id, $user_id);
}

// 5. SOCIAL NETWORKS
echo "Inserting Social Networks...\n";
$socials = [
    ['nome' => 'Facebook', 'icone' => 'bi-facebook', 'link' => 'https://facebook.com/santis', 'cor' => '#1877F2'],
    ['nome' => 'Instagram', 'icone' => 'bi-instagram', 'link' => 'https://instagram.com/santis', 'cor' => '#E4405F'],
    ['nome' => 'LinkedIn', 'icone' => 'bi-linkedin', 'link' => 'https://linkedin.com/company/santis', 'cor' => '#0A66C2']
];
foreach ($socials as $social) {
    insertEntry('social_networks', $social['nome'], [
        'nome' => $social['nome'],
        'icone' => $social['icone'],
        'link' => $social['link'],
        'cor' => $social['cor']
    ], $db, $tenant_id, $user_id);
}

echo "Done migrating content!\n";
