<?php

namespace Villermen\Minecraft;

use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Villermen\Minecraft\Controller\ApiController;
use Villermen\Minecraft\Controller\SimplePageController;
use Villermen\Minecraft\Service\AppConfig;

class App
{
    protected const ROUTES = [
        '/' => [SimplePageController::class, 'homepageAction'],
        '/api/server-info' => [ApiController::class, 'serverInfoAction'],
    ];

    public function run(Request $request): Response
    {
        $response = new Response();

        $path = $request->getPathInfo();

        $container = $this->createContainer();

        $route = (self::ROUTES[$path] ?? null);

        if (!$route) {
            $route = [SimplePageController::class, 'notFoundAction'];
        }

        $controller = $container->get($route[0]);
        call_user_func([$controller, $route[1]], $request, $response);

        return $response;
    }

    protected function createContainer(): ContainerInterface
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(AppConfig::PROJECT_ROOT . '/config'));
        $loader->load('services.yml');
        $container->compile();
        return $container;
    }
}
