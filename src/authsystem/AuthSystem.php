<?php

namespace authsystem;

use authsystem\event\EventListener;
use authsystem\messages\Messages;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

class AuthSystem extends PluginBase
{
    private static AuthSystem $_instance;

    private Messages $messages;
    private DataProviders $dataProviders;
    private AuthManager $authManager;

    public function onEnable() : void
    {
        self::$_instance = $this;

        $this->createNecessaryFolders();
        $this->initializeAll();

        $this->getLogger()->info("AuthSystem loaded. Made with love.");
        $this->getLogger()->info("Plugin owner @Gewinum.");
    }

    public static function getInstance() : AuthSystem
    {
        return self::$_instance;
    }

    public function getMessages() : Messages
    {
        return $this->messages;
    }

    public function getDataProviders() : DataProviders
    {
        return $this->dataProviders;
    }

    public function getAuthManager() : AuthManager
    {
        return $this->authManager;
    }

    private function createNecessaryFolders()
    {
        @mkdir($this->getDataFolder());
        @mkdir($this->getDataFolder() . "data-providers");
    }

    private function initializeAll()
    {
        $this->messages = new Messages;
        $this->dataProviders = new DataProviders;
        $this->authManager = new AuthManager;

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }
}