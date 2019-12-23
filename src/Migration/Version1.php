<?php

namespace Villermen\Minecraft\Migration;

use Villermen\Minecraft\Service\Database;

class Version1 implements MigrationInterface
{
    /** @var Database */
    protected $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function execute(): void
    {
        $this->database->query('CREATE TABLE profile_cache (
            raw_uuid CHAR(32) PRIMARY KEY NOT NULL,
            name VARCHAR(16) NOT NULL,
            skin_url VARCHAR(255) NULL,
            cache_time INT(11) NOT NULL
        )');
    }
}
