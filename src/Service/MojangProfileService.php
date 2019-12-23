<?php

namespace Villermen\Minecraft\Service;

use Villermen\Minecraft\Model\MojangProfile;

class MojangProfileService
{
    protected const DB_CACHE_LIFETIME = (60 * 60);

    /** @var Database */
    protected $database;

    /** @var MojangProfile[] */
    protected $localCache = [];

    public function __construct(Database $database)
    {
        $this->database = $database;
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

    protected function fetchProfileByName(string $name): MojangProfile
    {
        $name = strtolower($name);

        // Local cache
        $cachedProfiles = array_values(array_filter($this->localCache, function (MojangProfile $profile) use ($name) {
            return strtolower($profile->getName()) === $name;
        }));
        if (count($cachedProfiles) > 0) {
            return $cachedProfiles[0];
        }

        // DB cache
        $result = $this->database->query('SELECT raw_uuid, name, skin_url FROM profile_cache WHERE name = :name AND cache_time >= :minimum_cache_time', [
            'name' => $name,
            'minimum_cache_time' => (time() - self::DB_CACHE_LIFETIME),
        ]);
        if (count($result) > 0) {
            $profile = new MojangProfile($result[0]['raw_uuid'], $result[0]['name'], $result[0]['skin_url']);
            $this->localCache[] = $profile;
            return $profile;
        }

        // Convert to UUID using Mojang. We can't get skin URL in one go so we reuse fetchProfileByUuid() for that
        $response = json_decode(
            @file_get_contents(sprintf('https://api.mojang.com/users/profiles/minecraft/%s', urlencode($name))),
            true
        );

        if (!$response || isset($response['error'])) {
            // TODO: Custom exception
            throw new \Exception('Could not retrieve UUID by username from Mojang API.');
        }

        return $this->fetchProfileByUuid($response['id']);
    }

    protected function fetchProfileByUuid(string $rawUuid): MojangProfile
    {
        // Local cache
        $cachedProfiles = array_values(array_filter($this->localCache, function (MojangProfile $profile) use ($rawUuid) {
            return $profile->getRawUuid() === $rawUuid;
        }));
        if (count($cachedProfiles) > 0) {
            return $cachedProfiles[0];
        }

        // DB cache
        $result = $this->database->query('SELECT raw_uuid, name, skin_url FROM profile_cache WHERE raw_uuid = :raw_uuid AND cache_time >= :minimum_cache_time', [
            'raw_uuid' => $rawUuid,
            'minimum_cache_time' => (time() - self::DB_CACHE_LIFETIME),
        ]);
        if (count($result) > 0) {
            $profile = new MojangProfile($result[0]['raw_uuid'], $result[0]['name'], $result[0]['skin_url']);
            $this->localCache[] = $profile;
            return $profile;
        }

        // Call Mojang
        $response = json_decode(
            @file_get_contents(sprintf('https://sessionserver.mojang.com/session/minecraft/profile/%s', $rawUuid)),
            true
        );

        if (!$response || isset($response['error'])) {
            // TODO: Custom exception
            throw new \Exception('Could not retrieve profile by UUID from Mojang API.');
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

    protected function addToCache(MojangProfile $profile): void
    {
        $this->localCache[] = $profile;
        $this->database->query('INSERT INTO profile_cache
            (raw_uuid, name, skin_url, cache_time) VALUES (:raw_uuid, :name, :skin_url, :cache_time)
            ON DUPLICATE KEY UPDATE name=:name, cache_time=:cache_time, skin_url=:skin_url
        ', [
            'raw_uuid' => $profile->getRawUuid(),
            'name' => $profile->getName(),
            'skin_url' => $profile->getSkinUrl(),
            'cache_time' => time(),
        ]);
    }
}
