<?php

namespace gewinum\authsystem\common;

use gewinum\authsystem\database\schemes\UserScheme;

class UserSession
{
    public UserScheme $scheme;

    public bool $authorised;
}