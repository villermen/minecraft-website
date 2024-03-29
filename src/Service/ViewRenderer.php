<?php

namespace Villermen\Minecraft\Service;

use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ViewRenderer
{
    private Environment $twig;

    public function __construct(AppConfig $config, TwigExtensions $twigExtensions, ContainerInterface $container)
    {
        $loader = new FilesystemLoader($config->getViewDirectory());
        $this->twig = new Environment($loader);

        foreach ($twigExtensions->createFunctions() as $twigFunction) {
            $this->twig->addFunction($twigFunction);
        }

        $this->twig->addGlobal('request', $container->get('request'));
        $this->twig->addGlobal('response', $container->get('response'));
    }

    public function renderView(string $viewFile, array $parameters = []): string
    {
        return $this->twig->render($viewFile, $parameters);
    }
}
