<?php

namespace Villermen\Minecraft\Service;

use Villermen\Minecraft\Exception\ServerQueryException;
use xPaw\MinecraftPing;
use xPaw\MinecraftPingException;

class ServerInfoService
{
    private AppConfig $config;

    public function __construct(AppConfig $config)
    {
        $this->config = $config;
    }

    /**
     * @throws ServerQueryException
     */
    public function getServerInfo(): array
    {
        try {
            $ping = new MinecraftPing(
                $this->config->getMinecraftServerHost(),
                $this->config->getMinecraftServerPort(),
                1
            );
            $info = $ping->Query();

            if (!$info || !$info['version']) {
                throw new ServerQueryException(
                    'Failed to query server. It\'s probably busy doing something weird again.'
                );
            }

            // Parse only Minecraft component of version ("Spigot 1.15.1" -> "1.15.1")
            $version = null;
            if (preg_match('/([\d.]+)$/', ($info['version']['name'] ?? ''), $matches)) {
                $version = $matches[1];
            }

            $players = [];
            foreach (($info['players']['sample'] ?? []) as $player) {
                $players[] = [
                    'uuid' => $player['id'],
                    'name' => $player['name'],
                ];
            }

            return [
                'version' => $version,
                'players' => $players,
            ];
        } catch (MinecraftPingException $exception) {
            throw new ServerQueryException('Failed to query server. It\'s probably down.');
        }
    }
}
