<?php

namespace Villermen\Minecraft\Service;

use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ViewRenderer
{
    private Environment $twig;

    public function __construct(AppConfig $config, TwigExtensions $twigExtensions, Request $request)
    {
        $loader = new FilesystemLoader($config->getViewDirectory());
        $this->twig = new Environment($loader);

        foreach ($twigExtensions->createFunctions() as $twigFunction) {
            $this->twig->addFunction($twigFunction);
        }

        $this->twig->addGlobal('request', $request);
    }

    public function renderView(string $viewFile, array $parameters = []): string
    {
        return $this->twig->render($viewFile, $parameters);
    }
}
