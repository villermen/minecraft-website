<?php

namespace Villermen\Minecraft\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Villermen\Minecraft\Service\ViewRenderer;

class SimplePageController
{
    /** @var ViewRenderer */
    protected $viewRenderer;

    public function __construct(ViewRenderer $viewRenderer)
    {
        $this->viewRenderer = $viewRenderer;
    }

    public function homepageAction(Request $request, Response $response): void
    {
        $response->setContent($this->viewRenderer->renderView('page/home.html.twig'));
    }

    public function notFoundAction(Request $request, Response $response): void
    {
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $response->setContent($this->viewRenderer->renderView('not-found.html.twig'));
    }
}
