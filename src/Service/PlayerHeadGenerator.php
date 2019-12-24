<?php

namespace Villermen\Minecraft\Service;

use Villermen\Minecraft\Model\MojangProfile;

class PlayerHeadGenerator
{
    protected const HEAD_CACHE_TIME = (60 * 60);

    /** @var AppConfig */
    protected $config;

    public function __construct(AppConfig $config)
    {
        $this->config = $config;
    }

    public function createPlayerHeadPng(MojangProfile $profile, int $size): string
    {
        if ($size < 9 || $size > 2048) {
            throw new \InvalidArgumentException(sprintf('Unsupported player head size "%s".', $size));
        }

        $cacheDirectory = $this->config['project_root'] . '/cache';

        $headFile = $cacheDirectory . sprintf('/head-%s-%s.png', $profile->getRawUuid(), $size);

        if (file_exists($headFile) && filemtime($headFile) >= time() - self::HEAD_CACHE_TIME) {
            return file_get_contents($headFile);
        }

        // Clean up cache (we're doing heavy stuff now anyway)
        foreach (glob($cacheDirectory . '/head-*-*.png') as $cacheFile) {
            if (filemtime($cacheFile) < time() - self::HEAD_CACHE_TIME) {
                unlink($cacheFile);
            }
        }

        $skinUrl = ($profile->getSkinUrl() ?? $this->config['project_root'] . '/resource/steve-skin.png');

        $skinGd = @imagecreatefromstring(file_get_contents($skinUrl));
        $headGd = imagecreatetruecolor($size, $size);
        $transparentColor = imagecolorallocate($headGd, 246, 168, 96);
        imagecolortransparent($headGd, $transparentColor);
        imagefilledrectangle($headGd, 0, 0, $size, $size, $transparentColor);
        imagecolortransparent($headGd);
        imagecopyresized($headGd, $skinGd, $size / 18, $size / 18, 8, 8, 8 / 9 * $size, 8 / 9 * $size, 8, 8);
        imagecopyresized($headGd, $skinGd, 0, 0, 40, 8, $size, $size, 8, 8);
        imagedestroy($skinGd);

        imagepng($headGd, $headFile);
        return file_get_contents($headFile);
    }
}
