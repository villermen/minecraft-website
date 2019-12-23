<?php

namespace Villermen\Minecraft\Migration;

use Psr\Container\ContainerInterface;
use Villermen\Minecraft\Service\Database;

class Migrator
{
    /** @var Database */
    protected $database;

    /** @var ContainerInterface */
    protected $container;

    public function __construct(Database $database, ContainerInterface $container)
    {
        $this->database = $database;
        $this->container = $container;
    }

    public function migrate(): void
    {
        $this->database->query('CREATE TABLE IF NOT EXISTS migration (
            version INT(11) PRIMARY KEY NOT NULL,
            migration_time INT(11) NOT NULL
        )');
        $result = $this->database->query('SELECT MAX(version) AS max_version FROM migration');
        $version = ($result[0]['max_version'] ?? 0);

        echo sprintf('Current schema version: %s.', $version) . PHP_EOL;

        while (true) {
            $nextMigrationClass = __NAMESPACE__ . '\\Version' . ++$version;

            if (!$this->container->has($nextMigrationClass)) {
                break;
            }

            $migration = $this->container->get($nextMigrationClass);

            if (!($migration instanceof MigrationInterface)) {
                throw new \LogicException(sprintf(
                    'Migration %s does not implement %s.',
                    $nextMigrationClass,
                    MigrationInterface::class
                ));
            }

            echo sprintf('Migrating database to version %s...', $version) . PHP_EOL;

            $migration->execute();

            $this->database->query(
                'INSERT INTO migration (version, migration_time) VALUES (:version, :migration_time)',
                [
                    'version' => $version,
                    'migration_time' => time(),
                ]
            );
        }

        echo 'Done migrating.' . PHP_EOL;
    }
}
