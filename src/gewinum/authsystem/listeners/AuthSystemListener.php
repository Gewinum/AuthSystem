<?php

namespace gewinum\authsystem\listeners;

use gewinum\authsystem\AuthSystemCore;
use gewinum\authsystem\managers\UserSessionsManager;
use gewinum\authsystem\managers\UsersManager;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\player\Player;

class AuthSystemListener implements Listener
{
    public function onPlayerJoin(PlayerJoinEvent $event)
    {
        $this->getCore()->getUsersManager()->initializeUser($event->getPlayer());
    }

    public function onPlayerQuit(PlayerQuitEvent $event)
    {
        $this->getUserSessionsManager()->deleteUserSession($event->getPlayer());
    }

    public function onBlockPlace(BlockPlaceEvent $event)
    {
        $player = $event->getPlayer();

        if (!$this->getUserSessionsManager()->isUserAuthorised($player)) {
            $event->cancel();
        }
    }

    public function onBlockBreak(BlockBreakEvent $event)
    {
        $player = $event->getPlayer();

        if (!$this->getUserSessionsManager()->isUserAuthorised($player)) {
            $event->cancel();
        }
    }

    public function onInventoryTransaction(InventoryTransactionEvent $event)
    {
        $player = $event->getTransaction()->getSource();

        if (!$this->getUserSessionsManager()->isUserAuthorised($player)) {
            $event->cancel();
        }
    }

    public function onPlayerMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();

        if (!$this->getUserSessionsManager()->isUserAuthorised($player)) {
            $event->cancel();
        }
    }

    /**
     * @priority LOWEST
     */
    public function onPlayerChat(PlayerChatEvent $event)
    {
        $player = $event->getPlayer();

        $session = $this->getUserSessionsManager()->getUserSession($event->getPlayer());

        if ($session === null) {
            $event->cancel();
            return;
        }

        if (!$session->authorised) {
            $event->cancel();
            $this->getUsersManager()->processPassword($player, $event->getMessage());
            return;
        }
    }

    /**
     * @priority LOWEST
     */
    public function onCommand(CommandEvent $event)
    {
        $sender = $event->getSender();

        if (!$sender instanceof Player) {
            return;
        }

        if (!$this->getUserSessionsManager()->isUserAuthorised($sender)) {
            $event->cancel();
        }
    }

    private function getCore(): AuthSystemCore
    {
        return AuthSystemCore::getInstance();
    }

    private function getUsersManager(): UsersManager
    {
        return $this->getCore()->getUsersManager();
    }

    private function getUserSessionsManager(): UserSessionsManager
    {
        return $this->getCore()->getUserSessionsManager();
    }
}