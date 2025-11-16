<?php

// Let dev-server handle regular files.
if (php_sapi_name() === 'cli-server') {
    if (preg_match('~^(/[^#?]+)~', $_SERVER['REQUEST_URI'], $matches)) {
        if (is_file(__DIR__ . $matches[1])) {
            return false;
        }
    }
}

use Symfony\Component\HttpFoundation\Request;
use Villermen\Minecraft\App;

require_once(__DIR__ . '/../vendor/autoload.php');

$request = Request::createFromGlobals();
$app = new App();
$response = $app->run($request);
$response->prepare($request);
$response->send();
