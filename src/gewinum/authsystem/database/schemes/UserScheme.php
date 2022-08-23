<?php

namespace gewinum\authsystem\database\schemes;

use gewinum\authsystem\database\base\BaseScheme;

class UserScheme extends BaseScheme
{
    public int $id;

    public string $name;

    public ?string $password;

    public ?string $xuid;
}