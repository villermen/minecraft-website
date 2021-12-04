<?php

namespace Villermen\Minecraft\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Villermen\Minecraft\Exception\ServerQueryException;
use Villermen\Minecraft\Service\ServerInfoService;
use Villermen\Minecraft\Service\ViewRenderer;

class SimplePageController
{
    private ViewRenderer $viewRenderer;
    private ServerInfoService $serverInfoService;

    public function __construct(ViewRenderer $viewRenderer, ServerInfoService $serverInfoService)
    {
        $this->viewRenderer = $viewRenderer;
        $this->serverInfoService = $serverInfoService;
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
        $players = [];
        $error = null;
        try {
            $players = $this->serverInfoService->getServerInfo()['players'];
        } catch (ServerQueryException $exception) {
            $error = $exception->getMessage();
        }

        $response->setContent($this->viewRenderer->renderView('page/online.html.twig', [
            'players' => $players,
            'error' => $error,
        ]));
    }

    public function webdevAction(Request $request, Response $response): void
    {
        $response->setContent($this->viewRenderer->renderView('page/webdev.html.twig'));
    }

    public function contactAction(Request $request, Response $response): void
    {
        $response->setContent($this->viewRenderer->renderView('page/contact.html.twig'));
    }

    public function worldsAction(Request $request, Response $response): void
    {
        $response->setContent($this->viewRenderer->renderView('page/worlds.html.twig'));
    }

    public function commandsAction(Request $request, Response $response): void
    {
        $response->setContent($this->viewRenderer->renderView('page/commands.html.twig'));
    }

    public function notFoundAction(Request $request, Response $response): void
    {
        $response->setStatusCode(Response::HTTP_NOT_FOUND);
        $response->setContent($this->viewRenderer->renderView('page/not-found.html.twig'));
    }
}
