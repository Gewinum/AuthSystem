<?php

namespace gewinum\authsystem\database\providers;

use gewinum\authsystem\constants\DatabaseQueriesConstants;
use gewinum\authsystem\database\base\BaseProvider;
use gewinum\authsystem\database\schemes\UserScheme;
use pocketmine\promise\Promise;
use pocketmine\promise\PromiseResolver;
use poggit\libasynql\SqlError;

class MySqlProvider extends BaseProvider
{
    public function initialize(): void
    {
        $this->getConnection()->executeGeneric(DatabaseQueriesConstants::INITIALIZE);
    }

    public function getUserByName(string $userName): Promise
    {
        $resolver = new PromiseResolver();

        $this->getConnection()->executeSelect(DatabaseQueriesConstants::GET_USER,
            ["username" => $userName],
            function(array $rows) use($resolver) {
                if (!isset($rows[0])) {
                    $resolver->resolve(null);
                    return;
                }

                $userScheme = new UserScheme();

                $userScheme->exportFromArray($rows[0]);

                $resolver->resolve($userScheme);
            },
            function(SqlError $sqlError) use($resolver) {
                $resolver->reject();
            }
        );

        return $resolver->getPromise();
    }

    public function createUser(string $userName): Promise
    {
        $resolver = new PromiseResolver();

        $this->getConnection()->executeInsert(DatabaseQueriesConstants::CREATE_USER,
            ["username" => $userName],
            function(int $insertedId) use($resolver) {
                $resolver->resolve($insertedId);
            },
            function(SqlError $sqlError) use($resolver) {
                $resolver->reject();
            }
        );

        return $resolver->getPromise();
    }

    public function setPassword(string $userName, string $password): Promise
    {
        $resolver = new PromiseResolver();

        $this->getConnection()->executeChange(DatabaseQueriesConstants::SET_USER_PASSWORD,
            ["username" => $userName, "password" => $password],
            function(int $affectedRows) use($resolver) {
                $resolver->resolve($affectedRows);
            },
            function(SqlError $sqlError) use($resolver) {
                $resolver->reject();
            }
        );

        return $resolver->getPromise();
    }

    public function setXuid(string $userName, string $xuid): Promise
    {
        $resolver = new PromiseResolver();

        $this->getConnection()->executeChange(DatabaseQueriesConstants::SET_USER_XUID,
            ["username" => $userName, "xuid" => $xuid],
            function(int $affectedRows) use($resolver) {
                $resolver->resolve($affectedRows);
            },
            function(SqlError $sqlError) use($resolver) {
                $resolver->reject();
            }
        );

        return $resolver->getPromise();
    }
}