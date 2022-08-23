<?php

namespace gewinum\authsystem\database;

use gewinum\authsystem\database\base\BaseProvider;
use gewinum\authsystem\database\providers\MySqlProvider;
use gewinum\authsystem\database\providers\SqliteProvider;

class DatabaseProviders
{
    /**
     * @var BaseProvider[]
     */
    private array $providers = [];

    private BaseProvider $chosenProvider;

    public function __construct(array $databaseInfo)
    {
        $this->providers = [
            "sqlite" => new SqliteProvider,
            "mysql" => new MySqlProvider
        ];

        $this->chosenProvider = $this->providers[$databaseInfo["type"]];
    }

    public function getProvider(string $providerName): ?BaseProvider
    {
        return $this->chosenProvider[$providerName] ?? null;
    }

    public function getChosenProvider(): BaseProvider
    {
        return $this->chosenProvider;
    }
}