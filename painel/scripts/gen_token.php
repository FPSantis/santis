<?php
require_once __DIR__ . '/../vendor/autoload.php';
use Firebase\JWT\JWT;
$secretKey = "santis_super_secret_development_key_2026";
$payload = [
    "iat" => time(),
    "iss" => "https://painel.santis.ddev.site",
    "nbf" => time(),
    "exp" => time() + 7200,
    "data" => ["userId" => 1, "tenant" => 1]
];
echo JWT::encode($payload, $secretKey, "HS256");
