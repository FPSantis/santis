<?php
require_once __DIR__ . '/../vendor/autoload.php';
$_SERVER['AUTH_USER_ID'] = 1;
$controller = new Painel\Http\Controllers\ContentTypeController();
$controller->index();
