<?php

namespace gewinum\authsystem\database\interfaces;

use gewinum\authsystem\database\schemes\UserScheme;
use pocketmine\promise\Promise;

interface IProvider
{
    public function initialize(): void;

    public function getUserByName(string $userName): Promise;

    public function createUser(string $userName): Promise;

    public function setPassword(string $userName, string $password): Promise;

    public function setXuid(string $userName, string $xuid): Promise;
}