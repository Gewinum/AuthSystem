<?php

namespace authsystem\data\providers;

use authsystem\data\providers\base\DataProvider;
use pocketmine\utils\Config;

class ConfigDataProvider extends DataProvider
{
    private Config $dataConfig;

    public function getName() : string
    {
        return "config";
    }

    public function getConfigDefaults() : array
    {
        return [

        ];
    }

    public function load()
    {
        $this->dataConfig = new Config($this->getDataFolder() . "playerData.json", Config::JSON);
    }

    public function getUserData(string $name) : ?array
    {
        return $this->dataConfig->get($name, null);
    }

    public function setUserPassword(string $name, string $passwordHash) : bool
    {
        if($this->getUserData($name) === null) {
            return false;
        }

        $this->dataConfig->setNested($name . ".password", $passwordHash);

        $this->dataConfig->save();

        return true;
    }

    public function setUserXuid(string $name, string $xuid) : bool
    {
        if($this->getUserData($name) === null) {
            return false;
        }

        $this->dataConfig->setNested($name . ".xuid", $xuid);

        $this->dataConfig->save();

        return true;
    }

    public function loadDefaultUserData(string $name) : bool
    {
        if($this->getUserData($name) !== null) {
            return false;
        }

        $this->dataConfig->set($name, [
            "password" => "",
            "xuid" => ""
        ]);

        $this->dataConfig->save();

        return true;
    }
}