<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/src/bootstrap.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$params = [];
if ($method === 'PUT' || $method === 'POST') {
    $input = file_get_contents('php://input');
    if ($input) {
        if (json_validate($input)) {
            $params = json_decode($input, true);
        } else {
            parse_str($input, $params);
        }
    }
}

$router->dispatch($method, $path, $params);
