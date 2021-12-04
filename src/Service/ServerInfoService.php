<?php

namespace Villermen\Minecraft\Service;

use xPaw\MinecraftPing;

class ServerInfoService
{
    private AppConfig $config;

    public function __construct(AppConfig $config)
    {
        $this->config = $config;
    }

    public function getServerInfo(): array
    {
        $ping = new MinecraftPing($this->config->getMinecraftServerHost(), $this->config->getMinecraftServerPort());
        $info = $ping->Query();

        // Parse only Minecraft component of version ("Spigot 1.15.1" -> "1.15.1")
        $version = null;
        if (preg_match('/([\d\.]+)$/', $info['version']['name'], $matches)) {
            $version = $matches[1];
        }

        $players = [];
        if (isset($info['players']['sample'])) {
            foreach ($info['players']['sample'] as $player) {
                $players[] = [
                    'uuid' => $player['id'],
                    'name' => $player['name'],
                ];
            }
        }

        return [
            'version' => $version,
            'players' => $players,
        ];
    }
}
