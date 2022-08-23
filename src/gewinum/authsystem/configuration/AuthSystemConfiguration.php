<?php

namespace gewinum\authsystem\configuration;

use gewinum\authsystem\AuthSystemCore;
use gewinum\authsystem\constants\ConfigurationConstants;
use gewinum\authsystem\constants\FileConstants;
use pocketmine\utils\Config;

class AuthSystemConfiguration
{
    private Config $config;

    public function __construct()
    {
        $this->config = new Config($this->getCore()->getDataFolder() . FileConstants::CONFIGURATION_FILE_NAME, Config::YAML, $this->getDefaults());
    }

    public function getSetting(string $key): mixed
    {
        return $this->config->get($key, null);
    }

    private function getDefaults(): array
    {
        return [
            ConfigurationConstants::XBOX_AUTH_ENABLED => true,
            ConfigurationConstants::PASSWORD_MINIMAL_LENGTH => 3,
            ConfigurationConstants::PASSWORD_MAXIMAL_LENGTH => 200
        ];
    }

    private function getCore(): AuthSystemCore
    {
        return AuthSystemCore::getInstance();
    }
}