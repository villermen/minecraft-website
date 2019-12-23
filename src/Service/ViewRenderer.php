<?php

namespace Villermen\Minecraft\Service;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class ViewRenderer
{
    /** @var Environment */
    protected $twig;

    public function __construct(AppConfig $config, TwigExtensions $twigExtensions)
    {
        $loader = new FilesystemLoader($config['project_root'] . '/view');
        $this->twig = new Environment($loader);

        foreach ($twigExtensions->createFunctions() as $twigFunction) {
            $this->twig->addFunction($twigFunction);
        }
    }

    public function renderView(string $viewFile, array $parameters = []): string
    {
        return $this->twig->render($viewFile, $parameters);
    }
}
