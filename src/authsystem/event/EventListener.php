<?php

namespace authsystem\event;

use authsystem\AuthManager;
use authsystem\AuthSystem;
use authsystem\messages\Messages;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\player\Player;

class EventListener implements Listener
{
    public function onJoin(PlayerJoinEvent $event)
    {
        $this->getSystem()->getAuthManager()->initializePlayer($event->getPlayer());
    }

    public function onQuit(PlayerQuitEvent $event)
    {
        $this->getAuthManager()->onPlayerQuit($event->getPlayer());
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event)
    {
        if($event->getOrigin()->getPlayer() === null) {
            return;
        }

        if($this->isAuthorised($event->getOrigin()->getPlayer())) {
            return;
        }

        if($event->getPacket() instanceof MovePlayerPacket) {
            $event->cancel();
        }
    }

    public function onInteract(PlayerInteractEvent $event)
    {
        if(!$this->isAuthorised($event->getPlayer())) {
            $event->cancel();
        }
    }

    public function onInventoryTransaction(InventoryTransactionEvent $event)
    {
        if(!$this->isAuthorised($event->getTransaction()->getSource())) {
            $event->cancel();
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event)
    {
        if(!$this->isAuthorised($event->getPlayer())) {
            $event->cancel();
        }
    }

    public function onBlockBreak(BlockBreakEvent $event)
    {
        if(!$this->isAuthorised($event->getPlayer())) {
            $event->cancel();
        }
    }

    public function commandPreProcess(PlayerCommandPreprocessEvent $event)
    {
        $player = $event->getPlayer();

        if($this->isAuthorised($player)) {
            return;
        }

        $event->cancel();

        $message = $event->getMessage();

        if(str_starts_with($message, "/")) {
            $player->sendMessage($this->getMessages()->get("NotAuthorisedYet"));
            return;
        }

        $this->getAuthManager()->processPassword($player, $message);
    }

    private function getSystem() : AuthSystem
    {
        return AuthSystem::getInstance();
    }

    private function getMessages() : Messages
    {
        return $this->getSystem()->getMessages();
    }

    private function getAuthManager() : AuthManager
    {
        return $this->getSystem()->getAuthManager();
    }

    private function isAuthorised(Player $player) : bool
    {
        return $this->getAuthManager()->isAuthorised($player);
    }
}