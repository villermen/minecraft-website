<?php

namespace Villermen\Minecraft\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Villermen\Minecraft\Service\ServerinfoService;

class ApiController
{
    /** @var ServerinfoService */
    protected $serverinfoService;

    public function __construct(ServerinfoService $serverinfoService)
    {
        $this->serverinfoService = $serverinfoService;
    }

    public function serverInfoAction(Request $request, Response $response): void
    {
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($this->serverinfoService->getServerinfo()));
    }
}
