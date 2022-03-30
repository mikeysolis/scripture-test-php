<?php

// Autoload Composer packages
require __DIR__ . '/../vendor/autoload.php';

$request = $_SERVER['REQUEST_URI'];
$url = explode('.', $request);

switch ($url[0]) {
    case '/':
        require __DIR__ . '/home.php';
        break;
    case '':
        require __DIR__ . '/home.php';
        break;
    case '/chapters':
        require __DIR__ . '/api/chapters.php';
        break;
    case '/verses':
        require __DIR__ . '/api/verses.php';
        break;
    case '/verse':
        require __DIR__ . '/api/verse.php';
        break;
    default:
        http_response_code(404);
        require __DIR__ . '/404.php';
        break;
}
