<?php

namespace Villermen\Minecraft\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Villermen\Minecraft\Service\MojangProfileService;
use Villermen\Minecraft\Service\PlayerHeadGenerator;
use Villermen\Minecraft\Service\ServerinfoService;

class ApiController
{
    /** @var ServerinfoService */
    protected $serverinfoService;

    /** @var MojangProfileService */
    protected $mojangProfileService;

    /** @var PlayerHeadGenerator */
    protected $playerHeadGenerator;

    public function __construct(
        ServerinfoService $serverinfoService,
        MojangProfileService $mojangProfileService,
        PlayerHeadGenerator $playerHeadGenerator
    ) {
        $this->serverinfoService = $serverinfoService;
        $this->mojangProfileService = $mojangProfileService;
        $this->playerHeadGenerator = $playerHeadGenerator;
    }

    public function serverInfoAction(Request $request, Response $response): void
    {
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($this->serverinfoService->getServerinfo()));
    }

    public function playerHeadAction(Request $request, Response $response): void
    {
        if (!$request->query->has('player')) {
            $response->setStatusCode(Response::HTTP_BAD_REQUEST);
            $response->setContent('"player" query parameter is required.');
            return;
        }

        $size = (int)$request->query->get('size');
        if ($size < 18 || $size > 1024) {
            $size = 18;
        }

        $profile = $this->mojangProfileService->resolveProfile($request->query->get('player'));
        $headPng = $this->playerHeadGenerator->createPlayerHeadPng($profile, $size);

        $response->headers->set('Content-Type', 'image/png');
        $response->setContent($headPng);
    }
}
