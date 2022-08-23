<?php

namespace gewinum\authsystem\managers;

use gewinum\authsystem\base\BaseManager;
use gewinum\authsystem\constants\ConfigurationConstants;
use gewinum\authsystem\database\schemes\UserScheme;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class UsersManager extends BaseManager
{
    public function initializeUser(Player $user): void
    {
        $user->setImmobile();

        $this->tryToGetUser($user);
    }

    public function processPassword(Player $user, string $password)
    {
        $userScheme = $this->getUserSessionsManager()->getUserSession($user)->scheme;

        $hasToRegister = !isset($userScheme->password);

        if ($hasToRegister) {
            $this->processRegistration($user, $password);
        } else {
            $this->processAuthorisation($user, $password);
        }
    }

    private function processRegistration(Player $user, string $password): void
    {
        $minimalPasswordLength = $this->getConfiguration()->getSetting(ConfigurationConstants::PASSWORD_MINIMAL_LENGTH);
        $maximalPasswordLength = $this->getConfiguration()->getSetting(ConfigurationConstants::PASSWORD_MAXIMAL_LENGTH);

        if (strlen($password) < $minimalPasswordLength or strlen($password) > $maximalPasswordLength) {
            $user->sendMessage(TextFormat::YELLOW . "Password must contain from $minimalPasswordLength letters to $maximalPasswordLength.");
            return;
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $this->getChosenDatabaseProvider()->setPassword($user->getName(), $hash)
            ->onCompletion(
                function(int $affectedRows) use($user) {
                    $this->successAuth($user, true);
                },
                function() use ($user) {
                    $user->kick("Some problems with register now. Try again later, please");
                }
            );
    }

    private function processAuthorisation(Player $user, string $password): void
    {
        $userScheme = $this->getUserSessionsManager()->getUserSession($user)->scheme;

        $status = password_verify($password, $userScheme->password);

        if (!$status) {
            $user->sendMessage(TextFormat::RED . "Invalid password");
        } else {
            $this->successAuth($user, false);
        }
    }

    private function successAuth(Player $user, bool $isRegister): void
    {
        $user->setImmobile(false);

        $user->sendMessage(TextFormat::GREEN . "Success authentication!");

        $this->getUserSessionsManager()->setUserAuthorised($user);

        if ($user->getXuid() !== "") {
            $this->getChosenDatabaseProvider()->setXuid($user->getName(), $user->getXuid());
        }
    }

    private function postInitializeUser(Player $user, UserScheme $userScheme): void
    {
        $hasToRegister = !isset($userScheme->password);

        $this->getUserSessionsManager()->createUserSession($user, $userScheme);

        if ($this->tryAutoAuth($user, $userScheme)) {
            $user->sendMessage(TextFormat::GREEN . "Log in with XUID");
            $this->successAuth($user, false);
            return;
        }

        if ($hasToRegister) {
            $user->sendMessage(TextFormat::YELLOW . "Please, type in your password for register.");
        } else {
            $user->sendMessage(TextFormat::YELLOW . "Please, type in your password for authorise.");
        }
    }

    private function tryAutoAuth(Player $user, UserScheme $userScheme): bool
    {
        if (!$this->getConfiguration()->getSetting(ConfigurationConstants::XBOX_AUTH_ENABLED)) {
            return false;
        }

        if ($user->getXuid() === "" or !isset($userScheme->xuid)) {
            return false;
        }

        return $user->getXuid() === $userScheme->xuid;
    }

    private function tryToGetUser(Player $user): void
    {
        $this->getChosenDatabaseProvider()->getUserByName($user->getName())
            ->onCompletion(
                function(?UserScheme $userScheme) use($user) {
                    if ($userScheme === null) {
                        $this->tryToRegisterUser($user);
                        return;
                    }

                    $this->postInitializeUser($user, $userScheme);
                },
                function() use($user) {
                    $this->getCore()->getLogger()->error("Unexpected error happened");
                    $user->kick("There is some trouble with auth, sorry.");
                }
            );
    }

    private function tryToRegisterUser(Player $user): void
    {
        $this->getChosenDatabaseProvider()->createUser($user->getName())
            ->onCompletion(
                function(int $affectedRows) use($user) {
                    $this->tryToGetUser($user);
                },
                function() use($user) {
                    $this->getCore()->getLogger()->error("Unexpected error happened");
                    $user->kick("There is some trouble with register, sorry.");
                }
            );
    }

    private function getUserSessionsManager(): UserSessionsManager
    {
        return $this->getCore()->getUserSessionsManager();
    }
}