<?php

namespace authsystem;

use authsystem\data\providers\base\DataProvider;
use authsystem\messages\Messages;
use http\Exception\BadConversionException;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class AuthManager
{
    private Config $settings;

    private array $playersToAuth;

    public function __construct()
    {
        $this->settings = new Config($this->getSystem()->getDataFolder() . "settings.yml", Config::YAML, $this->getConfigDefaults());

        $this->playersToAuth = [];

        $this->loadDefaultProvider();
    }

    public function isAuthorised(Player $player) : bool
    {
        return !in_array($player->getName(), $this->playersToAuth);
    }

    public function initializePlayer(Player $player)
    {
        $this->getCurrentDataProvider()->loadDefaultUserData($player->getName());

        $userData = $this->getCurrentDataProvider()->getUserData($player->getName());

        if($this->settings->get("xuid-authorization") and $this->tryToAuthoriseXuid($player, $userData)) {
            $this->loginPlayer($player, true);
            return;
        }

        $this->setAuthorised($player, false);

        if($userData["password"] === "") {
            $player->sendMessage($this->getKey("AskForRegister"));
        } else {
            $player->sendMessage($this->getKey("AskForAuthorise"));
        }
    }

    public function onPlayerQuit(Player $player)
    {
        $this->playersToAuth = array_diff($this->playersToAuth, array($player->getName()));
    }

    public function processPassword(Player $player, string $password)
    {
        $userData = $this->getCurrentDataProvider()->getUserData($player->getName());

        $hasToRegister = $userData["password"] === "";

        if($hasToRegister) {
            $this->tryToRegister($player, $password);
        } else {
            $this->tryToLogin($player, $password, $userData);
        }
    }

    private function tryToRegister(Player $player, string $password)
    {
        $minimalPasswordLength = $this->settings->get("minimal-password-length");
        $maximalPasswordLength = $this->settings->get("maximal-password-length");

        if(strlen($password) < $minimalPasswordLength) {
            $player->sendMessage($this->getKey("PasswordTooShort"));
            return;
        }

        if(strlen($password) > $maximalPasswordLength) {
            $player->sendMessage($this->getKey("PasswordTooLong"));
            return;
        }

        $this->getCurrentDataProvider()->setUserPassword($player->getName(), password_hash($password, PASSWORD_DEFAULT));
        $this->getCurrentDataProvider()->setUserXuid($player->getName(), $player->getXuid());
        $this->loginPlayer($player, false);
    }

    private function tryToLogin(Player $player, string $password, array $userData)
    {
        if(!password_verify($password, $userData["password"])) {
            $player->kick($this->getKey("PasswordInvalid"));
            return;
        }

        $this->loginPlayer($player, false);
    }

    private function loginPlayer(Player $player, bool $xuid)
    {
        if($xuid) {
            $player->sendMessage($this->getKey("XuidAuthoriseSuccess"));
        } else {
            $player->sendMessage($this->getKey("PasswordAuthoriseSuccess"));
        }

        $this->setAuthorised($player, true);
    }

    private function tryToAuthoriseXuid(Player $player, array $userData) : bool
    {
        if($userData["xuid"] === "" or $player->getXuid() === "") {
            return false;
        }

        return $player->getXuid() === $userData["xuid"];
    }

    private function setAuthorised(Player $player, bool $status)
    {
        if(!$status and $this->isAuthorised($player)) {
            $this->playersToAuth[] = $player->getName();
        } elseif(!$this->isAuthorised($player)) {
            $this->playersToAuth = array_diff($this->playersToAuth, array($player->getName()));
        }

        $player->setImmobile(!$status);
    }

    private function loadDefaultProvider()
    {
        $provider = $this->getDataProviders()->getProviderByName($this->settings->get("data-provider"));

        if($provider === null) {
            throw new \Exception($this->getKey("DataProviderInvalid"));
        }

        $this->getDataProviders()->setCurrent($provider);
    }

    private function getSystem() : AuthSystem
    {
        return AuthSystem::getInstance();
    }

    private function getMessages() : Messages
    {
        return $this->getSystem()->getMessages();
    }

    private function getKey(string $key) : string
    {
        return $this->getMessages()->get($key);
    }

    private function getDataProviders() : DataProviders
    {
        return $this->getSystem()->getDataProviders();
    }

    private function getCurrentDataProvider() : DataProvider
    {
        return $this->getDataProviders()->getCurrent();
    }

    private function getConfigDefaults()
    {
        return [
            "data-provider" => "config",
            "xuid-authorization" => true,
            "minimal-password-length" => 6,
            "maximal-password-length" => 50
        ];
    }
}