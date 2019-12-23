<?php

namespace Villermen\Minecraft\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Villermen\Minecraft\Service\ServerinfoService;
use Villermen\Minecraft\Service\ViewRenderer;

class SimplePageController
{
    /** @var ViewRenderer */
    protected $viewRenderer;

    /** @var ServerinfoService */
    protected $serverinfoService;

    public function __construct(ViewRenderer $viewRenderer, ServerinfoService $serverinfoService)
    {
        $this->viewRenderer = $viewRenderer;
        $this->serverinfoService = $serverinfoService;
    }

    public function homepageAction(Request $request, Response $response): void
    {
        $response->setContent($this->viewRenderer->renderView('page/home.html.twig'));
    }

    public function rulesAction(Request $request, Response $response): void
    {
        $response->setContent($this->viewRenderer->renderView('page/rules.html.twig'));
    }

    public function onlineAction(Request $request, Response $response): void
    {
        $response->setContent($this->viewRenderer->renderView('page/online.html.twig', [
            'players' => $this->serverinfoService->getServerinfo()['players'],
        ]));
    }

    public function notFoundAction(Request $request, Response $response): void
    {
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $response->setContent($this->viewRenderer->renderView('not-found.html.twig'));
    }
}
