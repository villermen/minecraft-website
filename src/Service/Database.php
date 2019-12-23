<?php

namespace Villermen\Minecraft\Service;

class Database
{
    /** @var AppConfig */
    protected $config;

    /** @var \PDO|null */
    protected $pdo;

    public function __construct(AppConfig $config)
    {
        $this->config = $config;
    }

    public function query(string $query, array $parameters = []): array
    {
        $this->connect();

        $statement = $this->pdo->prepare($query);
        $statement->execute($parameters);

        // fetchAll() is not allowed when dealing with a non-SELECT query
        if ($statement->columnCount() === 0) {
            return [];
        }

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    protected function connect(): void
    {
        if ($this->pdo) {
            return;
        }

        $this->pdo = new \PDO(
            sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=UTF8',
                $this->config['database_host'],
                $this->config['database_host'],
                $this->config['database_database']
            ),
            $this->config['database_username'],
            $this->config['database_password'],
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]
        );
    }
}
