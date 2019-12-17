<?php

namespace Villermen\Minecraft;

use Psr\Container\ContainerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Villermen\Minecraft\Controller\SimplePageController;

class App
{
    public const PROJECT_ROOT = __DIR__ . '/..';

    protected const ROUTES = [
        '/' => [SimplePageController::class, 'homepageAction'],
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

        if ($route) {
            $controller = $container->get($route[0]);
            call_user_func([$controller, $route[1]], $request, $response);
        } else {
            $this->notFoundAction($request, $response);
        }

        return $response;
    }

    protected function createContainer(): ContainerInterface
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(self::PROJECT_ROOT . '/config'));
        $loader->load('services.yml');
        $container->compile();
        return $container;
    }
}
