<?php

namespace authsystem\data\providers\base;

use authsystem\AuthSystem;
use JetBrains\PhpStorm\Pure;
use pocketmine\utils\Config;

abstract class DataProvider
{
    protected Config $config;

    public function __construct()
    {
        $this->config = new Config($this->getDataFolder() . "data-providers/" . $this->getName() . ".yml", Config::YAML, $this->getConfigDefaults());
    }

    public abstract function getName() : string;

    public abstract function getConfigDefaults() : array;

    public abstract function load();

    public abstract function getUserData(string $name) : ?array;

    public abstract function setUserPassword(string $name, string $passwordHash) : bool;

    public abstract function setUserXuid(string $name, string $xuid) : bool;

    public abstract function loadDefaultUserData(string $name) : bool;

    protected function getSystem() : AuthSystem
    {
        return AuthSystem::getInstance();
    }

    protected function getDataFolder() : string
    {
        return $this->getSystem()->getDataFolder();
    }

}