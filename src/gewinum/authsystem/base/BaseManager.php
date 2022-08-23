<?php

namespace gewinum\authsystem\base;

use gewinum\authsystem\AuthSystemCore;
use gewinum\authsystem\configuration\AuthSystemConfiguration;
use gewinum\authsystem\database\AuthDatabase;
use gewinum\authsystem\database\base\BaseProvider;

abstract class BaseManager
{
    protected function getCore(): AuthSystemCore
    {
        return AuthSystemCore::getInstance();
    }

    protected function getDatabase(): AuthDatabase
    {
        return $this->getCore()->getAuthDatabase();
    }

    protected function getChosenDatabaseProvider(): BaseProvider
    {
        return $this->getDatabase()->getChosenProvider();
    }

    protected function getConfiguration(): AuthSystemConfiguration
    {
        return $this->getCore()->getConfiguration();
    }
}