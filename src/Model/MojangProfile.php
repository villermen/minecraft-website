<?php

namespace Villermen\Minecraft\Model;

use Villermen\Minecraft\Service\MojangProfileService;

class MojangProfile
{
    /** @var string */
    protected $rawUuid;

    /** @var string */
    protected $formattedUuid;

    /** @var string */
    protected $name;

    /** @var string|null */
    protected $skinUrl;

    public function __construct(string $rawUuid, string $name, ?string $skinUrl)
    {
        $this->rawUuid = $rawUuid;
        $this->name = $name;
        $this->skinUrl = $skinUrl;

        $this->formattedUuid = MojangProfileService::formatUuid($rawUuid);
    }

    public function getRawUuid(): string
    {
        return $this->rawUuid;
    }

    public function getFormattedUuid(): string
    {
        return $this->formattedUuid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSkinUrl(): ?string
    {
        return $this->skinUrl;
    }
}
