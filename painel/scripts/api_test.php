<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Simulate internal request via Bramus Router
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/api/secure/entries/portfolio';

// We bypass token auth by injecting the user ID directly into the global server array
// since we know AuthMiddleware requires valid Bearer, but here we run script manually
$_SERVER['AUTH_USER_ID'] = 1;

$controller = new Painel\Http\Controllers\EntryController();
ob_start();
$controller->index('portfolio');
$output = ob_get_clean();
echo $output;
