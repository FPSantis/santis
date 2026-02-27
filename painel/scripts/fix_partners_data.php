<?php
require_once __DIR__ . '/../vendor/autoload.php';
$db = Painel\Core\Database::getInstance();

$partners = [
    ['name' => 'DigitalOcean', 'logo' => 'digitalocean.svg'],
    ['name' => 'Google Cloud', 'logo' => 'google-cloud.svg'],
    ['name' => 'Google Gemini', 'logo' => 'google-gemini.svg'],
    ['name' => 'HIBP', 'logo' => 'hibp.svg'],
    ['name' => 'Hostinger', 'logo' => 'hostinger.svg'],
    ['name' => 'Mailchimp', 'logo' => 'mailchimp.svg'],
    ['name' => 'Meta', 'logo' => 'meta.svg'],
    ['name' => 'MySQL', 'logo' => 'mysql.svg'],
    ['name' => 'OnlyOffice', 'logo' => 'onlyoffice.svg'],
    ['name' => 'Symantec', 'logo' => 'symantec.svg']
];

$tenantId = 1;
$stmtType = $db->prepare("SELECT id FROM content_types WHERE slug = 'partners'");
$stmtType->execute();
$type = $stmtType->fetch();
if (!$type) {
    die("Content Type 'partners' not found.\n");
}
$contentTypeId = $type['id'];

// Limpar para reinserir limpo
$db->prepare("DELETE FROM entries WHERE content_type_id = :ct_id")->execute(['ct_id' => $contentTypeId]);

foreach ($partners as $p) {
    $contentData = [
        'nome' => $p['name'],
        'link' => '#',
        'logo' => '/partners/' . $p['logo']
    ];
    
    $stmt = $db->prepare("INSERT INTO entries 
        (tenant_id, content_type_id, title, slug, content_data, status, created_at) 
        VALUES (:tenant_id, :ct_id, :title, :slug, :data, 'published', NOW())");
    
    $slug = strtolower(str_replace(' ', '-', $p['name']));
    $stmt->execute([
        'tenant_id' => $tenantId,
        'ct_id' => $contentTypeId,
        'title' => $p['name'],
        'slug' => $slug,
        'data' => json_encode($contentData)
    ]);
    echo "Inserted partner: {$p['name']}\n";
}
