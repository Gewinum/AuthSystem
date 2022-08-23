<?php

namespace gewinum\authsystem;

use gewinum\authsystem\configuration\AuthSystemConfiguration;
use gewinum\authsystem\database\AuthDatabase;
use gewinum\authsystem\listeners\AuthSystemListener;
use gewinum\authsystem\managers\UserSessionsManager;
use gewinum\authsystem\managers\UsersManager;
use pocketmine\plugin\PluginBase;

class AuthSystemCore extends PluginBase
{
    private static self $_instance;

    private AuthDatabase $authDatabase;

    private AuthSystemConfiguration $authSystemConfiguration;

    private UsersManager $usersManager;
    private UserSessionsManager $userSessionsManager;

    public function onEnable(): void
    {
        self::$_instance = $this;

        $this->initializeDataDirectory();
        $this->saveResources();
        $this->initializeDatabase();
        $this->initializeConfiguration();
        $this->initializeManagers();
        $this->initializeListeners();
    }

    public static function getInstance(): AuthSystemCore
    {
        return self::$_instance;
    }

    public function getAuthDatabase(): AuthDatabase
    {
        return $this->authDatabase;
    }

    public function getConfiguration(): AuthSystemConfiguration
    {
        return $this->authSystemConfiguration;
    }

    public function getUsersManager(): UsersManager
    {
        return $this->usersManager;
    }

    public function getUserSessionsManager(): UserSessionsManager
    {
        return $this->userSessionsManager;
    }

    private function initializeDataDirectory(): void
    {
        @mkdir($this->getDataFolder());
    }

    private function saveResources(): void
    {
        foreach($this->getResources() as $resource) {
            $this->saveResource($resource->getFilename());
        }
    }

    private function initializeDatabase(): void
    {
        $this->authDatabase = new AuthDatabase;
        $this->authDatabase->initialize();
    }

    private function initializeConfiguration(): void
    {
        $this->authSystemConfiguration = new AuthSystemConfiguration;
    }

    private function initializeManagers(): void
    {
        $this->usersManager = new UsersManager;
        $this->userSessionsManager = new UserSessionsManager;
    }

    private function initializeListeners(): void
    {
        $this->getServer()->getPluginManager()->registerEvents(new AuthSystemListener(), $this);
    }
}