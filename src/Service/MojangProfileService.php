<?php

namespace Villermen\Minecraft\Service;

use Villermen\Minecraft\Exception\ProfileFetchException;
use Villermen\Minecraft\Model\MojangProfile;

class MojangProfileService
{
    private const FILE_CACHE_LIFETIME = (60 * 60);

    private AppConfig $config;

    /** @var MojangProfile[] */
    private array $localCache = [];

    public function __construct(AppConfig $config)
    {
        $this->config = $config;
    }

    public function resolveProfile(string $nameOrUuid): MojangProfile
    {
        if ($this->isValidName($nameOrUuid)) {
            return $this->fetchProfileByName($nameOrUuid);
        }

        if ($this->isValidUuid($nameOrUuid)) {
            $rawUuid = self::formatUuid($nameOrUuid, true);
            return $this->fetchProfileByUuid($rawUuid);
        }

        throw new \InvalidArgumentException('Passed value does not appear to be a valid Minecraft name or UUID.');
    }

    public static function isValidName(string $name): bool
    {
        return preg_match('/^[a-zA-Z0-9_]{3,16}$/', $name);
    }

    public static function isValidUuid(string $uuid): bool
    {
        return preg_match('/^([0-9a-f]{32}|[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12})$/', $uuid);
    }

    public static function formatUuid(string $uuid, bool $raw = false): string
    {
        if (!self::isValidUuid($uuid)) {
            throw new \InvalidArgumentException('Passed UUID is not a valid Minecraft UUID.');
        }

        // Convert to raw UUID so we know what we're dealing with.
        $uuid = str_replace('-', '', $uuid);

        if ($raw) {
            return $uuid;
        }

        return (
            substr($uuid, 0, 8) . '-' .
            substr($uuid, 8, 4) . '-' .
            substr($uuid, 12, 4) . '-' .
            substr($uuid, 16, 4) . '-' .
            substr($uuid, 20, 12)
        );
    }

    /**
     * @throws ProfileFetchException
     */
    private function fetchProfileByName(string $name): MojangProfile
    {
        $name = strtolower($name);

        // Local cache
        $cachedProfiles = array_values(array_filter($this->localCache, function (MojangProfile $profile) use ($name) {
            return strtolower($profile->getName()) === $name;
        }));
        if (count($cachedProfiles) > 0) {
            return $cachedProfiles[0];
        }

        // File cache
        $fileProfile = $this->getFileCacheProfile(null, $name);
        if ($fileProfile) {
            $this->localCache[] = $fileProfile;
            return $fileProfile;
        }

        // Convert to UUID using Mojang. We can't get skin URL in one go so we reuse fetchProfileByUuid() for that
        $response = json_decode(
            @file_get_contents(sprintf('https://api.mojang.com/users/profiles/minecraft/%s', urlencode($name))),
            true
        );

        if (!$response || isset($response['error'])) {
            throw new ProfileFetchException('Could not retrieve UUID by username from Mojang API.');
        }

        return $this->fetchProfileByUuid($response['id']);
    }

    /**
     * @throws ProfileFetchException
     */
    private function fetchProfileByUuid(string $rawUuid): MojangProfile
    {
        // Local cache
        $cachedProfiles = array_values(array_filter($this->localCache, function (MojangProfile $profile) use ($rawUuid) {
            return $profile->getRawUuid() === $rawUuid;
        }));
        if (count($cachedProfiles) > 0) {
            return $cachedProfiles[0];
        }

        // File cache
        $fileProfile = $this->getFileCacheProfile($rawUuid, null);
        if ($fileProfile) {
            $this->localCache[] = $fileProfile;
            return $fileProfile;
        }

        // Call Mojang
        $response = json_decode(
            @file_get_contents(sprintf('https://sessionserver.mojang.com/session/minecraft/profile/%s', $rawUuid)),
            true
        );

        if (!$response || isset($response['error'])) {
            throw new ProfileFetchException('Could not retrieve profile by UUID from Mojang API.');
        }

        $name = $response['name'];

        $skinUrl = null;
        foreach ($response['properties'] as $property) {
            if ($property['name'] === 'textures') {
                $value = json_decode(base64_decode($property['value']), true);
                if (isset($value['textures']['SKIN'])) {
                    $skinUrl = $value['textures']['SKIN']['url'];
                }
                break;
            }
        }

        $profile = new MojangProfile($rawUuid, $name, $skinUrl);
        $this->addToCache($profile);
        return $profile;
    }

    private function addToCache(MojangProfile $profile): void
    {
        $this->localCache[] = $profile;

        foreach (array_unique(array_merge(
            $this->getCacheFiles($profile->getRawUuid(), null),
            $this->getCacheFiles(null, $profile->getName()),
        )) as $cacheFile) {
            unlink($cacheFile);
        }
        file_put_contents(sprintf(
            '%s/profile-%s-%s.json',
            $this->config->getCacheDirectory(),
            $profile->getRawUuid(),
            $profile->getName()
        ), json_encode([
            'raw_uuid' => $profile->getRawUuid(),
            'name' => $profile->getName(),
            'skin_url' => $profile->getSkinUrl(),
        ]));
    }

    private function getFileCacheProfile(?string $uuid, ?string $name): ?MojangProfile
    {
        $cacheFiles = $this->getCacheFiles($uuid, $name);
        if (count($cacheFiles) === 0) {
            return null;
        }
        if (count($cacheFiles) > 1) {
            throw new \Exception('Multiple cache files for the same profile were obtained.');
        }

        if (filemtime($cacheFiles[0]) < time() - self::FILE_CACHE_LIFETIME) {
            return null;
        }

        $cachedData = json_decode(file_get_contents($cacheFiles[0]), true);
        return new MojangProfile(
            $cachedData['raw_uuid'],
            $cachedData['name'],
            $cachedData['skin_url'],
        );
    }

    private function getCacheFiles(?string $uuid, ?string $name): array
    {
        if (!$uuid && !$name) {
            throw new \InvalidArgumentException('At least one of UUID and name must be supplied.');
        }

        if ($uuid) {
            $uuid = strtolower($uuid);
            if (!preg_match('/^[a-f0-9]+$/', $uuid)) {
                throw new \InvalidArgumentException('UUID has an invalid format.');
            }
        } else {
            $uuid = '*';
        }

        if ($name) {
            $name = strtolower($name);
            if (!preg_match('/^[a-z0-9_]+$/', $name)) {
                throw new \InvalidArgumentException('Name has an invalid format.');
            }
        } else {
            $name = '*';
        }

        return glob(sprintf('%s/profile-%s-%s.json', $this->config->getCacheDirectory(), $uuid, $name));
    }
}
