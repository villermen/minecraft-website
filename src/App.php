<?php

namespace Villermen\Minecraft;

use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Villermen\Minecraft\Controller\ApiController;
use Villermen\Minecraft\Controller\DonationController;
use Villermen\Minecraft\Controller\SimplePageController;
use Villermen\Minecraft\Service\AppConfig;

class App
{
    protected const ROUTES = [
        '/' => [SimplePageController::class, 'homepageAction'],
        '/rules' => [SimplePageController::class, 'rulesAction'],
        '/online' => [SimplePageController::class, 'onlineAction'],
        '/webdev' => [SimplePageController::class, 'webdevAction'],
        '/contact' => [SimplePageController::class, 'contactAction'],
        '/donating' => [DonationController::class, 'donatingAction'],
        '/worlds' => [SimplePageController::class, 'worldsAction'],
        '/commands' => [SimplePageController::class, 'commandsAction'],
        '/api/server-info' => [ApiController::class, 'serverInfoAction'],
        '/api/player-head' => [ApiController::class, 'playerHeadAction'],
    ];

    public function run(Request $request): Response
    {
        $response = new Response();

        $path = $request->getPathInfo();

        $container = self::createContainer($request, $response);

        $route = (self::ROUTES[$path] ?? null);

        if (!$route) {
            $route = [SimplePageController::class, 'notFoundAction'];
        }

        $controller = $container->get($route[0]);
        call_user_func([$controller, $route[1]], $request, $response);

        return $response;
    }

    public static function createContainer(?Request $request = null, ?Response $response = null): ContainerInterface
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(AppConfig::PROJECT_ROOT . '/config'));
        $loader->load('services.yml');
        $container->set('request', ($request ?? new Request()));
        $container->set('response', ($response ?? new Response()));
        $container->compile();
        return $container;
    }
}
