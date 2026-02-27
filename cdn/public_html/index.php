<?php
// Captura o host requisitado (ex: cdn.santis.net.br)
$host = $_SERVER['HTTP_HOST'];

// Verifica se o host começa com "cdn." e extrai o domínio principal
if (preg_match('/^cdn\.(.+)$/i', $host, $matches)) {
    $domain = $matches[1];
    // Redireciona de forma permanente (301) para www.dominio
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: https://www." . $domain . "/");
    exit();
}

// Fallback preventivo caso a regra acima não de match
header("HTTP/1.1 301 Moved Permanently");
header("Location: https://www.santis.net.br/");
exit();
