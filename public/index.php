<?php

use Symfony\Component\HttpFoundation\Request;
use Villermen\Minecraft\App;

require_once(__DIR__ . '/../vendor/autoload.php');

$request = Request::createFromGlobals();
$app = new App();
$response = $app->run($request);
$response->prepare($request);
$response->send();
