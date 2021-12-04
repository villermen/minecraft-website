<?php

namespace Villermen\Minecraft\Service;

use Twig\TwigFunction;

class TwigExtensions
{
    private AppConfig $config;

    public function __construct(AppConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @return TwigFunction[]
     */
    public function createFunctions(): array
    {
        return [
            new TwigFunction('path', [$this, 'pathFunction']),
            new TwigFunction('random_banner', [$this, 'randomBannerFunction']),
        ];
    }

    public function pathFunction(string $path): string
    {
        return rtrim($this->config->getBasePath(), '/') . '/' . ltrim($path, '/');
    }

    public function randomBannerFunction(): string
    {
        $bannerGlob = $this->config->getProjectRoot() . '/public/img/banner/*.png';
        $banners = glob($bannerGlob);
        $banner = $banners[mt_rand(0, count($banners) - 1)];
        return $this->pathFunction('/img/banner/' . basename($banner));
    }
}
