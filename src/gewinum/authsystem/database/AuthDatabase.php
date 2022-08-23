<?php

namespace gewinum\authsystem\database;

use gewinum\authsystem\AuthSystemCore;
use gewinum\authsystem\constants\DatabaseQueriesConstants;
use gewinum\authsystem\constants\FileConstants;
use Exception;
use gewinum\authsystem\database\base\BaseProvider;
use gewinum\authsystem\database\schemes\UserScheme;
use pocketmine\promise\Promise;
use pocketmine\promise\PromiseResolver;
use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

class AuthDatabase
{
    private DataConnector $connection;

    private DatabaseProviders $providers;

    public function initialize(): void
    {
        $databaseInfo = $this->getDatabaseInfo();

        $this->connection = libasynql::create($this->getCore(), $databaseInfo, [
            "mysql" => "queries/mysql.sql",
            "sqlite" => "queries/sqlite.sql"
        ]);

        $this->providers = new DatabaseProviders($databaseInfo);

        $this->initializeDatabase();
    }

    public function getProviders(): DatabaseProviders
    {
        return $this->providers;
    }

    public function getChosenProvider(): BaseProvider
    {
        return $this->getProviders()->getChosenProvider();
    }

    public function getConnection(): DataConnector
    {
        return $this->connection;
    }

    private function getCore(): AuthSystemCore
    {
        return AuthSystemCore::getInstance();
    }

    private function getDatabaseInfo(): array
    {
        $fileName = $this->getCore()->getDataFolder() . FileConstants::DATABASE_CONFIG_NAME;

        if (!file_exists($fileName)) {
            throw new Exception("For some reason config file doesn't exist");
        }

        return yaml_parse(file_get_contents($fileName));
    }

    private function initializeDatabase(): void
    {
        $this->getChosenProvider()->initialize();
    }
}