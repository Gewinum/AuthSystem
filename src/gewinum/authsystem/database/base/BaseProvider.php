<?php

namespace gewinum\authsystem\database\base;

use gewinum\authsystem\AuthSystemCore;
use gewinum\authsystem\database\AuthDatabase;
use gewinum\authsystem\database\interfaces\IProvider;
use poggit\libasynql\DataConnector;

abstract class BaseProvider implements IProvider
{
    protected function getCore(): AuthSystemCore
    {
        return AuthSystemCore::getInstance();
    }

    protected function getDatabase(): AuthDatabase
    {
        return $this->getCore()->getAuthDatabase();
    }

    protected function getConnection(): DataConnector
    {
        return $this->getDatabase()->getConnection();
    }
}