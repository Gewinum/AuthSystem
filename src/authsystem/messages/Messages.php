<?php

namespace authsystem\messages;

use authsystem\AuthManager;
use authsystem\AuthSystem;
use pocketmine\utils\Config;

class Messages
{
    private Config $messagesConfig;

    public function __construct()
    {
        $this->messagesConfig = new Config($this->getSystem()->getDataFolder() . "messages.yml", Config::YAML, $this->getMessagesDefaults());
    }

    public function get(string $key) : string
    {
        return $this->messagesConfig->get($key);
    }

    private function getMessagesDefaults()
    {
        return [
            "NotAuthorisedYet" => "§cYou didn't register/authorise yet",
            "AskForRegister" => "§ePlease, §bregister §eby typing password into chat",
            "AskForAuthorise" => "§ePlease, §bauthorise §eby typing password into chat",
            "PasswordTooShort" => "§cTyped password is too §6short",
            "PasswordTooLong" => "§cTyped password is too §6long",
            "PasswordInvalid" => "§4Invalid password!",
            "XuidAuthoriseSuccess" => "§6XUID §bauthorisation §aSUCCESSFUL!",
            "PasswordAuthoriseSuccess" => "§aSUCCESSFUL §bauthorisation!",
            "DataProviderInvalid" => "Invalid data provider in config"
        ];
    }

    private function getSystem() : AuthSystem
    {
        return AuthSystem::getInstance();
    }
}