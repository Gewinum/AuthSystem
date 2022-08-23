<?php

namespace gewinum\authsystem\managers;

use gewinum\authsystem\base\BaseManager;
use gewinum\authsystem\common\UserSession;
use gewinum\authsystem\database\schemes\UserScheme;
use pocketmine\player\Player;

class UserSessionsManager extends BaseManager
{
    /**
     * @var array<string, UserSession>
     */
    private array $userSessions = [];

    public function getUserSession(Player $user): ?UserSession
    {
        return $this->userSessions[$user->getName()] ?? null;
    }

    public function isUserAuthorised(Player $user): bool
    {
        $session = $this->getUserSession($user);

        if ($session === null) {
            return false;
        }

        return $session->authorised;
    }

    public function setUserAuthorised(Player $user, bool $status = true): void
    {
        $session = $this->getUserSession($user);

        if ($session === null) {
            return;
        }

        $session->authorised = $status;

        $this->userSessions[$user->getName()] = $session;
    }

    public function createUserSession(Player $user, UserScheme $userScheme): void
    {
        if ($this->getUserSession($user) !== null) {
            return;
        }

        $userSession = new UserSession;

        $userSession->authorised = false;
        $userSession->scheme = $userScheme;

        $this->userSessions[$user->getName()] = $userSession;
    }

    public function deleteUserSession(Player $user): void
    {
        if ($this->getUserSession($user) !== null) {
            unset($this->userSessions[$user->getName()]);
        }
    }
}