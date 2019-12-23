<?php

namespace Villermen\Minecraft\Model;

use Villermen\Minecraft\Service\MojangProfileService;

class MojangProfile
{
    /** @var string */
    protected $name;

    /** @var string */
    protected $rawUuid;

    /** @var string */
    protected $formattedUuid;

    public function __construct(string $rawUuid, string $name)
    {
        $this->rawUuid = $rawUuid;
        $this->name = $name;

        $this->formattedUuid = MojangProfileService::formatUuid($rawUuid);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRawUuid(): string
    {
        return $this->rawUuid;
    }

    public function getFormattedUuid(): string
    {
        return $this->formattedUuid;
    }
}
