<?php

namespace Villermen\Minecraft\Migration;

interface MigrationInterface
{
    public function execute(): void;
}
