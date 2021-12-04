<?php

namespace Villermen\Minecraft\Service;

use Symfony\Component\Yaml\Yaml;

class AppConfig
{
    public const PROJECT_ROOT = __DIR__ . '/../..';

    private array $config;

    public function __construct()
    {
        $this->config = Yaml::parseFile(self::PROJECT_ROOT . '/config/app.yml')['app'];
    }

    public function getProjectRoot(): string
    {
        return self::PROJECT_ROOT;
    }

    public function getCacheDirectory(): string
    {
        return sprintf('%s/cache', $this->getProjectRoot());
    }

    public function getResourceDirectory(): string
    {
        return sprintf('%s/resource', $this->getProjectRoot());
    }

    public function getViewDirectory(): string
    {
        return sprintf('%s/view', $this->getProjectRoot());
    }

    public function getMinecraftServerHost(): string
    {
        return $this->config['minecraft_server_host'];
    }

    public function getMinecraftServerPort(): string
    {
        return $this->config['minecraft_server_port'];
    }

    public function getBasePath(): string
    {
        return $this->config['base_path'];
    }
}
