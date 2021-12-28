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
        $banner = null;
        $monthDay = (int)date('nd');
        if ($monthDay >= 1220 || $monthDay <= 107) {
            $banner = 'onmate_vacguy99_creativechristmas.png';
        } else {
            $bannerGlob = sprintf('%s/public/img/banner/*.png', $this->config->getProjectRoot());
            $banners = glob($bannerGlob);
            $banner = basename($banners[mt_rand(0, count($banners) - 1)]);
        }

        return $this->pathFunction(sprintf('/img/banner/%s', $banner));
    }
}
